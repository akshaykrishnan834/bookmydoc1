<?php
// Database connection
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

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get email from form
    $email = $_POST['email'];
    
    // Check if email already exists in database
    $check_email = "SELECT * FROM doctorreg WHERE email = ?";
    $stmt = $conn->prepare($check_email);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        // Email already exists
        // Redirect back to the signup form with an error message
        header("Location: doctorreg.php?error=email_exists");
        exit();
    } else {
        // Email is available, proceed with registration
        $name = $_POST['name'];
        $phone = $_POST['phone'];
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Always hash passwords
        
        // Insert new doctor
        $insert_query = "INSERT INTO doctorreg (name, phone, email, password) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($insert_query);
        $stmt->bind_param("ssss", $name, $phone, $email, $password);
        
        if ($stmt->execute()) {
            // Registration successful
            header("Location: doctorlog.php?registration=success");
            exit();
        } else {
            // Registration failed
            header("Location: doctorreg.php?error=registration_failed");
            exit();
        }
    }
    
    $stmt->close();
    $conn->close();
}
?>