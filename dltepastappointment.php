<?php
// Include database connection
include('db_connection.php');

// Set timezone to Indian Standard Time
date_default_timezone_set('Asia/Kolkata');

// Current date and time
$current_date = date('Y-m-d');

// If your appointment status needs to be updated instead of deleted
$sql2 = "UPDATE appointment_requests SET status = 'expired' WHERE appointment_date < ? OR (appointment_date = ?)";
$stmt2 = $conn->prepare($sql2);
$stmt2->bind_param("ss", $current_date, $current_date);
$stmt2->execute();

$conn->close();
?>