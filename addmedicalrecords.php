<?php 
session_start(); 
require 'db_connection.php'; // Include your database connection file

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["medical_record"]) && !empty($_POST['file_name'])) {
    $patient_id = $_SESSION['id']; // Assuming the user is logged in
    $file = $_FILES["medical_record"];
    $upload_dir = "uploads/"; // Directory to store files
    $user_file_name = preg_replace("/[^a-zA-Z0-9_\-]/", "_", $_POST['file_name']); // Sanitize file name input
    
    // Ensure the directory exists
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }
    
    // Get the file extension
    $file_ext = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));
    
    // Allowed file types
    $allowed_types = ['pdf', 'jpg', 'jpeg', 'png', 'doc', 'docx'];
    
    if (in_array($file_ext, $allowed_types)) {
        $file_path = $upload_dir . $user_file_name . "." . $file_ext; // Save with user-defined name
        
        if (move_uploaded_file($file["tmp_name"], $file_path)) {
            // Insert into database
            $stmt = $conn->prepare("INSERT INTO medical_records (patient_id, record_filename) VALUES (?, ?)");
            $stmt->bind_param("is", $patient_id, $file_path);
            
            if ($stmt->execute()) {
                $success_message = "Medical record uploaded successfully.";
            } else {
                $error_message = "Error uploading record.";
            }
        } else {
            $error_message = "File upload failed.";
        }
    } else {
        $error_message = "Invalid file type. Please upload PDF, JPG, PNG, DOC or DOCX files only.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Medical Document</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background-color: #f5f8fc;
            color: #333;
            line-height: 1.6;
            padding: 20px;
        }
        
        .container {
            max-width: 600px;
            margin: 40px auto;
            background: white;
            border-radius: 12px;
            box-shadow: 0 8px 24px rgba(149, 157, 165, 0.2);
            overflow: hidden;
        }
        
        .header {
            background: linear-gradient(135deg, #3a8ffe 0%, #1a6dff 100%);
            color: white;
            padding: 25px 30px;
            text-align: center;
            position: relative;
        }
        
        .header h2 {
            font-weight: 600;
            font-size: 1.8rem;
            margin-bottom: 8px;
        }
        
        .form-container {
            padding: 30px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }

        .input-field {
            width: 100%;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 1rem;
            margin-bottom: 10px;
        }

        .file-upload {
            position: relative;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 35px 20px;
            border: 2px dashed #d0d7de;
            border-radius: 8px;
            background-color: #fafbfc;
            cursor: pointer;
        }
        
        .file-upload:hover {
            border-color: #3a8ffe;
            background-color: #f0f7ff;
        }
        
        .file-upload input[type="file"] {
            position: absolute;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            opacity: 0;
            cursor: pointer;
        }
        
        .file-icon {
            font-size: 48px;
            color: #3a8ffe;
            margin-bottom: 15px;
        }
        
        .file-label {
            font-weight: 500;
            color: #444;
        }
        
        .file-name {
            margin-top: 15px;
            font-weight: 500;
            display: none;
        }
        
        .submit-btn {
            width: 100%;
            padding: 14px;
            border: none;
            border-radius: 6px;
            background: linear-gradient(135deg, #3a8ffe 0%, #1a6dff 100%);
            color: white;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(26, 109, 255, 0.3);
        }
        
        .alert {
            padding: 16px;
            border-radius: 6px;
            margin-bottom: 20px;
            font-weight: 500;
        }
        
        .alert-success {
            background-color: #e3f8ec;
            color: #0a6245;
            border-left: 4px solid #0a6245;
        }
        
        .alert-error {
            background-color: #feeef0;
            color: #b91c1c;
            border-left: 4px solid #b91c1c;
        }
        
        .back-btn {
            display: inline-flex;
            align-items: center;
            padding: 10px 15px;
            background-color: rgba(255, 255, 255, 0.2);
            color: white;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 500;
            position: absolute;
            left: 20px;
            top: 50%;
            transform: translateY(-50%);
            transition: background-color 0.3s ease;
        }
        
        .back-btn:hover {
            background-color: rgba(255, 255, 255, 0.3);
        }
        
        .back-btn i {
            margin-right: 5px;
        }
        
        .button-group {
            display: flex;
            gap: 15px;
            margin-top: 15px;
        }
        
        .cancel-btn {
            flex: 1;
            padding: 14px;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            background: white;
            color: #4b5563;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-align: center;
            text-decoration: none;
            display: inline-block;
        }
        
        .cancel-btn:hover {
            background-color: #f9fafb;
        }
        
        .submit-btn {
            flex: 2;
        }
        
        @media (max-width: 768px) {
            .container {
                margin: 20px auto;
            }
            
            .header {
                padding-top: 50px;
            }
            
            .back-btn {
                top: 15px;
                left: 50%;
                transform: translateX(-50%);
                background-color: rgba(255, 255, 255, 0.3);
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            
            <h2>Upload Medical Record</h2>
            <p>Securely add your medical documents to your profile</p>
        </div>
        
        <div class="form-container">
            <?php if(isset($success_message)): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> <?php echo $success_message; ?>
                </div>
            <?php endif; ?>
            
            <?php if(isset($error_message)): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i> <?php echo $error_message; ?>
                </div>
            <?php endif; ?>
            
            <form action="" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="file_name">Enter File Name:</label>
                    <input type="text" name="file_name" id="file_name" class="input-field" placeholder="Enter file name..." required>
                </div>

                <div class="form-group">
                    <div class="file-upload">
                        <input type="file" name="medical_record" id="file-input" required>
                        <div class="file-icon">
                            <i class="fas fa-file-medical"></i>
                        </div>
                        <div class="file-label">Click to browse or drag & drop your file</div>
                    </div>
                </div>

                <div class="button-group">
                    <a href="medical_record.php" class="cancel-btn">Back to Records</a>
                    <button type="submit" class="submit-btn">
                        <i class="fas fa-upload"></i> Upload Document
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <script>
        // Display file name when selected
        document.getElementById('file-input').addEventListener('change', function(e) {
            const fileName = e.target.files[0] ? e.target.files[0].name : '';
            const fileLabel = document.querySelector('.file-label');
            
            if (fileName) {
                fileLabel.textContent = 'Selected: ' + fileName;
            } else {
                fileLabel.textContent = 'Click to browse or drag & drop your file';
            }
        });
    </script>
</body>
</html>