<?php session_start(); include('db_connection.php'); 
include('patientheader2.php');

// Get the selected doctor's ID from the URL
if (!isset($_GET['doctor_id'])) {
    die("No doctor selected.");
}

$doctor_id = intval($_GET['doctor_id']);

// Fetch doctor details
$sql = "SELECT name, specialization, profile_photo FROM doctorreg WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $doctor_id);
$stmt->execute();
$result = $stmt->get_result();
$doctor = $result->fetch_assoc();

if (!$doctor) {
    die("Doctor not found.");
}

// Fetch available slots for the selected doctor
$sql = "SELECT id, start_time, end_time, DATE_FORMAT(start_time, '%h:%i %p') as formatted_start, 
        DATE_FORMAT(end_time, '%h:%i %p') as formatted_end, 
        DAYNAME(start_time) as day_name, 
        DATE_FORMAT(start_time, '%M %e, %Y') as formatted_date
        FROM doctor_availability 
        WHERE doctor_id = ? 
        ORDER BY start_time";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $doctor_id);
$stmt->execute();
$result = $stmt->get_result();

// Group slots by date
$slots_by_date = [];
while ($row = $result->fetch_assoc()) {
    $date_key = $row['formatted_date'];
    if (!isset($slots_by_date[$date_key])) {
        $slots_by_date[$date_key] = [];
    }
    $slots_by_date[$date_key][] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Appointment with Dr. <?php echo htmlspecialchars($doctor['name']); ?></title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #2c6ced;
            --secondary-color: #f8f9fa;
            --text-color: #333;
            --light-gray: #f5f7fa;
            --border-radius: 12px;
        }
        
        body {
            background-color: var(--secondary-color);
            color: var(--text-color);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .page-container {
            max-width: 1000px;
            margin: 40px auto;
            padding: 0 20px;
        }
        
        .doctor-card {
            background: white;
            border-radius: var(--border-radius);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
            padding: 25px;
            margin-bottom: 30px;
            display: flex;
            align-items: center;
            gap: 20px;
        }
        
        .doctor-image {
            width: 90px;
            height: 90px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid var(--primary-color);
        }
        
        .doctor-info h2 {
            color: var(--primary-color);
            margin-bottom: 5px;
            font-weight: 600;
        }
        
        .doctor-info p {
            color: #666;
            font-size: 1.1rem;
            margin-bottom: 0;
        }
        
        .date-container {
            background: white;
            border-radius: var(--border-radius);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            margin-bottom: 25px;
            overflow: hidden;
        }
        
        .date-header {
            background-color: var(--primary-color);
            color: white;
            padding: 15px 20px;
            font-weight: 500;
        }
        
        .slots-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 15px;
            padding: 20px;
        }
        
        .slot-card {
            background: var(--light-gray);
            border-radius: 8px;
            padding: 15px;
            text-align: center;
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }
        
        .slot-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(44, 108, 237, 0.15);
            border-color: var(--primary-color);
        }
        
        .slot-time {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--text-color);
            margin-bottom: 15px;
        }
        
        .book-btn {
            background-color: var(--primary-color);
            color: white;
            border: none;
            border-radius: 6px;
            padding: 8px 16px;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .book-btn:hover {
            background-color: #1c59cc;
            transform: scale(1.05);
        }
        
        .no-slots {
            text-align: center;
            padding: 30px;
            color: #666;
            font-size: 1.1rem;
        }
        
        .back-link {
            display: inline-block;
            margin-bottom: 20px;
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
        }
        
        .back-link:hover {
            text-decoration: underline;
        }
        
        /* Animation for slots */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .slot-card {
            animation: fadeIn 0.4s ease forwards;
            animation-delay: calc(var(--delay) * 0.1s);
            opacity: 0;
        }
    </style>
</head>
<body>

<div class="page-container">
    <a href="browsedoct.php" class="back-link">
        <i class="fas fa-arrow-left"></i> Back to Doctors
    </a>
    
    <div class="doctor-card">
        
        <div class="doctor-info">
            <h2>Dr. <?php echo htmlspecialchars($doctor['name']); ?></h2>
            <p><i class="fas fa-stethoscope"></i> <?php echo htmlspecialchars($doctor['specialization']); ?></p>
        </div>
    </div>
    
    <h3 class="mb-4">Available Appointment Slots</h3>
    
    <?php if (count($slots_by_date) > 0) { ?>
        <?php $delay_counter = 0; ?>
        <?php foreach ($slots_by_date as $date => $slots) { ?>
            <div class="date-container">
                <div class="date-header">
                    <i class="far fa-calendar-alt me-2"></i> <?php echo $slots[0]['day_name']; ?>, <?php echo $date; ?>
                </div>
                <div class="slots-container">
                    <?php foreach ($slots as $slot) { ?>
                        <div class="slot-card" style="--delay: <?php echo $delay_counter++; ?>">
                            <div class="slot-time">
                                <i class="far fa-clock me-1"></i>
                                <?php echo $slot['formatted_start']; ?> - <?php echo $slot['formatted_end']; ?>
                            </div>
                            <form method="GET" action="appointmentdate.php">
                                <input type="hidden" name="slot_id" value="<?php echo $slot['id']; ?>">
                                <input type="hidden" name="doctor_id" value="<?php echo $doctor_id; ?>">
                                <button type="submit" class="book-btn">
                                    <i class="fas fa-calendar-check me-1"></i> Book Now
                                </button>
                            </form>
                        </div>
                    <?php } ?>
                </div>
            </div>
        <?php } ?>
    <?php } else { ?>
        <div class="no-slots">
            <i class="far fa-calendar-times mb-3" style="font-size: 3rem; color: #dc3545;"></i>
            <p>No available slots with Dr. <?php echo htmlspecialchars($doctor['name']); ?> at the moment.</p>
            <p>Please check back later or contact our clinic for assistance.</p>
        </div>
    <?php } ?>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php $conn->close(); ?>