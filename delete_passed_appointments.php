<?php
session_start();
// Database connection details
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "bookmydoc";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Delete appointments that have passed
$sql = "DELETE FROM appointment_requests 
        WHERE appointment_date < CURDATE() 
        OR (appointment_date = CURDATE() AND 
            EXISTS (
                SELECT 1 FROM doctor_availability da 
                WHERE da.id = appointment_requests.slot_id 
                AND CONCAT(appointment_date, ' ', da.end_time) < NOW()
            )
        )";

if ($conn->query($sql) === TRUE) {
    echo "<script>
        alert('Passed appointments have been deleted successfully.');
        window.location.href = 'manageappointments.php';
    </script>";
} else {
    echo "<script>
        alert('Error deleting passed appointments: " . $conn->error . "');
        window.location.href = 'manageappointments.php';
    </script>";
}

$conn->close();
?> 