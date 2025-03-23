<?php
include('db_connection.php');
include('adminheader.php');

// Handle patient deletion
if (isset($_GET['delete'])) {
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
$query = "SELECT * FROM patientreg $where_clause LIMIT $offset, $records_per_page";
$result = mysqli_query($conn, $query);

// Handle delete doctor action
if (isset($_GET['delete'])) {
    $doctor_id = mysqli_real_escape_string($conn, $_GET['delete']);
    $delete_query = "DELETE FROM patientreg WHERE id = '$doctor_id'";
    
    if (mysqli_query($conn, $delete_query)) {
        echo "<script>alert('patient deleted successfully!'); window.location.href='adminmanagepatient.php';</script>";
    } else {
        echo "<script>alert('Error deleting doctor.');</script>";
    }}
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
        <div class="row mb-4">
            <div class="col-12 d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="page-title">Manage Patients</h2>
                    <p class="text-muted">View and manage registered patients</p>
                </div>
                
               
            </div>
        </div>

        <!-- Alert Messages -->
        <?php if (isset($_GET['msg']) && $_GET['msg'] == 'deleted'): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            Patient record has been successfully deleted.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

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
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($patient = mysqli_fetch_assoc($result)): ?>
                            <tr>
                                <td><?php echo $patient['id']; ?></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <img src="<?php echo $patient['profile_pic'] ?: 'assets/default-avatar.png'; ?>" 
                                             class="patient-avatar me-3" 
                                             onerror="this.onerror=null; this.src='images/profilepicdoct.jpg';">
                                        <div>
                                            
                                        <div class="fw-bold">
    <a href="adminpatprofile.php?patient_id=<?php echo urlencode($patient['id']); ?>">
        <?php echo htmlspecialchars($patient['name']); ?>
    </a>
</div>

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
                                    <span class="badge <?php echo ($patient['action'] == 'enabled') ? 'bg-success' : 'bg-danger'; ?>">
                                        <?php echo ucfirst($patient['action']); ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <a href="toggle_patient_status.php?id=<?php echo $patient['id']; ?>" 
                                           class="btn btn-sm <?php echo ($patient['action'] == 'enabled') ? 'btn-warning' : 'btn-success'; ?>">
                                            <i class="fas <?php echo ($patient['action'] == 'enabled') ? 'fa-ban' : 'fa-check'; ?>"></i> 
                                            <?php echo ($patient['action'] == 'enabled') ? 'Disable' : 'Enable'; ?>
                                        </a>
                                    
                                        <a href="?delete=<?php echo $patient['id']; ?>" class="btn btn-sm btn-outline-danger"
                           onclick="return confirm('Are you sure you want to delete this Patient?');">
                            Delete
                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>

    <script>
        function confirmDelete(id) {
            if (confirm("Are you sure you want to delete this patient?")) {
                window.location.href = "manage_patients.php?delete=" + id;
            }
        }
    </script>
</body>
</html>
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
        .fw-bold a { text-decoration: none; color: inherit; }

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