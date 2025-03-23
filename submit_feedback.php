<?php
session_start();
include('db_connection.php'); // Include database connection

// Check if the patient is logged in
if (!isset($_SESSION['id'])) {
    $_SESSION['error'] = "Please log in to submit feedback.";
    header("Location: login.php");
    exit;
}

$patient_id = $_SESSION['id']; // Get logged-in patient ID

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $doctor_id = intval($_POST['doctor_id']);
    $rating = intval($_POST['rating']);
    $feedback_text = trim($_POST['feedback_text']);

    // Validate input
    if ($doctor_id <= 0 || $rating < 1 || $rating > 5 || empty($feedback_text)) {
        $_SESSION['error'] = "Invalid feedback. Please provide a valid rating and comments.";
        header("Location: browse_doctors.php");
        exit;
    }

    // Insert feedback into the database
    $sql = "INSERT INTO feedback (doctor_id, patient_id, rating, feedback_text) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iiis", $doctor_id, $patient_id, $rating, $feedback_text);

    if ($stmt->execute()) {
        $_SESSION['success'] = "✅ Feedback submitted successfully!";
    } else {
        $_SESSION['error'] = "❌ Error submitting feedback. Please try again.";
    }

    $stmt->close();
    $conn->close();

    // Redirect back to doctor listing page
    header("Location: browsedoct.php");
    exit;
} else {
    $_SESSION['error'] = "Invalid request.";
    header("Location: browsedoct.php");
    exit;
}
?>
