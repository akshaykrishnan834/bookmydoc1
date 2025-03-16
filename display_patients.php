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

$patient_id = $_SESSION['id'];

$stmt = $conn->prepare("SELECT name, email, phone, gender, dob FROM patientreg WHERE id = ?");
$stmt->bind_param("i", $patient_id);
$stmt->execute();
$result = $stmt->get_result();
$patient = $result->fetch_assoc();
$stmt->close();

// Calculate age from date of birth
$age = null;
if (!empty($patient['dob'])) {
    $birthDate = new DateTime($patient['dob']);
    $today = new DateTime('today');
    $age = $birthDate->diff($today)->y;
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Profile</title>
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
        
        body {
            background-color: var(--background-color);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            color: var(--text-color);
        }
        
        .container2 {
            max-width: 800px;
            margin: 40px auto;
            padding: 0 20px;
        }
        
        .profile-card {
            background: var(--card-background);
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            overflow: hidden;
            margin-top: 20px;
            transition: transform 0.3s ease;
        }
        
        .profile-card:hover {
            transform: translateY(-5px);
        }
        
        .profile-header {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            padding: 60px 40px;
            color: white;
            text-align: center;
            position: relative;
        }
        
        .profile-avatar {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background-color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            border: 5px solid rgba(255, 255, 255, 0.3);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        
        .avatar-text {
            font-size: 3rem;
            color: var(--primary-color);
            font-weight: bold;
        }
        
        .profile-name {
            font-size: 2.2rem;
            margin: 0;
            letter-spacing: 0.5px;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        
        .profile-role {
            font-size: 1rem;
            margin-top: 5px;
            opacity: 0.9;
            letter-spacing: 1px;
            text-transform: uppercase;
        }
        
        .profile-content {
            padding: 40px;
        }
        
        .info-item {
            background: rgba(44, 107, 237, 0.05);
            padding: 25px;
            border-radius: 15px;
            margin-bottom: 20px;
            transition: all 0.3s ease;
            border-left: 4px solid transparent;
        }
        
        .info-item:hover {
            background: rgba(44, 107, 237, 0.1);
            border-left: 4px solid var(--primary-color);
            transform: translateX(5px);
        }
        
        .info-label {
            color: var(--text-muted);
            font-weight: 600;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
        }
        
        .info-label i {
            margin-right: 10px;
            color: var(--primary-color);
            font-size: 1.2rem;
        }
        
        .info-value {
            font-size: 1.2rem;
            color: var(--text-color);
            font-weight: 500;
            padding-left: 30px;
        }
        
       .action-button {
            text-align: center;
            margin-top: 30px;
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            justify-content: center;
        }
        
        .profile-btn {
            background: linear-gradient(to right, var(--primary-color), var(--secondary-color));
            color: white;
            border: none;
            padding: 15px 25px;
            border-radius: 30px;
            font-size: 0.9rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 5px 15px rgba(44, 107, 237, 0.3);
            letter-spacing: 0.5px;
            display: inline-flex;
            align-items: center;
            text-decoration: none;
        }
        
        .profile-btn i {
            margin-right: 8px;
        }
        
        .profile-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(44, 107, 237, 0.4);
        }
        
        .profile-btn:active {
            transform: translateY(0);
        }
        
        .reset-password-btn {
            background: linear-gradient(to right, #ff6b6b, #ff8e8e);
        }
        
        .reset-contact-btn {
            background: linear-gradient(to right, #4CAF50, #8BC34A);
        }
        
        .change-photo-btn {
            background: linear-gradient(to right, #9C27B0, #E040FB);
        }
        
        @media (max-width: 768px) {
            .profile-btn {
                padding: 12px 20px;
                font-size: 0.85rem;
                width: calc(50% - 10px);
            }
            
            .action-button {
                padding: 0 15px;
            }
        }
        
    </style>
</head>
<body>
    <div class="container2">
        <?php if ($patient): ?>
            <div class="profile-card">
                <div class="profile-header">
                    <h1 class="profile-name"><?php echo htmlspecialchars($patient['name']); ?></h1>
                    <p class="profile-role">Patient</p>
                </div>
                <div class="profile-content">
                    <!-- Previous info items remain the same -->
                    <div class="info-item">
                        <div class="info-label"><i class="fas fa-phone"></i> Phone Number</div>
                        <div class="info-value"><?php echo htmlspecialchars($patient['phone']); ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label"><i class="fas fa-envelope"></i> Email Address</div>
                        <div class="info-value"><?php echo htmlspecialchars($patient['email']); ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label"><i class="fas fa-birthday-cake"></i> Date of Birth</div>
                        <div class="info-value"><?php echo !empty($patient['dob']) ? htmlspecialchars(date('F j, Y', strtotime($patient['dob']))) : 'Not provided'; ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label"><i class="fas fa-user"></i> Age</div>
                        <div class="info-value"><?php echo $age !== null ? htmlspecialchars($age) . ' years' : 'Not available'; ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label"><i class="fas fa-venus-mars"></i> Gender</div>
                        <div class="info-value"><?php echo !empty($patient['gender']) ? htmlspecialchars($patient['gender']) : 'Not provided'; ?></div>
                    </div>
                    <div class="action-button">
                        <a href="update_patientprofile.php" class="profile-btn">
                            <i class="fas fa-user-edit"></i> Update Profile
                        </a>
                        <a href="patientprofileresetpass.php" class="profile-btn reset-password-btn">
                            <i class="fas fa-key"></i> Reset Password
                        </a>
                        <a href="patientcontactinfo.php" class="profile-btn reset-contact-btn">
                            <i class="fas fa-address-card"></i> Reset Contact Info
                        </a>
                        <a href="patientprofilepic.php" class="profile-btn change-photo-btn">
                            <i class="fas fa-camera"></i> Change Photo
                        </a>
                        <a href="medical_record.php" class="profile-btn reset-contact-btn">
                            <i class="fas fa-address-card"></i> Medical Records
                        </a>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="profile-card">
                <div class="profile-header">
                    <h1 class="profile-name">Profile Not Found</h1>
                </div>
                <div class="profile-content">
                    <p style="text-align:center; padding: 20px;">Your profile information could not be found. Please contact support for assistance.</p>
                    <div class="action-button">
                        <a href="patientprofile.php" class="profile-btn">
                            <i class="fas fa-home"></i> Return Home
                        </a>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>