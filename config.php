<?php
$host = "localhost";  // Change if using a remote database
$user = "root";       // Your database username
$password = "";       // Your database password
$dbname = "registration";  // Your database name

// Create a connection
$conn = new mysqli($host, $user, $password, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Database Connection Failed: " . $conn->connect_error);
}

// Razorpay credentials
define('RAZORPAY_KEY_ID', 'rzp_test_er335hWwGlo6uyV9PYp8kXWMej');
define('RAZORPAY_KEY_SECRET', 'your_actual_secret_key'); // This should be your actual secret key from Razorpay
?>
