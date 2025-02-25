<?php
session_start();
include('db_connection.php');
include('doctorheader.php');

$doctor_id = $_SESSION['id']; // Get logged-in doctor's ID

// Fetch pending appointment requests
$sql = "SELECT ar.id, u.name AS patient_name, ar.appointment_date, da.start_time, da.end_time 
        FROM appointment_requests ar
        JOIN patientreg u ON ar.user_id = u.id
        JOIN doctor_availability da ON ar.slot_id = da.id
        WHERE ar.doctor_id = ? AND ar.status = 'Pending'
        ORDER BY ar.appointment_date";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $doctor_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Appointment Requests</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-5">
    <h2 class="text-center">Pending Appointment Requests</h2>

    <?php if ($result->num_rows > 0) { ?>
        <table class="table table-bordered mt-4">
            <thead>
                <tr>
                    <th>Patient Name</th>
                    <th>Appointment Date</th>
                    <th>Time Slot</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['patient_name']); ?></td>
                        <td><?php echo $row['appointment_date']; ?></td>
                        <td><?php echo $row['start_time'] . " - " . $row['end_time']; ?></td>
                        <td>
                            <form method="POST" action="approve_reject_appointment.php" style="display:inline;">
                                <input type="hidden" name="appointment_id" value="<?php echo $row['id']; ?>">
                                <button type="submit" name="action" value="approve" class="btn btn-success">Approve</button>
                            </form>
                            <form method="POST" action="approve_reject_appointment.php" style="display:inline;">
                                <input type="hidden" name="appointment_id" value="<?php echo $row['id']; ?>">
                                <button type="submit" name="action" value="reject" class="btn btn-danger">Reject</button>
                            </form>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    <?php } else { ?>
        <p class="text-center">No pending appointments.</p>
    <?php } ?>
</div>

</body>
</html>

<?php
$conn->close();
?>
