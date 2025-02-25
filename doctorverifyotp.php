<?php
session_start();

// Check if OTP is stored in session
if (!isset($_SESSION['otp'])) {
    header('Location: doctorforgotpassword.php');  // If OTP isn't stored, redirect to forgot password page
    exit();
}

// Handle OTP verification
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['otp'])) {
    $inputOtp = $_POST['otp'];

    // Check if entered OTP matches the stored OTP
    if ($inputOtp == $_SESSION['otp']) {
        // OTP is verified, redirect to password reset page
        header('Location: doctorreset_password.php');
        exit();
    } else {
        $error = "Invalid OTP. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify OTP - BookMyDoc</title>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(to right, #00b4d8, #0077b6);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .form-box {
            background: white;
            padding: 30px;
            border-radius: 16px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
            width: 400px;
            text-align: center;
            animation: fadeIn 0.8s ease-in-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .logo-container {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
        }

        .logo {
            width: 50px;
            height: 50px;
            margin-right: 10px;
        }

        .logo-container h2 {
            margin: 0;
            font-size: 1.8rem;
            color: #0077b6;
        }

        .logo-container h5 {
            font-size: 1rem;
            color: #666;
            margin-top: 5px;
        }

        input {
            display: block;
            width: 85%;
            margin: 10px auto;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 1rem;
            transition: 0.3s;
        }

        input:focus {
            border-color: #0077b6;
            box-shadow: 0 0 8px rgba(0, 119, 182, 0.3);
        }

        .error {
            color: red;
            font-size: 0.9rem;
            margin-bottom: 10px;
        }

        button {
            width: 90%;
            padding: 12px;
            background: #0077b6;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1.1rem;
            cursor: pointer;
            transition: 0.3s;
        }

        button:hover {
            background: #005f99;
        }

        .form-footer {
            margin-top: 15px;
            font-size: 0.9rem;
            color: #555;
        }

        .form-footer a {
            color: #0077b6;
            text-decoration: none;
        }

        .form-footer a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

    <div class="form-box">
        <div class="logo-container">
            <img src="images/logo.png" alt="Logo" class="logo">
            <div>
                <h2>BookMyDoc</h2>
                <h5>Online Doctor Appointment Booking</h5>
            </div>
        </div>
        <h3 style="color: #333;">Verify OTP</h3>
        <p style="color: #555;">Enter the OTP sent to your email to reset your password.</p>

        <!-- Form to verify OTP -->
        <form method="POST" action="">
            <input type="text" name="otp" placeholder="Enter OTP" required><br><br>
            <button type="submit">Verify OTP</button>
        </form>

        <!-- Error message -->
        <?php if (!empty($error)): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        
        <div class="form-footer">
            <p>Didn't receive an OTP? <a href="patientforgotpassword.php">Resend OTP</a></p>
        </div>
    </div>

</body>
</html>
