<?php
// update_profile.php

session_start();
$userId = $_SESSION['id']; // Assuming session stores user ID

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "bookmydoc";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    if (isset($_FILES['profile_photo']) && $_FILES['profile_photo']['error'] === UPLOAD_ERR_OK) {
        $photoTmp = $_FILES['profile_photo']['tmp_name'];
        $photoName = basename($_FILES['profile_photo']['name']);
        $targetDir = "uploads/";
        $targetFilePath = $targetDir . $photoName;

        if (move_uploaded_file($photoTmp, $targetFilePath)) {
            $sql = "UPDATE doctorreg SET email=?, phone=?, password=?, profile_photo=? WHERE id=?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssssi", $email, $phone, $password, $targetFilePath, $userId);
        } else {
            echo "Failed to upload profile photo.";
            exit;
        }
    } else {
        $sql = "UPDATE doctorreg SET email=?, phone=?, password=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssi", $email, $phone, $password, $userId);
    }

    if ($stmt->execute()) {
        // Redirect to profile page after successful update
        header("Location: doctorac.php");
        exit;
    } else {
        echo "Error updating profile: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>
