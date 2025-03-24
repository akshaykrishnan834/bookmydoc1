<?php 
session_start(); 
include('db_connection.php'); 
include('adminheader.php');

// Handle enable/disable doctor action
if (isset($_GET['toggle_action'])) { // Ensure the database connection is included
    $doctor_id = mysqli_real_escape_string($conn, $_GET['toggle_action']);
    $current_action = mysqli_real_escape_string($conn, $_GET['action']);
    
    $new_action = ($current_action == 'enabled') ? 'disabled' : 'enabled';
    $update_query = "UPDATE doctorreg SET action = '$new_action' WHERE id = '$doctor_id'";
    mysqli_query($conn, $update_query);

    echo "<script>window.location.href='" . $_SERVER['PHP_SELF'] . "';</script>";
    exit();
}


// Handle delete doctor action
if (isset($_GET['delete'])) {
    $doctor_id = mysqli_real_escape_string($conn, $_GET['delete']);
    $delete_query = "DELETE FROM doctorreg WHERE id = '$doctor_id'";
    
    if (mysqli_query($conn, $delete_query)) {
        echo "<script>alert('Doctor deleted successfully!'); window.location.href='adminmanagedoct.php';</script>";
    } else {
        echo "<script>alert('Error deleting doctor.');</script>";
    }
}

// Fetch pending doctor approvals
$pending_query = "SELECT * FROM doctorreg WHERE status = 'Pending'"; 
$pending_result = mysqli_query($conn, $pending_query);

// Fetch all registered doctors
$doctors_query = "SELECT * FROM doctorreg ORDER BY name ASC";
$doctors_result = mysqli_query($conn, $doctors_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Doctors - BookMyDoc</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
       body { 
    
    color: #333;
    line-height: 1.6;
    margin: 0;
    padding: 0;
}

.container2 { 
    max-width: 1200px; 
    margin: 0 auto; 
    background: #fff; 
    padding: 0;
    
}

h2, h3 {
    color: #2d3e50;
    font-weight: 600;
}

/* Page header styling */
h2.text-center {
    font-size: 24px;
    padding: 20px 0;
    margin: 0;
    background: #fff;
    border-bottom: none;
    text-align: left !important;
    padding-left: 20px;
}

/* Page subtitle */
.page-subtitle {
    color: #6c757d;
    font-size: 14px;
    margin-top: -15px;
    margin-bottom: 20px;
    padding-left: 20px;
}

/* Table styling */
.table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
    margin-bottom: 0;
    border: none;
    box-shadow: none;
}

.table th {
    background: #f8f9fa;
    color: #495057;
    text-align: left;
    padding: 15px 20px;
    font-weight: 600;
    border-bottom: 1px solid #dee2e6;
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: 1px;
}

.table td {
    padding: 15px 20px;
    border-bottom: 1px solid #f0f0f0;
    vertical-align: middle;
    font-size: 14px;
}

.table tbody tr:hover {
    background-color: #f8f9fa;
}

/* Status badge */
.status-badge, .action-enabled, .action-disabled {
    display: inline-block;
    padding: 6px 12px;
    border-radius: 4px;
    font-size: 12px;
    font-weight: 500;
}

.action-enabled, .approved, .status-badge.enabled {
    background: #28a745;
    color: white;
}

.action-disabled, .status-badge.disabled {
    background: #6c757d;
    color: white;
}

.pending {
    background: #ffc107;
    color: #212529;
}

/* Action buttons */
.action-btn {
    display: inline-block;
    padding: 6px 12px;
    border-radius: 4px;
    text-decoration: none;
    transition: all 0.2s ease;
    font-size: 12px;
    font-weight: 500;
    margin-right: 5px;
    border: none;
}

.toggle-btn, .disable-btn {
    background: #ffc107;
    color: #212529;
}

.delete-btn {
    background: #fff;
    color: #dc3545;
    border: 1px solid #dc3545;
}

.approve-btn {
    background: #28a745;
    color: white;
}

.reject-btn {
    background: #dc3545;
    color: white;
}

/* User photos */
.doctor-photo, img[width="50"] {
    width: 40px !important;
    height: 40px !important;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid #f0f0f0;
    margin-right: 10px;
}

/* Patient/Doctor name styling */
.patient-name, .table td:nth-child(2) {
    font-weight: 600;
    font-size: 14px;
}

.patient-id, .table td:nth-child(2) small {
    display: block;
    color: #6c757d;
    font-size: 12px;
    font-weight: normal;
}

/* Contact info styling */
.contact-email {
    display: block;
    margin-bottom: 2px;
}

.contact-phone {
    display: block;
    color: #6c757d;
}

/* Age/Gender styling */
.age-gender-info {
    line-height: 1.4;
}

/* Header section */
.page-header {
    padding: 20px;
    background: #f8f9fa;
    border-bottom: 1px solid #dee2e6;
    margin-bottom: 0;
}

.page-header h1 {
    margin: 0;
    font-size: 28px;
    color: #2d3e50;
}

.page-header p {
    margin: 5px 0 0;
    color: #6c757d;
}

/* Empty state */
.empty-state {
    text-align: center;
    padding: 30px;
    color: #6c757d;
}

/* Adaptations for the specific layout in the image */
h2, h3 {
    font-weight: 600;
    margin-bottom: 5px;
}

/* Create the top section like in the image */
.header-section {
    background: #fff;
    padding: 20px;
    border-bottom: 1px solid #e3e3e3;
}

.header-section h1 {
    font-size: 24px;
    margin: 0;
    color: #2d3e50;
}

.header-section p {
    margin: 5px 0 0;
    color: #6c757d;
    font-size: 14px;
}

/* Responsive styles */
@media (max-width: 768px) {
    .table {
        display: block;
        overflow-x: auto;
    }
    
    .container2 {
        margin: 0;
        width: 100%;
    }
}

/* Override specific elements to match the image */
.table th:first-child, .table td:first-child {
    padding-left: 20px;
}

/* Remove default container padding */
.container2 {
    padding: 0;
}

/* Add ID column width */
.table th:first-child, .table td:first-child {
    width: 60px;
}

/* Add specific button styles to match the image */
.action-btn.delete-btn {
    background-color: #fff;
    color: #dc3545;
    border: 1px solid #dc3545;
}

.action-btn.toggle-btn, .action-btn.disable-btn {
    background-color: #ffc107;
    color: #212529;
}

/* Adjust the enabled badge */
.status-badge.enabled, .action-enabled {
    background-color: #28a745;
    color: white;
    font-size: 12px;
    padding: 6px 12px;
    border-radius: 4px;
}
.fw-bold a { text-decoration: none; color: inherit; }
        
    </style>
</head>
<body>

<div class="container2">
    <br>

    <!-- Pending Doctor Approvals Section -->
    <h3>Pending Approvals</h3>
    <?php if (mysqli_num_rows($pending_result) > 0): ?>
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Experience</th>
                    <th>Specialization</th>
                    <th>Qualification</th>
                    <th>Degree Certificate</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($pending_result)) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['id']); ?></td>
                        <td><?php echo htmlspecialchars($row['name']); ?></td>
                        <td><?php echo htmlspecialchars($row['experience']); ?> years</td>
                        <td><?php echo htmlspecialchars($row['specialization']); ?></td>
                        <td><?php echo htmlspecialchars($row['qualifications']); ?></td>
                        <td>
                            <a href="<?php echo htmlspecialchars($row['degree_certificate']); ?>" 
                               class="certificate-link" target="_blank">
                                View Certificate
                            </a>
                        </td>
                        <td>
                            <a href="approvedoct.php?id=<?php echo $row['id']; ?>" 
                               class="action-btn approve-btn" 
                               onclick="return confirm('Approve this doctor?')">
                                Approve
                            </a>
                            <button type="button" class="action-btn reject-btn" data-bs-toggle="modal" data-bs-target="#rejectModal<?php echo $row['id']; ?>">
        Reject
    </button>

    <!-- Rejection Reason Modal -->
    <div class="modal fade" id="rejectModal<?php echo $row['id']; ?>" tabindex="-1" aria-labelledby="rejectModalLabel<?php echo $row['id']; ?>" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="rejectModalLabel<?php echo $row['id']; ?>">Provide Rejection Reason</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="rejectdoctor.php" method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="doctor_id" value="<?php echo $row['id']; ?>">
                        <div class="mb-3">
                            <label for="rejection_reason" class="form-label">Rejection Reason:</label>
                            <textarea class="form-control" name="rejection_reason" id="rejection_reason" rows="3" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">Confirm Rejection</button>
                    </div>
                </form>
            </div>
        </div>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    <?php else: ?>
        <div class="empty-state">No pending doctor approvals.</div>
    <?php endif; ?>

    <hr>


    <!-- All Doctors Section -->
    <h3>Registered Doctors</h3>
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Photo</th>
                <th>Name</th>
                <th>Specialization</th>
                <th>Contact</th>
                <th>Approval Status</th>
                <th>Action Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while($doctor = mysqli_fetch_assoc($doctors_result)): ?>
                <tr>
                    <td><?php echo $doctor['id']; ?></td>
                    <td>
                        <img src="<?php echo $doctor['profile_photo'] ?: 'assets/default-doctor.png'; ?>" 
                             class="doctor-photo" width="50" height="50" onerror="this.onerror=null; this.src='images/profilepicdoct.jpg';" >
                    </td>
                    <td><div class="fw-bold">
    <a href="admindoctprofile.php?doctor_id=<?php echo urlencode($doctor['id']); ?>">
        <?php echo htmlspecialchars($doctor['name']); ?>
    </a>
</div></td>
                    <td><?php echo htmlspecialchars($doctor['specialization']); ?></td>
                    <td><?php echo htmlspecialchars($doctor['email']); ?><br><?php echo htmlspecialchars($doctor['phone']); ?></td>
                    <td>
                        <span class="status-badge <?php echo strtolower($doctor['status']); ?>">
                            <?php echo ucfirst($doctor['status']); ?>
                        </span>
                    </td>
                    <td>
                        <span class="status-badge action-<?php echo strtolower($doctor['action']); ?>">
                            <?php echo ucfirst($doctor['action']); ?>
                        </span>
                    </td>
                    <td>
                        <a href="?toggle_action=<?php echo $doctor['id']; ?>&action=<?php echo $doctor['action']; ?>" class="action-btn toggle-btn">
                            <?php echo ($doctor['action'] == 'enabled') ? 'Disable' : 'Enable'; ?>
                        </a>
                        <a href="?delete=<?php echo $doctor['id']; ?>" class="action-btn delete-btn"
                           onclick="return confirm('Are you sure you want to delete this doctor?');">
                            Delete
                        </a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

</div>
</body>
</html>
