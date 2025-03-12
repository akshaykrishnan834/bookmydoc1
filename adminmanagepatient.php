<?php
include('db_connection.php');
include('adminheader.php');

// Handle patient deletion
if(isset($_GET['delete'])) {
    $id = mysqli_real_escape_string($conn, $_GET['delete']);
    $delete_query = "DELETE FROM patientreg WHERE id = '$id'";
    mysqli_query($conn, $delete_query);
    header('Location: manage_patients.php?msg=deleted');
}

// Pagination
$records_per_page = 10;
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$offset = ($page - 1) * $records_per_page;

// Search functionality
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$where_clause = $search ? "WHERE name LIKE '%$search%' OR email LIKE '%$search%' OR phone LIKE '%$search%'" : "";

// Count total records for pagination
$count_query = "SELECT COUNT(*) as total FROM patientreg $where_clause";
$count_result = mysqli_query($conn, $count_query);
$total_records = mysqli_fetch_assoc($count_result)['total'];
$total_pages = ceil($total_records / $records_per_page);

// Fetch patients
$query = "SELECT * FROM patientreg $where_clause
          LIMIT $offset, $records_per_page";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Patients - BookMyDoc</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-light">
    <div class="container-fluid py-4">
        <!-- Header Section -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h2 class="page-title">Manage Patients</h2>
                        <p class="text-muted">View and manage registered patients</p>
                    </div>
                    
                    <!-- Added Search Form -->
                    <div class="search-form d-flex">
                        <form action="" method="GET" class="d-flex">
                            <input type="text" name="search" class="form-control" placeholder="Search patients..." 
                                   value="<?php echo htmlspecialchars($search); ?>">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Alert Messages -->
        <?php if(isset($_GET['msg']) && $_GET['msg'] == 'deleted'): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            Patient record has been successfully deleted.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <!-- Patients Table -->
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Patient Name</th>
                                <th>Contact Information</th>
                                <th>Age/Gender</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($patient = mysqli_fetch_assoc($result)): ?>
                            <tr>
                                <td><?php echo $patient['id']; ?></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <img src="<?php echo $patient['profile_pic'] ?: 'assets/default-avatar.png'; ?>" 
                                             class="patient-avatar me-3" 
                                             onerror="this.onerror=null; this.src='images/profilepicdoct.jpg';">
                                        <div>
                                            <div class="fw-bold"><?php echo htmlspecialchars($patient['name']); ?></div>
                                            <div class="text-muted small">ID: <?php echo $patient['id']; ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div><?php echo htmlspecialchars($patient['email']); ?></div>
                                    <div class="text-muted small"><?php echo htmlspecialchars($patient['phone']); ?></div>
                                </td>
                                <td>
                                    <div><?php echo $patient['age']; ?> years</div>
                                    <div class="text-muted small"><?php echo $patient['gender']; ?></div>
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-sm btn-outline-danger" 
                                                onclick="confirmDelete(<?php echo $patient['id']; ?>)">
                                            <i class="fas fa-trash-alt"></i> Delete
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <?php if($total_pages > 1): ?>
                <nav class="mt-4">
                    <ul class="pagination justify-content-center">
                        <li class="page-item <?php echo $page <= 1 ? 'disabled' : ''; ?>">
                            <a class="page-link" href="?page=<?php echo $page-1; ?>&search=<?php echo urlencode($search); ?>">
                                Previous
                            </a>
                        </li>
                        <?php for($i = 1; $i <= $total_pages; $i++): ?>
                        <li class="page-item <?php echo $page == $i ? 'active' : ''; ?>">
                            <a class="page-link" href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>">
                                <?php echo $i; ?>
                            </a>
                        </li>
                        <?php endfor; ?>
                        <li class="page-item <?php echo $page >= $total_pages ? 'disabled' : ''; ?>">
                            <a class="page-link" href="?page=<?php echo $page+1; ?>&search=<?php echo urlencode($search); ?>">
                                Next
                            </a>
                        </li>
                    </ul>
                </nav>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirm Deletion</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this patient record? This action cannot be undone.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <a href="#" id="confirmDelete" class="btn btn-danger">Delete</a>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* Page Styles */
        .page-title {
            color: #2a3f54;
            font-weight: 700;
            font-size: 1.8rem;
            margin-bottom: 0.5rem;
        }

        /* Search Form */
        .search-form .form-control {
            border-radius: 8px 0 0 8px;
            border: 1px solid #dee2e6;
            padding: 0.625rem 1rem;
        }

        .search-form .btn {
            border-radius: 0 8px 8px 0;
            padding: 0.625rem 1.25rem;
        }

        /* Table Styles */
        .table th {
            background-color: #f8f9fa;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 0.5px;
            padding: 1rem;
        }

        .table td {
            padding: 1rem;
            vertical-align: middle;
        }

        /* Patient Avatar */
        .patient-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #fff;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        /* Card Styles */
        .card {
            border-radius: 15px;
            border: none;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.05);
        }

        .card-body {
            padding: 1.5rem;
        }

        /* Button Styles */
        .btn-group .btn {
            padding: 0.25rem 0.5rem;
            margin: 0 2px;
        }

        .btn-group .btn i {
            font-size: 0.875rem;
        }

        .btn-success {
            background: linear-gradient(135deg, #28a745 0%, #218838 100%);
            border: none;
            padding: 0.625rem 1.25rem;
        }

        .btn-success:hover {
            background: linear-gradient(135deg, #218838 0%, #1e7e34 100%);
            transform: translateY(-1px);
        }

        /* Badge Styles */
        .badge {
            padding: 0.5rem 0.75rem;
            font-weight: 500;
            border-radius: 6px;
        }

        /* Pagination Styles */
        .pagination {
            gap: 0.25rem;
        }

        .page-link {
            border-radius: 6px;
            padding: 0.5rem 1rem;
            color: #2a3f54;
        }

        .page-item.active .page-link {
            background-color: #2a3f54;
            border-color: #2a3f54;
        }

        /* Modal Styles */
        .modal-content {
            border-radius: 15px;
            border: none;
        }

        .modal-header {
            background-color: #f8f9fa;
            border-bottom: 1px solid #edf2f9;
        }

        .modal-footer {
            border-top: 1px solid #edf2f9;
        }
    </style>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Function to open delete confirmation modal with patient id
        function confirmDelete(id) {
            const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
            document.getElementById('confirmDelete').href = `manage_patients.php?delete=${id}`;
            modal.show();
        }

        // Function to export patient data
        function exportToExcel() {
            // Implement Excel export functionality
            window.location.href = 'export_patients.php';
        }
    </script>
</body>
</html>