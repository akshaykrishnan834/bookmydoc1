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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - BookMyDoc</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f0f5ff;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .profile-container {
            max-width: 800px;
            margin: 40px auto;
            padding: 0;
            background: transparent;
        }
        .profile-header {
            background: linear-gradient(135deg, #3a8ffe 0%, #1a5eff 100%);
            color: white;
            padding: 30px;
            border-radius: 15px 15px 0 0;
            text-align: center;
            position: relative;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        .profile-header h2 {
            margin: 0;
            font-weight: 600;
            font-size: 28px;
        }
        .profile-header p {
            margin-top: 10px;
            opacity: 0.9;
            font-size: 16px;
        }
        .card-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            padding: 30px;
            background-color: white;
            border-radius: 0 0 15px 15px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        .profile-card {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            transition: all 0.3s ease;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
        }
        .profile-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }
        .card-icon {
            height: 120px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 40px;
        }
        .password-card .card-icon { background-color: #ff7043; color: white; }
        .contact-card .card-icon { background-color: #4caf50; color: white; }
        .photo-card .card-icon { background-color: #ffca28; color: white; }
        .medical-card .card-icon { background-color: #ef5350; color: white; }
        .update-card .card-icon { background-color: #42a5f5; color: white; }
        
        .card-content {
            padding: 20px;
            text-align: center;
        }
        .card-content h3 {
            margin: 0 0 10px 0;
            font-size: 18px;
            font-weight: 600;
        }
        .card-content p {
            color: #666;
            font-size: 14px;
            margin: 0 0 15px 0;
        }
        .btn-card {
            width: 100%;
            padding: 10px;
            border: none;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        .btn-card:hover {
            transform: scale(1.05);
        }
        .btn-password { background-color: #ff7043; color: white; }
        .btn-password:hover { background-color: #f4511e; }
        .btn-contact { background-color: #4caf50; color: white; }
        .btn-contact:hover { background-color: #388e3c; }
        .btn-photo { background-color: #ffca28; color: white; }
        .btn-photo:hover { background-color: #ffb300; }
        .btn-medical { background-color: #ef5350; color: white; }
        .btn-medical:hover { background-color: #e53935; }
        .btn-update { background-color: #42a5f5; color: white; }
        .btn-update:hover { background-color: #1e88e5; }
        .back-button {
            position: absolute;
            top: 30px;
            left: 30px;
            background-color: rgba(255, 255, 255, 0.2);
            color: white;
            border: none;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            transition: all 0.3s ease;
            text-decoration:none;
        }
        .back-button:hover {
            background-color: rgba(255, 255, 255, 0.3);
            transform: scale(1.1);
        }
        .footer-btn {
            text-align: center;
            margin-top: 20px;
        }
        .btn-back-profile {
            background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
            color: white;
            padding: 12px 25px;
            border: none;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s ease;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
        .btn-back-profile:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.15);
        }
    </style>
</head>
<body>
    <div class="profile-container">
        <div class="profile-header">
            <a href="display_patients.php" class="back-button"><i class="fas fa-arrow-left"></i></a>
            <h2>Manage Your Profile</h2>
            <p>Update your personal information and account settings</p>
        </div>
        
        <div class="card-container">
            <div class="profile-card password-card">
                <div class="card-icon">
                    <i class="fas fa-key"></i>
                </div>
                <div class="card-content">
                    <h3>Password</h3>
                    <p>Change your account password</p>
                    <a href="patientprofileresetpass.php" class="btn btn-card btn-password">Update Password</a>
                </div>
            </div>
            
            <div class="profile-card contact-card">
                <div class="card-icon">
                    <i class="fas fa-address-book"></i>
                </div>
                <div class="card-content">
                    <h3>Contact Info</h3>
                    <p>Update phone and email</p>
                    <a href="patientcontactinfo.php" class="btn btn-card btn-contact">Edit Contact</a>
                </div>
            </div>
            
            <div class="profile-card photo-card">
                <div class="card-icon">
                    <i class="fas fa-camera"></i>
                </div>
                <div class="card-content">
                    <h3>Profile Photo</h3>
                    <p>Update your profile picture</p>
                    <a href="patientprofilepic.php" class="btn btn-card btn-photo">Change Photo</a>
                </div>
            </div>
            
            <div class="profile-card medical-card">
                <div class="card-icon">
                    <i class="fas fa-file-medical"></i>
                </div>
                <div class="card-content">
                    <h3>Medical Records</h3>
                    <p>View and manage your records</p>
                    <a href="medical_record.php" class="btn btn-card btn-medical">Manage Records</a>
                </div>
            </div>
            
            <div class="profile-card update-card">
                <div class="card-icon">
                    <i class="fas fa-user-edit"></i>
                </div>
                <div class="card-content">
                    <h3>Personal Info</h3>
                    <p>Update your personal details</p>
                    <a href="update_patientprofile.php" class="btn btn-card btn-update">Edit Profile</a>
                </div>
            </div>
        </div>
        
        <div class="footer-btn">
            <a href="display_patients.php" class="btn btn-back-profile">
                <i class="fas fa-user-circle me-2"></i> Back to Profile
            </a>
        </div>
    </div>
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>