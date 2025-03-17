<?php
// Start session if it's not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include database connection
include('db_connection.php');

// Check if user is logged in
if (!isset($_SESSION['id'])) {
    // Redirect to login page if not logged in
    header('Location: login.php');
    exit();
}

// Get the appointment ID from URL parameter
$appointment_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$patient_id = $_SESSION['id'];

if ($appointment_id <= 0) {
    // Invalid appointment ID
    $_SESSION['error'] = "Invalid appointment ID.";
    header('Location: appointmentstat.php');
    exit();
}

// Verify the appointment belongs to the logged-in patient
$verify_sql = "SELECT id FROM appointment_requests WHERE id = ? AND user_id = ?";
$verify_stmt = $conn->prepare($verify_sql);
$verify_stmt->bind_param("ii", $appointment_id, $patient_id);
$verify_stmt->execute();
$verify_result = $verify_stmt->get_result();

if ($verify_result->num_rows === 0) {
    // Appointment doesn't exist or doesn't belong to this user
    $_SESSION['error'] = "You don't have permission to cancel this appointment.";
    header('Location: appointmentstat.php');
    exit();
}

// Delete the appointment
$delete_sql = "DELETE FROM appointment_requests WHERE id = ? AND user_id = ?";
$delete_stmt = $conn->prepare($delete_sql);
$delete_stmt->bind_param("ii", $appointment_id, $patient_id);

// Execute the delete operation
if ($delete_stmt->execute()) {
    // Also delete any related payment records if needed
    $delete_payment_sql = "DELETE FROM payments WHERE appointment_id = ?";
    $delete_payment_stmt = $conn->prepare($delete_payment_sql);
    $delete_payment_stmt->bind_param("i", $appointment_id);
    $delete_payment_stmt->execute();
    
    // Success message
    $_SESSION['success'] = "Your appointment has been cancelled successfully.";
} else {
    // Error message
    $_SESSION['error'] = "Failed to cancel appointment. Please try again.";
}

// Redirect back to appointments page
header('Location: appointmentstat.php');
exit();
?>