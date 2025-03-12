<?php 
session_start(); 
include('db_connection.php'); 
include('patientheader2.php');

// Set timezone to handle time comparisons correctly
date_default_timezone_set('Asia/Kolkata'); // Change to your timezone if needed

// Ensure doctor_id and appointment_date are passed
if (!isset($_GET['doctor_id']) || !isset($_GET['appointment_date'])) {
    die("Invalid request. Doctor ID and appointment date are required.");
}

$doctor_id = intval($_GET['doctor_id']);
$appointment_date = $_GET['appointment_date'];

// Validate date format
if (!preg_match("/^\d{4}-\d{2}-\d{2}$/", $appointment_date)) {
    die("Invalid date format.");
}

// Check if date is not in the past
$today = date('Y-m-d');
if ($appointment_date < $today) {
    die("Cannot book appointments for past dates.");
}

// Get current time as DateTime object for reliable comparison
$currentDate = date('Y-m-d');
$currentTime = date('H:i:s');
$currentDateTime = new DateTime(); // Creates DateTime object with current time

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
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Available Time Slots</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #3498db;
            --accent-color: #e74c3c;
            --background-color: #f8f9fa;
        }

        body {
            background-color: var(--background-color);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .timeslot-container {
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

        .timeslot-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
            border: none;
            transition: transform 0.3s ease;
        }

        .timeslot-card:hover {
            transform: translateY(-5px);
        }

        .doctor-info {
            display: flex;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .doctor-avatar {
            width: 60px;
            height: 60px;
            background-color: var(--secondary-color);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 1rem;
        }

        .doctor-avatar i {
            color: white;
            font-size: 1.5rem;
        }

        .doctor-details h3 {
            margin: 0;
            color: var(--primary-color);
            font-size: 1.2rem;
            font-weight: 600;
        }

        .doctor-details p {
            margin: 0;
            color: #666;
            font-size: 0.9rem;
        }

        .date-display {
            font-size: 1.2rem;
            margin-bottom: 1.5rem;
            color: var(--primary-color);
            padding: 15px;
            background-color: #e8f4fd;
            border-radius: 8px;
            text-align: center;
        }

        .instruction {
            color: #666;
            margin-bottom: 1.5rem;
            padding: 1rem;
            background-color: #f8f9fa;
            border-radius: 8px;
        }

        .time-slots-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
            margin-bottom: 1.5rem;
        }

        @media (max-width: 768px) {
            .time-slots-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 576px) {
            .time-slots-grid {
                grid-template-columns: 1fr;
            }
        }

        .time-slot {
            background-color: #e8f4fd;
            border-radius: 10px;
            padding: 1.2rem;
            text-align: center;
            transition: all 0.3s ease;
            cursor: pointer;
            border: 2px solid transparent;
        }

        .time-slot:hover:not(.unavailable) {
            transform: translateY(-3px);
            background-color: #d4e9f7;
            border-color: var(--secondary-color);
        }

        .time-slot.selected {
            background-color: var(--secondary-color);
            color: white;
            border-color: #2980b9;
        }

        .time-slot.unavailable {
            background-color: #f5f5f5;
            color: #ccc;
            cursor: not-allowed;
        }

        .time-slot i {
            font-size: 1.2rem;
            margin-bottom: 0.5rem;
            color: var(--secondary-color);
        }

        .time-slot.selected i {
            color: white;
        }

        .time-slot.unavailable i {
            color: #ccc;
        }

        .time-slot-time {
            font-weight: 600;
            font-size: 1.1rem;
            margin-bottom: 0.2rem;
        }

        .no-slots {
            text-align: center;
            color: #666;
            font-style: italic;
            padding: 2rem;
            background-color: #f8f9fa;
            border-radius: 10px;
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

        .btn-book {
            background-color: var(--secondary-color);
            color: white;
            border: none;
            padding: 0.8rem 2rem;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-book:hover {
            background-color: #2980b9;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(52, 152, 219, 0.3);
        }

        .btn-book:disabled {
            background-color: #95a5a6;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }

        .highlight-red {
            background-color: #f9e0e0 !important;
            border: 1px solid #e74c3c !important;
        }
    </style>
</head>
<body>

<div class="timeslot-container">
    <h2 class="text-center page-title">Available Time Slots</h2>
    
    <div class="card timeslot-card p-4">
        <div class="doctor-info">
            <div class="doctor-avatar">
                <i class="fas fa-user-md"></i>
            </div>
            <div class="doctor-details">
                <h3>Dr. <?php echo htmlspecialchars($doctor['name']); ?></h3>
                <p><?php echo htmlspecialchars($doctor['specialization']); ?></p>
            </div>
        </div>

        <div class="date-display">
            <i class="far fa-calendar-alt me-2"></i>
            <?php echo date('l, F j, Y', strtotime($appointment_date)); ?>
        </div>

        <div class="instruction">
            <i class="fas fa-info-circle me-2"></i>
            Please select a time slot for your appointment with Dr. <?php echo htmlspecialchars($doctor['name']); ?>.
        </div>

        <?php
        // Get all time slots
        $sql = "SELECT id, start_time, end_time 
                FROM doctor_availability 
                WHERE doctor_id = ? 
                ORDER BY start_time ASC";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $doctor_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        // Check if there are existing appointments for these slots
        $bookedSlots = [];
        $appointmentSql = "SELECT slot_id FROM appointment_requests WHERE doctor_id = ? AND appointment_date = ?";
        $apptStmt = $conn->prepare($appointmentSql);
        $apptStmt->bind_param("is", $doctor_id, $appointment_date);
        $apptStmt->execute();
        $apptResult = $apptStmt->get_result();
        
        while ($appt = $apptResult->fetch_assoc()) {
            $bookedSlots[] = $appt['slot_id'];
        }
        
        if ($result->num_rows > 0) {
            echo '<div class="time-slots-grid">';
            
            while ($row = $result->fetch_assoc()) {
                $slot_id = $row['id'];
                $start_time = $row['start_time'];
                $end_time = $row['end_time'];
                
                // Check if slot is already booked
                $isBooked = in_array($slot_id, $bookedSlots);
                
                // Check if slot is in the past (for current date)
                $isPastSlot = false;
                
                if ($appointment_date == $currentDate) {
                    // Create DateTime object for the slot's start time today
                    $slotDateTime = new DateTime($appointment_date . ' ' . $start_time);
                    
                    // Compare with current time using DateTime comparison
                    if ($slotDateTime <= $currentDateTime) {
                        $isPastSlot = true;
                    }
                }
                
                // Determine class for the slot
                $slotClass = '';
                if ($isPastSlot || $isBooked) {
                    $slotClass = 'unavailable';
                    // Add highlight-red class for past slots
                    if ($isPastSlot) {
                        $slotClass .= ' highlight-red';
                    }
                }
                
                echo "<div class='time-slot $slotClass' data-slot-id='$slot_id'>";
                echo "<i class='far fa-clock'></i>";
                echo "<div class='time-slot-time'>" . date('g:i A', strtotime($start_time)) . " - " . date('g:i A', strtotime($end_time)) . "</div>";
                
                if ($isPastSlot) {
                    echo "<div class='availability'>Past Time</div>";
                } else if ($isBooked) {
                    echo "<div class='availability'>Booked</div>";
                } else {
                    echo "<div class='availability'>Available</div>";
                }
                
                echo "</div>";
            }
            
            echo '</div>';
        } else {
            echo '<div class="no-slots">';
            echo '<i class="fas fa-calendar-times fa-3x mb-3"></i>';
            echo '<p>No time slots available for this doctor on the selected date.</p>';
            echo '<p>Please select a different date or check with another doctor.</p>';
            echo '</div>';
        }
        ?>

        <form id="appointmentForm" method="GET" action="submitappointmentrqst.php">
            <input type="hidden" name="doctor_id" value="<?php echo $doctor_id; ?>">
            <input type="hidden" name="appointment_date" value="<?php echo $appointment_date; ?>">
            <input type="hidden" id="slot_id" name="slot_id" value="">

            <div class="btn-container">
                <a href="appointmentdate.php?doctor_id=<?php echo $doctor_id; ?>" class="btn btn-back">
                    <i class="fas fa-arrow-left me-2"></i>Back to Date Selection
                </a>
                
                <button type="submit" id="bookBtn" class="btn btn-book" disabled>
                    <i class="fas fa-calendar-check me-2"></i>Proceed to Confirmation
                </button>
            </div>
        </form>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script>
$(document).ready(function() {
    // Handle time slot selection
    $('.time-slot:not(.unavailable)').click(function() {
        // Remove selected class from all time slots
        $('.time-slot').removeClass('selected');
        
        // Add selected class to clicked time slot
        $(this).addClass('selected');
        
        // Update hidden input with selected slot id
        $('#slot_id').val($(this).data('slot-id'));
        
        // Enable book button
        $('#bookBtn').prop('disabled', false);
    });
});
</script>

</body>
</html>