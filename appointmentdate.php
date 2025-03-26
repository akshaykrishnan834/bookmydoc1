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
            --success-color: #27ae60;
            --background-color: #f0f4f8;
        }

        body {
            background: linear-gradient(135deg, var(--background-color), #e6edf5);

            min-height: 100vh;
        }

        .date-selection-container {
            max-width: 900px;
            margin: 4rem auto;
            padding: 0 2rem;
        }

        .page-title {
            color: var(--primary-color);
            margin-bottom: 3rem;
            font-weight: 700;
            text-align: center;
            text-transform: uppercase;
            letter-spacing: 2px;
            position: relative;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.1);
        }

        .page-title::after {
            content: '';
            position: absolute;
            bottom: -15px;
            left: 50%;
            transform: translateX(-50%);
            width: 100px;
            height: 4px;
            background: linear-gradient(45deg, var(--secondary-color), var(--accent-color));
            border-radius: 2px;
        }

        .date-selection-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 15px 30px rgba(0,0,0,0.1);
            padding: 2rem;
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .date-selection-card::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(59, 133, 182, 0.1) 0%, transparent 70%);
            transform: rotate(30deg);
        }

        .date-selection-card > * {
            position: relative;
            z-index: 1;
        }

        .doctor-info {
            display: flex;
            align-items: center;
            background: linear-gradient(45deg, #f8f9fa, #e9ecef);
            padding: 1.5rem;
            border-radius: 15px;
            margin-bottom: 2rem;
            box-shadow: inset 0 2px 5px rgba(0,0,0,0.05);
        }

        .doctor-avatar {
            width: 80px;
            height: 80px;
            background: linear-gradient(45deg, var(--secondary-color), var(--accent-color));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 1.5rem;
            box-shadow: 0 5px 15px rgba(52,152,219,0.3);
        }

        .doctor-avatar i {
            color: white;
            font-size: 2rem;
        }

        .doctor-details h3 {
            margin: 0;
            color: var(--primary-color);
            font-size: 1.5rem;
            font-weight: 600;
        }

        .doctor-details p {
            margin: 5px 0 0;
            color: #777;
            font-size: 1rem;
            font-style: italic;
        }

        .instruction {
            background: #e8f4fd;
            padding: 1.5rem;
            border-radius: 15px;
            margin-bottom: 2rem;
            color: var(--primary-color);
            border-left: 5px solid var(--secondary-color);
        }

        .calendar {
            padding: 1.5rem;
            background: rgba(255,255,255,0.9);
            border-radius: 15px;
        }

        .month-nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            padding: 0 1rem;
        }

        .month-title {
            font-size: 1.4rem;
            font-weight: 600;
            color: var(--primary-color);
            text-transform: uppercase;
        }

        .month-nav-btn {
            background: none;
            border: none;
            color: var(--secondary-color);
            font-size: 1.5rem;
            padding: 0.5rem;
            transition: all 0.3s ease;
        }

        .month-nav-btn:hover {
            color: var(--accent-color);
            transform: scale(1.2);
        }

        .date-grid {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 15px;
        }

        .weekday-header {
            text-align: center;
            font-weight: 600;
            color: var(--primary-color);
            padding: 15px 0;
            background: #e8f4fd;
            border-radius: 10px;
            margin-bottom: 10px;
        }

        .date-item {
            text-align: center;
            padding: 1rem;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.3s ease;
            background: white;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }

        .date-item:hover:not(.disabled):not(.empty) {
            background: linear-gradient(45deg, #3498db, #2980b9);
            color: white;
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(52,152,219,0.3);
        }

        .date-item.active {
            background: linear-gradient(45deg, #0077b6, #00b4d8);
            color: white;
            box-shadow: 0 5px 15px rgba(52,152,219,0.4);
            transform: scale(1.05);
        }

        .date-item.disabled {
            background: #f5f5f5;
            color: #ccc;
            cursor: not-allowed;
            box-shadow: none;
        }

        .date-item.empty {
            background: transparent;
            box-shadow: none;
            cursor: default;
        }

        .day-number {
            font-size: 1.3rem;
            font-weight: 600;
        }

        .month-text {
            font-size: 0.9rem;
            margin-top: 5px;
            opacity: 0.8;
        }

        .btn-continue {
            background: linear-gradient(45deg, #0077b6, #00b4d8);
            border: none;
            padding: 1rem 3rem;
            border-radius: 30px;
            font-weight: 600;
            color: white;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .btn-continue:hover {
            background: linear-gradient(45deg, #2980b9);
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(52,152,219,0.4);
        }

        .btn-continue.disabled {
            background: #95a5a6;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }
    </style>
</head>
<body>

<div class="date-selection-container">
    <h2 class="page-title">Choose Your Appointment Date</h2>
    
    <div class="card date-selection-card">
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
            Select a date for your appointment from the calendar below. Available dates are interactive, and you'll proceed to choose a time slot after selection.
        </div>

        <div class="calendar">
            <?php
            $currentMonth = date('F');
            $currentYear = date('Y');
            ?>
            
            <div class="month-nav">
                <div class="month-title"><?php echo $currentMonth . ' ' . $currentYear; ?></div>
            </div>
            
            <div class="date-grid">
                <?php
                $dayNames = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
                foreach ($dayNames as $day) {
                    echo "<div class='weekday-header'>$day</div>";
                }

                $firstDate = new DateTime($today);
                $firstDate->modify('first day of this month');
                $firstDayOfMonthWeekday = (int)$firstDate->format('w');

                for ($i = 0; $i < $firstDayOfMonthWeekday; $i++) {
                    echo "<div class='date-item empty'></div>";
                }

                $currentDate = new DateTime($today);
                $currentDate->modify('first day of this month');
                $endDateObj = new DateTime($endDate);
                $lastDayOfMonth = new DateTime($today);
                $lastDayOfMonth->modify('last day of this month');
                
                while ($currentDate <= $lastDayOfMonth) {
                    $dateStr = $currentDate->format('Y-m-d');
                    $dayNumber = $currentDate->format('j');
                    $monthText = $currentDate->format('M');
                    $isToday = $dateStr === $today;
                    $isPast = $currentDate < new DateTime($today);
                    
                    $hasAvailability = !$isPast;
                    $class = $isToday ? 'active' : '';
                    $class = (!$hasAvailability || $currentDate > $endDateObj) ? 'disabled' : $class;
                    
                    echo "<div class='date-item $class' data-date='$dateStr'>";
                    echo "<div class='day-number'>$dayNumber</div>";
                    echo "<div class='month-text'>$monthText</div>";
                    echo "</div>";
                    
                    $currentDate->modify('+1 day');
                }
                
                $lastDayWeekday = (int)$lastDayOfMonth->format('w');
                if ($lastDayWeekday < 6) {
                    $remainingCells = 6 - $lastDayWeekday;
                    for ($i = 0; $i < $remainingCells; $i++) {
                        if ($currentDate <= $endDateObj) {
                            $dateStr = $currentDate->format('Y-m-d');
                            $dayNumber = $currentDate->format('j');
                            $monthText = $currentDate->format('M');
                            
                            echo "<div class='date-item' data-date='$dateStr'>";
                            echo "<div class='day-number'>$dayNumber</div>";
                            echo "<div class='month-text'>$monthText</div>";
                            echo "</div>";
                            
                            $currentDate->modify('+1 day');
                        } else {
                            echo "<div class='date-item empty'></div>";
                        }
                    }
                }
                ?>
            </div>

            <form id="dateForm" method="GET" action="book_appointment_page.php">
                <input type="hidden" name="doctor_id" value="<?php echo $doctor_id; ?>">
                <input type="hidden" id="selected_date" name="appointment_date" value="<?php echo $today; ?>">
                
                <div class="text-center mt-4">
                    <button type="submit" id="continueBtn" class="btn btn-continue">
                        <i class="fas fa-arrow-right me-2"></i>Proceed to Time Selection
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script>
$(document).ready(function() {
    $('.date-item:not(.disabled):not(.empty)').click(function() {
        $('.date-item').removeClass('active');
        $(this).addClass('active');
        $('#selected_date').val($(this).data('date'));
        $('#continueBtn').removeClass('disabled');
    });

    // Add subtle animation on page load
    $('.date-selection-card').css('opacity', 0).animate({ opacity: 1 }, 1000);
    $('.date-item').each(function(index) {
        $(this).delay(50 * index).css('opacity', 0).animate({ opacity: 1 }, 500);
    });
});
</script>

</body>
</html>