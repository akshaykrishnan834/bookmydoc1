<?php
session_start();
include('db_connection.php');
include('doctorheader.php');

// Directly use the doctor's ID from the session
$doctor_id = $_SESSION['id']; // Assuming the doctor is already logged in

// Function to get appointments by status with patient name and filter by doctor_id
function getAppointmentsByStatus($conn, $status, $doctor_id) {
    $sql = "SELECT a.*, p.name as patient_name, s.start_time as appointment_time, s.end_time as appointment_end
            FROM appointment_requests a
            LEFT JOIN patientreg p ON a.user_id = p.id
            LEFT JOIN doctor_availability s ON a.slot_id = s.id
            WHERE a.status = ? AND s.doctor_id = ?"; // Filter by doctor_id
            
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $status, $doctor_id); // Use "si" to bind string and integer
    $stmt->execute();
    $result = $stmt->get_result();
    
    $appointments = [];
    while ($row = $result->fetch_assoc()) {
        $appointments[] = $row;
    }
    
    $stmt->close();
    return $appointments;
}

// Get approved and rejected appointments for the logged-in doctor
$approvedAppointments = getAppointmentsByStatus($conn, 'Approved', $doctor_id);
$rejectedAppointments = getAppointmentsByStatus($conn, 'Rejected', $doctor_id);

// Close database connection when done
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Appointment Status</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            
           
        }
        
        h1 {
            text-align: center;
            font-size: 36px;
            color: #333;
            margin-bottom: 40px;
        }
        
        .container2 {
            background-color: white;
            padding: 30px;
            border-radius: 10px;
           
        }

        

        h2 {
            font-size: 28px;
            color: #007bff;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 15px;
            text-align: left;
            border: 1px solid #ddd;
        }

        th {
            background-color: #007bff;
            color: white;
        }

        .status-approved {
            color: #28a745;
            font-weight: bold;
        }

        .status-rejected {
            color: #dc3545;
            font-weight: bold;
        }

        .alert {
            margin-bottom: 20px;
            padding: 15px;
            border-radius: 5px;
        }

        .alert-success {
            background-color: #28a745;
            color: white;
        }

        .alert-danger {
            background-color: #dc3545;
            color: white;
        }

        .btn-back {
            display: inline-block;
            background-color: #007bff;
            color: white;
            padding: 12px 20px;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }

        .btn-back:hover {
            background-color: #0056b3;
        }

        .table-responsive {
            overflow-x: auto;
        }
    </style>
</head>
<body>
    <br>
    <br>
    <h1>Appointment Management</h1>
    <div class="container2">
        <?php if(isset($_SESSION['success'])): ?>
            <div class="alert alert-success">
                <?php 
                    echo $_SESSION['success']; 
                    unset($_SESSION['success']);
                ?>
            </div>
        <?php endif; ?>
        
        <?php if(isset($_SESSION['error'])): ?>
            <div class="alert alert-danger">
                <?php 
                    echo $_SESSION['error']; 
                    unset($_SESSION['error']);
                ?>
            </div>
        <?php endif; ?>

        <!-- Approved Appointments Section -->
        <div class="appointment-section">
            <h2>Approved Appointments</h2>
            <?php if(empty($approvedAppointments)): ?>
                <p>No approved appointments found.</p>
            <?php else: ?>
                <div class="table-responsive">
                    <table>
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
                            <?php foreach($approvedAppointments as $appointment): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($appointment['id']); ?></td>
                                    <td><?php echo htmlspecialchars($appointment['patient_name'] ?? 'N/A'); ?></td>
                                    <td><?php echo htmlspecialchars($appointment['appointment_date'] ?? 'N/A'); ?></td>
                                    <td><?php echo htmlspecialchars($appointment['appointment_time'] ?? 'N/A'); ?></td>
                                    <td><?php echo htmlspecialchars($appointment['appointment_end'] ?? 'N/A'); ?></td>
                                    <td class="status-approved"><?php echo htmlspecialchars($appointment['status']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
        <br>
        <br>
        <!-- Rejected Appointments Section -->
        <div class="appointment-section">
            <h2>Rejected Appointments</h2>
            <?php if(empty($rejectedAppointments)): ?>
                <p>No rejected appointments found.</p>
            <?php else: ?>
                <div class="table-responsive">
                    <table>
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
                            <?php foreach($rejectedAppointments as $appointment): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($appointment['id']); ?></td>
                                    <td><?php echo htmlspecialchars($appointment['patient_name'] ?? 'N/A'); ?></td>
                                    <td><?php echo htmlspecialchars($appointment['appointment_date'] ?? 'N/A'); ?></td>
                                    <td><?php echo htmlspecialchars($appointment['appointment_time'] ?? 'N/A'); ?></td>
                                    <td><?php echo htmlspecialchars($appointment['appointment_end'] ?? 'N/A'); ?></td>
                                    <td class="status-rejected"><?php echo htmlspecialchars($appointment['status']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
        
      
    </div>
</body>
</html>
