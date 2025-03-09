<?php 
session_start(); 
include('db_connection.php'); 
include('patientheader2.php');

// Ensure doctor_id is passed
if (!isset($_GET['doctor_id'])) {
    die("Invalid request. Doctor ID is required.");
}

$doctor_id = intval($_GET['doctor_id']);

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

// Get today's date and next 30 days for the calendar
$today = date('Y-m-d');
$endDate = date('Y-m-d', strtotime('+30 days'));
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Select Appointment Date</title>
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

        .date-selection-container {
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

        .date-selection-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
            border: none;
            transition: transform 0.3s ease;
        }

        .date-selection-card:hover {
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

        .calendar {
            margin-top: 1.5rem;
        }

        .date-grid {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 10px;
            margin-bottom: 2rem;
        }

        .weekday-header {
            text-align: center;
            font-weight: 600;
            color: var(--primary-color);
            padding: 10px 0;
        }

        .date-item {
            text-align: center;
            padding: 15px 0;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .date-item:hover:not(.disabled) {
            background-color: #e8f4fd;
            transform: scale(1.05);
        }

        .date-item.active {
            background-color: var(--secondary-color);
            color: white;
        }

        .date-item.disabled {
            color: #ccc;
            background-color: #f5f5f5;
            cursor: not-allowed;
        }

        .date-item .day-number {
            font-size: 1.2rem;
            font-weight: 600;
        }

        .date-item .month-text {
            font-size: 0.8rem;
            margin-top: 5px;
        }

        .btn-continue {
            background-color: var(--secondary-color);
            border: none;
            padding: 0.8rem 2rem;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-continue:hover {
            background-color: #2980b9;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(52, 152, 219, 0.3);
        }

        .btn-continue.disabled {
            background-color: #95a5a6;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }

        .instruction {
            color: #666;
            margin-bottom: 1.5rem;
            padding: 1rem;
            background-color: #e8f4fd;
            border-radius: 8px;
        }
    </style>
</head>
<body>

<div class="date-selection-container">
    <h2 class="text-center page-title">Select Appointment Date</h2>
    
    <div class="card date-selection-card p-4">
        <div class="doctor-info">
            <div class="doctor-avatar">
                <i class="fas fa-user-md"></i>
            </div>
            <div class="doctor-details">
                <h3>Dr. <?php echo htmlspecialchars($doctor['name']); ?></h3>
                <p><?php echo htmlspecialchars($doctor['specialization']); ?></p>
            </div>
        </div>

        <div class="instruction">
            <i class="fas fa-info-circle me-2"></i>
            Please select a date for your appointment. Available dates are highlighted. After selecting a date, you'll be able to choose from available time slots.
        </div>

        <div class="calendar">
            <div class="date-grid">
                <?php
                // Display day names
                $dayNames = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
                foreach ($dayNames as $day) {
                    echo "<div class='weekday-header'>$day</div>";
                }

                // Get the first day of the current month
                $firstDayOfMonth = date('Y-m-01');
                $firstDayWeekday = date('w', strtotime($firstDayOfMonth));
                
                // Add empty slots for days before the first day of the month
                for ($i = 0; $i < $firstDayWeekday; $i++) {
                    echo "<div></div>";
                }

                // Generate next 30 days
                $currentDate = new DateTime($today);
                $endDateObj = new DateTime($endDate);
                
                while ($currentDate <= $endDateObj) {
                    $dateStr = $currentDate->format('Y-m-d');
                    $dayNumber = $currentDate->format('j');
                    $monthText = $currentDate->format('M');
                    $isToday = $dateStr === $today;
                    
                    // Check if this date has any available slots
                    // In a real app, you would query the database to check availability
                    $hasAvailability = true; // Placeholder logic
                    
                    $class = $isToday ? 'active' : '';
                    $class = !$hasAvailability ? 'disabled' : $class;
                    
                    echo "<div class='date-item $class' data-date='$dateStr'>";
                    echo "<div class='day-number'>$dayNumber</div>";
                    echo "<div class='month-text'>$monthText</div>";
                    echo "</div>";
                    
                    $currentDate->modify('+1 day');
                }
                ?>
            </div>

            <form id="dateForm" method="GET" action="book_appointment_page.php">
                <input type="hidden" name="doctor_id" value="<?php echo $doctor_id; ?>">
                <input type="hidden" id="selected_date" name="appointment_date" value="<?php echo $today; ?>">
                
                <div class="text-center">
                    <button type="submit" id="continueBtn" class="btn btn-continue btn-lg">
                        <i class="fas fa-arrow-right me-2"></i>Continue to Time Selection
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script>
$(document).ready(function() {
    // Handle date selection
    $('.date-item:not(.disabled)').click(function() {
        // Remove active class from all dates
        $('.date-item').removeClass('active');
        
        // Add active class to selected date
        $(this).addClass('active');
        
        // Update hidden input with selected date
        $('#selected_date').val($(this).data('date'));
        
        // Enable continue button
        $('#continueBtn').removeClass('disabled');
    });
});
</script>

</body>
</html>