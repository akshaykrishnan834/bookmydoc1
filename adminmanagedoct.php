<?php
include('db_connection.php');
include('adminheader.php');

// Handle doctor deletion
if(isset($_GET['delete'])) {
    $id = mysqli_real_escape_string($conn, $_GET['delete']);
    $delete_query = "DELETE FROM doctorreg WHERE id = '$id'";
    mysqli_query($conn, $delete_query);
    header('Location: manage_doctors.php?msg=deleted');
}

// Fetch all doctors
$query = "SELECT * FROM doctorreg ORDER BY name ASC";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Doctors - BookMyDoc</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-light">
    <div class="container-fluid py-4">
        <div class="row mb-4">
            <div class="col-12 d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="page-title">Manage Doctors</h2>
                    <p class="text-muted">View and manage all registered doctors</p>
                </div>
                
            </div>
        </div>

        <?php if(isset($_GET['msg']) && $_GET['msg'] == 'deleted'): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            Doctor has been successfully deleted.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php endif; ?>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Photo</th>
                                <th>Name</th>
                                <th>Specialization</th>
                                <th>Contact</th>
                                <th>Status</th>
                                
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($doctor = mysqli_fetch_assoc($result)): ?>
                            <tr>
                                <td><?php echo $doctor['id']; ?></td>
                                <td>
                                    <img src="<?php echo $doctor['profile_photo'] ?: 'assets/default-doctor.png'; ?>" 
                                         class="doctor-photo" 
                                         onerror="this.onerror=null; this.src='images/profilepicdoct.jpg';" >
                                </td>
                                <td>
                                    <div class="fw-bold"><?php echo htmlspecialchars($doctor['name']); ?></div>
                                    <div class="text-muted small">ID: <?php echo $doctor['id']; ?></div>
                                </td>
                                <td><?php echo htmlspecialchars($doctor['specialization']); ?></td>
                                <td>
                                    <div><?php echo htmlspecialchars($doctor['email']); ?></div>
                                    <div class="text-muted small"><?php echo htmlspecialchars($doctor['phone']); ?></div>
                                </td>
                                <td>
                                    <span class="badge <?php echo $doctor['status'] == 'active' ? 'bg-success' : 'bg-danger'; ?>">
                                        <?php echo ucfirst($doctor['status']); ?>
                                    </span>
                                </td>
                                <td>
                                    
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

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirm Deletion</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this doctor? This action cannot be undone.
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
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        /* Table Styles */
        .table th {
            background-color: #f8f9fa;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 0.5px;
        }

        /* Doctor Photo */
        .doctor-photo {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #fff;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        /* Action Buttons */
        .btn-group .btn {
            padding: 0.25rem 0.5rem;
            margin: 0 2px;
        }

        .btn-group .btn i {
            font-size: 0.875rem;
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

        /* Add Doctor Button */
        .btn-primary {
            background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
            border: none;
            padding: 0.625rem 1.25rem;
            font-weight: 500;
            border-radius: 8px;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #224abe 0%, #1a3a98 100%);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(78, 115, 223, 0.2);
        }

        /* Status Badge */
        .badge {
            padding: 0.5rem 0.75rem;
            font-weight: 500;
            border-radius: 6px;
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
        function confirmDelete(id) {
            const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
            document.getElementById('confirmDelete').href = `manage_doctors.php?delete=${id}`;
            modal.show();
        }
    </script>
</body>
</html>