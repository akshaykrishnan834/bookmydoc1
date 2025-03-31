<?php
session_start();
include('db_connection.php');

// Database connection
$conn = new mysqli("localhost", "root", "", "bookmydoc");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$doctor_id = $_SESSION['id'];
$message = "";

// Fetch current consultation fee
$sql = "SELECT fees FROM doctorreg WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $doctor_id);
$stmt->execute();
$stmt->bind_result($current_fee);
$stmt->fetch();
$stmt->close();

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_fee = $_POST['consultation_fee']; // Changed to match the input name in your form
    
    if (!is_numeric($new_fee) || $new_fee < 0) {
        $message = "<div class='alert alert-danger'>Invalid fee amount. Please enter a valid number.</div>";
    } else {
        $update_sql = "UPDATE doctorreg SET fees = ? WHERE id = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("di", $new_fee, $doctor_id);
        
        if ($update_stmt->execute()) {
            $message = "<div class='alert alert-success'>Consultation fee updated successfully!</div>";
            $current_fee = $new_fee;
        } else {
            $message = "<div class='alert alert-danger'>Error updating fee. Please try again.</div>";
        }
        $update_stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Consultation Fee</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .container {
            max-width: 500px;
            background: white;
            padding: 35px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            border-top: 5px solid #4e73df;
            position: relative;
        }
        
        h2 {
            text-align: center;
            margin-bottom: 30px;
            color: #4e73df;
            font-weight: 600;
            font-size: 28px;
        }
        
        .fee-icon {
            display: flex;
            justify-content: center;
            margin-bottom: 25px;
        }
        
        .fee-icon i {
            font-size: 50px;
            color: #4e73df;
            background: rgba(78, 115, 223, 0.1);
            padding: 20px;
            border-radius: 50%;
        }
        
        .form-label {
            font-weight: 500;
            color: #555;
        }
        
        .form-control {
            padding: 12px 15px;
            border-radius: 10px;
            border: 1px solid #e0e0e0;
            font-size: 16px;
            box-shadow: none;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            border-color: #4e73df;
            box-shadow: 0 0 0 3px rgba(78, 115, 223, 0.25);
        }
        
        .input-group-text {
            background: #4e73df;
            color: white;
            border-color: #4e73df;
            font-weight: 500;
            border-radius: 10px 0 0 10px;
        }
        
        .btn-primary {
            background: #4e73df;
            border: none;
            padding: 12px;
            font-weight: 500;
            border-radius: 10px;
            margin-top: 10px;
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            background: #3a57c2;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(78, 115, 223, 0.4);
        }
        
        .btn-secondary {
            background: #6c757d;
            border: none;
            padding: 12px;
            font-weight: 500;
            border-radius: 10px;
            margin-top: 10px;
            transition: all 0.3s ease;
        }
        
        .btn-secondary:hover {
            background: #5a6268;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(108, 117, 125, 0.4);
        }
        
        .alert {
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 25px;
        }
        
        .alert-success {
            background-color: #e6fff2;
            border-color: #ccf7e5;
            color: #0d9868;
        }
        
        .alert-danger {
            background-color: #fff2f2;
            border-color: #ffd6d6;
            color: #d63031;
        }
        
        .current-fee {
            background: rgba(78, 115, 223, 0.1);
            padding: 12px;
            border-radius: 10px;
            margin-bottom: 20px;
            text-align: center;
            color: #4e73df;
            font-weight: 500;
        }
        
        .btn-back {
            position: absolute;
            top: 20px;
            left: 20px;
            background: transparent;
            color: #4e73df;
            border: none;
            font-size: 24px;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        
        .btn-back:hover {
            color: #3a57c2;
            transform: translateX(-3px);
        }
        
        .button-group {
            display: flex;
            gap: 15px;
        }
    </style>
</head>
<body>

<div class="container">
    <!-- Back Button -->
    <a href="updatedoctor.php" class="btn-back">
        <i class="fas fa-arrow-left"></i>
    </a>
    
    <h2>Update Consultation Fee</h2>
    
    <div class="fee-icon">
        <i class="fas fa-rupee-sign"></i>
    </div>
    
    <?php echo $message; ?>
    
    <div class="current-fee">
        <div class="small text-muted mb-1">Current Fee</div>
        <div class="h3">₹<?php echo htmlspecialchars($current_fee); ?></div>
    </div>
    
    <form method="POST">
        <div class="mb-4">
            <label for="consultation_fee" class="form-label">New Consultation Fee</label>
            <div class="input-group">
                <span class="input-group-text">₹</span>
                <input type="number" step="0.01" class="form-control" id="consultation_fee" name="consultation_fee" value="<?php echo htmlspecialchars($current_fee); ?>" required>
            </div>
            <div class="form-text text-muted mt-2">Enter the new consultation fee amount</div>
        </div>
        
        <div class="button-group">
            <a href="dashboard.php" class="btn btn-secondary w-50">
                <i class="fas fa-times me-2"></i> Cancel
            </a>
            <button type="submit" class="btn btn-primary w-50">
                <i class="fas fa-save me-2"></i> Update Fee
            </button>
        </div>
    </form>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>