<?php
session_start();

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

// Initialize variables
$error = "";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (!empty($email) && !empty($password)) {
        // Prepare SQL query to check if the email exists
        $stmt = $conn->prepare("SELECT * FROM admin WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 1) {
            // Fetch user data
            $row = $result->fetch_assoc();

            // Verify the password (check the hash if it's hashed in the database)
            if (password_verify($password, $row['password'])) {
                // If credentials are correct, store session variables
                $_SESSION['id'] = $row['id'];
                $_SESSION['email'] = $row['email'];

                // Redirect to home page
                header("Location: admindashboard.php");
                exit();
            } else {
                // Invalid password
                $error = "Invalid email or password.";
            }
        } else {
            // Invalid email
            $error = "Invalid email or password.";
        }

        // Close the statement
        $stmt->close();
    } else {
        // Empty fields
        $error = "Please fill in all fields.";
    }
}

// Close the connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - BookMyDoc</title>
    <style>
        body {
            background-image: url('images/bg2.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
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
            margin-bottom: 15px;
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
        .input-group {
            position: relative;
            margin: 20px auto;
            width: 90%;
        }
        input {
            display: block;
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
            outline: none;
        }
        input.valid {
            border-color: #28a745;
            background-color: rgba(40, 167, 69, 0.05);
        }
        input.invalid {
            border-color: #dc3545;
            background-color: rgba(220, 53, 69, 0.05);
        }
        .validation-icon {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            width: 16px;
            height: 16px;
            display: none;
        }
        .valid-icon {
            color: #28a745;
        }
        .invalid-icon {
            color: #dc3545;
        }
        .error {
            color: red;
            font-size: 0.9rem;
            margin-bottom: 10px;
            display: none;
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
        .forgot-password {
            margin-top: 10px;
            font-size: 0.9rem;
        }
        .forgot-password a {
            color: #0077b6;
            text-decoration: none;
        }
        .forgot-password a:hover {
            text-decoration: underline;
        }
        .back-to-home a {
            display: inline-flex;
            align-items: center;
            padding: 15px 9px;
            font-size: 1rem;
            font-weight: bold;
            color: white;
            background: linear-gradient(135deg, #0077b6, #1e90ff);
            border-radius: 30px;
            text-decoration: none;
            transition: all 0.3s ease-in-out;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            margin: 0 5px;
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
        <h3 style="color: #333;">Welcome Back Admin!</h3>

        <?php if ($error): ?>
            <div class="error" style="display: block;"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" action="" id="loginForm">
            <div class="input-group">
                <input type="email" id="email" name="email" placeholder="Email Address" required>
                <svg class="validation-icon valid-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                </svg>
                <svg class="validation-icon invalid-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                </svg>
            </div>
            <div class="input-group">
                <input type="password" id="password" name="password" placeholder="Password" required>
                <svg class="validation-icon valid-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                </svg>
                <svg class="validation-icon invalid-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                </svg>
            </div>
            <button type="submit" id="submitBtn">Login</button>
        </form>
        <br>
        <div class="back-to-home">
            <a href="home.php">
                &nbsp;<img src="images/home_738822.png" alt="Home" class="home-icon">Home</a>
            <a href="role.php">
                &nbsp;<img src="images/staff.png" alt="Home" class="home-icon">Role</a>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const emailInput = document.getElementById('email');
            const passwordInput = document.getElementById('password');
            const submitBtn = document.getElementById('submitBtn');
            const form = document.getElementById('loginForm');
            
            // Email validation function
            function validateEmail() {
                const email = emailInput.value.trim();
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                
                if (email === '') {
                    setInvalid(emailInput);
                    return false;
                } else if (!emailRegex.test(email)) {
                    setInvalid(emailInput);
                    return false;
                } else {
                    setValid(emailInput);
                    return true;
                }
            }
            
            // Password validation function
            function validatePassword() {
                const password = passwordInput.value.trim();
                
                if (password === '') {
                    setInvalid(passwordInput);
                    return false;
                } else if (password.length < 6) {
                    setInvalid(passwordInput);
                    return false;
                } else {
                    setValid(passwordInput);
                    return true;
                }
            }
            
            // Set input as valid
            function setValid(input) {
                input.classList.remove('invalid');
                input.classList.add('valid');
                const parent = input.parentElement;
                parent.querySelector('.valid-icon').style.display = 'block';
                parent.querySelector('.invalid-icon').style.display = 'none';
            }
            
            // Set input as invalid
            function setInvalid(input) {
                input.classList.remove('valid');
                input.classList.add('invalid');
                const parent = input.parentElement;
                parent.querySelector('.valid-icon').style.display = 'none';
                parent.querySelector('.invalid-icon').style.display = 'block';
            }
            
            // Reset validation state
            function resetValidation(input) {
                input.classList.remove('valid', 'invalid');
                const parent = input.parentElement;
                parent.querySelector('.valid-icon').style.display = 'none';
                parent.querySelector('.invalid-icon').style.display = 'none';
            }
            
            // Check if form is valid and update submit button
            function updateSubmitButton() {
                const isEmailValid = validateEmail();
                const isPasswordValid = validatePassword();
                
                if (isEmailValid && isPasswordValid) {
                    submitBtn.disabled = false;
                } else {
                    submitBtn.disabled = true;
                }
            }
            
            // Event listeners for real-time validation
            emailInput.addEventListener('input', function() {
                if (emailInput.value.trim() === '') {
                    resetValidation(emailInput);
                } else {
                    validateEmail();
                }
                updateSubmitButton();
            });
            
            passwordInput.addEventListener('input', function() {
                if (passwordInput.value.trim() === '') {
                    resetValidation(passwordInput);
                } else {
                    validatePassword();
                }
                updateSubmitButton();
            });
            
            // Validate on blur (when user clicks out of field)
            emailInput.addEventListener('blur', validateEmail);
            passwordInput.addEventListener('blur', validatePassword);
            
            // Form submission validation
            form.addEventListener('submit', function(event) {
                const isEmailValid = validateEmail();
                const isPasswordValid = validatePassword();
                
                if (!isEmailValid || !isPasswordValid) {
                    event.preventDefault();
                }
            });
            
            // Initial button state
            updateSubmitButton();
        });
    </script>
</body>
</html>