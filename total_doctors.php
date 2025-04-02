<?php 
session_start(); 
include('db_connection.php'); 

// Fetch all registered doctors, grouped by department (or specialization if department isn't available)
$doctors_query = "SELECT * FROM doctorreg WHERE status = 'Approved' ORDER BY specialization, name ASC"; // Assuming 'department' exists; if not, replace with 'specialization'
$doctors_result = mysqli_query($conn, $doctors_query);

// Organize doctors by department
$doctors_by_dept = [];
while ($doctor = mysqli_fetch_assoc($doctors_result)) {
    $dept = $doctor['department'] ?? $doctor['specialization']; // Fallback to specialization if department is missing
    $doctors_by_dept[$dept][] = $doctor;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctors Invoice by Department - BookMyDoc</title>
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
        .container2 {
            max-width: 1200px;
            margin: 20px auto;
            background: #fff;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h2, h3 {
            color: #2d3e50;
            font-weight: 600;
        }
        .invoice-header {
            text-align: center;
            margin-bottom: 20px;
        }
        .invoice-header h2 {
            font-size: 28px;
            margin: 0;
        }
        .invoice-header p {
            color: #6c757d;
            margin: 5px 0;
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
        }
        .dept-section {
            margin-bottom: 30px;
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
        }
        .print-btn:hover {
            background: #218838;
        }
        @media print {
            .print-btn, .no-print {
                display: none;
            }
            .container2 {
                box-shadow: none;
                margin: 0;
                width: 100%;
            }
        }
    </style>
</head>
<body>
<div class="container2">
    <div class="invoice-header">
        <h2>BookMyDoc - Online Doctor Appointment Booking System </h2>
        <h2>Doctor's Analysis</h2>
        <p>Generated on: <?php echo date('F d, Y'); ?></p>
        <p>List of Approved Doctors by Department</p>
    </div>

    <?php if (empty($doctors_by_dept)): ?>
        <div class="empty-state">No approved doctors found.</div>
    <?php else: ?>
        <?php foreach ($doctors_by_dept as $dept => $doctors): ?>
            <div class="dept-section">
                <h3><?php echo htmlspecialchars($dept); ?></h3>
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Qualifications</th>
                            <th>Specialization</th>
                            <th>Contact</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($doctors as $doctor): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($doctor['id']); ?></td>
                                <td><?php echo htmlspecialchars($doctor['name']); ?></td>
                                <td><?php echo htmlspecialchars($doctor['qualifications']); ?></td>
                                <td><?php echo htmlspecialchars($doctor['specialization']); ?></td>
                                <td>
                                    <?php echo htmlspecialchars($doctor['email']); ?><br>
                                    <?php echo htmlspecialchars($doctor['phone']); ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <button class="print-btn no-print" onclick="window.print()">Print Invoice</button>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>