<?php
require 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $token = $_POST['token'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if ($password !== $confirm_password) {
        die("Passwords do not match.");
    }

    $stmt = $conn->prepare("SELECT * FROM patientreg WHERE reset_token=? AND reset_token_expires > NOW()");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Update password and remove token
        $stmt = $conn->prepare("UPDATE users SET password=?, reset_token=NULL, reset_token_expiry=NULL WHERE reset_token=?");
        $stmt->bind_param("ss", $hashed_password, $token);
        $stmt->execute();

        echo "Password updated successfully! <a href='login.php'>Login</a>";
    } else {
        echo "Invalid or expired token.";
    }
}
?>
