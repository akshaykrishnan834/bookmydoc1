<?php
session_start();
if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "bookmydoc";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$doctor_id = $_SESSION['id'];

// Fetch existing details
$stmt = $conn->prepare("SELECT age, qualifications, experience, specialization, degree_certificate, address, status FROM doctorreg WHERE id = ?");
$stmt->bind_param("i", $doctor_id);
$stmt->execute();
$result = $stmt->get_result();
$doctor = $result->fetch_assoc();
$stmt->close();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $age = $_POST['age'];
    $qualifications = $_POST['qualifications'];
    $experience = $_POST['experience'];
    $specialization = $_POST['specialization'];
    $address = $_POST['address'];

    // Ensure the uploads directory exists
    $upload_dir = 'uploads/degrees/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true); // Create directory if not exists
    }

    // Handle file upload
    $new_file_path = $doctor['degree_certificate']; // Default value if no new file uploaded
    if (isset($_FILES['degree_certificate']) && $_FILES['degree_certificate']['error'] === UPLOAD_ERR_OK) {
        $file_tmp = $_FILES['degree_certificate']['tmp_name'];
        $file_name = basename($_FILES['degree_certificate']['name']);
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        // Only allow PDF files
        if ($file_ext === 'pdf') {
            $new_file_path = $upload_dir . $doctor_id . "_degree.pdf"; // Store file as id_degree.pdf

            if (move_uploaded_file($file_tmp, $new_file_path)) {
                echo "<div class='alert alert-success'>Degree certificate uploaded successfully.</div>";
            } else {
                echo "<div class='alert alert-error'>Failed to upload file.</div>";
            }
        } else {
            echo "<div class='alert alert-error'>Only PDF files are allowed.</div>";
        }
    }

    // Update database with pending status
    $update_stmt = $conn->prepare("UPDATE doctorreg SET age=?, qualifications=?, experience=?, specialization=?, degree_certificate=?, address=?, status='pending' WHERE id=?");
    $update_stmt->bind_param("isssssi", $age, $qualifications, $experience, $specialization, $new_file_path, $address, $doctor_id);
    $update_stmt->execute();
    $update_stmt->close();

    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Profile - BookMyDoc</title>
    <style>
        :root {
            --primary-color: #2a6dd0;
            --primary-dark: #1d4e96;
            --primary-light: #eef5ff;
            --success-color: #28a745;
            --error-color: #dc3545;
            --text-color: #333;
            --text-light: #666;
            --border-color: #e1e4e8;
            --border-radius: 10px;
            --shadow: 0 2px 15px rgba(0, 0, 0, 0.1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
        }

        body {
            background: linear-gradient(135deg, #f6f9ff 0%, #f1f6ff 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            line-height: 1.6;
        }

        .container {
            background: white;
            padding: 40px;
            max-width: 800px;
            width: 100%;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
        }

        .header {
            text-align: center;
            margin-bottom: 40px;
        }

        .header h2 {
            color: var(--primary-color);
            font-size: 2rem;
            margin-bottom: 10px;
        }

        .header p {
            color: var(--text-light);
            font-size: 1.1rem;
        }

        .form-group {
            margin-bottom: 25px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            color: var(--text-color);
            font-weight: 500;
        }

        input, textarea, select {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid var(--border-color);
            border-radius: var(--border-radius);
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        input:focus, textarea:focus, select:focus {
            border-color: var(--primary-color);
            outline: none;
            box-shadow: 0 0 0 3px rgba(42, 109, 208, 0.1);
        }

        .file-input {
            padding: 20px;
            background: var(--primary-light);
            border: 2px dashed var(--primary-color);
            text-align: center;
            cursor: pointer;
            position: relative;
        }

        .file-input input {
            opacity: 0;
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            cursor: pointer;
        }

        .file-input i {
            font-size: 24px;
            color: var(--primary-color);
            margin-bottom: 10px;
        }

        .file-input p {
            color: var(--primary-color);
            font-weight: 500;
        }

        button {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
            color: white;
            padding: 14px 28px;
            border: none;
            border-radius: var(--border-radius);
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            width: 100%;
            transition: all 0.3s ease;
        }

        button:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(42, 109, 208, 0.2);
        }

        button:disabled {
            background: var(--text-light);
            cursor: not-allowed;
            transform: none;
        }

        .profile-details {
            background: var(--primary-light);
            padding: 30px;
            border-radius: var(--border-radius);
        }

        .detail-item {
            display: flex;
            margin-bottom: 20px;
            padding-bottom: 20px;
            border-bottom: 1px solid rgba(42, 109, 208, 0.1);
        }

        .detail-item:last-child {
            margin-bottom: 0;
            padding-bottom: 0;
            border-bottom: none;
        }

        .detail-label {
            width: 200px;
            font-weight: 600;
            color: var(--primary-color);
        }

        .detail-value {
            flex: 1;
            color: var(--text-color);
        }

        .alert {
            padding: 15px 20px;
            border-radius: var(--border-radius);
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .alert-success {
            background-color: #d4edda;
            color: var(--success-color);
        }

        .alert-error {
            background-color: #f8d7da;
            color: var(--error-color);
        }

        .certificate-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: var(--primary-color);
            text-decoration: none;
            padding: 8px 16px;
            background: white;
            border-radius: var(--border-radius);
            transition: all 0.3s ease;
        }

        .certificate-link:hover {
            background: var(--primary-color);
            color: white;
        }

        @media (max-width: 768px) {
            .container {
                padding: 20px;
            }

            .detail-item {
                flex-direction: column;
            }

            .detail-label {
                width: 100%;
                margin-bottom: 5px;
            }
        }
        .back-button {
            background: white;
            color: var(--primary-color);
            padding: 12px 24px;
            border: 2px solid var(--primary-color);
            border-radius: var(--border-radius);
            text-decoration: none;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
        }

        .back-button:hover {
            background: var(--primary-color);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(42, 109, 208, 0.15);
        }

    </style>
</head>
<body>
<div class="container">
        <div class="header">
            <h2>Doctor Profile Update</h2>
            <p>Complete your professional profile to get started</p>
        </div>

        <?php if (empty($doctor['qualifications']) || empty($doctor['specialization'])): ?>
            <form action="" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="age">Age</label>
                    <input type="number" id="age" name="age" required min="25" max="80" placeholder="Enter your age">
                </div>

                <div class="form-group">
                    <label for="qualifications">Professional Qualifications</label>
                    <textarea id="qualifications" name="qualifications" required rows="3" 
                        placeholder="Enter your qualifications (e.g., MBBS, MD Cardiology)"></textarea>
                </div>

                <div class="form-group">
                    <label for="experience">Years of Experience</label>
                    <input type="number" id="experience" name="experience" required min="0" max="50" 
                        placeholder="Enter years of experience">
                </div>

                <div class="form-group">
                    <label for="specialization">Medical Specialization</label>
                    <input type="text" id="specialization" name="specialization" required 
                        placeholder="Enter your specialization">
                </div>
                
                <div class="form-group">
                    <label for="address">Address</label>
                    <textarea id="address" name="address" required rows="3" 
                        placeholder="Enter your clinic/hospital address"></textarea>
                </div>

                <div class="form-group">
                    <label>Medical Degree Certificate</label>
                    <div class="file-input">
                        <input type="file" name="degree_certificate" accept="application/pdf" required>
                        <i class="fas fa-file-medical"></i>
                        <p>Upload your degree certificate (PDF only)</p>
                    </div>
                </div>
                

                <button type="submit">
                    <i class="fas fa-user-md"></i> Complete Profile
                </button>
                
            </form>
        <?php else: ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                Your profile has been successfully updated
            </div>

            <div class="profile-details">
                <div class="detail-item">
                    <div class="detail-label">Age</div>
                    <div class="detail-value"><?= htmlspecialchars($doctor['age']) ?> years</div>
                </div>

                <div class="detail-item">
                    <div class="detail-label">Qualifications</div>
                    <div class="detail-value"><?= htmlspecialchars($doctor['qualifications']) ?></div>
                </div>

                <div class="detail-item">
                    <div class="detail-label">Experience</div>
                    <div class="detail-value"><?= htmlspecialchars($doctor['experience']) ?> years</div>
                </div>

                <div class="detail-item">
                    <div class="detail-label">Specialization</div>
                    <div class="detail-value"><?= htmlspecialchars($doctor['specialization']) ?></div>
                </div>
                
                <div class="detail-item">
                    <div class="detail-label">Address</div>
                    <div class="detail-value"><?= htmlspecialchars($doctor['address']) ?></div>
                </div>

                <div class="detail-item">
                    <div class="detail-label">Degree Certificate</div>
                    <div class="detail-value">
                        <a href="<?= htmlspecialchars($doctor['degree_certificate']) ?>" class="certificate-link" target="_blank">
                            <i class="fas fa-file-pdf"></i> View Certificate
                        </a>
                    </div>
                </div>
            </div>
            <div>
                <br>
            <a href="doctorac.php" class="back-button">
                <i class="fas fa-arrow-left"></i>
                Back to Profile
            </a>
        </div>
        <?php endif; ?>
    </div>
</body>
</html>