<?php
session_start();
include 'db_connection.php'; // Ensure you have a working DB connection file

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Database connection
    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Get doctor ID from session (ensure session is set)
    if (!isset($_SESSION['id'])) {
        echo "<script>alert('Session expired. Please log in again.'); window.location.href='login.php';</script>";
        exit();
    }
    $id = $_SESSION['id'];

    // Sanitize input data
    $name = htmlspecialchars($_POST['name'] ?? '');
    $age = intval($_POST['age'] ?? 0);
    $qualifications = htmlspecialchars($_POST['qualifications'] ?? '');
    $experience = intval($_POST['experience'] ?? 0);
    $specialization = htmlspecialchars($_POST['specialization'] ?? '');

    // Handle file uploads
    $uploadDir = "uploads/";
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $allowedImageTypes = ['image/jpeg', 'image/png', 'image/gif'];
    $allowedDocTypes = ['application/pdf'];

    // Initialize file paths
    $profilePhotoPath = null;
    $degreePath = null;

    // Profile Photo Upload
    if (!empty($_FILES['profilePhoto']['name'])) {
        $profilePhoto = $_FILES['profilePhoto'];
        $extension = pathinfo($profilePhoto["name"], PATHINFO_EXTENSION);
        $profilePhotoPath = $uploadDir . "profile_pic_" . $id . ".jpg"; // Consistent file name

        if (in_array($profilePhoto['type'], $allowedImageTypes) && $profilePhoto['size'] < 2 * 1024 * 1024) { // Max 2MB
            move_uploaded_file($profilePhoto["tmp_name"], $profilePhotoPath);
        } else {
            echo "<script>alert('Invalid profile photo format or size. Only JPG, PNG, GIF under 2MB allowed.'); window.history.back();</script>";
            exit();
        }
    }

    // Degree Certificate Upload
    if (!empty($_FILES['degree']['name'])) {
        $degreeCertificate = $_FILES['degree'];
        $degreePath = $uploadDir . "degree_" . $id . ".pdf"; // Unique per doctor

        if (in_array($degreeCertificate['type'], $allowedDocTypes) && $degreeCertificate['size'] < 5 * 1024 * 1024) { // Max 5MB
            move_uploaded_file($degreeCertificate["tmp_name"], $degreePath);
        } else {
            echo "<script>alert('Invalid degree certificate format or size. Only PDF under 5MB allowed.'); window.history.back();</script>";
            exit();
        }
    }

    // Build the SQL query dynamically
    $sql = "UPDATE doctorreg SET name=?, age=?, qualifications=?, experience=?, specialization=?";
    $params = [$name, $age, $qualifications, $experience, $specialization];
    $types = "sisss";

    if ($profilePhotoPath) {
        $sql .= ", profile_photo=?";
        $params[] = $profilePhotoPath;
        $types .= "s";
    }

    if ($degreePath) {
        $sql .= ", degree_certificate=?";
        $params[] = $degreePath;
        $types .= "s";
    }

    $sql .= " WHERE id=?";
    $params[] = $id;
    $types .= "i";

    // Prepare and execute the update statement
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("Error preparing statement: " . $conn->error);
    }

    $stmt->bind_param($types, ...$params);

    if ($stmt->execute()) {
        echo "<script>alert('Profile updated successfully!'); window.location.href='doctorprofile.php';</script>";
    } else {
        echo "<script>alert('Error updating profile. Try again!'); window.history.back();</script>";
    }

    $stmt->close();
    $conn->close();
}
?>
