<?php
session_start();
include('db_connection.php');

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['appointment_id'], $_POST['action'])) {
    $appointment_id = $_POST['appointment_id'];
    $action = $_POST['action'];

    // Determine the new status based on the action
    if ($action === 'approve') {
        $new_status = 'Approved';
    } elseif ($action === 'reject') {
        $new_status = 'Rejected';
    } else {
        $_SESSION['error'] = "Invalid action.";
        header("Location: appointment_requests.php");
        exit();
    }

    // Update the appointment status in the database
    $sql = "UPDATE appointment_requests SET status = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $new_status, $appointment_id);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Appointment successfully " . strtolower($new_status) . ".";
    } else {
        $_SESSION['error'] = "Failed to update appointment status.";
    }

    $stmt->close();
    $conn->close();

    // Redirect back to appointment requests page
    header("Location: manageappointments.php");
    exit();
} else {
    $_SESSION['error'] = "Invalid request.";
    header("Location: manageappointments.php");
    exit();
}
?>
