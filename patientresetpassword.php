<?php
session_start();

// Check if OTP and email exist in session
if (!isset($_SESSION['otp']) || !isset($_SESSION['email'])) {
    header('Location: patientforgotpassword.php'); // Redirect if OTP isn't verified
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
        $stmt = $conn->prepare("UPDATE patientreg SET password = ? WHERE email = ?");
        $stmt->bind_param("ss", $hashedPassword, $email);

        if ($stmt->execute()) {
            // Unset specific session variables instead of destroying the session
            unset($_SESSION['otp']);
            unset($_SESSION['email']);

            // Redirect to login page after password reset
            header("Location: patientlog.php");
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

        /* Validation styles */
        input.valid {
            border-color: #28a745;
            box-shadow: 0 0 8px rgba(40, 167, 69, 0.3);
            background-color: rgba(40, 167, 69, 0.05);
        }

        input.invalid {
            border-color: #dc3545;
            box-shadow: 0 0 8px rgba(220, 53, 69, 0.3);
            background-color: rgba(220, 53, 69, 0.05);
        }

        .validation-message {
            margin: 5px 0;
            font-size: 0.85rem;
            text-align: left;
            padding: 0 25px;
            transition: 0.3s;
        }

        .validation-item {
            margin: 3px 0;
            display: flex;
            align-items: center;
        }

        .validation-item.valid {
            color: #28a745;
        }

        .validation-item.invalid {
            color: #dc3545;
        }

        .validation-icon {
            margin-right: 5px;
            font-weight: bold;
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
            
            <div class="validation-message" id="validation-message">
                <div class="validation-item" id="length-validation">
                    <span class="validation-icon">•</span> At least 8 characters
                </div>
                <div class="validation-item" id="letter-validation">
                    <span class="validation-icon">•</span> At least one letter
                </div>
                <div class="validation-item" id="number-validation">
                    <span class="validation-icon">•</span> At least one number
                </div>
            </div>
            
            <button type="submit" id="resetButton" disabled>Reset Password</button>
        </form>

        <!-- Display error message -->
        <?php if (!empty($error)): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <div class="form-footer">
            <p>Remember your password? <a href="patientlog.php">Log In</a></p>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const passwordInput = document.getElementById('new_password');
            const resetButton = document.getElementById('resetButton');
            const lengthValidation = document.getElementById('length-validation');
            const letterValidation = document.getElementById('letter-validation');
            const numberValidation = document.getElementById('number-validation');
            
            // Function to validate password
            function validatePassword() {
                const password = passwordInput.value;
                
                // Check length
                const isLengthValid = password.length >= 8;
                updateValidationItem(lengthValidation, isLengthValid);
                
                // Check for letters
                const hasLetter = /[A-Za-z]/.test(password);
                updateValidationItem(letterValidation, hasLetter);
                
                // Check for numbers
                const hasNumber = /\d/.test(password);
                updateValidationItem(numberValidation, hasNumber);
                
                // Overall validation
                const isValid = isLengthValid && hasLetter && hasNumber;
                
                // Update input styling
                if (password.length > 0) {
                    passwordInput.classList.remove('valid', 'invalid');
                    passwordInput.classList.add(isValid ? 'valid' : 'invalid');
                } else {
                    passwordInput.classList.remove('valid', 'invalid');
                }
                
                // Enable/disable submit button
                resetButton.disabled = !isValid;
            }
            
            // Function to update validation items
            function updateValidationItem(element, isValid) {
                element.classList.remove('valid', 'invalid');
                element.classList.add(isValid ? 'valid' : 'invalid');
            }
            
            // Add event listener for input changes
            passwordInput.addEventListener('input', validatePassword);
            
            // Validate on form submission
            document.getElementById('resetForm').addEventListener('submit', function(event) {
                validatePassword();
                if (resetButton.disabled) {
                    event.preventDefault();
                }
            });
        });
    </script>
</body>
</html>