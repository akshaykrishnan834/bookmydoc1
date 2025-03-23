<?php
include('db_connection.php');

if (isset($_GET['id'])) {
    $patient_id = mysqli_real_escape_string($conn, $_GET['id']);

    // Fetch current status
    $query = "SELECT action FROM patientreg WHERE id = '$patient_id'";
    $result = mysqli_query($conn, $query);
    $row = mysqli_fetch_assoc($result);
    $new_status = ($row['action'] == 'enabled') ? 'disabled' : 'enabled';

    // Update status
    $update_query = "UPDATE patientreg SET action = '$new_status' WHERE id = '$patient_id'";
    mysqli_query($conn, $update_query);
}

// Redirect back to manage patients page
header("Location: adminmanagepatient.php");
exit();
?>
