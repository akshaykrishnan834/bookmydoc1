<?php require 'db_connection.php'; // Include database connection 

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
            --primary-color: #4361ee;
            --primary-dark: #3a56d4;
            --secondary-color: #f1f5f9;
            --accent-color: #10b981;
            --text-color: #1e293b;
            --text-light: #64748b;
            --border-color: #e2e8f0;
            --danger-color: #ef4444;
            --hover-color: #f8fafc;
            --card-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f1f5f9;
            color: var(--text-color);
            line-height: 1.6;
        }
        
        .container {
            max-width: 1000px;
            margin: 40px auto;
            padding: 0;
            border-radius: 16px;
            background-color: transparent;
        }
        
        .header-card {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
            border-radius: 16px;
            padding: 30px;
            margin-bottom: 25px;
            box-shadow: var(--card-shadow);
            position: relative;
            color: white;
        }
        
        .back-button {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 15px;
            background-color: rgba(255, 255, 255, 0.15);
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s;
            backdrop-filter: blur(5px);
            margin-bottom: 15px;
        }
        
        .back-button:hover {
            background-color: rgba(255, 255, 255, 0.25);
            transform: translateY(-2px);
        }
        
        h2 {
            margin: 0;
            font-size: 32px;
            font-weight: 700;
        }
        
        .patient-meta {
            margin-top: 5px;
            font-size: 16px;
            opacity: 0.9;
        }
        
        .content-card {
            background-color: white;
            border-radius: 16px;
            box-shadow: var(--card-shadow);
            padding: 30px;
            margin-bottom: 25px;
        }
        
        h3 {
            color: var(--primary-color);
            font-size: 22px;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid var(--border-color);
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        h3 i {
            color: var(--primary-color);
        }
        
        .patient-info {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
        }
        
        .info-group {
            margin-bottom: 20px;
            transition: all 0.3s;
            padding: 5px;
            border-radius: 8px;
        }
        
        .info-group:hover {
            transform: translateY(-2px);
            background-color: var(--hover-color);
        }
        
        .info-label {
            font-weight: 600;
            display: block;
            margin-bottom: 8px;
            color: var(--text-light);
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .info-value {
            font-size: 18px;
            padding: 12px 15px;
            background-color: var(--secondary-color);
            border-radius: 8px;
            display: block;
            font-weight: 500;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        }
        
        .records {
            margin-top: 15px;
        }
        
        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            margin-top: 15px;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        }
        
        th, td {
            padding: 15px 20px;
            text-align: left;
        }
        
        th {
            background-color: var(--primary-color);
            color: white;
            font-weight: 500;
            text-transform: uppercase;
            font-size: 14px;
            letter-spacing: 0.5px;
        }
        
        tr:nth-child(even) {
            background-color: var(--secondary-color);
        }
        
        tr {
            transition: all 0.2s;
        }
        
        tr:hover {
            background-color: var(--hover-color);
            transform: scale(1.005);
        }
        
        td {
            border-bottom: 1px solid var(--border-color);
        }
        
        .download-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 15px;
            background-color: var(--accent-color);
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s;
        }
        
        .download-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(16, 185, 129, 0.2);
        }
        
        .no-records {
            text-align: center;
            padding: 40px 20px;
            background-color: var(--secondary-color);
            border-radius: 12px;
            color: var(--text-light);
            font-size: 16px;
            box-shadow: inset 0 2px 5px rgba(0, 0, 0, 0.05);
        }
        
        .no-records i {
            font-size: 40px;
            color: var(--text-light);
            margin-bottom: 15px;
            opacity: 0.7;
        }
        
        .badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 50px;
            font-size: 14px;
            font-weight: 500;
            background-color: var(--primary-color);
            color: white;
            margin-left: 10px;
        }
        
        @media (max-width: 768px) {
            .container {
                margin: 20px 15px;
            }
            
            .header-card, .content-card {
                padding: 20px;
            }
            
            .patient-info {
                grid-template-columns: 1fr;
            }
            
            h2 {
                font-size: 24px;
            }
            
            th, td {
                padding: 12px 10px;
                font-size: 14px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header-card">
            <a href="adminmanagepatient.php" class="back-button">
                <i class="fas fa-arrow-left"></i> Back to Patient List
            </a>
            <h2><?php echo htmlspecialchars($patient['name']); ?></h2>
            <div class="patient-meta">
                ID: <?php echo htmlspecialchars($patient['id']); ?> | 
                <?php echo htmlspecialchars($patient['age']); ?> Years | 
                <?php echo htmlspecialchars($patient['gender']); ?>
            </div>
        </div>
        
        <div class="content-card">
            <h3>
                <i class="fas fa-user-circle"></i> Personal Information
            </h3>
            <div class="patient-info">
                <div class="info-group">
                    <span class="info-label">Full Name</span>
                    <span class="info-value"><?php echo htmlspecialchars($patient['name']); ?></span>
                </div>
                
                <div class="info-group">
                    <span class="info-label">Age</span>
                    <span class="info-value"><?php echo htmlspecialchars($patient['age']); ?> Years</span>
                </div>
                
                <div class="info-group">
                    <span class="info-label">Gender</span>
                    <span class="info-value"><?php echo htmlspecialchars($patient['gender']); ?></span>
                </div>
                
                <div class="info-group">
                    <span class="info-label">Contact Number</span>
                    <span class="info-value">
                        <i class="fas fa-phone fa-sm" style="color: var(--text-light);"></i> 
                        <?php echo htmlspecialchars($patient['phone']); ?>
                    </span>
                </div>
                
                <div class="info-group">
                    <span class="info-label">Email Address</span>
                    <span class="info-value">
                        <i class="fas fa-envelope fa-sm" style="color: var(--text-light);"></i> 
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
                                    <i class="fas fa-file-pdf" style="color: var(--danger-color);"></i>
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
                        <p>No medical records found for this patient.</p>
                        <p style="margin-top: 10px; font-size: 14px;">Records will appear here once uploaded.</p>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
</body>
</html>