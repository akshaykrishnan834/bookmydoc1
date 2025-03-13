<?php
include('db_connection.php'); 
include('patientheader.php');



// Fetch appointment details
$sql = "SELECT ar.id, d.name AS doctor_name, ar.appointment_date, ar.status, u.name AS patient_name
        FROM appointment_requests ar
        JOIN doctorreg d ON ar.doctor_id = d.id
        JOIN patientreg u ON ar.user_id = u.id
        WHERE ar.id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $appointment_id);
$stmt->execute();
$result = $stmt->get_result();

if($result->num_rows === 0) {
    echo "<script>alert('Appointment not found!'); window.location.href='my_appointments.php';</script>";
    exit;
}

$appointment = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Successful</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .container {
            max-width: 600px;
            margin: 50px auto;
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            text-align: center;
        }
        .success-icon {
            color: #38a169;
            font-size: 50px;
        }
        .btn-home {
            margin-top: 20px;
            background-color: #38a169;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
        }
        .btn-home:hover {
            background-color: #2f855a;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2 class="text-success">Payment Successful!</h2>
        <p>Your appointment with <b>Dr. <?php echo htmlspecialchars($appointment['doctor_name']); ?></b> on <b><?php echo date('Y-m-d', strtotime($appointment['appointment_date'])); ?></b> is confirmed.</p>
        <p>Thank you for your payment.</p>
        <a href="my_appointments.php" class="btn-home">View Appointments</a>
    </div>
</body>
</html>
