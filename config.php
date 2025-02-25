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
?>
