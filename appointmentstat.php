<?php
include('db_connection.php');
include('patientheader.php'); // Assuming this is the patient dashboard header

// Assuming user ID is stored in session after login
$patient_id = $_SESSION['id'] ?? null;


// Fetch patient's appointment details
$sql = "SELECT ar.id, d.name AS doctor_name, d.specialization, ar.appointment_date, 
               da.start_time, da.end_time, ar.status, ar.created_at
        FROM appointment_requests ar
        JOIN doctorreg d ON ar.doctor_id = d.id
        JOIN doctor_availability da ON ar.slot_id = da.id
        WHERE ar.user_id = ?
        ORDER BY ar.appointment_date DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $patient_id);
$stmt->execute();
$result = $stmt->get_result();

$appointments = [];
while ($row = $result->fetch_assoc()) {
    $appointments[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Appointments</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .status-pending { color: #e67e22; font-weight: 600; }
        .status-approved { color: #27ae60; font-weight: 600; }
        .status-rejected { color: #c0392b; font-weight: 600; }
        .appointment-card { border-radius: 10px; box-shadow: 0 2px 15px rgba(0,0,0,0.1); }
    </style>
</head>
<body class="bg-light">

<div class="container mt-5">
    <h2 class="text-center mb-4">📅 My Appointments</h2>

    <div class="card appointment-card p-4">
        <table class="table table-hover table-bordered">
            <thead class="table-dark">
                <tr>
                    <th>Doctor Name</th>
                    <th>Specialization</th>
                    <th>Appointment Date</th>
                    <th>Time Slot</th>
                    <th>Status</th>
                    <th>Requested On</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($appointments)): ?>
                    <?php foreach ($appointments as $app): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($app['doctor_name']); ?></td>
                            <td><?php echo htmlspecialchars($app['specialization']); ?></td>
                            <td><?php echo $app['appointment_date']; ?></td>
                            <td><?php echo $app['start_time'] . " - " . $app['end_time']; ?></td>
                            <td>
                                <?php
                                    if ($app['status'] === 'Pending') {
                                        echo "<span class='status-pending'>⏳ Waiting for doctor's approval</span>";
                                    } elseif ($app['status'] === 'Approved') {
                                        echo "<span class='status-approved'>✅ Doctor approved</span>";
                                    } else {
                                        echo "<span class='status-rejected'>❌ Doctor rejected</span>";
                                    }
                                ?>
                            </td>
                            <td><?php echo $app['created_at'] ?? 'N/A'; ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="text-center">No appointments found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>
