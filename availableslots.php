<?php
session_start();
include('doctorheader.php');

// Database connection
$servername = "localhost";
$username = "root";  // Your database username
$password = "";  // Your database password
$dbname = "bookmydoc"; // Your database name

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$success_message = '';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_slots'])) {
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];
    $doctor_id = $_SESSION['id'];  // Assuming the doctor's ID is stored in the session

    $start_timestamp = strtotime($start_time);
    $end_timestamp = strtotime($end_time);

    // Ensure start time is before end time
    if ($start_timestamp > $end_timestamp) {
        [$start_timestamp, $end_timestamp] = [$end_timestamp, $start_timestamp];
        [$start_time, $end_time] = [$end_time, $start_time];
    }

    // Prepare SQL statement for inserting slots
    $stmt = $conn->prepare("INSERT INTO doctor_availability (doctor_id, start_time, end_time) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $doctor_id, $slot_start, $slot_end);

    // Generate 15-minute intervals and insert into DB
    $success_slots = [];
    $error_slots = [];

    while ($start_timestamp < $end_timestamp) {
        $slot_start = date('H:i', $start_timestamp);
        $slot_end_timestamp = $start_timestamp + (15 * 60); // Add 15 minutes

        if ($slot_end_timestamp > $end_timestamp) {
            break;
        }

        $slot_end = date('H:i', $slot_end_timestamp);

        if ($stmt->execute()) {
            $success_slots[] = "$slot_start - $slot_end";
        } else {
            $error_slots[] = "$slot_start - $slot_end";
        }

        $start_timestamp = $slot_end_timestamp;
    }

    if (count($success_slots) > 0) {
        $success_message = "Successfully created " . count($success_slots) . " time slots.";
    }

    if (count($error_slots) > 0) {
        $error_message = "Failed to create " . count($error_slots) . " time slots.";
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Set Availability</title>
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <style>
        :root {
            --primary-color: #2563eb;
            --secondary-color: #1e40af;
            --success-color: #22c55e;
            --error-color: #ef4444;
            --background-color: #f8fafc;
            --card-background: #ffffff;
            --text-color: #1e293b;
        }

        

        .edcontainer {
            max-width: 800px;
            margin: 2rem auto;
            padding: 2rem;
            background: var(--card-background);
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1);
        }

        h2 {
            color: var(--text-color);
            font-size: 1.875rem;
            margin-bottom: 2rem;
            font-weight: 600;
        }

        .form-group {
            margin-bottom: 1.5rem;
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        label {
            font-weight: 500;
            color: var(--text-color);
            font-size: 0.875rem;
        }

        input[type="time"] {
            padding: 0.75rem;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            font-size: 1rem;
            transition: border-color 0.2s;
            width: 100%;
            max-width: 200px;
        }

        input[type="time"]:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        .button-group {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
        }

        .btn {
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 6px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
            font-size: 0.875rem;
        }

        .btn-primary {
            background-color: var(--primary-color);
            color: white;
        }

        .btn-primary:hover {
            background-color: var(--secondary-color);
        }

        .btn-secondary {
            background-color: #e2e8f0;
            color: var(--text-color);
        }

        .btn-secondary:hover {
            background-color: #cbd5e1;
        }

        .alert {
            padding: 1rem;
            border-radius: 6px;
            margin-bottom: 1.5rem;
        }

        .alert-success {
            background-color: #dcfce7;
            color: #166534;
            border: 1px solid #86efac;
        }

        .alert-error {
            background-color: #fee2e2;
            color: #991b1b;
            border: 1px solid #fca5a5;
        }

        #slots-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 1rem;
            margin-top: 1.5rem;
        }

        .slot-item {
            background: #f8fafc;
            padding: 1rem;
            border-radius: 6px;
            border: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.2s;
        }

        .slot-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1);
        }

        .slot-item i {
            color: var(--primary-color);
        }

        #slot-preview {
            margin-top: 2rem;
            padding-top: 2rem;
            border-top: 1px solid #e2e8f0;
        }

        @media (max-width: 640px) {
            .container {
                padding: 1rem;
            }

            .button-group {
                flex-direction: column;
            }

            .btn {
                width: 100%;
            }
        }
    </style>
</head>
<body>

<div class="edcontainer">
    <h2>Set Your Availability</h2>

    <?php if ($success_message): ?>
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i>
            <?php echo $success_message; ?>
        </div>
    <?php endif; ?>

    <?php if ($error_message): ?>
        <div class="alert alert-error">
            <i class="fas fa-exclamation-circle"></i>
            <?php echo $error_message; ?>
        </div>
    <?php endif; ?>

    <form method="post">
        <div class="form-group">
            <label for="start_time">Start Time</label>
            <input type="time" id="start_time" name="start_time" required>
        </div>

        <div class="form-group">
            <label for="end_time">End Time</label>
            <input type="time" id="end_time" name="end_time" required>
        </div>

        <div class="button-group">
            <button type="button" class="btn btn-secondary" id="preview-btn">
                <i class="fas fa-eye"></i> Preview Slots
            </button>
            <button type="submit" class="btn btn-primary" name="submit_slots">
                <i class="fas fa-save"></i> Submit Slots
            </button>
        </div>
    </form>

    <div id="slot-preview" style="display: none;">
        <h3>Available Time Slots</h3>
        <div id="slots-container"></div>
    </div>
</div>

<script>
document.getElementById('preview-btn').addEventListener('click', function() {
    const startTime = document.getElementById('start_time').value;
    const endTime = document.getElementById('end_time').value;

    if (!startTime || !endTime) {
        alert('Please enter both start and end times');
        return;
    }

    const [startHours, startMinutes] = startTime.split(':').map(Number);
    const [endHours, endMinutes] = endTime.split(':').map(Number);

    let start = new Date();
    start.setHours(startHours, startMinutes, 0, 0);

    let end = new Date();
    end.setHours(endHours, endMinutes, 0, 0);

    if (start > end) {
        [start, end] = [end, start];
    }

    const slots = [];
    const slotDuration = 15 * 60 * 1000; // 15 minutes in milliseconds

    let current = new Date(start);
    while (current < end) {
        let slotStart = current.toTimeString().substr(0, 5);
        current = new Date(current.getTime() + slotDuration);

        if (current > end) break;

        let slotEnd = current.toTimeString().substr(0, 5);
        slots.push(`${slotStart} - ${slotEnd}`);
    }

    const slotsContainer = document.getElementById('slots-container');
    slotsContainer.innerHTML = '';

    if (slots.length === 0) {
        slotsContainer.textContent = 'No valid slots within the selected time range';
    } else {
        slots.forEach(slot => {
            const slotElement = document.createElement('div');
            slotElement.className = 'slot-item';
            slotElement.innerHTML = `<i class="far fa-clock"></i> ${slot}`;
            slotsContainer.appendChild(slotElement);
        });
    }

    document.getElementById('slot-preview').style.display = 'block';
});
</script>

</body>
</html>