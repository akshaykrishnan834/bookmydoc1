<?php
session_start();
include('db_connection.php');

// Fetch all pending appointment requests with updated table and field references
$sql = "SELECT ar.id, ar.user_id, ar.doctor_id, ar.slot_id, ar.appointment_date, ar.status, ar.created_at, 
               u.name AS patient_name, u.email AS patient_email, u.phone AS patient_phone, 
               d.name AS doctor_name, s.slot_time 
        FROM appointment_requests ar 
        JOIN patientreg u ON ar.user_id = u.id 
        JOIN doctorreg d ON ar.doctor_id = d.id 
        JOIN doctor_availability s ON ar.slot_id = s.id 
        WHERE ar.status = 'Pending'";

$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Appointment Requests</title>
    <style>
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: center; }
        th { background-color: #f2f2f2; }
        .success { color: green; }
        .error { color: red; }
        .btn { padding: 5px 10px; margin: 2px; border: none; cursor: pointer; }
        .approve-btn { background-color: #4CAF50; color: white; }
        .reject-btn { background-color: #f44336; color: white; }
    </style>
</head>
<body>
    <h2>Pending Appointment Requests</h2>

    <?php if (isset($_SESSION['success'])): ?>
        <p class="success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></p>
    <?php endif; ?>
    <?php if (isset($_SESSION['error'])): ?>
        <p class="error"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></p>
    <?php endif; ?>

    <?php if ($result->num_rows > 0): ?>
        <table>
            <tr>
                <th>Patient Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Doctor Name</th>
                <th>Slot Time</th>
                <th>Appointment Date</th>
                <th>Status</th>
                <th>Created At</th>
                <th>Actions</th>
            </tr>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['patient_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['patient_email']); ?></td>
                    <td><?php echo htmlspecialchars($row['patient_phone']); ?></td>
                    <td><?php echo htmlspecialchars($row['doctor_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['slot_time']); ?></td>
                    <td><?php echo htmlspecialchars($row['appointment_date']); ?></td>
                    <td><?php echo htmlspecialchars($row['status']); ?></td>
                    <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                    <td>
                        <form method="POST" action="update_appointment_status.php" style="display:inline;">
                            <input type="hidden" name="appointment_id" value="<?php echo $row['id']; ?>">
                            <input type="hidden" name="action" value="approve">
                            <button type="submit" class="btn approve-btn">Approve</button>
                        </form>

                        <form method="POST" action="update_appointment_status.php" style="display:inline;">
                            <input type="hidden" name="appointment_id" value="<?php echo $row['id']; ?>">
                            <input type="hidden" name="action" value="reject">
                            <button type="submit" class="btn reject-btn">Reject</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
    <?php else: ?>
        <p>No pending appointment requests.</p>
    <?php endif; ?>

    <?php $conn->close(); ?>
</body>
</html>