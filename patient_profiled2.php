<?php
session_start();
include('db_connection.php');

if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "<p>Invalid patient ID.</p>";
    exit;
}

$patient_id = intval($_GET['id']); // Secure patient ID input

// Fetch patient details from patientreg table
$sql = "SELECT id, name, phone, profile_pic, age, dob, email, gender FROM patientreg WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $patient_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $patient = $result->fetch_assoc();
} else {
    echo "<p>Patient not found.</p>";
    exit;
}
$stmt->close();

// Fetch medical records
$sql_medical = "SELECT record_id, record_filename, uploaded_at FROM medical_records WHERE patient_id = ? ORDER BY uploaded_at DESC";
$stmt_medical = $conn->prepare($sql_medical);
$stmt_medical->bind_param("i", $patient_id);
$stmt_medical->execute();
$medical_records = $stmt_medical->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt_medical->close();

// Fetch consultation notes
$sql_notes = "SELECT consultation_notes, appointment_date FROM appointment_requests
              WHERE user_id = ? AND consultation_notes IS NOT NULL 
              ORDER BY appointment_date DESC";
$stmt_notes = $conn->prepare($sql_notes);
$stmt_notes->bind_param("i", $patient_id);
$stmt_notes->execute();
$consultation_notes = $stmt_notes->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt_notes->close();

// Handle file upload
$message = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_FILES['file']) && isset($_POST['patient_id'])) {
        $upload_dir = "uploads/";
        $file_name = basename($_FILES["file"]["name"]);
        $target_file = $upload_dir . time() . "_" . $file_name;
        $file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        $allowed_types = ["pdf", "doc", "docx", "jpg", "png"];
        if (!in_array($file_type, $allowed_types)) {
            $message = "<div class='alert alert-danger'><i class='fas fa-exclamation-circle'></i> Invalid file type. Only PDF, DOC, JPG, PNG allowed.</div>";
        } else {
            if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) {
                $stmt = $conn->prepare("INSERT INTO medical_records (patient_id, record_filename) VALUES (?, ?)");
                $stmt->bind_param("is", $patient_id, $target_file);

                if ($stmt->execute()) {
                    $message = "<div class='alert alert-success'><i class='fas fa-check-circle'></i> Record uploaded successfully!</div>";
                } else {
                    $message = "<div class='alert alert-danger'><i class='fas fa-exclamation-circle'></i> Error uploading record.</div>";
                }
                $stmt->close();
            } else {
                $message = "<div class='alert alert-danger'><i class='fas fa-exclamation-circle'></i> Failed to upload file.</div>";
            }
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Profile | Medical Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #4361ee;
            --secondary: #3a0ca3;
            --accent: #4cc9f0;
            --light: #f8f9fa;
            --dark: #212529;
            --success: #4CAF50;
            --danger: #FF5252;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, var(--primary), var(--accent));
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 30px 15px;
            color: var(--dark);
        }

        .profile-container {
            width: 100%;
            max-width: 1000px;
            background: rgba(255, 255, 255, 0.95);
            padding: 30px;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15);
            transition: all 0.3s ease;
        }

        .profile-header {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #eee;
            padding-bottom: 25px;
        }

        @media (min-width: 768px) {
            .profile-header {
                flex-direction: row;
                text-align: left;
                align-items: flex-start;
            }
        }

        .profile-pic-container {
            position: relative;
            margin-bottom: 20px;
        }

        @media (min-width: 768px) {
            .profile-pic-container {
                margin-right: 30px;
                margin-bottom: 0;
            }
        }

        .profile-pic {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            border: 5px solid white;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        .profile-badge {
            position: absolute;
            bottom: 10px;
            right: 10px;
            background: var(--primary);
            color: white;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
        }

        .patient-info {
            flex-grow: 1;
        }

        .patient-info h3 {
            color: var(--primary);
            font-weight: 600;
            margin-bottom: 15px;
            font-size: 28px;
        }

        .info-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 12px;
        }

        @media (min-width: 768px) {
            .info-grid {
                grid-template-columns: 1fr 1fr;
            }
        }

        .info-item {
            display: flex;
            align-items: center;
        }

        .info-item i {
            color: var(--primary);
            margin-right: 10px;
            font-size: 18px;
            width: 25px;
            text-align: center;
        }

        .section-title {
            color: var(--primary);
            font-weight: 600;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid var(--accent);
            display: flex;
            align-items: center;
        }

        .section-title i {
            margin-right: 10px;
            color: var(--accent);
        }

        .table-medical {
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        }

        .table-medical th {
            background-color: var(--primary);
            color: white;
            font-weight: 500;
            border: none;
            padding: 12px 15px;
        }

        .table-medical td {
            padding: 12px 15px;
            vertical-align: middle;
        }

        .table-medical tr:nth-child(even) {
            background-color: rgba(67, 97, 238, 0.05);
        }

        .table-medical tr:hover {
            background-color: rgba(67, 97, 238, 0.1);
        }

        .btn {
            border: none;
            font-weight: 500;
            padding: 10px 20px;
            border-radius: 50px;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn i {
            font-size: 16px;
        }

        .btn-primary {
            background: var(--primary);
            color: white;
            box-shadow: 0 4px 12px rgba(67, 97, 238, 0.3);
        }

        .btn-primary:hover {
            background: var(--secondary);
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(67, 97, 238, 0.4);
        }

        .btn-success {
            background: var(--success);
            color: white;
            box-shadow: 0 4px 12px rgba(76, 175, 80, 0.3);
        }

        .btn-success:hover {
            background: #3d8b40;
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(76, 175, 80, 0.4);
        }

        .btn-upload {
            background: var(--accent);
            color: white;
            box-shadow: 0 4px 12px rgba(76, 201, 240, 0.3);
            width: 100%;
        }

        .btn-upload:hover {
            background: var(--secondary);
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(76, 201, 240, 0.4);
        }

        .upload-form {
            background: rgba(67, 97, 238, 0.05);
            padding: 20px;
            border-radius: 15px;
            margin-bottom: 20px;
        }

        .form-label {
            font-weight: 500;
            color: var(--dark);
        }

        .form-control {
            border-radius: 10px;
            padding: 12px 15px;
            border: 1px solid #e0e0e0;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 0.25rem rgba(67, 97, 238, 0.25);
        }

        .empty-records {
            text-align: center;
            padding: 30px;
            background: rgba(67, 97, 238, 0.05);
            border-radius: 10px;
            color: #888;
        }

        .empty-records i {
            font-size: 48px;
            color: #ccc;
            margin-bottom: 15px;
        }

        .alert {
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .alert i {
            font-size: 20px;
        }

        .alert-success {
            background-color: rgba(76, 175, 80, 0.1);
            border-left: 4px solid var(--success);
            color: #388e3c;
        }

        .alert-danger {
            background-color: rgba(255, 82, 82, 0.1);
            border-left: 4px solid var(--danger);
            color: #d32f2f;
        }

        .file-info {
            font-size: 14px;
            color: #666;
            margin-top: 5px;
        }

        .card-hover {
            transition: all 0.3s ease;
        }
        
        .card-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body>

<div class="profile-container card-hover">
    <div class="profile-header">
        <div class="profile-pic-container">
            <img src="<?= htmlspecialchars($patient['profile_pic'] ?: 'images/profilepicdoct.jpg') ?>" 
                 onerror="this.onerror=null; this.src='images/profilepicdoct.jpg';" 
                 class="profile-pic" alt="Patient Picture">
            <span class="profile-badge"><i class="fas fa-user"></i></span>
        </div>
        <div class="patient-info">
            <h3><?= htmlspecialchars($patient['name']); ?></h3>
            <div class="info-grid">
                <div class="info-item">
                    <i class="fas fa-id-card"></i>
                    <span><strong>ID:</strong> <?= htmlspecialchars($patient['id']); ?></span>
                </div>
                <div class="info-item">
                    <i class="fas fa-envelope"></i>
                    <span><strong>Email:</strong> <?= htmlspecialchars($patient['email']); ?></span>
                </div>
                <div class="info-item">
                    <i class="fas fa-phone"></i>
                    <span><strong>Phone:</strong> <?= htmlspecialchars($patient['phone']); ?></span>
                </div>
                <div class="info-item">
                    <i class="fas fa-venus-mars"></i>
                    <span><strong>Gender:</strong> <?= htmlspecialchars($patient['gender']); ?></span>
                </div>
                <div class="info-item">
                    <i class="fas fa-birthday-cake"></i>
                    <span><strong>Age:</strong> <?= htmlspecialchars($patient['age']); ?></span>
                </div>
                <div class="info-item">
                    <i class="fas fa-calendar-alt"></i>
                    <span><strong>DOB:</strong> <?= htmlspecialchars($patient['dob']); ?></span>
                </div>
            </div>
        </div>
    </div>

    <h4 class="section-title"><i class="fas fa-notes-medical"></i> Consultation History</h4>
    
    <?php if (!empty($consultation_notes)): ?>
        <div class="table-responsive">
            <table class="table table-bordered table-medical">
                <thead>
                    <tr>
                        <th><i class="fas fa-calendar"></i> Appointment Date</th>
                        <th><i class="fas fa-file-medical-alt"></i> Consultation Notes</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($consultation_notes as $note): ?>
                        <tr>
                            <td><?= date("M d, Y", strtotime($note['appointment_date'])); ?></td>
                            <td><?= nl2br(htmlspecialchars($note['consultation_notes'])); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="empty-records">
            <i class="fas fa-clipboard-list d-block"></i>
            <p>No consultation notes available for this patient.</p>
        </div>
    <?php endif; ?>

    <h4 class="section-title"><i class="fas fa-file-medical"></i> Medical Records</h4>

    <?php if (!empty($medical_records)): ?>
        <div class="table-responsive">
            <table class="table table-bordered table-medical">
                <thead>
                    <tr>
                        <th><i class="fas fa-hashtag"></i> Record ID</th>
                        <th><i class="fas fa-file-alt"></i> Filename</th>
                        <th><i class="fas fa-calendar-day"></i> Uploaded Date</th>
                        <th><i class="fas fa-download"></i> Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($medical_records as $record): ?>
                        <tr>
                            <td><?= $record['record_id']; ?></td>
                            <td>
                                <?php 
                                $filename = basename($record['record_filename']);
                                $file_extension = pathinfo($filename, PATHINFO_EXTENSION);
                                $icon_class = 'fa-file';
                                
                                switch($file_extension) {
                                    case 'pdf': $icon_class = 'fa-file-pdf'; break;
                                    case 'doc': 
                                    case 'docx': $icon_class = 'fa-file-word'; break;
                                    case 'jpg': 
                                    case 'jpeg': 
                                    case 'png': $icon_class = 'fa-file-image'; break;
                                }
                                ?>
                                <i class="fas <?= $icon_class; ?> me-2" style="color: var(--primary);"></i>
                                <?= htmlspecialchars($filename); ?>
                            </td>
                            <td><?= date("M d, Y", strtotime($record['uploaded_at'])); ?></td>
                            <td>
                                <a href="<?= $record['record_filename']; ?>" download class="btn btn-success">
                                    <i class="fas fa-download"></i> Download
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="empty-records">
            <i class="fas fa-folder-open d-block"></i>
            <p>No medical records available for this patient.</p>
        </div>
    <?php endif; ?>

    <h4 class="section-title mt-4"><i class="fas fa-upload"></i> Upload Medical Record</h4>

    <form action="" method="post" enctype="multipart/form-data" class="upload-form mb-3">
        <input type="hidden" name="patient_id" value="<?= $patient_id; ?>">
        <div class="mb-3">
            <label for="file" class="form-label">Choose a file to upload</label>
            <input type="file" name="file" id="file" class="form-control" required>
            <div class="file-info">
                <small><i class="fas fa-info-circle"></i> Accepted file types: PDF, DOC, DOCX, JPG, PNG</small>
            </div>
        </div>
        <button type="submit" class="btn btn-upload">
            <i class="fas fa-cloud-upload-alt"></i> Upload Medical Record
        </button>
    </form>

    <?= $message; ?>

    <div class="text-center mt-4">
        <a href="doctorprofile.php" class="btn btn-primary">
            <i class="fas fa-arrow-left"></i> Back to Dashboard
        </a>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Add animation to message alerts
    document.addEventListener('DOMContentLoaded', function() {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(alert => {
            setTimeout(() => {
                alert.style.opacity = '0';
                alert.style.transition = 'opacity 0.5s ease';
                setTimeout(() => {
                    alert.style.display = 'none';
                }, 500);
            }, 5000);
        });
    });
</script>
</body>
</html>