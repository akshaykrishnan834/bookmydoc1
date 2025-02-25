<?php
session_start();
include('db_connection.php');

if (isset($_GET['id'])) {
    $doctor_id = intval($_GET['id']);

    // Update status to "Rejected" in the database
    $query = "UPDATE doctorreg SET status = 'Rejected' WHERE id = ?";
    $stmt = mysqli_prepare($conn, $query);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $doctor_id);
        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['message'] = "Doctor has been rejected successfully.";
        } else {
            $_SESSION['error'] = "Failed to reject the doctor. Please try again.";
        }
        mysqli_stmt_close($stmt);
    } else {
        $_SESSION['error'] = "Database error. Please try again.";
    }

    mysqli_close($conn);
}

// Redirect back to the pending approvals page
header("Location: managedoctors.php");
exit();
?>
