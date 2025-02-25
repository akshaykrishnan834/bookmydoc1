<?php
session_start();
include('db_connection.php');

// Check if user is logged in
if (!isset($_SESSION['id'])) {
    die("Please log in to book an appointment.");
}

$patient_id = $_SESSION['id'];

// Check if form data is set
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $slot_id = intval($_POST['slot_id']);
    $doctor_id = intval($_POST['doctor_id']);
    $appointment_date = $_POST['appointment_date'];

    // Validate the appointment date
    if (empty($appointment_date)) {
        die("⚠️ Appointment date is required.");
    }

    // ✅ Check if the appointment already exists for the doctor, slot, and date (without user_id)
    $checkDuplicateQuery = "SELECT COUNT(*) AS existing_count 
                            FROM appointment_requests
                            WHERE doctor_id = ? AND slot_id = ? AND appointment_date = ?";
    $stmt = $conn->prepare($checkDuplicateQuery);
    $stmt->bind_param("iis", $doctor_id, $slot_id, $appointment_date);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();

    if ($result['existing_count'] > 0) {
        echo "<script>alert('⚠️ An appointment for this time slot already exists. Please select another slot.'); 
              window.location.href = 'patientprofile.php';</script>";
        exit();
    }

    // ✅ Check if the slot already has 10 patients (to prevent overbooking)
    $checkSlotCapacityQuery = "SELECT COUNT(*) AS booked_count 
                               FROM appointment_requests
                               WHERE doctor_id = ? AND slot_id = ? AND appointment_date = ? AND status IN ('Pending', 'Approved')";
    $stmt = $conn->prepare($checkSlotCapacityQuery);
    $stmt->bind_param("iis", $doctor_id, $slot_id, $appointment_date);
    $stmt->execute();
    $capacityResult = $stmt->get_result()->fetch_assoc();

    if ($capacityResult['booked_count'] >= 10) {
        echo "<script>alert('❌ This time slot is fully booked. Please select another slot.'); 
              window.location.href = 'patientprofile.php';</script>";
        exit();
    }

    // ✅ Insert the appointment request with status 'Pending' if all checks pass
    $insertQuery = "INSERT INTO appointment_requests (user_id, doctor_id, slot_id, appointment_date, status) 
                    VALUES (?, ?, ?, ?, 'Pending')";
    $stmt = $conn->prepare($insertQuery);
    $stmt->bind_param("iiis", $patient_id, $doctor_id, $slot_id, $appointment_date);

    if ($stmt->execute()) {
        echo "<script>alert('✅ Appointment request submitted successfully. Waiting for doctor approval.'); 
              window.location.href = 'patientprofile.php';</script>";
    } else {
        echo "Error: " . $stmt->error;
    }
} else {
    echo "Invalid request.";
}
?>
