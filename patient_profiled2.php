<?php
session_start();
include('db_connection.php');

if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "<p>Invalid patient ID.</p>";
    exit;
}

$patient_id = intval($_GET['id']); // Secure the ID input

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

// Fetch medical records (only record_filename) from medical_records table
$sql_medical = "SELECT record_id, record_filename, uploaded_at FROM medical_records WHERE patient_id = ? ORDER BY uploaded_at DESC";
$stmt_medical = $conn->prepare($sql_medical);
$stmt_medical->bind_param("i", $patient_id);
$stmt_medical->execute();
$medical_records = $stmt_medical->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt_medical->close();

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Patient Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(to right, #3498db, #2ac8dd);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .profile-container {
            max-width: 700px;
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0px 5px 15px rgba(0, 0, 0, 0.2);
            text-align: center;
            animation: fadeIn 0.5s ease-in-out;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: scale(0.95); }
            to { opacity: 1; transform: scale(1); }
        }
        .profile-pic {
            width: 130px;
            height: 130px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid #007bff;
            transition: transform 0.3s ease-in-out;
        }
        .profile-pic:hover {
            transform: scale(1.1);
        }
        h2 {
            color: #007bff;
            margin-top: 10px;
        }
        .info-table {
            margin-top: 20px;
        }
        .info-table th {
            text-align: left;
            padding: 8px;
            background: #007bff;
            color: white;
        }
        .info-table td {
            padding: 8px;
            background: #f8f9fa;
            color: #333;
        }
        .medical-records {
            margin-top: 25px;
            text-align: left;
        }
        .medical-records h4 {
            color: #007bff;
            margin-bottom: 10px;
        }
        .table-medical {
            width: 100%;
            border-radius: 10px;
            overflow: hidden;
        }
        .table-medical th {
            background: #007bff;
            color: white;
            text-align: left;
            padding: 10px;
        }
        .table-medical td {
            background: #f8f9fa;
            padding: 10px;
            color: #333;
        }
        .btn-back {
            margin-top: 20px;
            background: #007bff;
            color: white;
            padding: 10px 20px;
            border-radius: 30px;
            transition: 0.3s;
            font-weight: bold;
        }
        .btn-back:hover {
            background: #0056b3;
            transform: translateY(-2px);
        }
        .btn-download {
            background: #28a745;
            color: white;
            padding: 7px 12px;
            border-radius: 5px;
            transition: 0.3s;
        }
        .btn-download:hover {
            background: #218838;
            transform: scale(1.05);
        }
        .no-records {
            color: #dc3545;
            font-weight: bold;
            text-align: center;
            margin-top: 10px;
        }
    </style>
</head>
<body>

<div class="profile-container">
    <img src="<?php echo htmlspecialchars($patient['profile_pic'] ? 'uploads/' . $patient['profile_pic'] : 'default_profile.png'); ?>" 
    onerror="this.onerror=null; this.src='images/profilepicdoct.jpg';" class="profile-pic">

    <h2><?php echo htmlspecialchars($patient['name']); ?></h2>

    <table class="info-table table table-bordered">
        <tr><th>Patient ID</th><td><?php echo htmlspecialchars($patient['id']); ?></td></tr>
        <tr><th>Name</th><td><?php echo htmlspecialchars($patient['name']); ?></td></tr>
        <tr><th>Phone</th><td><?php echo htmlspecialchars($patient['phone']); ?></td></tr>
        <tr><th>Gender</th><td><?php echo htmlspecialchars($patient['gender']); ?></td></tr>
        <tr><th>Age</th><td><?php echo htmlspecialchars($patient['age']); ?></td></tr>
        <tr><th>Date of Birth</th><td><?php echo htmlspecialchars($patient['dob']); ?></td></tr>
        <tr><th>Email</th><td><?php echo htmlspecialchars($patient['email']); ?></td></tr>
    </table>

    <div class="medical-records">
        <h4>Medical Records</h4>
        <?php if (!empty($medical_records)): ?>
            <table class="table table-bordered table-medical">
                <thead>
                    <tr>
                        <th>Record ID</th>
                        <th>Record Filename</th>
                        <th>Uploaded Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($medical_records as $record): ?>
                        <?php 
                            $file_path = "uploads/" . $record['record_filename'];
                            $file_status = file_exists($file_path) ? "Available" : "File Not Found";
                            $disabled = file_exists($file_path) ? "" : "disabled";
                        ?>
                        <tr>
                            <td><?php echo htmlspecialchars($record['record_id']); ?></td>
                            <td><?php echo htmlspecialchars($record['record_filename']) . " ($file_status)"; ?></td>
                            <td><?php echo htmlspecialchars($record['uploaded_at']); ?></td>
                            <td>
                                <a href="<?php echo $file_path; ?>" 
                                   download="<?php echo htmlspecialchars($record['record_filename']); ?>" 
                                   class="btn btn-download <?php echo $disabled; ?>">
                                   Download
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="no-records">No medical records available.</p>
        <?php endif; ?>
    </div>

    <a href="doctorprofile.php" class="btn btn-back">Back to Dashboard</a>
</div>

</body>
</html>
