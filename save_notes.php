<?php
session_start();
include('db_connection.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['id'])) {
    $appointment_id = $_POST['appointment_id'];
    $consultation_notes = $_POST['consultation_notes'];
    $doctor_id = $_SESSION['id'];

    // Verify that this appointment belongs to the logged-in doctor
    $stmt = $conn->prepare("
        SELECT a.id 
        FROM appointment_requests a
        JOIN doctor_availability s ON a.slot_id = s.id
        WHERE a.id = ? AND s.doctor_id = ?
    ");
    $stmt->bind_param("ii", $appointment_id, $doctor_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Update the consultation notes
        $update_stmt = $conn->prepare("
            UPDATE appointment_requests 
            SET consultation_notes = ?
            WHERE id = ?
        ");
        $update_stmt->bind_param("si", $consultation_notes, $appointment_id);
        
        if ($update_stmt->execute()) {
            $_SESSION['success_message'] = "Consultation notes saved successfully.";
        } else {
            $_SESSION['error_message'] = "Error saving consultation notes.";
        }
        $update_stmt->close();
    } else {
        $_SESSION['error_message'] = "Unauthorized access.";
    }
    $stmt->close();
    $conn->close();
    
    header("Location: displayappointments.php");
    exit();
} 