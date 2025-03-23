<?php
session_start();
include('patientheader2.php');

if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "bookmydoc";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = '';
$messageType = '';
$patient_id = $_SESSION['id'];

// Fetch current contact info
$stmt = $conn->prepare("SELECT email, phone FROM patientreg WHERE id = ?");
$stmt->bind_param("i", $patient_id);
$stmt->execute();
$result = $stmt->get_result();
$patient = $result->fetch_assoc();
$stmt->close();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    
    // Validation
    $errors = [];
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format";
    }
    
    if (!preg_match("/^[0-9]{10}$/", $phone)) {
        $errors[] = "Phone number must be 10 digits";
    }
    
    if (empty($errors)) {
        // Check if email already exists for another user
        $stmt = $conn->prepare("SELECT id FROM patientreg WHERE email = ? AND id != ?");
        $stmt->bind_param("si", $email, $patient_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $message = "This email is already registered with another account.";
            $messageType = "error";
        } else {
            // Update contact info
            $stmt = $conn->prepare("UPDATE patientreg SET email = ?, phone = ? WHERE id = ?");
            $stmt->bind_param("ssi", $email, $phone, $patient_id);
            
            if ($stmt->execute()) {
                $message = "Contact information updated successfully!";
                $messageType = "success";
                
                // Update the displayed information
                $patient['email'] = $email;
                $patient['phone'] = $phone;
            } else {
                $message = "Error updating information. Please try again.";
                $messageType = "error";
            }
        }
        $stmt->close();
    } else {
        $message = implode("<br>", $errors);
        $messageType = "error";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Contact Information</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #2c6bed;
            --secondary-color: #66b5ff;
            --accent-color: #ff6b6b;
            --background-color: #f5f9ff;
            --card-background: #ffffff;
            --text-color: #333333;
            --text-muted: #6c757d;
            --border-radius: 20px;
            --box-shadow: 0 10px 30px rgba(44, 107, 237, 0.1);
        }
        
        .container2 {
            max-width: 600px;
            margin: 40px auto;
            padding: 0 20px;
        }
        
        .contact-card {
            background: var(--card-background);
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            padding: 40px;
        }
        
        .form-group {
            margin-bottom: 25px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: var(--text-color);
            font-weight: 600;
        }
        
        .form-control {
            width: 100%;
            padding: 12px;
            border: 2px solid #e1e1e1;
            border-radius: 10px;
            font-size: 16px;
            transition: border-color 0.3s ease;
        }
        
        .form-control:focus {
            border-color: var(--primary-color);
            outline: none;
        }
        
        .submit-btn {
            background: linear-gradient(to right, var(--primary-color), var(--secondary-color));
            color: white;
            border: none;
            padding: 15px 40px;
            border-radius: 30px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
            width: 100%;
        }
        
        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(44, 107, 237, 0.3);
        }
        
        .message {
            margin: 20px 0;
            padding: 15px;
            border-radius: 10px;
        }
        
        .message.success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .message.error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .back-btn {
            display: inline-block;
            text-decoration: none;
            color: var(--primary-color);
            margin-top: 20px;
            font-weight: 600;
            transition: color 0.3s ease;
        }
        
        .back-btn:hover {
            color: var(--secondary-color);
        }
    </style>
</head>
<body>
    <div class="container2">
        <div class="contact-card">
            <h2>Update Contact Information</h2>
            
            <?php if (!empty($message)): ?>
                <div class="message <?php echo $messageType; ?>">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>
            
            <form method="POST">
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" 
                           id="email" 
                           name="email" 
                           class="form-control" 
                           value="<?php echo htmlspecialchars($patient['email']); ?>" 
                           required>
                </div>
                
                <div class="form-group">
                    <label for="phone">Phone Number</label>
                    <input type="tel" 
                           id="phone" 
                           name="phone" 
                           class="form-control" 
                           value="<?php echo htmlspecialchars($patient['phone']); ?>" 
                           pattern="[0-9]{10}" 
                           title="Please enter a valid 10-digit phone number"
                           required>
                </div>
                
                <button type="submit" class="submit-btn">
                    <i class="fas fa-save"></i> Update Contact Information
                </button>
            </form>
            
            <a href="patientupdatebutton.php" class="back-btn">
                <i class="fas fa-arrow-left"></i> Back to Profile
            </a>
        </div>
    </div>

    <script>
        // Add phone number formatting
        document.getElementById('phone').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length > 10) {
                value = value.slice(0, 10);
            }
            e.target.value = value;
        });
    </script>
</body>
</html>