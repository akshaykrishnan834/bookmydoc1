<?php
session_start();
require 'db_connection.php'; // Include your database connection file
include 'patientheader2.php';
require 'delete_passed_appointments.php';

if (!isset($_SESSION['id'])) {
    die("Unauthorized access");
}

$patient_id = $_SESSION['id'];

$sql = "SELECT ar.id, ar.appointment_date, ar.status, ar.payment_status, ar.patient_condition, 
               d.name AS doctor_name, s.start_time, s.end_time, ar.rejection_reason
        FROM appointment_requests ar
        JOIN doctorreg d ON ar.doctor_id = d.id
        JOIN doctor_availability s ON ar.slot_id = s.id
        WHERE ar.user_id = ?
        ORDER BY ar.appointment_date DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $patient_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Appointments</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
    body {
        background-color: #f8f9fa;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    
    .page-header {
        background-color: transparent;
        color: #333;
        padding: 1.5rem 0 0.5rem;
        margin-bottom: 0;
        box-shadow: none;
        border-radius: 0;
    }
    .table thead th {
        background: linear-gradient(to right, #3498db, #2ac8dd);
        color: white;
        font-weight: 600;
        border: none;
        padding: 15px;
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    /* Add this if you want subtle borders between header cells */
    .table thead th:not(:last-child) {
        border-right: 1px solid rgba(255, 255, 255, 0.1);
    }
    
    .page-title {
        font-weight: 600;
        margin-bottom: 0;
        color: #333;
        border-bottom: 3px solid #2196F3;
        display: inline-block;
        padding-bottom: 5px;
    }
    
    .appointments-container {
        background-color: transparent;
        box-shadow: none;
        padding: 0;
        margin-bottom: 30px;
    }
    
    /* Appointment schedule header */
    .appointment-header {
        background-color: #2196F3;
        color: white;
        padding: 15px 20px;
        border-radius: 5px 5px 0 0;
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 20px;
    }
    
    .appointment-title {
        font-size: 18px;
        font-weight: 500;
        margin-bottom: 0;
        display: flex;
        align-items: center;
    }
    
    .appointment-title i {
        margin-right: 10px;
    }
    
    .status-indicators {
        display: flex;
        gap: 10px;
    }
    
    .status-indicator {
        display: flex;
        align-items: center;
        background-color: rgba(255, 255, 255, 0.2);
        padding: 5px 15px;
        border-radius: 20px;
        font-size: 14px;
    }
    
    .status-dot {
        height: 10px;
        width: 10px;
        border-radius: 50%;
        margin-right: 8px;
    }
    
    .status-pending .status-dot {
        background-color: #FFC107;
    }
    
    .status-approved .status-dot {
        background-color: #4CAF50;
    }
    
    .status-rejected .status-dot {
        background-color: #F44336;
    }
    
    /* Table styling */
    .table {
        border-collapse: separate;
        border-spacing: 0;
        border: none;
        margin-bottom: 0;
    }
    
    .table thead th {
        background-color: #f5f5f5;
        color: #555;
        font-weight: 600;
        border: none;
        padding: 15px;
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .table tbody tr {
        background-color: white;
        transition: none;
    }
    
    .table tbody tr:hover {
        background-color: #f9f9f9;
        transform: none;
        box-shadow: none;
    }
    
    .table td {
        padding: 15px;
        vertical-align: middle;
        border-top: 1px solid #f5f5f5;
        font-size: 0.95rem;
    }
    
    /* Doctor info */
    .doctor-info {
        display: flex;
        align-items: center;
    }
    
    .doctor-icon {
        width: 32px;
        height: 32px;
        background-color: #E3F2FD;
        color: #2196F3;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 10px;
    }
    
    /* Specialization badge */
    .specialization {
        background-color: #E3F2FD;
        color: #2196F3;
        border-radius: 20px;
        padding: 5px 15px;
        font-size: 13px;
        display: inline-block;
    }
    
    /* Date and time */
    .date-time {
        display: flex;
        align-items: center;
        flex-direction: row;
        gap: 5px;
    }
    
    .time-slot {
        color: inherit;
        font-size: inherit;
    }
    
    /* Status badges */
    .status-badge {
        padding: 0;
        border-radius: 0;
        background-color: transparent;
    }
    
    .status-badge.status-pending {
        color: #FFC107;
        background-color: transparent;
    }
    
    .status-badge.status-approved {
        color: #4CAF50;
        background-color: transparent;
    }
    
    .status-badge.status-rejected {
        color: #F44336;
        background-color: transparent;
        cursor: pointer;
    }
    .status-badge.status-expired {
    color: #6c757d; /* Grey color for expired */
    background-color: transparent;
}

    /* Date with icon */
    .date-with-icon {
        display: flex;
        align-items: center;
        gap: 8px;
        color: #757575;
    }
    
    /* Action buttons */
    .btn {
        border-radius: 4px;
        padding: 6px 12px;
    }
    
    .btn-outline-danger {
        color: #dc3545;
        border-color: #dc3545;
        background-color: transparent;
    }
    
    .btn-sm {
        padding: 4px 8px;
        font-size: 0.875rem;
    }
    
    /* Empty state */
    .empty-state {
        text-align: center;
        padding: 30px 20px;
    }

    /* Rejection reason modal styling */
    .rejection-reason-modal .modal-header {
        background-color: #F44336;
        color: white;

    }

    .rejection-reason-modal .modal-body {
        padding: 20px;
    }

    .rejection-reason-modal .reason-content {
        background-color: #f8f9fa;
        padding: 15px;
        border-radius: 5px;
        border-left: 4px solid #F44336;
    }
    </style>
</head>
<body>
    <div class="page-header">
        <div class="container">
            <h2 class="page-title">My Appointments</h2>
            <br><br>
        </div>
    </div>
    
    <div class="container">
        <div class="appointments-container">
            <?php if ($result->num_rows > 0) : ?>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Doctor</th>
                                <th>Schedule</th>
                                <th>Status</th>
                                <th>Condition</th>
                                <th>Payment</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php while ($row = $result->fetch_assoc()) : ?>
                            <tr>
                                <td>#<?php echo $row['id']; ?></td>
                                <td class="doctor-name"><?php echo $row['doctor_name']; ?></td>
                                <td>
                                    <div class="date-time">
                                        <span class="appointment-date">
                                            <i class="far fa-calendar-alt me-1"></i>
                                            <?php echo date('M d, Y', strtotime($row['appointment_date'])); ?>
                                        </span>
                                        <span class="time-slot">
                                            <i class="far fa-clock me-1"></i>
                                            <?php echo date('h:i A', strtotime($row['start_time'])) . ' - ' . 
                                                        date('h:i A', strtotime($row['end_time'])); ?>
                                        </span>
                                    </div>
                                </td>
                                <td>
    <?php if ($row['status'] == 'approved') : ?>
        <span class="status-badge status-approved">
            <i class="fas fa-check-circle"></i> Approved
        </span>
    <?php elseif ($row['status'] == 'pending') : ?>
        <span class="status-badge status-pending">
            <i class="fas fa-clock"></i> Pending
        </span>
    <?php elseif ($row['status'] == 'rejected') : ?>
        <span class="status-badge status-rejected" 
              data-bs-toggle="modal" 
              data-bs-target="#rejectionModal<?php echo $row['id']; ?>" 
              style="text-decoration: none; cursor: pointer;">
            <i class="fas fa-times-circle"></i> Rejected (View Reason)
        </span>
    <?php elseif ($row['status'] == 'expired') : ?>
        <span class="status-badge status-expired" style="color: #6c757d;">
            <i class="fas fa-hourglass-end"></i> Time Up
        </span>
    <?php endif; ?>
</td>

                                        
                                        <!-- Rejection Reason Modal -->
                                        <div class="modal fade rejection-reason-modal" id="rejectionModal<?php echo $row['id']; ?>" tabindex="-1" aria-labelledby="rejectionModalLabel" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="rejectionModalLabel">
                                                            <i class="fas fa-exclamation-circle me-2"></i>
                                                            Appointment Rejection
                                                        </h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <h6>Appointment #<?php echo $row['id']; ?> with Dr. <?php echo $row['doctor_name']; ?></h6>
                                                        <p><strong>Date:</strong> <?php echo date('M d, Y', strtotime($row['appointment_date'])); ?></p>
                                                        <p><strong>Time:</strong> <?php echo date('h:i A', strtotime($row['start_time'])) . ' - ' . 
                                                                date('h:i A', strtotime($row['end_time'])); ?></p>
                                                        
                                                        <div class="reason-content mt-3">
                                                            <h6><strong>Reason for Rejection:</strong></h6>
                                                            <p><?php echo !empty($row['rejection_reason']) ? htmlspecialchars($row['rejection_reason']) : 'No reason provided.'; ?></p>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                        <a href="browsedoct.php" class="btn btn-primary">Book New Appointment</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                
                                </td>
                                <td>
                                    <div class="long-text" data-bs-toggle="tooltip" data-bs-placement="top" 
                                         title="<?php echo htmlspecialchars($row['patient_condition']); ?>">
                                        <?php echo htmlspecialchars($row['patient_condition']); ?>
                                    </div>
                                </td>
                                <td>
                                    <?php if ($row['status'] == 'approved') : ?>
                                        <?php if (strtolower($row['payment_status']) === 'paid') : ?>
                                            <button class="btn btn-success" disabled>
                                                <i class="fas fa-check-circle me-1"></i> Paid
                                            </button>
                                        <?php else : ?>
                                            <a href="payment.php?appointment_id=<?php echo $row['id']; ?>" class="btn btn-primary">
                                                <i class="fas fa-credit-card me-1"></i> Pay Now
                                            </a>
                                        <?php endif; ?>
                                    <?php elseif ($row['status'] == 'pending') : ?>
                                        <button class="btn btn-warning" disabled>
                                            <i class="fas fa-hourglass-half me-1"></i> Waiting
                                        </button>
                                    <?php elseif ($row['status'] == 'rejected') : ?>
                                        <button class="btn btn-danger" disabled>
                                            <i class="fas fa-ban me-1"></i> Rejected
                                        </button>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if (($row['status'] == 'pending' || $row['status'] == 'approved') && strtolower($row['payment_status']) !== 'paid') : ?>
                                        <a href="cancel_appointment.php?id=<?php echo $row['id']; ?>" 
                                           class="btn btn-danger" onclick="return confirm('Are you sure you want to cancel this appointment?');">
                                            <i class="fas fa-times me-1"></i> Cancel
                                        </a>
                                    <?php else : ?>
                                        <button class="btn btn-secondary" disabled>No Action</button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php else : ?>
                <div class="empty-state">
                    <i class="far fa-calendar-times"></i>
                    <h3>No appointments found</h3>
                    <p>You don't have any appointments scheduled yet.</p>
                    <a href="browsedoct.php" class="btn btn-primary mt-3">
                        <i class="fas fa-plus-circle me-1"></i> Book New Appointment
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Initialize tooltips
        document.addEventListener('DOMContentLoaded', function() {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });
    </script>
</body>
</html>