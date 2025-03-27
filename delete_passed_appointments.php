<?php
$servername = "localhost";
$username = "root"; // Change as needed
$password = ""; // Change as needed
$dbname = "bookmydoc"; // Your database name

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

date_default_timezone_set('Asia/Kolkata'); // Set timezone to Kolkata

function updatePastAppointmentsStatus($conn) {
    // Get the current date and time
    $currentDateTime = date('Y-m-d H:i:s');
    
    // SQL query to update past appointments status to 'expired' only if payment_status is 'pending'
    $sql = "UPDATE appointment_requests ar
            JOIN doctor_availability da ON ar.slot_id = da.id
            SET ar.status = 'expired'
            WHERE CONCAT(ar.appointment_date, ' ', da.start_time) < ? 
            AND ar.status NOT IN ('expired', 'rejected')
            AND ar.payment_status = 'pending'";
    
    // Prepare statement
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("s", $currentDateTime);
        
        // Execute the statement
        if ($stmt->execute()) {
            echo "";
        } else {
            echo "Error updating records: " . $stmt->error;
        }
        
        // Close statement
        $stmt->close();
    } else {
        echo "Error preparing statement: " . $conn->error;
    }
}

// Call the function
updatePastAppointmentsStatus($conn);
?>
