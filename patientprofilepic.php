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

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["profile_pic"])) {
    $patient_id = $_SESSION['id'];
    $file = $_FILES["profile_pic"];
    
    // Validate file
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
    $maxSize = 5 * 1024 * 1024; // 5MB
    
    if (!in_array($file['type'], $allowedTypes)) {
        $message = "Only JPG, PNG and GIF files are allowed.";
        $messageType = "error";
    } elseif ($file['size'] > $maxSize) {
        $message = "File size must be less than 5MB.";
        $messageType = "error";
    } elseif ($file['error'] !== UPLOAD_ERR_OK) {
        $message = "Error uploading file. Please try again.";
        $messageType = "error";
    } else {
        // Create uploads directory if it doesn't exist
        $uploadDir = 'uploads/profile_pics/';
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        
        // Generate unique filename
        $fileExtension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $newFilename = $patient_id . '_' . time() . '.' . $fileExtension;
        $targetPath = $uploadDir . $newFilename;
        
        if (move_uploaded_file($file['tmp_name'], $targetPath)) {
            // Update database with new profile picture path
            $stmt = $conn->prepare("UPDATE patientreg SET profile_pic = ? WHERE id = ?");
            $stmt->bind_param("si", $targetPath, $patient_id);
            
            if ($stmt->execute()) {
                $message = "Profile picture updated successfully!";
                $messageType = "success";
            } else {
                $message = "Error updating database. Please try again.";
                $messageType = "error";
            }
            $stmt->close();
        } else {
            $message = "Error saving file. Please try again.";
            $messageType = "error";
        }
    }
}

// Get current profile picture
$patient_id = $_SESSION['id'];
$stmt = $conn->prepare("SELECT profile_pic FROM patientreg WHERE id = ?");
$stmt->bind_param("i", $patient_id);
$stmt->execute();
$result = $stmt->get_result();
$patient = $result->fetch_assoc();
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Profile Picture</title>
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
        
        .upload-card {
            background: var(--card-background);
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            padding: 40px;
            text-align: center;
        }
        
        .current-photo {
            width: 200px;
            height: 200px;
            border-radius: 50%;
            margin: 0 auto 30px;
            border: 5px solid var(--primary-color);
            object-fit: cover;
            background-color: #f0f0f0;
        }
        
        .upload-form {
            max-width: 400px;
            margin: 0 auto;
        }
        
        .file-input-container {
            position: relative;
            margin-bottom: 30px;
        }
        
        .file-input {
            display: none;
        }
        
        .file-input-label {
            background: linear-gradient(to right, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 15px 30px;
            border-radius: 30px;
            cursor: pointer;
            display: inline-block;
            transition: all 0.3s ease;
            font-weight: 600;
        }
        
        .file-input-label:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(44, 107, 237, 0.3);
        }
        
        .submit-btn {
            background: linear-gradient(to right, var(--accent-color), #ff8e8e);
            color: white;
            border: none;
            padding: 15px 40px;
            border-radius: 30px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
            margin-top: 20px;
        }
        
        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255, 107, 107, 0.3);
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
        
        .selected-file-name {
            margin-top: 10px;
            color: var(--text-muted);
            font-size: 0.9rem;
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
        <div class="upload-card">
            <h2>Change Profile Picture</h2>
            
            <?php if (!empty($message)): ?>
                <div class="message <?php echo $messageType; ?>">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>
            
            <img src="<?php echo !empty($patient['profile_pic']) ? htmlspecialchars($patient['profile_pic']) : 'default-profile.jpg'; ?>" 
                 onerror="this.onerror=null; this.src='images/profilepicdoct.jpg';"
                 class="current-photo"
                 onerror="this.src='default-profile.jpg'">
            
            <form class="upload-form" method="POST" enctype="multipart/form-data">
                <div class="file-input-container">
                    <input type="file" name="profile_pic" id="profile_pic" class="file-input" accept="image/*" required>
                    <label for="profile_pic" class="file-input-label">
                        <i class="fas fa-cloud-upload-alt"></i> Choose New Photo
                    </label>
                    <div class="selected-file-name"></div>
                </div>
                
                <button type="submit" class="submit-btn">
                    <i class="fas fa-check"></i> Update Profile Picture
                </button>
            </form>
            
            <a href="patientupdatebutton.php" class="back-btn">
                <i class="fas fa-arrow-left"></i> Back to Profile
            </a>
        </div>
    </div>

    <script>
        document.getElementById('profile_pic').addEventListener('change', function(e) {
            const fileName = e.target.files[0]?.name || 'No file selected';
            document.querySelector('.selected-file-name').textContent = fileName;
        });
    </script>
</body>
</html>