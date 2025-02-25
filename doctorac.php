<?php
session_start();
include('doctorheader.php');
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

$stmt = $conn->prepare("SELECT name, email, age, qualifications, experience, specialization, degree_certificate, profile_photo FROM doctorreg WHERE id = ?");
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
    <title>Dr. <?= htmlspecialchars($doctor['name']) ?> - Profile</title>
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

        .edcontainer {
            max-width: 1000px;
            margin: 2rem auto;
            padding: 0 1rem;
        }

        .profile-card {
            background: var(--white);
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            overflow: hidden;
            display: grid;
            grid-template-columns: 300px 1fr;
        }

        .profile-sidebar {
            padding: 2rem;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: var(--white);
            text-align: center;
        }

        .profile-photo-container {
            position: relative;
            width: 180px;
            height: 180px;
            margin: 0 auto 1.5rem;
            border-radius: 50%;
            overflow: hidden;
            border: 4px solid var(--white);
            box-shadow: var(--shadow);
        }

        .profile-photo {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .verification-badge {
            position: absolute;
            bottom: 10px;
            right: 10px;
            background: #2ecc71;
            padding: 0.5rem;
            border-radius: 50%;
            color: white;
            box-shadow: var(--shadow);
        }

        .doctor-name {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .doctor-specialization {
            font-size: 1rem;
            opacity: 0.9;
            margin-bottom: 1.5rem;
        }

        .quick-info {
            display: flex;
            justify-content: center;
            gap: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .info-item {
            text-align: center;
        }

        .info-label {
            font-size: 0.8rem;
            opacity: 0.9;
        }

        .info-value {
            font-size: 1.2rem;
            font-weight: 600;
        }

        .profile-main {
            padding: 2rem;
        }

        .section {
            margin-bottom: 2rem;
        }

        .section-title {
            font-size: 1.2rem;
            color: var(--primary-color);
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .section-content {
            color: var(--text-light);
            font-size: 1rem;
        }

        .qualification-item {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 0.5rem;
        }

        .qualification-icon {
            color: var(--primary-color);
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1.5rem;
            background: var(--primary-color);
            color: var(--white);
            text-decoration: none;
            border-radius: var(--border-radius);
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            font-size: 1rem;
        }

        .btn:hover {
            background: #005f99;
            transform: translateY(-2px);
        }

        .certificate-link {
            color: var(--primary-color);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .certificate-link:hover {
            text-decoration: underline;
        }

        @media (max-width: 768px) {
            .profile-card {
                grid-template-columns: 1fr;
            }

            .profile-sidebar {
                padding: 1.5rem;
            }

            .profile-photo-container {
                width: 150px;
                height: 150px;
            }
        }
        .action-buttons {
            margin-top: 2rem;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
        }

        .action-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 0.75rem 1.5rem;
            color: var(--white);
            text-decoration: none;
            border-radius: var(--border-radius);
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            font-size: 1rem;
            text-align: center;
        }

        .btn-password {
            background: linear-gradient(135deg, #ff6b6b, #ff8e8e);
        }

        .btn-password:hover {
            background: linear-gradient(135deg, #ff5252, #ff7675);
            transform: translateY(-2px);
        }

        .btn-contact {
            background: linear-gradient(135deg, #4CAF50, #8BC34A);
        }

        .btn-contact:hover {
            background: linear-gradient(135deg, #43A047, #7CB342);
            transform: translateY(-2px);
        }

        .btn-photo {
            background: linear-gradient(135deg, #9C27B0, #E040FB);
        }

        .btn-photo:hover {
            background: linear-gradient(135deg, #8E24AA, #D500F9);
            transform: translateY(-2px);
        }

        @media (max-width: 768px) {
            .action-buttons {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="edcontainer">
        <div class="profile-card">
            <div class="profile-sidebar">
                <div class="profile-photo-container">
                <img src="<?= htmlspecialchars($doctor['profile_photo']) ?>" onerror="this.onerror=null; this.src='images/profilepicdoct.jpg';" class="profile-photo">

                <div class="verification-badge"><i class="fas fa-check"></i>
                    </div>
                </div>
                <h1 class="doctor-name">Dr. <?= htmlspecialchars($doctor['name']) ?></h1>
                <p class="doctor-specialization"><?= htmlspecialchars($doctor['specialization']) ?></p>
                
                <div class="quick-info">
                    <div class="info-item">
                        <div class="info-value"><?= htmlspecialchars($doctor['experience']) ?>+</div>
                        <div class="info-label">Years Exp.</div>
                    </div>
                    <div class="info-item">
                        <div class="info-value">500+</div>
                        <div class="info-label">Patients</div>
                    </div>
                    <div class="info-item">
                        <div class="info-value">4.9</div>
                        <div class="info-label">Rating</div>
                    </div>
                </div>

                
<br>
<br>
<a href="adddoctors.php" class="btn">
                    <i class="fas fa-edit"></i>
                    Update Profile
                </a>
            </div>

            <div class="profile-main">
                <div class="section">
                    <h2 class="section-title">
                        
                    </h2>
                    <?php if (!empty($doctor['specialization']) && !empty($doctor['qualifications'])) { ?>
    <div class="section">
        <h2 class="section-title">
            <i class="fas fa-user-md"></i>
            About
        </h2>
        <p class="section-content">
            Experienced <?= htmlspecialchars($doctor['specialization']) ?> specialist with 
            <?= htmlspecialchars($doctor['experience']) ?> years of practice.  
            Committed to providing high-quality medical care and ensuring patient well-being.
        </p>
    </div>
    <?php } else { ?>
    <div class="alert alert-danger">
        <h4 style="color: red;">Please complete your profile by filling in Specialization and Qualifications.<h4>
    </div>
<?php } ?>

<?php if (isset($doctor['approved']) && $doctor['approved'] == 0) { ?>
    <div class="alert alert-warning">
        Your profile is not yet approved by the admin. Please wait for approval.
    </div>
<?php } ?>

                <div class="section">
                    <h2 class="section-title">
                        <i class="fas fa-graduation-cap"></i>
                        Qualifications
                    </h2>
                    <div class="section-content">
                        <?php
                        $qualifications = explode(',', $doctor['qualifications']);
                        foreach ($qualifications as $qualification) {
                            echo '<div class="qualification-item">';
                            echo '<i class="fas fa-check-circle qualification-icon"></i>';
                            echo '<span>' . htmlspecialchars(trim($qualification)) . '</span>';
                            echo '</div>';
                        }
                        ?>
                    </div>
                </div>

                <div class="section">
                    <h2 class="section-title">
                        <i class="fas fa-envelope"></i>
                        Contact Information
                    </h2>
                    <div class="section-content">
                        <p><i class="fas fa-envelope" style="margin-right: 0.5rem;"></i> <?= htmlspecialchars($doctor['email']) ?></p>
                    </div>
                </div>

                <?php if (!empty($doctor['degree_certificate'])): ?>
                
                    
                    <div class="action-buttons">
                        <a href="doctorprofileresetpassword.php" class="action-btn btn-password">
                            <i class="fas fa-key"></i>
                            Reset Password
                        </a>
                        <a href="doctorprofilecontact.php" class="action-btn btn-contact">
                            <i class="fas fa-address-card"></i>
                            Update Contact Info
                        </a>
                        <a href="doctorchangeprofile.php" class="action-btn btn-photo">
                            <i class="fas fa-camera"></i>
                            Change Photo
                        </a>
                    </div>
                </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>