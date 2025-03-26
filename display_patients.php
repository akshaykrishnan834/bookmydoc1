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

$stmt = $conn->prepare("SELECT name, email, phone, gender, dob, profile_pic FROM patientreg WHERE id = ?");
$stmt->bind_param("i", $patient_id);
$stmt->execute();
$result = $stmt->get_result();
$patient = $result->fetch_assoc();
$stmt->close();

$age = null;
if (!empty($patient['dob'])) {
    $birthDate = new DateTime($patient['dob']);
    $today = new DateTime('today');
    $age = $birthDate->diff($today)->y;
}

$conn->close();

$firstLetter = !empty($patient['name']) ? strtoupper(substr($patient['name'], 0, 1)) : '?';
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
            --background-color: #f0f4f8;
            --card-background: #ffffff;
            --text-color: #2c3e50;
            --text-muted: #6c757d;
        }

        .container2 {
    max-width: 1100px;
    padding: 20px;
    position: absolute;
    top: 150px; /* Adjust this value to control distance from top */
    left: 50%;
    transform: translateX(-50%); /* Only translate horizontally */
    width: 100%;
}
        .profile-card {
            background: var(--card-background);
            border-radius: 25px;
            box-shadow: 0 15px 35px rgba(44, 107, 237, 0.15);
            display: flex;
            overflow: hidden;
            transition: all 0.3s ease;
            width: 100%;
            max-width: 1100px;
        }

        .profile-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(44, 107, 237, 0.2);
        }

        .profile-left {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            width: 35%;
            padding: 40px;
            color: white;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .profile-avatar {
            width: 140px;
            height: 140px;
            border-radius: 50%;
            border: 5px solid rgba(255, 255, 255, 0.3);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
            overflow: hidden;
            background: white;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
            transition: all 0.3s ease;
        }

        .profile-avatar:hover {
            transform: scale(1.05);
        }

        .profile-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .avatar-text {
            font-size: 3rem;
            color: var(--primary-color);
            font-weight: 700;
        }

        .profile-name {
            font-size: 1.8rem;
            margin: 0;
            text-align: center;
            font-weight: 600;
            letter-spacing: 0.5px;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .profile-role {
            font-size: 0.9rem;
            margin-top: 8px;
            opacity: 0.9;
            text-transform: uppercase;
            letter-spacing: 1px;
            text-align: center;
        }

        .profile-right {
            width: 65%;
            padding: 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }

        .info-item {
            background: rgba(44, 107, 237, 0.05);
            padding: 20px;
            border-radius: 15px;
            transition: all 0.3s ease;
        }

        .info-item:hover {
            background: rgba(44, 107, 237, 0.1);
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        }

        .info-label {
            color: var(--text-muted);
            font-weight: 600;
            font-size: 0.9rem;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
        }

        .info-label i {
            margin-right: 10px;
            color: var(--primary-color);
            font-size: 1.1rem;
        }

        .info-value {
            font-size: 1.1rem;
            font-weight: 500;
            color: var(--text-color);
        }

        .action-button {
            display: flex;
            justify-content: center;
            gap: 15px;
        }

        .profile-btn {
            background: linear-gradient(45deg, var(--primary-color), var(--secondary-color));
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 25px;
            font-size: 0.9rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 5px 15px rgba(44, 107, 237, 0.3);
            text-decoration: none;
            display: flex;
            align-items: center;
        }

        .profile-btn i {
            margin-right: 8px;
        }

        .profile-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(44, 107, 237, 0.4);
        }

        @media (max-width: 768px) {
            .profile-card {
                flex-direction: column;
            }
            
            .profile-left, .profile-right {
                width: 100%;
            }
            
            .info-grid {
                grid-template-columns: 1fr;
            }
            
            .profile-avatar {
                width: 120px;
                height: 120px;
            }
            
            .profile-name {
                font-size: 1.5rem;
            }
            
            .container2 {
                padding: 10px;
            }
        }
    </style>
</head>
<body>
    
    <div class="container2">
        <?php if ($patient): ?>
            <div class="profile-card">
                <div class="profile-left">
                    <div class="profile-avatar">
                        <?php if (!empty($patient['profile_pic'])): ?>
                            <img src="<?php echo htmlspecialchars($patient['profile_pic']); ?>" alt="Profile Photo">
                        <?php else: ?>
                            <div class="avatar-text"><?php echo $firstLetter; ?></div>
                        <?php endif; ?>
                    </div>
                    <h1 class="profile-name"><?php echo htmlspecialchars($patient['name']); ?></h1>
                    <p class="profile-role">Patient</p>
                </div>
                <div class="profile-right">
                    <div class="info-grid">
                        <div class="info-item">
                            <div class="info-label"><i class="fas fa-phone"></i> Phone</div>
                            <div class="info-value"><?php echo htmlspecialchars($patient['phone']); ?></div>
                        </div>
                        <div class="info-item">
                            <div class="info-label"><i class="fas fa-envelope"></i> Email</div>
                            <div class="info-value"><?php echo htmlspecialchars($patient['email']); ?></div>
                        </div>
                        <div class="info-item">
                            <div class="info-label"><i class="fas fa-birthday-cake"></i> DOB</div>
                            <div class="info-value"><?php echo !empty($patient['dob']) ? htmlspecialchars(date('F j, Y', strtotime($patient['dob']))) : 'Not provided'; ?></div>
                        </div>
                        <div class="info-item">
                            <div class="info-label"><i class="fas fa-user"></i> Age</div>
                            <div class="info-value"><?php echo $age !== null ? htmlspecialchars($age) . ' years' : 'N/A'; ?></div>
                        </div>
                        <div class="info-item">
                            <div class="info-label"><i class="fas fa-venus-mars"></i> Gender</div>
                            <div class="info-value"><?php echo !empty($patient['gender']) ? htmlspecialchars($patient['gender']) : 'Not provided'; ?></div>
                        </div>
                    </div>
                    <div class="action-button">
                        <a href="patientupdatebutton.php?id=<?php echo $patient_id; ?>" class="profile-btn">
                            <i class="fas fa-user-edit"></i> Update Profile
                        </a>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="profile-card">
                <div class="profile-left">
                    <h1 class="profile-name">Profile Not Found</h1>
                </div>
                <div class="profile-right">
                    <p style="text-align: center; padding: 20px;">Your profile information could not be found. Please contact support.</p>
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