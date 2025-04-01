<?php
session_start();
include('db_connection.php');
include('doctorheader.php');

// Directly use the doctor's ID from the session
$doctor_id = $_SESSION['id']; // Assuming the doctor is already logged in

// Function to get appointments by status with patient name and filter by doctor_id
function getAppointmentsByStatus($conn, $status, $doctor_id) {
    $sql = "SELECT a.*, p.name as patient_name, s.start_time as appointment_time, 
                   s.end_time as appointment_end, a.rejection_reason, a.consultation_notes
            FROM appointment_requests a
            LEFT JOIN patientreg p ON a.user_id = p.id
            LEFT JOIN doctor_availability s ON a.slot_id = s.id
            WHERE a.status = ? AND s.doctor_id = ?";
            
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $status, $doctor_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $appointments = [];
    while ($row = $result->fetch_assoc()) {
        $appointments[] = $row;
    }
    
    $stmt->close();
    return $appointments;
}

// Get appointments for the logged-in doctor
$approvedAppointments = getAppointmentsByStatus($conn, 'Approved', $doctor_id);
$rejectedAppointments = getAppointmentsByStatus($conn, 'Rejected', $doctor_id);
$pendingAppointments =  getAppointmentsByStatus($conn, 'pending', $doctor_id);

// Close database connection when done
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Appointment Management</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@2.5.0/fonts/remixicon.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #4a6cf7;
            --secondary-color: #6a7ce0;
            --text-color: #333;
            --light-bg: #f4f7ff;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--light-bg);
            color: var(--text-color);
            line-height: 1.6;
        }

        .dashboard-container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 15px;
        }

        .page-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 2rem;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 1rem 2rem;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(74, 108, 247, 0.15);
        }

        .page-header h1 {
            font-size: 2rem;
            font-weight: 700;
            margin: 0;
        }

        .appointment-section {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            margin-bottom: 2rem;
            padding: 1.5rem;
        }

        .appointment-section h2 {
            color: var(--primary-color);
            border-bottom: 3px solid var(--primary-color);
            padding-bottom: 0.5rem;
            margin-bottom: 1rem;
        }

        .table {
            --bs-table-bg: transparent;
            --bs-table-accent-bg: transparent;
        }

        .table th {
            background-color: var(--primary-color);
            color: white;
            border: none;
            text-transform: uppercase;
            font-size: 0.9rem;
        }

        .table td {
            vertical-align: middle;
            border-color: #e9ecef;
        }

        .status-chip {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .status-approved {
            background-color: rgba(40, 167, 69, 0.1);
            color: #28a745;
        }

        .status-rejected {
            background-color: rgba(220, 53, 69, 0.1);
            color: #dc3545;
        }

        .status-pending {
            background-color: rgba(255, 193, 7, 0.1);
            color: #ffc107;
        }

        .patient-link {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s ease;
        }

        .patient-link:hover {
            color: var(--secondary-color);
            text-decoration: underline;
        }

        .empty-state {
            text-align: center;
            color: #6c757d;
            padding: 2rem;
            background-color: #f8f9fa;
            border-radius: 10px;
        }

        @media (max-width: 768px) {
            .page-header {
                flex-direction: column;
                text-align: center;
            }
        }

        .badge {
            font-size: 0.8rem;
            padding: 0.4em 0.8em;
        }
        
        .modal-dialog {
            max-width: 800px;
        }
        
        .modal textarea {
            resize: vertical;
            min-height: 150px;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <div class="page-header">
            <h1><i class="ri-calendar-check-line me-2"></i>Appointment Management</h1>
        </div>

        <!-- Pending Appointments Section -->
        <div class="appointment-section">
            <h2><i class="ri-time-line me-2"></i>Pending Appointments</h2>
            <?php if(empty($pendingAppointments)): ?>
                <div class="empty-state">
                    <i class="ri-inbox-line" style="font-size: 3rem; color: #6c757d;"></i>
                    <p class="mt-3">No pending appointments at the moment</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Patient Name</th>
                                <th>Date</th>
                                <th>Start Time</th>
                                <th>End Time</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($pendingAppointments as $appointment): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($appointment['id']); ?></td>
                                    <td>
                                        <a href="patient_profiled2.php?id=<?php echo urlencode($appointment['user_id']); ?>" 
                                           class="patient-link">
                                            <?php echo htmlspecialchars($appointment['patient_name'] ?? 'N/A'); ?>
                                        </a>
                                    </td>
                                    <td><?php echo htmlspecialchars($appointment['appointment_date'] ?? 'N/A'); ?></td>
                                    <td><?php echo htmlspecialchars($appointment['appointment_time'] ?? 'N/A'); ?></td>
                                    <td><?php echo htmlspecialchars($appointment['appointment_end'] ?? 'N/A'); ?></td>
                                    <td>
                                        <span class="status-chip status-pending">
                                            <?php echo htmlspecialchars($appointment['status']); ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>

        <!-- Approved Appointments Section -->
        <div class="appointment-section">
            <h2><i class="ri-check-double-line me-2"></i>Approved Appointments</h2>
            <?php if(empty($approvedAppointments)): ?>
                <div class="empty-state">
                    <i class="ri-checkbox-circle-line" style="font-size: 3rem; color: #6c757d;"></i>
                    <p class="mt-3">No approved appointments found</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Patient Name</th>
                                <th>Date</th>
                                <th>Start Time</th>
                                <th>End Time</th>
                                <th>Status</th>
                                <th>Notes</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($approvedAppointments as $appointment): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($appointment['id']); ?></td>
                                    <td>
                                        <a href="patient_profiled2.php?id=<?php echo urlencode($appointment['user_id']); ?>" 
                                           class="patient-link">
                                            <?php echo htmlspecialchars($appointment['patient_name'] ?? 'N/A'); ?>
                                        </a>
                                    </td>
                                    <td><?php echo htmlspecialchars($appointment['appointment_date'] ?? 'N/A'); ?></td>
                                    <td><?php echo htmlspecialchars($appointment['appointment_time'] ?? 'N/A'); ?></td>
                                    <td><?php echo htmlspecialchars($appointment['appointment_end'] ?? 'N/A'); ?></td>
                                    <td>
                                        <span class="status-chip status-approved">
                                            <?php echo htmlspecialchars($appointment['status']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if(!empty($appointment['consultation_notes'])): ?>
                                            <span class="badge bg-success">Notes Added</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">No Notes</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <button class="btn btn-primary btn-sm" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#notesModal<?php echo $appointment['id']; ?>">
                                            <?php echo empty($appointment['consultation_notes']) ? 'Add Notes' : 'Edit Notes'; ?>
                                        </button>
                                    </td>
                                </tr>
                                
                                <!-- Modal for each appointment -->
                                <div class="modal fade" id="notesModal<?php echo $appointment['id']; ?>" tabindex="-1">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Consultation Notes - <?php echo htmlspecialchars($appointment['patient_name']); ?></h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <form action="save_notes.php" method="POST">
                                                <div class="modal-body">
                                                    <input type="hidden" name="appointment_id" value="<?php echo $appointment['id']; ?>">
                                                    <div class="mb-3">
                                                        <label for="notes<?php echo $appointment['id']; ?>" class="form-label">Consultation Notes</label>
                                                        <textarea class="form-control" 
                                                                  id="notes<?php echo $appointment['id']; ?>" 
                                                                  name="consultation_notes" 
                                                                  rows="5"><?php echo htmlspecialchars($appointment['consultation_notes'] ?? ''); ?></textarea>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                    <button type="submit" class="btn btn-primary">Save Notes</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>

        <!-- Rejected Appointments Section -->
        <div class="appointment-section">
            <h2><i class="ri-close-circle-line me-2"></i>Rejected Appointments</h2>
            <?php if(empty($rejectedAppointments)): ?>
                <div class="empty-state">
                    <i class="ri-forbid-line" style="font-size: 3rem; color: #6c757d;"></i>
                    <p class="mt-3">No rejected appointments found</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Patient Name</th>
                                <th>Date</th>
                                <th>Start Time</th>
                                <th>End Time</th>
                                <th>Rejection Reason</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($rejectedAppointments as $appointment): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($appointment['id']); ?></td>
                                    <td>
                                        <a href="patient_profiled2.php?id=<?php echo urlencode($appointment['user_id']); ?>" 
                                           class="patient-link">
                                            <?php echo htmlspecialchars($appointment['patient_name'] ?? 'N/A'); ?>
                                        </a>
                                    </td>
                                    <td><?php echo htmlspecialchars($appointment['appointment_date'] ?? 'N/A'); ?></td>
                                    <td><?php echo htmlspecialchars($appointment['appointment_time'] ?? 'N/A'); ?></td>
                                    <td><?php echo htmlspecialchars($appointment['appointment_end'] ?? 'N/A'); ?></td>
                                    <td><?php echo htmlspecialchars($appointment['rejection_reason'] ?? 'No reason provided'); ?></td>
                                    <td>
                                        <span class="status-chip status-rejected">
                                            <?php echo htmlspecialchars($appointment['status']); ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>