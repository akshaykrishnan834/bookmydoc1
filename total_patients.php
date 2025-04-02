<?php
include('db_connection.php');

// Fetch all patients
$patients_query = "SELECT * FROM patientreg ORDER BY name ASC";
$patients_result = mysqli_query($conn, $patients_query);

// Fetch appointments for each patient
$appointments_query = "SELECT a.*, d.name AS doctor_name 
                      FROM appointment_requests a 
                      LEFT JOIN doctorreg d ON a.doctor_id = d.id 
                      ORDER BY a.user_id, a.appointment_date DESC";
$appointments_result = mysqli_query($conn, $appointments_query);

// Group appointments by patient
$appointments_by_patient = [];
while ($appointment = mysqli_fetch_assoc($appointments_result)) {
    $appointments_by_patient[$appointment['user_id']][] = $appointment;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patients Invoice - BookMyDoc</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            color: #333;
            line-height: 1.6;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 1200px;
            margin: 20px auto;
            background: #fff;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .invoice-header {
            text-align: center;
            margin-bottom: 20px;
        }
        .invoice-header h2 {
            font-size: 28px;
            color: #2a3f54;
            margin: 0;
        }
        .invoice-header p {
            color: #6c757d;
            margin: 5px 0;
        }
        .patient-section {
            margin-bottom: 30px;
        }
        .patient-section h3 {
            color: #2a3f54;
            font-weight: 600;
            margin-bottom: 10px;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .table th, .table td {
            padding: 10px;
            border: 1px solid #dee2e6;
            text-align: left;
        }
        .table th {
            background: #f8f9fa;
            color: #495057;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.85rem;
        }
        .print-btn {
            display: block;
            width: 150px;
            margin: 20px auto;
            padding: 10px;
            background: #28a745;
            color: white;
            text-align: center;
            text-decoration: none;
            border-radius: 5px;
            cursor: pointer;
            border: none;
        }
        .print-btn:hover {
            background: #218838;
        }
        .empty-state {
            text-align: center;
            padding: 20px;
            color: #6c757d;
        }
        @media print {
            .print-btn, .no-print {
                display: none;
            }
            .container {
                box-shadow: none;
                margin: 0;
                width: 100%;
            }
        }
    </style>
</head>
<body>
<div class="container">
    <div class="invoice-header">
    <h2>BookMyDoc - Online Doctor Appointment Booking System </h2>
    <h2>Appointments Analysis</h2>
        <p>Generated on: <?php echo date('F d, Y'); ?></p>
        <p>List of Patients and Their Appointments</p>
    </div>

    <?php if (mysqli_num_rows($patients_result) > 0): ?>
        <?php while ($patient = mysqli_fetch_assoc($patients_result)): ?>
            <div class="patient-section">
                <h3><?php echo htmlspecialchars($patient['name']); ?> (ID: <?php echo $patient['id']; ?>)</h3>
                <p>Email: <?php echo htmlspecialchars($patient['email']); ?> | Phone: <?php echo htmlspecialchars($patient['phone']); ?> | Age: <?php echo $patient['age']; ?> | Gender: <?php echo $patient['gender']; ?></p>
                
                <?php if (isset($appointments_by_patient[$patient['id']]) && !empty($appointments_by_patient[$patient['id']])): ?>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Appointment ID</th>
                                <th>Doctor</th>
                                <th>Date</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($appointments_by_patient[$patient['id']] as $appointment): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($appointment['id']); ?></td>
                                    <td><?php echo htmlspecialchars($appointment['doctor_name']); ?></td>
                                    <td><?php echo htmlspecialchars($appointment['appointment_date']); ?></td>
                                    <td><?php echo htmlspecialchars($appointment['status']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="empty-state">No appointments found for this patient.</div>
                <?php endif; ?>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <div class="empty-state">No patients found.</div>
    <?php endif; ?>

    <button class="print-btn no-print" onclick="window.print()">Print Invoice</button>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>