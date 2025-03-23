<?php
session_start();
include('patientheader2.php');
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

// Get the logged-in patient's ID from session
if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}
$patient_id = $_SESSION['id'];

// Fetch current patient details
$sql = "SELECT name, email, phone, profile_pic, age, dob, gender FROM patientreg WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $patient_id);
$stmt->execute();
$result = $stmt->get_result();
$patient = $result->fetch_assoc();
$stmt->close();

// Handle form submission
$update_msg = "";
$update_status = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $age = $_POST['age'];
    $dob = $_POST['dob'];
    $gender = $_POST['gender'];
    $new_password = $_POST['password'];
    $current_password = $_POST['current_password'];
    $profile_pic = $patient['profile_pic'];

    // Verify current password
    $password_verified = false;
    if (!empty($current_password)) {
        // Get current password hash from database
        $verify_sql = "SELECT password FROM patientreg WHERE id = ?";
        $verify_stmt = $conn->prepare($verify_sql);
        $verify_stmt->bind_param("i", $patient_id);
        $verify_stmt->execute();
        $verify_result = $verify_stmt->get_result();
        $password_data = $verify_result->fetch_assoc();
        $verify_stmt->close();
        
        // Verify password
        if ($password_data && password_verify($current_password, $password_data['password'])) {
            $password_verified = true;
        } else {
            $update_msg = "Incorrect current password. Profile not updated.";
            $update_status = "error";
        }
    } else {
        $update_msg = "Current password is required to update profile.";
        $update_status = "error";
    }

    // Continue with update only if password verified
    if ($password_verified) {
        // Handle profile picture upload
        if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] == 0) {
            // ... [existing file upload code] ...
        }

        // Update the profile details
        if ($update_status !== "error") {
            if (!empty($new_password)) {
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                $sql = "UPDATE patientreg SET name = ?, email = ?, phone = ?, age = ?, dob = ?, gender = ?, password = ?, profile_pic = ? WHERE id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ssssssssi", $name, $email, $phone, $age, $dob, $gender, $hashed_password, $profile_pic, $patient_id);
            } else {
                $sql = "UPDATE patientreg SET name = ?, email = ?, phone = ?, age = ?, dob = ?, gender = ?, profile_pic = ? WHERE id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("sssssssi", $name, $email, $phone, $age, $dob, $gender, $profile_pic, $patient_id);
            }

            if ($stmt->execute()) {
                $update_msg = "Profile updated successfully!";
                $update_status = "success";
                $patient['name'] = $name;
                $patient['email'] = $email;
                $patient['phone'] = $phone;
                $patient['age'] = $age;
                $patient['dob'] = $dob;
                $patient['gender'] = $gender;
                $patient['profile_pic'] = $profile_pic;
            } else {
                $update_msg = "Failed to update profile. Please try again.";
                $update_status = "error";
            }
            $stmt->close();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Profile</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #3a86ff;
            --primary-light: #83bcff;
            --primary-dark: #1a56cc;
            --secondary-color: #ff6b6b;
            --success-color: #4CAF50;
            --error-color: #f44336;
            --background: #f6f9fc;
            --card-bg: #ffffff;
            --text-primary: #2d3748;
            --text-secondary: #718096;
            --border-radius: 16px;
            --shadow-sm: 0 2px 10px rgba(0, 0, 0, 0.05);
            --shadow-md: 0 5px 20px rgba(0, 0, 0, 0.08);
            --shadow-lg: 0 10px 30px rgba(0, 0, 0, 0.12);
        }
        
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: var(--background);
            color: var(--text-primary);
            line-height: 1.6;
        }
        
        .container2 {
            max-width: 900px;
            margin: 40px auto;
            padding: 0 20px;
        }
        
        .form-card {
            background: var(--card-bg);
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-lg);
            overflow: hidden;
            position: relative;
        }
        
        .form-header {
            padding: 40px;
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
            color: white;
            text-align: center;
            position: relative;
        }
        
        .form-title {
            font-size: 2.2rem;
            margin-bottom: 10px;
            font-weight: 700;
            letter-spacing: -0.5px;
        }
        
        .form-subtitle {
            font-size: 1.1rem;
            opacity: 0.85;
            max-width: 80%;
            margin: 0 auto;
        }
        
        .profile-img-container {
            position: relative;
            margin: 20px auto 0;
            width: 130px;
            height: 130px;
        }
        
        .profile-img {
            width: 130px;
            height: 130px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid rgba(255, 255, 255, 0.8);
            box-shadow: var(--shadow-md);
        }
        
        .placeholder-avatar {
            width: 130px;
            height: 130px;
            border-radius: 50%;
            background: linear-gradient(180deg, #e2e8f0, #cbd5e0);
            border: 4px solid rgba(255, 255, 255, 0.8);
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .placeholder-avatar i {
            font-size: 60px;
            color: rgba(255, 255, 255, 0.9);
        }
        
        .form-content {
            padding: 40px;
        }
        
        .message {
            padding: 16px;
            border-radius: 12px;
            margin-bottom: 30px;
            font-weight: 500;
            display: flex;
            align-items: center;
            animation: fadeIn 0.5s ease-out;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .message i {
            margin-right: 12px;
            font-size: 1.3rem;
        }
        
        .message.success {
            background-color: rgba(76, 175, 80, 0.1);
            color: var(--success-color);
            border-left: 4px solid var(--success-color);
        }
        
        .message.error {
            background-color: rgba(244, 67, 54, 0.1);
            color: var(--error-color);
            border-left: 4px solid var(--error-color);
        }
        
        .form-group {
            margin-bottom: 28px;
            position: relative;
        }
        
        label {
            display: block;
            margin-bottom: 10px;
            font-weight: 600;
            color: var(--text-primary);
            font-size: 0.95rem;
        }
        
        input[type="text"],
        input[type="email"],
        input[type="password"],
        input[type="number"],
        input[type="date"],
        select {
            width: 100%;
            padding: 15px 20px;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            font-size: 1rem;
            transition: all 0.3s ease;
            color: var(--text-primary);
            background-color: #f8fafc;
        }
        
        input[type="text"]:focus,
        input[type="email"]:focus,
        input[type="password"]:focus,
        input[type="number"]:focus,
        input[type="date"]:focus,
        select:focus {
            border-color: var(--primary-color);
            background-color: white;
            box-shadow: 0 0 0 3px rgba(58, 134, 255, 0.15);
            outline: none;
        }
        
        .input-icon-wrapper {
            position: relative;
        }
        
        .input-icon {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-secondary);
            font-size: 1.1rem;
        }
        
        .input-with-icon {
            padding-left: 48px;
        }

        .form-row {
            display: flex;
            gap: 20px;
            margin-bottom: 28px;
        }
        
        .form-col {
            flex: 1;
            position: relative;
        }
        
        .file-upload-container {
            position: relative;
            margin-bottom: 30px;
        }
        
        .file-upload-label {
            display: block;
            text-align: center;
            padding: 18px 20px;
            border: 2px dashed #e2e8f0;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.3s ease;
            background-color: #f8fafc;
        }
        
        .file-upload-label:hover {
            border-color: var(--primary-color);
            background-color: rgba(58, 134, 255, 0.05);
        }
        
        .file-upload-label i {
            font-size: 1.8rem;
            color: var(--primary-color);
            margin-bottom: 8px;
            display: block;
        }
        
        .file-upload-label span {
            display: block;
            font-weight: 500;
            margin-bottom: 5px;
        }
        
        .file-upload-label small {
            color: var(--text-secondary);
        }
        
        .file-upload-input {
            position: absolute;
            width: 1px;
            height: 1px;
            padding: 0;
            margin: -1px;
            overflow: hidden;
            clip: rect(0, 0, 0, 0);
            border: 0;
        }
        
        .file-name-display {
            margin-top: 10px;
            font-size: 0.9rem;
            color: var(--text-secondary);
            text-align: center;
            display: none;
        }
        
        .buttons-container {
            display: flex;
            gap: 20px;
            margin-top: 40px;
        }
        
        .btn {
            padding: 15px 30px;
            border-radius: 30px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: var(--shadow-sm);
            flex: 1;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
        }
        
        .btn i {
            margin-right: 10px;
        }
        
        .btn-primary {
            background: linear-gradient(to right, var(--primary-color), var(--primary-dark));
            color: white;
            border: none;
        }
        
        .btn-primary:hover {
            box-shadow: 0 6px 20px rgba(58, 134, 255, 0.3);
            transform: translateY(-2px);
        }
        
        .btn-secondary {
            background: white;
            color: var(--primary-color);
            border: 2px solid var(--primary-color);
        }
        
        .btn-secondary:hover {
            background-color: rgba(58, 134, 255, 0.05);
            transform: translateY(-2px);
        }
        
        .password-toggle {
            position: absolute;
            right: 16px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: var(--text-secondary);
            z-index: 10;
        }
        
        /* Responsive styles */
        @media (max-width: 768px) {
            .container {
                padding: 0 15px;
                margin: 20px auto;
            }
            
            .form-header {
                padding: 30px 20px;
            }
            
            .form-title {
                font-size: 1.8rem;
            }
            
            .profile-img-container {
                width: 100px;
                height: 100px;
            }
            
            .profile-img, .placeholder-avatar {
                width: 100px;
                height: 100px;
            }
            
            .placeholder-avatar i {
                font-size: 45px;
            }
            
            .form-content {
                padding: 25px 20px;
            }
            
            .form-row {
                flex-direction: column;
                gap: 0;
            }
            
            .buttons-container {
                flex-direction: column-reverse;
                gap: 15px;
            }
            
            .btn {
                width: 100%;
                padding: 14px 20px;
            }
        }
        
        /* Decorative elements */
        .form-header:before {
            content: "";
            position: absolute;
            bottom: -20px;
            left: 0;
            width: 100%;
            height: 20px;
            background: linear-gradient(to right bottom, transparent 49%, var(--card-bg) 50%);
            z-index: 1;
        }
        
        .background-circles {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            z-index: 0;
        }
        
        .circle {
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.1);
        }
        
        .circle-1 {
            width: 100px;
            height: 100px;
            top: -30px;
            left: 10%;
        }
        
        .circle-2 {
            width: 150px;
            height: 150px;
            bottom: -50px;
            right: 5%;
        }
        
        .circle-3 {
            width: 60px;
            height: 60px;
            top: 40%;
            right: 20%;
        }
        
        /* Animation for file upload */
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }
        
        .pulse {
            animation: pulse 1.5s infinite;
        }
    </style>
</head>
<body>
    <div class="container2">
        <div class="form-card">
            <div class="form-header">
                <div class="background-circles">
                    <div class="circle circle-1"></div>
                    <div class="circle circle-2"></div>
                    <div class="circle circle-3"></div>
                </div>
                <h2 class="form-title">Update Your Profile</h2>
                <p class="form-subtitle">Personalize your account information</p>
                <div class="profile-img-container">
                    <?php if (!empty($patient['profile_pic']) && file_exists($patient['profile_pic'])): ?>
                        <img src="<?php echo htmlspecialchars($patient['profile_pic']); ?>" alt="Profile Picture" class="profile-img" id="profile-preview">
                    <?php else: ?>
                        <div class="placeholder-avatar" id="profile-placeholder">
                            <i class="fas fa-user"></i>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="form-content">
                <?php if ($update_msg): ?>
                    <div class="message <?php echo $update_status == 'success' ? 'success' : 'error'; ?>">
                        <i class="fas fa-<?php echo $update_status == 'success' ? 'check-circle' : 'exclamation-circle'; ?>"></i>
                        <?php echo $update_msg; ?>
                    </div>
                <?php endif; ?>

                <form method="POST" id="profileForm" enctype="multipart/form-data" onsubmit="return validateForm()">
                    <div class="form-group">
                        <label for="name">Full Name</label>
                        <div class="input-icon-wrapper">
                            <i class="fas fa-user input-icon"></i>
                            <input type="text" id="name" name="name" class="input-with-icon" value="<?php echo htmlspecialchars($patient['name']); ?>" required>
                        </div>
                    </div>

                    <!-- New form row for Age and DOB -->
                    <div class="form-row">
                        <div class="form-col">
                            <label for="age">Age</label>
                            <div class="input-icon-wrapper">

                                <input type="number" id="age" name="age" class="input-with-icon" min="1" max="120" value="<?php echo isset($patient['age']) ? htmlspecialchars($patient['age']) : ''; ?>">
                            </div>
                        </div>
                        
                        <div class="form-col">
                            <label for="dob">Date of Birth</label>
                            <div class="input-icon-wrapper">
                                <i class="fas fa-calendar-alt input-icon"></i>
                                <input type="date" id="dob" name="dob" class="input-with-icon" value="<?php echo isset($patient['dob']) ? htmlspecialchars($patient['dob']) : ''; ?>">
                            </div>
                        </div>
                    </div>

                    <!-- Gender field -->
                    <div class="form-group">
                        <label for="gender">Gender</label>
                        <div class="input-icon-wrapper">
                            <i class="fas fa-venus-mars input-icon"></i>
                            <select id="gender" name="gender" class="input-with-icon">
                                <option value="">Select Gender</option>
                                <option value="Male" <?php echo (isset($patient['gender']) && $patient['gender'] == 'Male') ? 'selected' : ''; ?>>Male</option>
                                <option value="Female" <?php echo (isset($patient['gender']) && $patient['gender'] == 'Female') ? 'selected' : ''; ?>>Female</option>
                                <option value="Other" <?php echo (isset($patient['gender']) && $patient['gender'] == 'Other') ? 'selected' : ''; ?>>Other</option>
                                <option value="Prefer not to say" <?php echo (isset($patient['gender']) && $patient['gender'] == 'Prefer not to say') ? 'selected' : ''; ?>>Prefer not to say</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="phone">Phone Number</label>
                        <div class="input-icon-wrapper">
                            <i class="fas fa-phone input-icon"></i>
                            <input type="text" id="phone" name="phone" class="input-with-icon" value="<?php echo htmlspecialchars($patient['phone']); ?>" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <div class="input-icon-wrapper">
                            <i class="fas fa-envelope input-icon"></i>
                            <input type="email" id="email" name="email" class="input-with-icon" value="<?php echo htmlspecialchars($patient['email']); ?>" required>
                        </div>
                    </div>

                    <div class="file-upload-container">
                        <label for="profile_pic" class="file-upload-label" id="upload-label">
                            <i class="fas fa-cloud-upload-alt"></i>
                            <span>Upload Profile Picture</span>
                            <small>JPG, JPEG, PNG or GIF (Max 5MB)</small>
                        </label>
                        <input type="file" id="profile_pic" name="profile_pic" class="file-upload-input" accept="image/*">
                        <div id="file-name" class="file-name-display"></div>
                    </div>
                    <div class="form-group">
    <label for="current_password">Current Password (required for changes)</label>
    <div class="input-icon-wrapper">
        <i class="fas fa-lock input-icon"></i>
        <input type="password" id="current_password" name="current_password" class="input-with-icon" placeholder="Enter your current password">
        <span class="password-toggle" onclick="toggleCurrentPassword()">
            <i id="current-password-icon" class="fas fa-eye"></i>
        </span>
    </div>
</div>
                    <div class="form-group">
                        <label for="password">New Password (leave blank to keep existing)</label>
                        <div class="input-icon-wrapper">
                            <i class="fas fa-lock input-icon"></i>
                            <input type="password" id="password" name="password" class="input-with-icon" placeholder="Enter new password if you want to change">
                            <span class="password-toggle" onclick="togglePassword()">
                                <i id="password-icon" class="fas fa-eye"></i>
                            </span>
                        </div>
                    </div>

                    <div class="buttons-container">
                        <a href="patientupdatebutton.php" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Profile
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Toggle current password visibility
function toggleCurrentPassword() {
    const passwordInput = document.getElementById('current_password');
    const passwordIcon = document.getElementById('current-password-icon');
    
    if (passwordInput.type === "password") {
        passwordInput.type = "text";
        passwordIcon.classList.remove('fa-eye');
        passwordIcon.classList.add('fa-eye-slash');
    } else {
        passwordInput.type = "password";
        passwordIcon.classList.remove('fa-eye-slash');
        passwordIcon.classList.add('fa-eye');
    }
}
        // Preview uploaded profile picture
        document.getElementById('profile_pic').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                const placeholder = document.getElementById('profile-placeholder');
                const preview = document.getElementById('profile-preview');
                const fileName = document.getElementById('file-name');
                
                reader.onload = function(e) {
                    if (preview) {
                        preview.src = e.target.result;
                    } else if (placeholder) {
                        // Create new preview image if it doesn't exist
                        const newPreview = document.createElement('img');
                        newPreview.src = e.target.result;
                        newPreview.alt = "Profile Picture";
                        newPreview.className = "profile-img";
                        newPreview.id = "profile-preview";
                        
                        const container = placeholder.parentNode;
                        container.replaceChild(newPreview, placeholder);
                    }
                    
                    // Show file name
                    fileName.textContent = file.name;
                    fileName.style.display = 'block';
                    
                    // Add animation to upload label
                    document.getElementById('upload-label').classList.add('pulse');
                    setTimeout(() => {
                        document.getElementById('upload-label').classList.remove('pulse');
                    }, 1500);
                };
                
                reader.readAsDataURL(file);
            }
        });
        
        // Toggle password visibility
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const passwordIcon = document.getElementById('password-icon');
            
            if (passwordInput.type === "password") {
                passwordInput.type = "text";
                passwordIcon.classList.remove('fa-eye');
                passwordIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = "password";
                passwordIcon.classList.remove('fa-eye-slash');
                passwordIcon.classList.add('fa-eye');
            }
        }
        
        // Form validation
        // Form validation
function validateForm() {
    let isValid = true;
    const name = document.getElementById('name').value.trim();
    const phone = document.getElementById('phone').value.trim();
    const email = document.getElementById('email').value.trim();
    const password = document.getElementById('password').value;
    const currentPassword = document.getElementById('current_password').value;
    const age = document.getElementById('age').value;
    
    // Simple validation
    if (name.length < 2) {
        alert("Please enter a valid name");
        isValid = false;
    }
    
    if (phone.length < 10) {
        alert("Please enter a valid phone number");
        isValid = false;
    }
    
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email)) {
        alert("Please enter a valid email address");
        isValid = false;
    }
    
    // Current password is required for any changes
    if (currentPassword.length === 0) {
        alert("Please enter your current password to save changes");
        isValid = false;
    }
    
    if (password.length > 0 && password.length < 6) {
        alert("New password must be at least 6 characters");
        isValid = false;
    }
    
    if (age && (parseInt(age) < 1 || parseInt(age) > 120)) {
        alert("Please enter a valid age between 1 and 120");
        isValid = false;
    }
    
    return isValid;
}

        // Auto-calculate age based on DOB
        document.getElementById('dob').addEventListener('change', function() {
            const dobDate = new Date(this.value);
            const today = new Date();
            let age = today.getFullYear() - dobDate.getFullYear();
            const monthDiff = today.getMonth() - dobDate.getMonth();
            
            if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < dobDate.getDate())) {
                age--;
            }
            
            if (age >= 0 && age <= 120) {
                document.getElementById('age').value = age;
            }
        });
        
    </script>
</body>
</html>