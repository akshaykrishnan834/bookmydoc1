<?php
session_start();

// Check if OTP and email exist in session
if (!isset($_SESSION['otp']) || !isset($_SESSION['email'])) {
    header('Location: doctorforgotpassword.php'); // Redirect if OTP isn't verified
    exit();
}

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

$error = ""; // Variable to store error messages

// Handle password reset form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['new_password'])) {
    $newPassword = trim($_POST['new_password']);

    // Password strength validation (at least 8 characters, 1 letter, and 1 number)
    if (strlen($newPassword) < 8 || !preg_match("/[A-Za-z]/", $newPassword) || !preg_match("/\d/", $newPassword)) {
        $error = "Password must be at least 8 characters long and include at least one letter and one number.";
    } else {
        // Hash the new password before storing it in the database
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

        // Get the email from the session
        $email = $_SESSION['email'];

        // Update the password in the database
        $stmt = $conn->prepare("UPDATE doctorreg SET password = ? WHERE email = ?");
        $stmt->bind_param("ss", $hashedPassword, $email);

        if ($stmt->execute()) {
            // Unset specific session variables instead of destroying the session
            unset($_SESSION['otp']);
            unset($_SESSION['email']);

            // Redirect to login page after password reset
            header("Location: doctorlog.php");
            exit();
        } else {
            $error = "Error updating password. Please try again.";
        }

        $stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - BookMyDoc</title>
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
            transition: all 0.3s;
        }

        input:focus {
            border-color: #0077b6;
            box-shadow: 0 0 8px rgba(0, 119, 182, 0.3);
        }

        /* Input validation styles */
        input.valid-input {
            border-color: #28a745;
            background-color: rgba(40, 167, 69, 0.1);
            box-shadow: 0 0 8px rgba(40, 167, 69, 0.2);
        }

        input.invalid-input {
            border-color: #dc3545;
            background-color: rgba(220, 53, 69, 0.1);
            box-shadow: 0 0 8px rgba(220, 53, 69, 0.2);
        }

        .error {
            color: #dc3545;
            font-size: 0.9rem;
            margin-bottom: 10px;
        }

        .validation-message {
            font-size: 0.85rem;
            text-align: left;
            width: 85%;
            margin: 5px auto;
            padding-left: 10px;
        }

        .validation-item {
            margin: 5px 0;
            position: relative;
            padding-left: 22px;
        }

        .validation-item:before {
            content: '';
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            width: 16px;
            height: 16px;
            background-size: contain;
            background-repeat: no-repeat;
        }

        .validation-item.valid {
            color: #28a745;
        }

        .validation-item.invalid {
            color: #dc3545;
        }

        .validation-item.valid:before {
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='%2328a745' viewBox='0 0 16 16'%3E%3Cpath d='M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0z'/%3E%3C/svg%3E");
        }

        .validation-item.invalid:before {
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='%23dc3545' viewBox='0 0 16 16'%3E%3Cpath d='M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z'/%3E%3C/svg%3E");
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
        }

        .form-footer a {
            color: #0077b6;
            text-decoration: none;
        }

        .form-footer a:hover {
            text-decoration: underline;
        }

        /* Password strength meter */
        .password-strength-meter {
            height: 5px;
            width: 85%;
            margin: 10px auto;
            background: #f1f1f1;
            border-radius: 3px;
            overflow: hidden;
        }

        .password-strength-meter-fill {
            height: 100%;
            width: 0;
            transition: width 0.3s ease;
        }

        .strength-weak .password-strength-meter-fill {
            background-color: #dc3545;
            width: 33%;
        }

        .strength-medium .password-strength-meter-fill {
            background-color: #ffc107;
            width: 66%;
        }

        .strength-strong .password-strength-meter-fill {
            background-color: #28a745;
            width: 100%;
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

        <h3 style="color: #333;">Reset Password</h3>
        <p style="color: #555;">Enter a new password below.</p>

        <!-- Password Reset Form -->
        <form method="POST" action="" id="resetForm">
            <input type="password" name="new_password" id="new_password" placeholder="Enter new password" required>
            
            <!-- Password strength meter -->
            <div class="password-strength-meter">
                <div class="password-strength-meter-fill"></div>
            </div>
            
            <!-- Live validation messages -->
            <div class="validation-message">
                <div id="length-validation" class="validation-item invalid">At least 8 characters</div>
                <div id="letter-validation" class="validation-item invalid">At least one letter</div>
                <div id="number-validation" class="validation-item invalid">At least one number</div>
            </div>
            
            <button type="submit" id="submit-btn" disabled>Reset Password</button>
        </form>

        <!-- Display error message -->
        <?php if (!empty($error)): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <div class="form-footer">
            <p>Remember your password? <a href="doctorlog.php">Log In</a></p>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const passwordInput = document.getElementById('new_password');
            const lengthValidation = document.getElementById('length-validation');
            const letterValidation = document.getElementById('letter-validation');
            const numberValidation = document.getElementById('number-validation');
            const submitBtn = document.getElementById('submit-btn');
            const strengthMeter = document.querySelector('.password-strength-meter');
            const strengthFill = document.querySelector('.password-strength-meter-fill');
            
            // Function to check password strength and update UI
            function validatePassword() {
                const password = passwordInput.value;
                
                // Check length
                const hasLength = password.length >= 8;
                lengthValidation.className = hasLength 
                    ? 'validation-item valid' 
                    : 'validation-item invalid';
                
                // Check for letters
                const hasLetter = /[A-Za-z]/.test(password);
                letterValidation.className = hasLetter 
                    ? 'validation-item valid' 
                    : 'validation-item invalid';
                
                // Check for numbers
                const hasNumber = /\d/.test(password);
                numberValidation.className = hasNumber 
                    ? 'validation-item valid' 
                    : 'validation-item invalid';
                
                // All validation passed?
                const isValid = hasLength && hasLetter && hasNumber;
                
                // Update input field class for color highlighting
                if (password.length === 0) {
                    // Remove both classes if empty
                    passwordInput.classList.remove('valid-input', 'invalid-input');
                } else if (isValid) {
                    // Add green highlight for valid input
                    passwordInput.classList.add('valid-input');
                    passwordInput.classList.remove('invalid-input');
                } else {
                    // Add red highlight for invalid input
                    passwordInput.classList.add('invalid-input');
                    passwordInput.classList.remove('valid-input');
                }
                
                // Enable/disable submit button
                submitBtn.disabled = !isValid;
                
                // Update strength meter
                strengthMeter.className = 'password-strength-meter';
                if (password.length === 0) {
                    strengthFill.style.width = '0';
                } else if (isValid) {
                    // Check if password also has special characters for "strong"
                    if (/[^A-Za-z0-9]/.test(password) && password.length > 10) {
                        strengthMeter.classList.add('strength-strong');
                    } else {
                        strengthMeter.classList.add('strength-medium');
                    }
                } else {
                    strengthMeter.classList.add('strength-weak');
                }
            }
            
            // Check password on input
            passwordInput.addEventListener('input', validatePassword);
            
            // Initial validation
            validatePassword();
        });
    </script>

</body>
</html>