<?php
session_start();
include('db_connection.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['doctor_id']) && isset($_POST['rejection_reason'])) {
        $doctor_id = intval($_POST['doctor_id']);
        $rejection_reason = trim($_POST['rejection_reason']);

        // Update doctor status and add rejection reason
        $sql = "UPDATE doctorreg SET status = 'Rejected', rejection_reason = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $rejection_reason, $doctor_id);

        if ($stmt->execute()) {
            echo "<script>alert('Doctor has been rejected successfully.'); window.location.href = 'adminmanagedoct.php';</script>";
        } else {
            echo "<script>alert('Error rejecting doctor. Please try again.'); window.location.href = 'adminmanagedoct.php';</script>";
        }

        $stmt->close();
    } else {
        echo "<script>alert('Invalid request.'); window.location.href = 'adminmanagedoct.php';</script>";
    }
} else {
    echo "<script>alert('Invalid request method.'); window.location.href = 'adminmanagedoct.php';</script>";
}

$conn->close();
?>
