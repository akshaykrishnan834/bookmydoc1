<?php
session_start();
include('db_connection.php');


// Check if user is logged in
if (!isset($_SESSION['id'])) {
    // Redirect to login page if not logged in
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['id'];

// Ensure all required parameters are present
if (!isset($_GET['doctor_id']) || !isset($_GET['appointment_date']) || !isset($_GET['slot_id'])) {
    die("Invalid request. Missing required parameters.");
}

// Get and sanitize parameters
$doctor_id = intval($_GET['doctor_id']);
$appointment_date = $_GET['appointment_date'];
$slot_id = intval($_GET['slot_id']);

// Validate date format
if (!preg_match("/^\d{4}-\d{2}-\d{2}$/", $appointment_date)) {
    die("Invalid date format.");
}

// Check if date is not in the past
$today = date('Y-m-d');
if ($appointment_date < $today) {
    die("Cannot book appointments for past dates.");
}

// Fetch doctor details
$sql = "SELECT name, specialization FROM doctorreg WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $doctor_id);
$stmt->execute();
$result = $stmt->get_result();
$doctor = $result->fetch_assoc();

if (!$doctor) {
    die("Doctor not found.");
}

// Fetch slot details
$sql = "SELECT start_time, end_time FROM doctor_availability WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $slot_id);
$stmt->execute();
$result = $stmt->get_result();
$slot = $result->fetch_assoc();

if (!$slot) {
    die("Time slot not found.");
}

// Initialize variables
$message = "";
$status = "";

// Check if slot is already booked for this date
$sql = "SELECT id FROM appointment_requests 
        WHERE doctor_id = ? AND slot_id = ? AND appointment_date = ? 
        AND (status = 'Pending' OR status = 'Approved')";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iis", $doctor_id, $slot_id, $appointment_date);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $message = "This time slot has already been booked. Please select another time slot.";
    $status = "error";
} else {
    // Process the appointment request when form is submitted
    if (isset($_POST['confirm'])) {
        // Insert into database
        $sql = "INSERT INTO appointment_requests (user_id, doctor_id, slot_id, appointment_date, status) 
                VALUES (?, ?, ?, ?, 'Pending')";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iiis", $user_id, $doctor_id, $slot_id, $appointment_date);
        
        if ($stmt->execute()) {
            $message = "Your appointment request has been submitted successfully. You will be notified once it's approved.";
            $status = "success";
        } else {
            $message = "There was an error processing your request: " . $conn->error;
            $status = "error";
        }
    }
}

// Fetch user details
$sql = "SELECT name, email, phone FROM patientreg WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirm Appointment</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #3498db;
            --accent-color: #e74c3c;
            --success-color: #2ecc71;
            --error-color: #e74c3c;
            --background-color: #f8f9fa;
        }

        body {
            background-color: var(--background-color);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .confirmation-container {
            max-width: 800px;
            margin: 2rem auto;
            padding: 0 1rem;
        }

        .page-title {
            color: var(--primary-color);
            margin-bottom: 2rem;
            font-weight: 600;
            position: relative;
            padding-bottom: 1rem;
            text-align: center;
        }

        .page-title::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 100px;
            height: 3px;
            background-color: var(--secondary-color);
        }

        .confirmation-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
            border: none;
            overflow: hidden;
        }

        .card-header {
            background-color: var(--secondary-color);
            color: white;
            padding: 1.5rem;
            border-bottom: none;
            font-weight: 600;
            font-size: 1.2rem;
        }

        .appointment-details {
            padding: 2rem;
        }

        .detail-row {
            margin-bottom: 1.5rem;
            display: flex;
            align-items: flex-start;
        }

        .detail-icon {
            width: 40px;
            height: 40px;
            background-color: #e8f4fd;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 1rem;
            flex-shrink: 0;
        }

        .detail-icon i {
            color: var(--secondary-color);
        }

        .detail-content {
            flex-grow: 1;
        }

        .detail-label {
            font-weight: 600;
            color: var(--primary-color);
            margin-bottom: 0.2rem;
        }

        .detail-value {
            color: #666;
        }

        .message-box {
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: flex-start;
        }

        .message-box i {
            margin-right: 1rem;
            font-size: 1.5rem;
        }

        .success-message {
            background-color: rgba(46, 204, 113, 0.1);
            border: 1px solid var(--success-color);
        }

        .success-message i {
            color: var(--success-color);
        }

        .error-message {
            background-color: rgba(231, 76, 60, 0.1);
            border: 1px solid var(--error-color);
        }

        .error-message i {
            color: var(--error-color);
        }

        .btn-container {
            display: flex;
            justify-content: space-between;
            margin-top: 2rem;
        }

        .btn-back {
            background-color: #95a5a6;
            color: white;
            border: none;
            padding: 0.8rem 2rem;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-back:hover {
            background-color: #7f8c8d;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(127, 140, 141, 0.3);
        }

        .btn-confirm {
            background-color: var(--secondary-color);
            color: white;
            border: none;
            padding: 0.8rem 2rem;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-confirm:hover {
            background-color: #2980b9;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(52, 152, 219, 0.3);
        }

        .divider {
            height: 1px;
            background-color: #eee;
            margin: 2rem 0;
        }

        .terms {
            background-color: #f8f9fa;
            padding: 1rem;
            border-radius: 8px;
            margin-top: 1.5rem;
        }

        .terms-title {
            font-weight: 600;
            color: var(--primary-color);
            margin-bottom: 0.5rem;
        }

        .terms-list {
            padding-left: 1.5rem;
            color: #666;
        }

        .terms-list li {
            margin-bottom: 0.5rem;
        }
    </style>
</head>
<body>

<div class="confirmation-container">
    <h2 class="page-title">Appointment Confirmation</h2>
    
    <?php if (isset($message) && !empty($message)): ?>
        <div class="message-box <?php echo $status === 'success' ? 'success-message' : 'error-message'; ?>">
            <i class="fas <?php echo $status === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'; ?>"></i>
            <div>
                <h5><?php echo $status === 'success' ? 'Success!' : 'Error!'; ?></h5>
                <p><?php echo $message; ?></p>
                <?php if ($status === 'success'): ?>
                    <p>You can view your appointment status in your dashboard.</p>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
    
    <div class="card confirmation-card">
        <div class="card-header">
            <i class="fas fa-calendar-check me-2"></i>
            Review Your Appointment Details
        </div>
        
        <div class="appointment-details">
            <div class="detail-row">
                <div class="detail-icon">
                    <i class="fas fa-user-md"></i>
                </div>
                <div class="detail-content">
                    <div class="detail-label">Doctor</div>
                    <div class="detail-value">Dr. <?php echo htmlspecialchars($doctor['name']); ?></div>
                    <div class="detail-value text-muted"><?php echo htmlspecialchars($doctor['specialization']); ?></div>
                </div>
            </div>
            
            <div class="detail-row">
                <div class="detail-icon">
                    <i class="far fa-calendar-alt"></i>
                </div>
                <div class="detail-content">
                    <div class="detail-label">Date</div>
                    <div class="detail-value"><?php echo date('l, F j, Y', strtotime($appointment_date)); ?></div>
                </div>
            </div>
            
            <div class="detail-row">
                <div class="detail-icon">
                    <i class="far fa-clock"></i>
                </div>
                <div class="detail-content">
                    <div class="detail-label">Time</div>
                    <div class="detail-value">
                        <?php echo date('g:i A', strtotime($slot['start_time'])); ?> - 
                        <?php echo date('g:i A', strtotime($slot['end_time'])); ?>
                    </div>
                </div>
            </div>
            
            <div class="detail-row">
                <div class="detail-icon">
                    <i class="fas fa-user"></i>
                </div>
                <div class="detail-content">
                    <div class="detail-label">Patient Information</div>
                    <div class="detail-value"><?php echo htmlspecialchars($user['name']); ?></div>
                    <div class="detail-value text-muted"><?php echo htmlspecialchars($user['email']); ?></div>
                    <div class="detail-value text-muted"><?php echo htmlspecialchars($user['phone']); ?></div>
                </div>
            </div>
            
            <div class="divider"></div>
            
            <div class="terms">
                <div class="terms-title"><i class="fas fa-info-circle me-2"></i>Important Notes</div>
                <ul class="terms-list">
                    <li>Please arrive 15 minutes before your scheduled appointment time.</li>
                    <li>Bring any relevant medical records or test results to your appointment.</li>
                    <li>Your appointment request will be reviewed by the doctor and you will receive a confirmation.</li>
                    <li>You can cancel your appointment up to 24 hours before the scheduled time.</li>
                    <li>If you need to reschedule, please contact the clinic directly.</li>
                </ul>
            </div>
            
            <?php if (!isset($message) || $status !== 'success'): ?>
                <form method="POST" action="confirm_appointment.php?doctor_id=<?php echo $doctor_id; ?>&appointment_date=<?php echo $appointment_date; ?>&slot_id=<?php echo $slot_id; ?>">
                    <div class="btn-container">
                        <a href="book_appointment_page.php?doctor_id=<?php echo $doctor_id; ?>&appointment_date=<?php echo $appointment_date; ?>" class="btn btn-back">
                            <i class="fas fa-arrow-left me-2"></i>Select Another Time
                        </a>
                        
                        <button type="submit" name="confirm" class="btn btn-confirm">
                            <i class="fas fa-check-circle me-2"></i>Confirm Appointment
                        </button>
                    </div>
                </form>
            <?php else: ?>
                <div class="btn-container">
                    <a href="browsedoct.php" class="btn btn-confirm">
                        <i class="fas fa-home me-2"></i>Go to Dashboard
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
</body>
</html>