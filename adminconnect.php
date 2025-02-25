<?php
    $email = $_POST['email'];
    $password = $_POST['password'];
   

    // Database connection
    $conn = new mysqli('localhost', 'root', '', 'registration');
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Insert data into the database
    $stmt = $conn->prepare("INSERT INTO adminl( email, password) VALUES( ?, ?)");
    $password_hashed = password_hash($password, PASSWORD_DEFAULT);
    $stmt->bind_param("ss", $email, $password_hashed );

    if ($stmt->execute()) {
        // Redirect to login page after successful registration
        
        exit(); // Ensure no further code is executed after the redirect
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
?>
