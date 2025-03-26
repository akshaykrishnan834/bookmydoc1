<?php 
require 'db_connection.php'; // Include database connection 

if (isset($_GET['patient_id'])) {
    $patient_id = intval($_GET['patient_id']);
    
    // Fetch patient details
    $query = "SELECT * FROM patientreg WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $patient_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $patient = $result->fetch_assoc();
    } else {
        echo "<p>No patient found.</p>";
        exit;
    }
    
    // Fetch medical records
    $query = "SELECT * FROM medical_records WHERE patient_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $patient_id);
    $stmt->execute();
    $medical_records = $stmt->get_result();
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
    <title>Patient Details</title>
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
            --danger-color: #dc2626;
            --hover-color: #f1f5f9;
            --card-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
            --gradient-light: rgba(255, 255, 255, 0.1);
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
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
        
        .header-card {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
            border-radius: 20px;
            padding: 40px;
            margin-bottom: 30px;
            box-shadow: var(--card-shadow);
            position: relative;
            color: white;
            overflow: hidden;
            display: flex;
            align-items: center;
            gap: 30px;
        }
        
        .header-card::before {
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
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid white;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
            transition: transform 0.3s ease;
        }
        
        .profile-photo:hover {
            transform: scale(1.05);
        }
        
        .header-content {
            flex: 1;
        }
        
        .back-button {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 12px 20px;
            background-color: var(--gradient-light);
            color: white;
            text-decoration: none;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s ease;
            backdrop-filter: blur(8px);
            margin-bottom: 20px;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .back-button:hover {
            background-color: rgba(255, 255, 255, 0.25);
            transform: translateX(-5px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        
        h2 {
            font-size: 36px;
            font-weight: 800;
            letter-spacing: -0.5px;
            position: relative;
            z-index: 1;
        }
        
        .patient-meta {
            margin-top: 10px;
            font-size: 16px;
            opacity: 0.9;
            font-weight: 500;
            position: relative;
            z-index: 1;
        }
        
        .content-card {
            background: white;
            border-radius: 20px;
            box-shadow: var(--card-shadow);
            padding: 40px;
            margin-bottom: 30px;
            position: relative;
            overflow: hidden;
            animation: slideUp 0.6s ease-out;
        }
        
        @keyframes slideUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        h3 {
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
        
        h3 i {
            color: var(--accent-color);
            font-size: 28px;
        }
        
        .patient-info {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 25px;
        }
        
        .info-group {
            padding: 10px;
            border-radius: 12px;
            transition: all 0.3s ease;
            background: var(--secondary-color);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        }
        
        .info-group:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        }
        
        .info-label {
            font-weight: 600;
            display: block;
            margin-bottom: 10px;
            color: var(--text-light);
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .info-value {
            font-size: 18px;
            padding: 15px;
            background: white;
            border-radius: 10px;
            font-weight: 500;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.05);
            display: flex;
            align-items: center;
            gap: 10px;
            transition: all 0.3s ease;
        }
        
        .info-value:hover {
            background: var(--hover-color);
        }
        
        .records {
            margin-top: 20px;
        }
        
        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            margin-top: 20px;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.05);
            background: white;
        }
        
        th, td {
            padding: 18px 25px;
            text-align: left;
        }
        
        th {
            background: linear-gradient(90deg, var(--primary-color), var(--primary-dark));
            color: white;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 14px;
            letter-spacing: 1px;
        }
        
        tr {
            transition: all 0.3s ease;
        }
        
        tr:nth-child(even) {
            background-color: var(--secondary-color);
        }
        
        tr:hover {
            background-color: var(--hover-color);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        }
        
        td {
            border-bottom: 1px solid var(--border-color);
        }
        
        .download-btn {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 12px 20px;
            background: linear-gradient(90deg, var(--accent-color), #0d9488);
            color: white;
            text-decoration: none;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(20, 184, 166, 0.3);
        }
        
        .download-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(20, 184, 166, 0.4);
            background: linear-gradient(90deg, #14b8a6, #0d9488);
        }
        
        .no-records {
            text-align: center;
            padding: 50px 25px;
            background: var(--secondary-color);
            border-radius: 12px;
            color: var(--text-light);
            font-size: 18px;
            box-shadow: inset 0 4px 15px rgba(0, 0, 0, 0.05);
            animation: fadeIn 0.5s ease-in;
        }
        
        .no-records i {
            font-size: 48px;
            color: var(--accent-color);
            margin-bottom: 20px;
            opacity: 0.8;
        }
        
        .badge {
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
            background: var(--accent-color);
            color: white;
            margin-left: 15px;
            box-shadow: 0 2px 8px rgba(20, 184, 166, 0.3);
        }
        
        @media (max-width: 768px) {
            .container {
                margin: 20px 10px;
                padding: 0 15px;
            }
            
            .header-card {
                flex-direction: column;
                align-items: flex-start;
                padding: 25px;
            }
            
            .profile-photo {
                width: 100px;
                height: 100px;
                margin-bottom: 20px;
            }
            
            .content-card {
                padding: 25px;
            }
            
            .patient-info {
                grid-template-columns: 1fr;
            }
            
            h2 {
                font-size: 28px;
            }
            
            th, td {
                padding: 14px 12px;
                font-size: 13px;
            }
            
            .download-btn {
                padding: 10px 15px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header-card">
            <!-- Profile Photo -->
            <img src="<?php echo htmlspecialchars($patient['profile_pic'] ?? 'default_profile.jpg'); ?>" 
            onerror="this.onerror=null; this.src='images/profilepicdoct.jpg';" 
                 alt="Profile Photo" 
                 class="profile-photo">
            
            <div class="header-content">
                <a href="adminmanagepatient.php" class="back-button">
                    <i class="fas fa-arrow-left"></i> Back to Patients
                </a>
                <h2><?php echo htmlspecialchars($patient['name']); ?></h2>
                <div class="patient-meta">
                    ID: <?php echo htmlspecialchars($patient['id']); ?> | 
                    <?php echo htmlspecialchars($patient['age']); ?> Years | 
                    <?php echo htmlspecialchars($patient['gender']); ?>
                </div>
            </div>
        </div>
        
        <div class="content-card">
            <h3>
                <i class="fas fa-user-circle"></i> Personal Information
            </h3>
            <div class="patient-info">
                <div class="info-group">
                    <span class="info-label">Full Name</span>
                    <span class="info-value">
                        <i class="fas fa-user fa-sm" style="color: var(--accent-color);"></i>
                        <?php echo htmlspecialchars($patient['name']); ?>
                    </span>
                </div>
                
                <div class="info-group">
                    <span class="info-label">Age</span>
                    <span class="info-value">
                        <i class="fas fa-calendar fa-sm" style="color: var(--accent-color);"></i>
                        <?php echo htmlspecialchars($patient['age']); ?> Years
                    </span>
                </div>
                
                <div class="info-group">
                    <span class="info-label">Gender</span>
                    <span class="info-value">
                        <i class="fas fa-venus-mars fa-sm" style="color: var(--accent-color);"></i>
                        <?php echo htmlspecialchars($patient['gender']); ?>
                    </span>
                </div>
                
                <div class="info-group">
                    <span class="info-label">Contact Number</span>
                    <span class="info-value">
                        <i class="fas fa-phone fa-sm" style="color: var(--accent-color);"></i>
                        <?php echo htmlspecialchars($patient['phone']); ?>
                    </span>
                </div>
                
                <div class="info-group">
                    <span class="info-label">Email Address</span>
                    <span class="info-value">
                        <i class="fas fa-envelope fa-sm" style="color: var(--accent-color);"></i>
                        <?php echo htmlspecialchars($patient['email']); ?>
                    </span>
                </div>
            </div>
        </div>
        
        <div class="content-card">
            <h3>
                <i class="fas fa-file-medical"></i> Medical Records
                <?php 
                    $record_count = $medical_records->num_rows;
                    echo "<span class='badge'>$record_count</span>";
                ?>
            </h3>
            <div class="records">
                <?php if ($medical_records->num_rows > 0) { ?>
                    <table>
                        <tr>
                            <th width="15%">Record ID</th>
                            <th width="40%">Filename</th>
                            <th width="30%">Uploaded At</th>
                            <th width="15%">Action</th>
                        </tr>
                        <?php while ($record = $medical_records->fetch_assoc()) { ?>
                            <tr>
                                <td><?php echo htmlspecialchars($record['record_id']); ?></td>
                                <td>
                                    <i class="fas fa-file-pdf" style="color: var(--danger-color); margin-right: 8px;"></i>
                                    <?php echo htmlspecialchars($record['record_filename']); ?>
                                </td>
                                <td><?php echo htmlspecialchars($record['uploaded_at']); ?></td>
                                <td>
                                    <a href="download.php?file=<?= urlencode($record['record_filename']); ?>" class="download-btn">
                                        <i class="fas fa-download"></i> Download
                                    </a>
                                </td>
                            </tr>
                        <?php } ?>
                    </table>
                <?php } else { ?>
                    <div class="no-records">
                        <i class="fas fa-file-medical"></i>
                        <p>No medical records found.</p>
                        <p style="margin-top: 15px; font-size: 16px;">Upload records to view them here.</p>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
</body>
</html>