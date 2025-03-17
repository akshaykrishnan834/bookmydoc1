<?php
session_start();
require 'db_connection.php';

if (!isset($_SESSION['id'])) {
    die("Unauthorized access");
}

$doctor_id = $_SESSION['id']; // Logged-in doctor's ID

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['appointment_id'], $_POST['action'])) {
    $appointment_id = $_POST['appointment_id'];
    $action = $_POST['action'];

    if ($action == 'approve') {
        // Approve the appointment
        $sql = "UPDATE appointment_requests SET status = 'approved', rejection_reason = NULL WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $appointment_id);
        $stmt->execute();
    } elseif ($action == 'reject' && isset($_POST['rejection_reason'])) {
        $rejection_reason = trim($_POST['rejection_reason']);

        // Update appointment status to rejected and store rejection reason
        $update_sql = "UPDATE appointment_requests SET status = 'rejected', rejection_reason = ? WHERE id = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("si", $rejection_reason, $appointment_id);

        if ($update_stmt->execute()) {
            // Successfully updated rejection reason
        } else {
            // Handle error if update fails
            error_log("Error updating rejection reason: " . $update_stmt->error);
        }
    }
}

// Redirect back to appointment requests page
header("Location: manageappointments.php");
exit();
?>
