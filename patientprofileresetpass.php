<?php
session_start();

if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}

// Database connection
$db_host = "localhost";
$db_user = "root";
$db_pass = "";
$db_name = "bookmydoc";

$conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$message = '';
$messageClass = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $old_password = $_POST['old_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    $doctor_id = $_SESSION['id'];

    // Verify if all fields are filled
    if (empty($old_password) || empty($new_password) || empty($confirm_password)) {
        $message = "All fields are required";
        $messageClass = "error";
    } 
    // Verify if new password matches confirm password
    else if ($new_password !== $confirm_password) {
        $message = "New password and confirm password do not match";
        $messageClass = "error";
    }
    // Verify password length
    else if (strlen($new_password) < 8) {
        $message = "New password must be at least 8 characters long";
        $messageClass = "error";
    }
    else {
        // Get current password from database
        $stmt = $conn->prepare("SELECT password FROM patientreg WHERE id = ?");
        $stmt->bind_param("i", $doctor_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($row = $result->fetch_assoc()) {
            // Verify current password
            if (password_verify($old_password, $row['password'])) {
                // Check if new password is same as current password
                if (password_verify($new_password, $row['password'])) {
                    $message = "New password cannot be the same as current password";
                    $messageClass = "error";
                } else {
                    // Hash new password
                    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                    
                    // Update password in database
                    $update_stmt = $conn->prepare("UPDATE patientreg SET password = ? WHERE id = ?");
                    $update_stmt->bind_param("si", $hashed_password, $doctor_id);
                    
                    if ($update_stmt->execute()) {
                        $message = "Password updated successfully";
                        $messageClass = "success";
                    } else {
                        $message = "Error updating password";
                        $messageClass = "error";
                    }
                    $update_stmt->close();
                }
            } else {
                $message = "Current password is incorrect";
                $messageClass = "error";
            }
        } else {
            $message = "User not found";
            $messageClass = "error";
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #2c6bed;
            --secondary-color: #66b5ff;
            --success-color: #4CAF50;
            --error-color: #ff4444;
            --background-color: #f5f9ff;
            --card-background: #ffffff;
            --text-color: #333333;
            --border-radius: 20px;
            --box-shadow: 0 10px 30px rgba(44, 107, 237, 0.1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background: linear-gradient(135deg, var(--background-color) 0%, #e6f0ff 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .container {
            width: 100%;
            max-width: 500px;
            background: var(--card-background);
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            padding: 40px;
            position: relative;
            overflow: hidden;
        }

        .container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: linear-gradient(to right, var(--primary-color), var(--secondary-color));
        }

        h2 {
            color: var(--text-color);
            text-align: center;
            margin-bottom: 30px;
            font-size: 2rem;
            font-weight: 600;
            position: relative;
            padding-bottom: 15px;
        }

        h2::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 60px;
            height: 4px;
            background: linear-gradient(to right, var(--primary-color), var(--secondary-color));
            border-radius: 2px;
        }

        .form-group {
            margin-bottom: 25px;
            position: relative;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: var(--text-color);
            font-weight: 500;
            font-size: 0.95rem;
        }

        .input-group {
            position: relative;
            display: flex;
            align-items: center;
        }

        .input-group input {
            width: 100%;
            padding: 15px;
            border: 2px solid #e1e1e1;
            border-radius: 12px;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: #f8faff;
        }

        .input-group input:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(44, 107, 237, 0.1);
            outline: none;
        }

        .toggle-password {
            position: absolute;
            right: 15px;
            color: #666;
            cursor: pointer;
            font-size: 1.1rem;
            transition: color 0.3s ease;
        }

        .toggle-password:hover {
            color: var(--primary-color);
        }

        .message {
            padding: 15px;
            margin-bottom: 25px;
            border-radius: 12px;
            font-weight: 500;
            text-align: center;
            animation: slideIn 0.3s ease;
        }

        @keyframes slideIn {
            from {
                transform: translateY(-20px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .success {
            background: rgba(76, 175, 80, 0.1);
            color: var(--success-color);
            border: 1px solid rgba(76, 175, 80, 0.2);
        }

        .error {
            background: rgba(255, 68, 68, 0.1);
            color: var(--error-color);
            border: 1px solid rgba(255, 68, 68, 0.2);
        }

        .btn {
            width: 100%;
            padding: 15px;
            background: linear-gradient(to right, var(--primary-color), var(--secondary-color));
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-top: 20px;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(44, 107, 237, 0.3);
        }

        .btn:active {
            transform: translateY(0);
        }

        .forgot-password {
            text-align: right;
            margin-top: 15px;
        }

        .forgot-password a {
            color: var(--primary-color);
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 500;
            transition: color 0.3s ease;
        }

        .forgot-password a:hover {
            color: var(--secondary-color);
            text-decoration: underline;
        }

        @media (max-width: 576px) {
            .container {
                padding: 30px 20px;
            }

            h2 {
                font-size: 1.75rem;
            }

            .input-group input {
                padding: 12px;
            }

            .btn {
                padding: 12px;
            }
        }
        .back-button {
            position: absolute;
            top: 20px;
            left: 20px;
            display: flex;
            align-items: center;
            gap: 5px;
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
            font-size: 0.9rem;
            transition: all 0.3s ease;
            padding: 8px 12px;
            border-radius: 8px;
            background: rgba(44, 107, 237, 0.1);
        }

        .back-button:hover {
            background: rgba(44, 107, 237, 0.2);
            transform: translateX(-2px);
        }

        .back-button i {
            font-size: 0.8rem;
        }

    </style>
</head>
<body>
    <div class="container">

    <a href="patientupdatebutton.php" class="back-button">
            <i class="fas fa-chevron-left"></i>
            Back to Profile
        </a>
       <br>
        <h2>Reset Password</h2>
        
        <?php if ($message): ?>
            <div class="message <?php echo $messageClass; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <div class="form-group">
                <label for="old_password">Current Password</label>
                <div class="input-group">
                    <input type="password" id="old_password" name="old_password" required>
                    <i class="toggle-password fas fa-eye" onclick="togglePassword('old_password')"></i>
                </div>
            </div>

            <div class="form-group">
                <label for="new_password">New Password</label>
                <div class="input-group">
                    <input type="password" id="new_password" name="new_password" required>
                    <i class="toggle-password fas fa-eye" onclick="togglePassword('new_password')"></i>
                </div>
            </div>

            <div class="form-group">
                <label for="confirm_password">Confirm New Password</label>
                <div class="input-group">
                    <input type="password" id="confirm_password" name="confirm_password" required>
                    <i class="toggle-password fas fa-eye" onclick="togglePassword('confirm_password')"></i>
                </div>
            </div>

            <div class="forgot-password">
                <a href="doctorforgotpassword.php">Forgot Password?</a>
            </div>

            <button type="submit" class="btn">Update Password</button>
        </form>
    </div>

    <script>
        function togglePassword(inputId) {
            const input = document.getElementById(inputId);
            const icon = input.nextElementSibling;
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }
    </script>
</body>
</html>