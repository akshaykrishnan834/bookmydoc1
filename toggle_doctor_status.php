<?php
include('db_connection.php');

if (isset($_GET['id'])) {
    $doctor_id = mysqli_real_escape_string($conn, $_GET['id']);

    // Fetch current status
    $query = "SELECT status FROM doctorreg WHERE id = '$doctor_id'";
    $result = mysqli_query($conn, $query);
    $row = mysqli_fetch_assoc($result);
    $new_status = ($row['action'] == 'enabled') ? 'disabled' : 'enabled';

    // Update status
    $update_query = "UPDATE doctorreg SET action = '$new_status' WHERE id = '$doctor_id'";
    mysqli_query($conn, $update_query);
}

// Redirect back to manage doctors page
header("Location: adminmanagedoct.php");
exit();
?>
