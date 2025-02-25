<?php 
session_start(); 
include('db_connection.php'); 
include('patientheader2.php');

// Ensure slot_id and doctor_id are passed
if (!isset($_GET['slot_id']) || !isset($_GET['doctor_id'])) {
    die("Invalid request.");
}

$slot_id = intval($_GET['slot_id']);
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

// Fetch slot details
$sql = "SELECT start_time, end_time FROM doctor_availability WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $slot_id);
$stmt->execute();
$result = $stmt->get_result();
$slot = $result->fetch_assoc();

if (!$slot) {
    die("Slot not found.");
}

// Get today's date to set as minimum date for the date picker
$today = date('Y-m-d');
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

        .appointment-container {
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

        .appointment-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
            border: none;
            transition: transform 0.3s ease;
        }

        .appointment-card:hover {
            transform: translateY(-5px);
        }

        .time-slot {
            background-color: #e8f4fd;
            border-radius: 10px;
            padding: 1rem;
            margin-bottom: 1.5rem;
        }

        .time-slot i {
            color: var(--secondary-color);
            margin-right: 0.5rem;
        }

        .form-label {
            color: var(--primary-color);
            font-weight: 500;
            margin-bottom: 0.5rem;
        }

        .form-control {
            border-radius: 8px;
            padding: 0.8rem;
            border: 2px solid #e2e8f0;
            transition: border-color 0.3s ease;
        }

        .form-control:focus {
            border-color: var(--secondary-color);
            box-shadow: none;
        }

        .btn-book {
            background-color: var(--secondary-color);
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
    </style>
</head>
<body>

<div class="appointment-container">
    <h2 class="text-center page-title">Book Your Appointment</h2>
    
    <div class="card appointment-card p-4">
        <div class="doctor-info">
            <div class="doctor-avatar">
                <i class="fas fa-user-md"></i>
            </div>
            <div class="doctor-details">
                <h3>Dr. <?php echo htmlspecialchars($doctor['name']); ?></h3>
                <p><?php echo htmlspecialchars($doctor['specialization']); ?></p>
            </div>
        </div>

        <div class="time-slot">
            <i class="far fa-clock"></i>
            <strong>Selected Time Slot:</strong> 
            <?php echo $slot['start_time']; ?> - <?php echo $slot['end_time']; ?>
        </div>

        <form method="POST" action="submitappointmentrqst.php">
            <input type="hidden" name="slot_id" value="<?php echo $slot_id; ?>">
            <input type="hidden" name="doctor_id" value="<?php echo $doctor_id; ?>">
            
            <div class="mb-4">
                <label for="appointment_date" class="form-label">
                    <i class="far fa-calendar-alt me-2"></i>Select Appointment Date
                </label>
                <input type="date" 
                       name="appointment_date" 
                       id="appointment_date" 
                       class="form-control" 
                       min="<?php echo $today; ?>" 
                       value="<?php echo $today; ?>"
                       required>
            </div>

            <div class="text-center">
                <button type="submit" class="btn btn-book btn-lg">
                    <i class="fas fa-calendar-check me-2"></i>Request Appointment
                </button>
            </div>
        </form>
    </div>
</div>

</body>
</html>