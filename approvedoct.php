<?php
session_start();
include('db_connection.php'); // Ensure your database connection is included

if (isset($_GET['id'])) {
    $doctor_id = $_GET['id'];

    // Update doctor status to 'Approved'
    $query = "UPDATE doctorreg SET status = 'Approved' WHERE id = '$doctor_id'";
    if (mysqli_query($conn, $query)) {
        echo "<script>alert('Doctor approved successfully!'); window.location.href='managedoctors.php';</script>";
    } else {
        echo "Error updating record: " . mysqli_error($conn);
    }
} else {
    echo "Invalid request!";
}
?>
