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
        $stmt = $conn->prepare("SELECT * FROM doctorreg WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 1) {
            $row = $result->fetch_assoc();

            if (password_verify($password, $row['password'])) {
                // Check if doctor account is disabled
                if (isset($row['action']) && $row['action'] === 'disabled') {
                    $_SESSION['id'] = $row['id'];
                    $_SESSION['email'] = $row['email'];
                    echo "<script>
                        window.location.href = 'doctor_disabled_notice.php';
                    </script>";
                    exit();
                }
                
                // Account is enabled, proceed with login
                $_SESSION['id'] = $row['id'];
                $_SESSION['email'] = $row['email'];
                header("Location: doctorprofile.php");
                exit();
            } else {
                $error = "Invalid password.";
            }
        } else {
            $error = "Invalid email.";
        }

        $stmt->close();
    } else {
        $error = "Please fill in all fields.";
    }
}

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
        .form-group {
            margin-bottom: 15px;
            position: relative;
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
        .valid-input {
            border-color: #28a745 !important;
            box-shadow: 0 0 5px rgba(40, 167, 69, 0.3) !important;
        }
        .invalid-input {
            border-color: #dc3545 !important;
            box-shadow: 0 0 5px rgba(220, 53, 69, 0.3) !important;
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
        <h3 style="color: #333;">Login To Enter As A Doctor</h3>

        <?php if ($error): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" action="" id="login-form">
            <div class="form-group">
                <input type="email" id="email" name="email" placeholder="Email Address" required>
            </div>
            
            <div class="form-group">
                <input type="password" id="password" name="password" placeholder="Password" required>
            </div>
            
            <button type="submit" id="submit-btn" disabled>Login</button>
        </form>
        
        <div class="forgot-password">
            <a href="doctorforgotpassword.php">Forgot password?</a>
        </div>
        <div class="form-footer">
            <p>Don't have an account? <a href="doctorreg.php">Sign Up</a></p>
        </div>
        <div class="back-to-home">
            <a href="home.php">
                &nbsp<img src="images/home_738822.png" alt="Home" class="home-icon">Home</a>
            <a href="role.php">
                &nbsp<img src="images/staff.png" alt="Home" class="home-icon">Role</a>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const emailInput = document.getElementById('email');
            const passwordInput = document.getElementById('password');
            const submitButton = document.getElementById('submit-btn');
            
            function validateEmail() {
                const email = emailInput.value.trim();
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                
                if (email === '' || !emailRegex.test(email)) {
                    emailInput.classList.remove('valid-input');
                    emailInput.classList.add('invalid-input');
                    return false;
                } else {
                    emailInput.classList.remove('invalid-input');
                    emailInput.classList.add('valid-input');
                    return true;
                }
            }
            
            function validatePassword() {
                const password = passwordInput.value.trim();
                
                if (password === '' || password.length < 6) {
                    passwordInput.classList.remove('valid-input');
                    passwordInput.classList.add('invalid-input');
                    return false;
                } else {
                    passwordInput.classList.remove('invalid-input');
                    passwordInput.classList.add('valid-input');
                    return true;
                }
            }
            
            function validateForm() {
                const isEmailValid = validateEmail();
                const isPasswordValid = validatePassword();
                
                submitButton.disabled = !(isEmailValid && isPasswordValid);
            }
            
            // Add event listeners
            emailInput.addEventListener('input', function() {
                validateEmail();
                validateForm();
            });
            
            passwordInput.addEventListener('input', function() {
                validatePassword();
                validateForm();
            });
            
            // Check form validity before submission
            document.getElementById('login-form').addEventListener('submit', function(event) {
                const isEmailValid = validateEmail();
                const isPasswordValid = validatePassword();
                
                if (!(isEmailValid && isPasswordValid)) {
                    event.preventDefault();
                }
            });
            
            // Initial validation on page load
            // Checking if input fields already have values (e.g., when browser autofills)
            if (emailInput.value.trim() !== '') validateEmail();
            if (passwordInput.value.trim() !== '') validatePassword();
            validateForm();
        });
    </script>
</body>
</html>