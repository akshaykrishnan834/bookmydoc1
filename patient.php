<?php
session_start();
include 'connect.php'; // Include database connection

// Check if user is logged in
if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}

// Get user details from session
$patient_id = $_SESSION['id'];

// Fetch patient details from database
$sql = "SELECT * FROM registrations WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $patient_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $patient = $result->fetch_assoc();
} else {
    echo "No patient found.";
    exit();
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Profile</title>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            background: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .profile-container {
            background: white;
            padding: 30px;
            border-radius: 16px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
            width: 450px;
            text-align: center;
        }
        h1 {
            color: #0077b6;
        }
        table {
            width: 100%;
            margin-top: 15px;
            border-collapse: collapse;
        }
        th, td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }
        th {
            background: #0077b6;
            color: white;
        }
        .btn {
            display: block;
            margin-top: 20px;
            padding: 12px;
            background: #0077b6;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-size: 1.1rem;
            cursor: pointer;
        }
        .btn:hover {
            background: #005f99;
        }
    </style>
</head>
<body>

<div class="profile-container">
    <h1>Welcome, <?= htmlspecialchars($patient['name']); ?></h1>

    <h2>Profile Details</h2>
    <table>
        <tr>
            <th>Email</th>
            <td><?= htmlspecialchars($patient['email']); ?></td>
        </tr>
        <tr>
            <th>Phone</th>
            <td><?= htmlspecialchars($patient['phone']); ?></td>
        </tr>
        <tr>
            <th>Age</th>
            <td><?= htmlspecialchars($patient['age']); ?></td>
        </tr>
        <tr>
            <th>Gender</th>
            <td><?= htmlspecialchars($patient['gender']); ?></td>
        </tr>
    </table>

    <a href="logout.php" class="btn">Logout</a>
</div>

</body>
</html>
