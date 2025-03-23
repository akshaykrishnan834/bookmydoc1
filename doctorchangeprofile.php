<?php
session_start();

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

$doctor_id = $_SESSION['id'];
$message = '';
$messageClass = '';

// Handle photo upload
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_FILES["profile_photo"]) && $_FILES["profile_photo"]["error"] == 0) {
        $allowed = ["jpg" => "image/jpg", "jpeg" => "image/jpeg", "png" => "image/png"];
        $filename = $_FILES["profile_photo"]["name"];
        $filetype = $_FILES["profile_photo"]["type"];
        $filesize = $_FILES["profile_photo"]["size"];

        // Verify file extension
        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        if (!array_key_exists($ext, $allowed)) {
            $message = "Error: Please select a valid file format (JPG, JPEG, PNG).";
            $messageClass = "error";
        }

        // Verify file size - 5MB maximum
        $maxsize = 5 * 1024 * 1024;
        if ($filesize > $maxsize) {
            $message = "Error: File size is larger than 5MB.";
            $messageClass = "error";
        }

        // Verify MYME type
        if (in_array($filetype, $allowed)) {
            // Create upload directory if it doesn't exist
            if (!file_exists("uploads")) {
                mkdir("uploads", 0777, true);
            }

            // Generate unique filename
            $new_filename = uniqid() . "." . $ext;
            $uploadpath = "uploads/" . $new_filename;

            if (move_uploaded_file($_FILES["profile_photo"]["tmp_name"], $uploadpath)) {
                // Update database with new photo path
                $stmt = $conn->prepare("UPDATE doctorreg SET profile_photo = ? WHERE id = ?");
                $stmt->bind_param("si", $uploadpath, $doctor_id);
                
                if ($stmt->execute()) {
                    $message = "Profile photo updated successfully!";
                    $messageClass = "success";
                } else {
                    $message = "Error updating profile photo in database.";
                    $messageClass = "error";
                }
                $stmt->close();
            } else {
                $message = "Error uploading file.";
                $messageClass = "error";
            }
        } else {
            $message = "Error: There was a problem uploading your file. Please try again.";
            $messageClass = "error";
        }
    } else {
        $message = "Error: " . $_FILES["profile_photo"]["error"];
        $messageClass = "error";
    }
}

// Get current profile photo
$stmt = $conn->prepare("SELECT profile_photo FROM doctorreg WHERE id = ?");
$stmt->bind_param("i", $doctor_id);
$stmt->execute();
$result = $stmt->get_result();
$doctor = $result->fetch_assoc();
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Profile Photo</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #0077b6;
            --secondary-color: #00b4d8;
            --text-color: #2d3436;
            --text-light: #636e72;
            --bg-color: #f5f6fa;
            --white: #ffffff;
            --border-radius: 12px;
            --shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
        }

        body {
            background: var(--bg-color);
            min-height: 100vh;
            padding: 2rem;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            background: var(--white);
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            padding: 2rem;
            position: relative;
        }

        .back-button {
            position: absolute;
            top: 1.5rem;
            left: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            background: rgba(0, 119, 182, 0.1);
        }

        .back-button:hover {
            background: rgba(0, 119, 182, 0.2);
            transform: translateX(-2px);
        }

        h2 {
            text-align: center;
            color: var(--text-color);
            margin-bottom: 2rem;
            padding-top: 1rem;
        }

        .current-photo {
            width: 200px;
            height: 200px;
            border-radius: 50%;
            margin: 0 auto 2rem;
            overflow: hidden;
            border: 4px solid var(--primary-color);
            box-shadow: var(--shadow);
        }

        .current-photo img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .upload-container {
            border: 2px dashed var(--primary-color);
            border-radius: var(--border-radius);
            padding: 2rem;
            text-align: center;
            margin-bottom: 2rem;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .upload-container:hover {
            background: rgba(0, 119, 182, 0.05);
        }

        .upload-container i {
            font-size: 3rem;
            color: var(--primary-color);
            margin-bottom: 1rem;
        }

        .upload-text {
            color: var(--text-color);
            margin-bottom: 0.5rem;
        }

        .upload-hint {
            color: var(--text-light);
            font-size: 0.9rem;
        }

        #profile_photo {
            display: none;
        }

        .message {
            padding: 1rem;
            margin-bottom: 1.5rem;
            border-radius: var(--border-radius);
            text-align: center;
        }

        .success {
            background: rgba(46, 213, 115, 0.1);
            color: #2ed573;
            border: 1px solid rgba(46, 213, 115, 0.2);
        }

        .error {
            background: rgba(255, 71, 87, 0.1);
            color: #ff4757;
            border: 1px solid rgba(255, 71, 87, 0.2);
        }

        .btn {
            display: block;
            width: 100%;
            padding: 1rem;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: var(--white);
            border: none;
            border-radius: var(--border-radius);
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 119, 182, 0.2);
        }

        .btn:disabled {
            opacity: 0.7;
            cursor: not-allowed;
            transform: none;
        }

        @media (max-width: 768px) {
            body {
                padding: 1rem;
            }

            .container {
                padding: 1.5rem;
            }

            .current-photo {
                width: 150px;
                height: 150px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="updatedoctor.php" class="back-button">
            <i class="fas fa-chevron-left"></i>
            Back to Profile
        </a>

        <br>
        <br>

        <h2>Change Profile Photo</h2>

        <?php if ($message): ?>
            <div class="message <?php echo $messageClass; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <div class="current-photo">
            <img src="<?php echo htmlspecialchars($doctor['profile_photo']); ?>" alt="Current profile photo" id="preview-image">
        </div>

        <form method="POST" enctype="multipart/form-data" id="upload-form">
            <label for="profile_photo" class="upload-container">
                <i class="fas fa-cloud-upload-alt"></i>
                <div class="upload-text">Click to upload new photo</div>
                <div class="upload-hint">Supported formats: JPG, JPEG, PNG (Max 5MB)</div>
            </label>
            <input type="file" id="profile_photo" name="profile_photo" accept="image/jpeg,image/jpg,image/png" required>
            <button type="submit" class="btn" id="submit-btn" disabled>Update Profile Photo</button>
        </form>
    </div>

    <script>
        const fileInput = document.getElementById('profile_photo');
        const submitBtn = document.getElementById('submit-btn');
        const previewImage = document.getElementById('preview-image');
        const uploadForm = document.getElementById('upload-form');

        fileInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                // Enable submit button
                submitBtn.disabled = false;

                // Show image preview
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImage.src = e.target.result;
                }
                reader.readAsDataURL(file);
            } else {
                submitBtn.disabled = true;
            }
        });

        uploadForm.addEventListener('submit', function() {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Uploading...';
        });
    </script>
</body>
</html>