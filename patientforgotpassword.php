<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // Correct path to autoload.php

// Start session to store OTP and email
session_start();

// Database connection
$host = 'localhost'; // Replace with your database host
$dbname = 'bookmydoc'; // Replace with your database name
$username = 'root'; // Replace with your database username
$password = ''; // Replace with your database password

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Function to generate a random OTP
function generateVerificationCode($length = 6) {
    return strval(random_int(100000, 999999)); // 6-digit OTP
}

// Function to send the OTP to the user's email
function sendVerificationEmail($recipientEmail, $verificationCode) {
    $mail = new PHPMailer(true);

    try {
        // SMTP Configuration
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'akshaykrishnan2027@mca.ajce.in';  // Replace with your Gmail address
        $mail->Password   = 'aks34#6767';     // Use your Gmail App Password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        // Sender and recipient details
        $mail->setFrom('akshaykrishnan2027@mca.ajce.in', 'BookMyDoc');
        $mail->addAddress($recipientEmail);

        // Email content
        $mail->Subject = 'Your OTP for Password Reset';
        $mail->Body    = "Your OTP for resetting your password is: $verificationCode\n\nThis OTP will expire in 10 minutes.";

        // Send the email
        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Email sending failed: " . $mail->ErrorInfo);
        return false;
    }
}

$errorMessage = "";

// Handle form submission for email input and sending OTP
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['email'])) {
    $email = $_POST['email'];

    // Validate the email
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        // Check if the email exists in the database
        $stmt = $pdo->prepare("SELECT * FROM patientreg WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            // Generate OTP
            $verificationCode = generateVerificationCode();

            // Store the OTP and email in the session for validation
            $_SESSION['otp'] = $verificationCode;
            $_SESSION['email'] = $email;

            // Send the OTP to the email address
            if (sendVerificationEmail($email, $verificationCode)) {
                // Redirect to OTP verification page
                header('Location: patientverifyotp.php');
                exit();
            } else {
                $errorMessage = "Failed to send OTP. Please try again.";
            }
        } else {
            $errorMessage = "Email address not found. Please try again.";
        }
    } else {
        $errorMessage = "Invalid email address. Please enter a valid email.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - BookMyDoc</title>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(to right, #1e90ff, #0077b6);
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

        form {
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 100%;
        }

        .input-container {
            width: 90%;
            margin: 10px 0;
        }

        input {
            width: 100%;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 1rem;
            transition: 0.3s;
            box-sizing: border-box;
        }

        input:focus {
            border-color: #0077b6;
            box-shadow: 0 0 8px rgba(0, 119, 182, 0.3);
        }

        input.valid {
            border-color: #28a745;
            box-shadow: 0 0 8px rgba(40, 167, 69, 0.3);
        }

        input.invalid {
            border-color: #dc3545;
            box-shadow: 0 0 8px rgba(220, 53, 69, 0.3);
        }

        .error-message {
            color: red;
            font-size: 0.9rem;
            margin-bottom: 10px;
            width: 90%;
            text-align: center;
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
            margin-top: 10px;
        }

        button:hover {
            background: #005f99;
        }

        button:disabled {
            background: #cccccc;
            cursor: not-allowed;
        }

        .form-footer {
            margin-top: 15px;
            font-size: 0.9rem;
            color: #555;
            width: 100%;
            text-align: center;
        }

        .form-footer a {
            color: #0077b6;
            text-decoration: none;
        }

        .form-footer a:hover {
            text-decoration: underline;
        }
        
        .back-to-home {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-top: 20px;
        }
        
        .back-to-home a {
            display: inline-flex;
            align-items: center;
            padding: 12px 15px;
            font-size: 1rem;
            font-weight: bold;
            color: white;
            background: linear-gradient(135deg, #0077b6, #1e90ff);
            border-radius: 30px;
            text-decoration: none;
            transition: all 0.3s ease-in-out;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }

        .back-to-home a:hover {
            background: linear-gradient(135deg, #005f8d, #1565c0);
            transform: scale(1.05);
        }

        .home-icon {
            width: 20px;
            height: 20px;
            margin-right: 10px;
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
        <h3 style="color: #333;">Forgot Your Password?</h3>
        <p style="color: #555;">Enter your email to receive an OTP for resetting your password.</p>

        <!-- Display error message if any -->
        <?php if (!empty($errorMessage)) { ?>
            <p class="error-message"> <?php echo $errorMessage; ?> </p>
        <?php } ?>

        <!-- Form to enter email for OTP -->
        <form method="POST" action="" id="resetForm">
            <div class="input-container">
                <input type="email" name="email" id="email" placeholder="Enter your email" required>
            </div>
            <button type="submit" id="submitBtn" disabled>Send OTP</button>
        </form>
        
        <div class="form-footer">
            <p>Remember your password? <a href="patientlog.php">Login here</a></p>
        </div>
        <div class="back-to-home">
            <a href="home.php">
                <img src="images/home_738822.png" alt="Home" class="home-icon">Home</a>
            <a href="role.php">
                <img src="images/staff.png" alt="Home" class="home-icon">Role</a>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const emailInput = document.getElementById('email');
            const submitBtn = document.getElementById('submitBtn');
            
            // Email validation function
            function validateEmail(email) {
                const emailRegex = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/;
                return emailRegex.test(String(email).toLowerCase());
            }
            
            // Live validation
            emailInput.addEventListener('input', function() {
                const email = this.value.trim();
                
                if (email === '') {
                    this.classList.remove('valid', 'invalid');
                    submitBtn.disabled = true;
                    return;
                }
                
                if (validateEmail(email)) {
                    this.classList.add('valid');
                    this.classList.remove('invalid');
                    submitBtn.disabled = false;
                } else {
                    this.classList.add('invalid');
                    this.classList.remove('valid');
                    submitBtn.disabled = true;
                }
            });
            
            // Form submission validation
            document.getElementById('resetForm').addEventListener('submit', function(event) {
                const email = emailInput.value.trim();
                
                if (!validateEmail(email)) {
                    event.preventDefault();
                    emailInput.classList.add('invalid');
                }
            });
        });
    </script>
</body>
</html>