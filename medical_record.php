<?php
session_start();
require 'db_connection.php'; // Include your database connection file
include 'patientheader2.php';

// Check if the user is logged in
if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit;
}

$patient_id = $_SESSION['id']; // Logged-in patient's ID

// Fetch uploaded records from the database
$stmt = $conn->prepare("SELECT record_id, record_filename, uploaded_at FROM medical_records WHERE patient_id = ? ORDER BY uploaded_at DESC");
$stmt->bind_param("i", $patient_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Medical Records</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        
        
        .container2 {
            max-width: 900px;
            margin: 40px auto;
            background: white;
            box-shadow: 0 8px 24px rgba(149, 157, 165, 0.2);
            overflow: hidden;
        }
        
        .header {
            background: linear-gradient(135deg, #3a8ffe 0%, #1a6dff 100%);
            color: white;
            padding: 25px 30px;
            text-align: center;
        }
        
        .header h2 {
            font-weight: 600;
            font-size: 1.8rem;
            margin-bottom: 8px;
        }
        
        .header p {
            opacity: 0.9;
            font-size: 1rem;
        }
        
        .content {
            padding: 30px;
        }
        
        .record-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        
        .record-card {
            background: #f8fafc;
            border-radius: 8px;
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border: 1px solid #e5e7eb;
        }
        
        .record-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }
        
        .record-header {
            background: #e6f0ff;
            padding: 15px;
            border-bottom: 1px solid #d1e0ff;
        }
        
        .file-icon {
            background: #1a6dff;
            color: white;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            margin-bottom: 10px;
        }
        
        .record-id {
            font-size: 0.85rem;
            color: #4b5563;
            margin-bottom: 5px;
        }
        
        .file-name {
            font-weight: 600;
            margin-bottom: 5px;
            color: #1a6dff;
            word-break: break-all;
        }
        
        .record-body {
            padding: 15px;
        }
        
        .date {
            display: flex;
            align-items: center;
            color: #6b7280;
            font-size: 0.9rem;
            margin-bottom: 15px;
        }
        
        .date i {
            margin-right: 5px;
            color: #9ca3af;
        }
        
        .download-btn {
            display: inline-block;
            background: #1a6dff;
            color: white;
            padding: 8px 15px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 500;
            transition: background 0.3s ease;
            cursor: pointer;
        }
        
        .download-btn:hover {
            background: #0054d1;
        }
        
        .download-btn i {
            margin-right: 5px;
        }
        
        .empty-state {
            text-align: center;
            padding: 40px 20px;
        }
        
        .empty-icon {
            font-size: 5rem;
            color: #d1d5db;
            margin-bottom: 20px;
        }
        
        .empty-text {
            color: #6b7280;
            font-size: 1.1rem;
            margin-bottom: 25px;
        }
        
        .upload-btn {
            display: inline-block;
            background: linear-gradient(135deg, #3a8ffe 0%, #1a6dff 100%);
            color: white;
            padding: 12px 24px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .upload-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(26, 109, 255, 0.3);
        }
        
        .actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .upload-new {
            display: inline-flex;
            align-items: center;
            background: #1a6dff;
            color: white;
            padding: 10px 18px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .upload-new:hover {
            background: #0054d1;
        }
        
        .upload-new i {
            margin-right: 5px;
        }
        
        .search-bar {
            position: relative;
            max-width: 300px;
        }
        
        .search-bar input {
            width: 100%;
            padding: 10px 15px;
            padding-left: 40px;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            font-size: 0.95rem;
            transition: border 0.3s ease;
        }
        
        .search-bar input:focus {
            outline: none;
            border-color: #3a8ffe;
        }
        
        .search-bar i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
        }
        
        .file-type {
            position: absolute;
            top: 15px;
            right: 15px;
            background: #e6f0ff;
            color: #1a6dff;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: 500;
        }
        
        @media (max-width: 768px) {
            .record-grid {
                grid-template-columns: 1fr;
            }
            
            .actions {
                flex-direction: column;
                align-items: stretch;
                gap: 15px;
            }
            
            .search-bar {
                max-width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="container2">
        <div class="header">
            <h2>Your Medical Records</h2>
            <p>Securely access and manage your uploaded medical documents</p>
        </div>
        
        <div class="content">
            <?php if ($result->num_rows > 0): ?>
                <div class="actions">
                    <a href="addmedicalrecords.php" class="upload-new">
                        <i class="fas fa-plus"></i> Upload New Record
                    </a>
                    <a href="patientupdatebutton.php" class="upload-new">
                         Back to Profile
                    </a>
                    <div class="search-bar">
                        <i class="fas fa-search"></i>
                        <input type="text" id="search-records" placeholder="Search records...">
                    </div>
                </div>
                
                <div class="record-grid" id="records-container">
                    <?php while ($row = $result->fetch_assoc()): 
                        $file_ext = pathinfo($row['record_filename'], PATHINFO_EXTENSION);
                        $file_icon = 'fas fa-file-medical';
                        
                        // Determine file icon based on file extension
                        if (in_array($file_ext, ['pdf'])) {
                            $file_icon = 'fas fa-file-pdf';
                        } elseif (in_array($file_ext, ['jpg', 'jpeg', 'png'])) {
                            $file_icon = 'fas fa-file-image';
                        } elseif (in_array($file_ext, ['doc', 'docx'])) {
                            $file_icon = 'fas fa-file-word';
                        }
                    ?>
                        <div class="record-card">
                            <div class="record-header">
                                <div class="file-icon">
                                    <i class="<?= $file_icon ?>"></i>
                                </div>
                                <div class="record-id">Record ID: <?= $row['record_id'] ?></div>
                                <div class="file-name"><?= basename($row['record_filename']) ?></div>
                                <span class="file-type"><?= strtoupper($file_ext) ?></span>
                            </div>
                            <div class="record-body">
                                <div class="date">
                                    <i class="far fa-calendar-alt"></i>
                                    <?= date('F j, Y, g:i a', strtotime($row['uploaded_at'])) ?>
                                </div>
                                <a href="<?= $row['record_filename'] ?>" target="_blank" download class="download-btn">
                                    <i class="fas fa-download"></i> Download
                                </a>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <div class="empty-icon">
                        <i class="fas fa-folder-open"></i>
                    </div>
                    <div class="empty-text">You haven't uploaded any medical records yet.</div>
                    <a href="addmedicalrecords.php" class="upload-btn">
                        <i class="fas fa-plus"></i> Upload Your First Record
                    </a>
                    <a href="patientupdatebutton.php" class="upload-btn">
                        Back to Profile
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <script>
        // Search functionality
        document.getElementById('search-records').addEventListener('keyup', function() {
            const searchTerm = this.value.toLowerCase();
            const records = document.querySelectorAll('.record-card');
            
            records.forEach(record => {
                const fileName = record.querySelector('.file-name').textContent.toLowerCase();
                const recordId = record.querySelector('.record-id').textContent.toLowerCase();
                
                if (fileName.includes(searchTerm) || recordId.includes(searchTerm)) {
                    record.style.display = 'block';
                } else {
                    record.style.display = 'none';
                }
            });
        });
    </script>
</body>
</html>