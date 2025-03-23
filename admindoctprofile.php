<?php require 'db_connection.php'; // Include database connection 

if (isset($_GET['doctor_id'])) {
    $doctor_id = intval($_GET['doctor_id']);
    
    // Fetch doctor details
    $query = "SELECT * FROM doctorreg WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $doctor_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $doctor = $result->fetch_assoc();
    } else {
        echo "<p>No doctor found.</p>";
        exit;
    }
    
} else {
    echo "<p>Invalid request.</p>";
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctor Profile</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #4361ee;
            --primary-light: #7289da;
            --primary-dark: #3a56e4;
            --secondary-color: #f1f5ff;
            --accent-color: #00b4d8;
            --text-color: #2a2a2a;
            --text-light: #6e7079;
            --border-color: #e4e9f7;
            --success-color: #2ecc71;
            --white: #ffffff;
            --card-shadow: 0 8px 30px rgba(0, 0, 0, 0.08);
        }
        
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fd;
            color: var(--text-color);
            line-height: 1.6;
        }
        
        .container {
            max-width: 1000px;
            margin: 40px auto;
            padding: 0 20px;
        }
        
        .card {
            background-color: var(--white);
            border-radius: 20px;
            box-shadow: var(--card-shadow);
            overflow: hidden;
        }
        
        .profile-header {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-light));
            color: var(--white);
            padding: 30px;
            position: relative;
        }
        
        .back-button {
            position: absolute;
            top: 20px;
            left: 20px;
            background-color: rgba(255, 255, 255, 0.2);
            color: var(--white);
            border: none;
            border-radius: 50px;
            padding: 10px 20px;
            display: flex;
            align-items: center;
            gap: 8px;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        
        .back-button:hover {
            background-color: rgba(255, 255, 255, 0.3);
            transform: translateX(-5px);
        }
        
        .profile-title {
            text-align: center;
            margin-top: 15px;
        }
        
        .profile-title h2 {
            font-size: 30px;
            margin-bottom: 5px;
        }
        
        .profile-title p {
            font-size: 18px;
            opacity: 0.9;
        }
        
        .profile-avatar {
            width: 120px;
            height: 120px;
            background-color: var(--white);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        
        .profile-avatar i {
            font-size: 60px;
            color: var(--primary-color);
        }
        
        .profile-body {
            padding: 30px;
        }
        
        .section-title {
            color: var(--primary-color);
            font-size: 22px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .doctor-info {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 25px;
        }
        
        .info-card {
            background-color: var(--secondary-color);
            border-radius: 15px;
            padding: 20px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .info-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
        }
        
        .info-header {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 15px;
        }
        
        .info-icon {
            width: 40px;
            height: 40px;
            background-color: var(--primary-color);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .info-icon i {
            color: var(--white);
            font-size: 18px;
        }
        
        .info-title {
            color: var(--text-light);
            font-size: 14px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .info-content {
            font-size: 16px;
            font-weight: 500;
            word-break: break-word;
        }
        
        .specialty-badge {
            display: inline-block;
            background-color: var(--accent-color);
            color: var(--white);
            padding: 8px 15px;
            border-radius: 50px;
            font-size: 14px;
            font-weight: 500;
            margin-top: 5px;
        }
        
        .experience-years {
            font-size: 36px;
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 5px;
        }
        
        .years-label {
            font-size: 14px;
            color: var(--text-light);
        }
        
        .contact-actions {
            display: flex;
            gap: 15px;
            margin-top: 30px;
        }
        
        .contact-btn {
            flex: 1;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            background-color: var(--primary-color);
            color: var(--white);
            padding: 12px 20px;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
        }
        
        .contact-btn.secondary {
            background-color: var(--secondary-color);
            color: var(--primary-color);
        }
        
        .contact-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        
        .qualifications-card {
            grid-column: 1 / -1;
        }
        
        @media (max-width: 768px) {
            .container {
                margin: 20px auto;
            }
            
            .profile-header {
                padding: 20px;
            }
            
            .back-button {
                top: 15px;
                left: 15px;
                padding: 8px 15px;
            }
            .profile-photo {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
            .profile-avatar {
                width: 100px;
                height: 100px;
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
            .profile-avatar i {
                font-size: 50px;
            }
            
            .profile-title h2 {
                font-size: 24px;
            }
            
            .profile-title p {
                font-size: 16px;
            }
            
            .doctor-info {
                grid-template-columns: 1fr;
            }
            
            .contact-actions {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="profile-header">
                <a href="adminmanagedoct.php" class="back-button">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
                
                
                
                <div class="profile-title">
                    <h2><?php echo htmlspecialchars($doctor['name']); ?></h2>
                    <p><?php echo htmlspecialchars($doctor['specialization']); ?></p>
                </div>
            </div>
            
            <div class="profile-body">
                <h3 class="section-title">
                    <i class="fas fa-info-circle"></i> Doctor Information
                </h3>
                
                <div class="doctor-info">
                    <div class="info-card">
                        <div class="info-header">
                            <div class="info-icon">
                                <i class="fas fa-envelope"></i>
                            </div>
                            <span class="info-title">Email</span>
                        </div>
                        <div class="info-content">
                            <?php echo htmlspecialchars($doctor['email']); ?>
                        </div>
                    </div>
                    
                    <div class="info-card">
                        <div class="info-header">
                            <div class="info-icon">
                                <i class="fas fa-phone-alt"></i>
                            </div>
                            <span class="info-title">Contact</span>
                        </div>
                        <div class="info-content">
                            <?php echo htmlspecialchars($doctor['phone']); ?>
                        </div>
                    </div>
                    
                    <div class="info-card">
                        <div class="info-header">
                            <div class="info-icon">
                                <i class="fas fa-user"></i>
                            </div>
                            <span class="info-title">Age</span>
                        </div>
                        <div class="info-content">
                            <?php echo htmlspecialchars($doctor['age']); ?> years old
                        </div>
                    </div>
                    
                    <div class="info-card">
                        <div class="info-header">
                            <div class="info-icon">
                                <i class="fas fa-map-marker-alt"></i>
                            </div>
                            <span class="info-title">Address</span>
                        </div>
                        <div class="info-content">
                            <?php echo htmlspecialchars($doctor['address']); ?>
                        </div>
                    </div>
                    
                    <div class="info-card">
                        <div class="info-header">
                            <div class="info-icon">
                                <i class="fas fa-user-md"></i>
                            </div>
                            <span class="info-title">Specialization</span>
                        </div>
                        <div class="info-content">
                            <span class="specialty-badge">
                                <?php echo htmlspecialchars($doctor['specialization']); ?>
                            </span>
                        </div>
                    </div>
                    
                    <div class="info-card">
                        <div class="info-header">
                            <div class="info-icon">
                                <i class="fas fa-briefcase"></i>
                            </div>
                            <span class="info-title">Experience</span>
                        </div>
                        <div class="info-content">
                            <div class="experience-years">
                                <?php echo htmlspecialchars($doctor['experience']); ?>
                            </div>
                            <div class="years-label">Years of Experience</div>
                        </div>
                    </div>
                    
                    <div class="info-card qualifications-card">
                        <div class="info-header">
                            <div class="info-icon">
                                <i class="fas fa-graduation-cap"></i>
                            </div>
                            <span class="info-title">Qualifications</span>
                        </div>
                        <div class="info-content">
                            <?php echo nl2br(htmlspecialchars($doctor['qualifications'])); ?>
                        </div>
                    </div>
                </div>
                
                <div class="contact-actions">
                    <a href="mailto:<?php echo htmlspecialchars($doctor['email']); ?>" class="contact-btn">
                        <i class="fas fa-envelope"></i> Email Doctor
                    </a>
                    <a href="tel:<?php echo htmlspecialchars($doctor['phone']); ?>" class="contact-btn secondary">
                        <i class="fas fa-phone-alt"></i> Call Doctor
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>