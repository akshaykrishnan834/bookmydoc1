<?php 
require 'db_connection.php'; // Include database connection 

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
            --primary-color: #4f46e5;
            --primary-dark: #4338ca;
            --secondary-color: #f8fafc;
            --accent-color: #14b8a6;
            --text-color: #1e293b;
            --text-light: #64748b;
            --border-color: #e5e7eb;
            --success-color: #2ecc71;
            --white: #ffffff;
            --card-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
            --gradient-light: rgba(255, 255, 255, 0.1);
        }
        
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        
        body {
            font-family: 'Inter', 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #f0f9ff, #e0f2fe);
            color: var(--text-color);
            line-height: 1.6;
            min-height: 100vh;
        }
        
        .container {
            max-width: 1100px;
            margin: 40px auto;
            padding: 0 20px;
            animation: fadeIn 0.5s ease-in;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .card {
            background: var(--white);
            border-radius: 20px;
            box-shadow: var(--card-shadow);
            overflow: hidden;
            animation: slideUp 0.6s ease-out;
        }
        
        @keyframes slideUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .profile-header {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
            color: var(--white);
            padding: 40px;
            position: relative;
            display: flex;
            align-items: center;
            gap: 30px;
            overflow: hidden;
        }
        
        .profile-header::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, var(--gradient-light), transparent);
            opacity: 0.3;
            pointer-events: none;
        }
        
        .profile-photo {
            width: 140px;
            height: 140px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid var(--white);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
            transition: transform 0.3s ease;
        }
        
        .profile-photo:hover {
            transform: scale(1.05);
        }
        
        .profile-title {
            flex: 1;
            position: relative;
            z-index: 1;
        }
        
        .profile-title h2 {
            font-size: 34px;
            font-weight: 800;
            margin-bottom: 8px;
            letter-spacing: -0.5px;
        }
        
        .profile-title p {
            font-size: 18px;
            opacity: 0.9;
            font-weight: 500;
        }
        
        .back-button {
            position: absolute;
            top: 20px;
            right: 20px; /* Changed from left to right */
            background: var(--gradient-light);
            color: var(--white);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 10px;
            padding: 12px 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
            backdrop-filter: blur(8px);
        }
        
        .back-button:hover {
            background: rgba(255, 255, 255, 0.25);
            transform: translateX(5px); /* Changed to positive value for right-side movement */
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        
        .profile-body {
            padding: 40px;
        }
        
        .section-title {
            color: var(--primary-color);
            font-size: 24px;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 3px solid var(--accent-color);
            display: flex;
            align-items: center;
            gap: 12px;
            font-weight: 700;
        }
        
        .section-title i {
            color: var(--accent-color);
            font-size: 28px;
        }
        
        .doctor-info {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 25px;
        }
        
        .info-card {
            background: var(--secondary-color);
            border-radius: 15px;
            padding: 20px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        }
        
        .info-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        }
        
        .info-header {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 15px;
        }
        
        .info-icon {
            width: 45px;
            height: 45px;
            background: linear-gradient(90deg, var(--primary-color), var(--primary-dark));
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .info-icon i {
            color: var(--white);
            font-size: 20px;
        }
        
        .info-title {
            color: var(--text-light);
            font-size: 14px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .info-content {
            font-size: 16px;
            font-weight: 500;
            word-break: break-word;
        }
        
        .specialty-badge {
            display: inline-block;
            background: var(--accent-color);
            color: var(--white);
            padding: 8px 15px;
            border-radius: 50px;
            font-size: 14px;
            font-weight: 600;
            margin-top: 5px;
            box-shadow: 0 2px 8px rgba(20, 184, 166, 0.3);
        }
        
        .experience-years {
            font-size: 40px;
            font-weight: 800;
            color: var(--primary-color);
            margin-bottom: 5px;
        }
        
        .years-label {
            font-size: 14px;
            color: var(--text-light);
            font-weight: 500;
        }
        
        .contact-actions {
            display: flex;
            gap: 20px;
            margin-top: 40px;
        }
        
        .contact-btn {
            flex: 1;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            background: linear-gradient(90deg, var(--primary-color), var(--primary-dark));
            color: var(--white);
            padding: 14px 25px;
            border-radius: 12px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
        }
        
        .contact-btn.secondary {
            background: var(--secondary-color);
            color: var(--primary-color);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        }
        
        .contact-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }
        
        .qualifications-card {
            grid-column: 1 / -1;
        }
        
        @media (max-width: 768px) {
            .container {
                margin: 20px 10px;
                padding: 0 15px;
            }
            
            .profile-header {
                flex-direction: column;
                align-items: flex-start;
                padding: 25px;
            }
            
            .profile-photo {
                width: 120px;
                height: 120px;
                margin-bottom: 20px;
            }
            
            .profile-title h2 {
                font-size: 28px;
            }
            
            .profile-title p {
                font-size: 16px;
            }
            
            .profile-body {
                padding: 25px;
            }
            
            .doctor-info {
                grid-template-columns: 1fr;
            }
            
            .contact-actions {
                flex-direction: column;
                gap: 15px;
            }
            
            .back-button {
                top: 15px;
                right: 15px; /* Adjusted for mobile */
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
                <img src="<?php echo htmlspecialchars($doctor['profile_photo'] ?? 'default_doctor.jpg'); ?>" 
                      onerror="this.onerror=null; this.src='images/profilepicdoct.jpg';"
                     class="profile-photo">
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