<?php
session_start();
include('db_connection.php');

// Check if user is logged in
if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['id'];

// Ensure all required parameters are present
if (!isset($_GET['doctor_id']) || !isset($_GET['appointment_date']) || !isset($_GET['slot_id'])) {
    die("Invalid request. Missing required parameters.");
}

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
    // Process the appointment request
    if (isset($_POST['confirm'])) {
        $patient_condition = isset($_POST['patient_condition']) ? trim($_POST['patient_condition']) : '';

        // Insert into database
        $sql = "INSERT INTO appointment_requests (user_id, doctor_id, slot_id, appointment_date, patient_condition, status) 
                VALUES (?, ?, ?, ?, ?, 'Pending')";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iiiss", $user_id, $doctor_id, $slot_id, $appointment_date, $patient_condition);

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
            --light-grey: #95a5a6;
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

        .page-title:after {
            content: '';
            position: absolute;
            width: 70px;
            height: 3px;
            background-color: var(--secondary-color);
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
        }

        .confirmation-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .card-header {
            background-color: var(--secondary-color);
            color: white;
            padding: 1.5rem;
            font-weight: 600;
            display: flex;
            align-items: center;
        }

        .appointment-details {
            padding: 2rem;
        }

        .detail-row {
            margin-bottom: 1.5rem;
            display: flex;
            align-items: flex-start;
        }

        .detail-label {
            font-weight: 600;
            color: var(--primary-color);
            margin-bottom: 0.5rem;
        }

        .message-box {
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
        }

        .success-message {
            background-color: rgba(46, 204, 113, 0.1);
            border: 1px solid var(--success-color);
            color: var(--success-color);
        }

        .error-message {
            background-color: rgba(231, 76, 60, 0.1);
            border: 1px solid var(--error-color);
            color: var(--error-color);
        }

        .detail-icon {
            width: 40px;
            height: 40px;
            background-color: rgba(52, 152, 219, 0.1);
            color: var(--secondary-color);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            flex-shrink: 0;
        }

        .detail-content {
            flex-grow: 1;
        }

        .btn-container {
            display: flex;
            justify-content: space-between;
            margin-top: 2rem;
        }

        .btn-confirm {
            background-color: var(--secondary-color);
            color: white;
            padding: 0.8rem 2rem;
            border-radius: 8px;
            border: none;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }

        .btn-confirm:hover {
            background-color: #2980b9;
        }

        .btn-back {
            background-color: var(--light-grey);
            color: white;
            padding: 0.8rem 2rem;
            border-radius: 8px;
            border: none;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }

        .btn-back:hover {
            background-color: #7f8c8d;
        }

        .btn i {
            margin-right: 8px;
        }

        .important-notes {
            background-color: #f8f9fa;
            border-radius: 10px;
            padding: 1.5rem;
            margin-top: 2rem;
            margin-bottom: 2rem;
        }

        .notes-title {
            display: flex;
            align-items: center;
            color: var(--primary-color);
            font-weight: 600;
            margin-bottom: 1rem;
        }

        .notes-title i {
            margin-right: 10px;
            color: var(--secondary-color);
        }

        .notes-list {
            list-style: none;
            padding-left: 0;
            margin-bottom: 0;
        }

        .notes-list li {
            position: relative;
            padding-left: 1.5rem;
            margin-bottom: 0.8rem;
            line-height: 1.5;
        }

        .notes-list li:before {
            content: "•";
            position: absolute;
            left: 0;
            color: var(--secondary-color);
        }

        .notes-list li:last-child {
            margin-bottom: 0;
        }
    </style>
</head>
<body>

<div class="confirmation-container">
    <h2 class="page-title">Appointment Confirmation</h2>
    
    <?php if (isset($message)): ?>
        <div class="message-box <?php echo $status === 'success' ? 'success-message' : 'error-message'; ?>">
            <p><?php echo $message; ?></p>
        </div>
    <?php endif; ?>
    
    <div class="card confirmation-card">
        <div class="card-header">
            <i class="fas fa-calendar-check me-2"></i> Review Your Appointment Details
        </div>
        
        <div class="appointment-details">
            <div class="detail-row">
                <div class="detail-icon">
                    <i class="fas fa-user-md"></i>
                </div>
                <div class="detail-content">
                    <div class="detail-label">Doctor</div>
                    <div>Dr. <?php echo htmlspecialchars($doctor['name']); ?></div>
                    <div><?php echo htmlspecialchars($doctor['specialization']); ?></div>
                </div>
            </div>

            <div class="detail-row">
                <div class="detail-icon">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <div class="detail-content">
                    <div class="detail-label">Date</div>
                    <div><?php echo date('l, F j, Y', strtotime($appointment_date)); ?></div>
                </div>
            </div>

            <div class="detail-row">
                <div class="detail-icon">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="detail-content">
                    <div class="detail-label">Time</div>
                    <div>
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
                    <div><?php echo htmlspecialchars($user['name']); ?></div>
                    <div><?php echo htmlspecialchars($user['email']); ?></div>
                    <div><?php echo htmlspecialchars($user['phone']); ?></div>
                </div>
            </div>

            <form method="POST" action="confirm_appointment.php?doctor_id=<?php echo $doctor_id; ?>&appointment_date=<?php echo $appointment_date; ?>&slot_id=<?php echo $slot_id; ?>">
                <div class="detail-row">
                    <div class="detail-icon">
                        <i class="fas fa-notes-medical"></i>
                    </div>
                    <div class="detail-content w-100">
                        <div class="detail-label">Patient Condition (Optional)</div>
                        <textarea name="patient_condition" class="form-control" rows="3" placeholder="Describe your condition..."></textarea>
                    </div>
                </div>

                <div class="important-notes">
                    <div class="notes-title">
                        <i class="fas fa-info-circle"></i> Important Notes
                    </div>
                    <ul class="notes-list">
                        <li>Please arrive 15 minutes before your scheduled appointment time.</li>
                        <li>Bring any relevant medical records or test results to your appointment.</li>
                        <li>Your appointment request will be reviewed by the doctor and you will receive a confirmation.</li>
                        <li>You can cancel your appointment up to 24 hours before the scheduled time.</li>
                    </ul>
                </div>
 
                <?php if (!isset($message) || $status !== 'success'): ?>
                    <div class="btn-container">
                        <a href="book_appointment_page.php?doctor_id=<?php echo $doctor_id; ?>&appointment_date=<?php echo $appointment_date; ?>" class="btn btn-back">
                            <i class="fas fa-arrow-left me-2"></i>Select Another Time
                        </a>
                        
                        <button type="submit" name="confirm" class="btn btn-confirm">
                            <i class="fas fa-check-circle me-2"></i>Confirm Appointment
                        </button>
                    </div>
                <?php else: ?>
                    <div class="btn-container">
                        <a href="appointmentstat.php" class="btn btn-confirm">
                            <i class="fas fa-home me-2"></i>Go to Dashboard
                        </a>
                    </div>
                <?php endif; ?>
            </form>
        </div>
    </div>
</div>

</body>
</html>