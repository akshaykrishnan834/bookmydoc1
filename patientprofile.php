<?php 
session_start(); 
include('patientheader2.php'); 

// Add authentication check 
if (!isset($_SESSION['id'])) {
    header("Location: patientlog.php");
    exit();
}

// Get patient info from session or database
$patient_name = $_SESSION['name'] ?? 'Patient';

// Count upcoming appointments
include('db_connection.php');
$patient_id = $_SESSION['id'];
$upcoming_query = "SELECT COUNT(*) as count FROM appointment_requests 
                  WHERE user_id = ? AND appointment_date >= CURDATE() AND status = 'Approved'";
$stmt = $conn->prepare($upcoming_query);
$stmt->bind_param("i", $patient_id);
$stmt->execute();
$upcoming_result = $stmt->get_result();
$upcoming_count = $upcoming_result->fetch_assoc()['count'];

// Get health tips from database (replace with actual query if you have a health_tips table)
$health_tips = [
    ["title" => "Stay Hydrated", "content" => "Drink at least 8 glasses of water daily to maintain proper bodily functions."],
    ["title" => "Regular Exercise", "content" => "Aim for at least 30 minutes of moderate physical activity most days of the week."],
    ["title" => "Balanced Diet", "content" => "Include plenty of fruits, vegetables, lean proteins, and whole grains in your diet."]
];

// Next available telemedicine slots (replace with actual query)
$tele_slots = [
    ["date" => "2025-03-13", "time" => "10:00 AM", "doctor" => "Dr. Sarah Chen", "specialization" => "General Medicine"],
    ["date" => "2025-03-13", "time" => "2:30 PM", "doctor" => "Dr. James Wilson", "specialization" => "Cardiology"],
    ["date" => "2025-03-14", "time" => "11:45 AM", "doctor" => "Dr. Emily Patel", "specialization" => "Pediatrics"]
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BookMyDoc - Patient Dashboard</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #3498db;
            --secondary-color: #2ecc71;
            --accent-color: #f39c12;
            --danger-color: #e74c3c;
            --text-color: #34495e;
            --light-bg: #f5f7fa;
            --border-radius: 10px;
        }
        
        body {
            background-color: var(--light-bg);
            color: var(--text-color);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .dashboard-content {
            margin-top: 80px;
            padding: 2rem;
            background: white;
            border-radius: var(--border-radius);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        }
        
        .welcome-section {
            background: linear-gradient(135deg, var(--primary-color), #1a6baa);
            color: white;
            padding: 2rem;
            border-radius: var(--border-radius);
            margin-bottom: 2rem;
            position: relative;
            overflow: hidden;
        }
        
        .welcome-section::after {
            content: "";
            position: absolute;
            top: 0;
            right: 0;
            width: 200px;
            height: 100%;
            background-image: url('assets/images/doctor-icon.png');
            background-size: contain;
            background-repeat: no-repeat;
            background-position: right center;
            opacity: 0.2;
        }
        
        .stats-card {
            background: white;
            border-radius: var(--border-radius);
            padding: 1.5rem;
            text-align: center;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s, box-shadow 0.3s;
            height: 100%;
        }
        
        .stats-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.1);
        }
        
        .stats-icon {
            font-size: 2.5rem;
            margin-bottom: 1rem;
            color: var(--primary-color);
        }
        
        .action-card {
            border-radius: var(--border-radius);
            padding: 1.5rem;
            text-align: center;
            transition: transform 0.3s;
            height: 100%;
            border: none;
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
        }
        
        .action-card:hover {
            transform: translateY(-5px);
        }
        
        .action-icon {
            font-size: 2rem;
            margin-bottom: 1rem;
            color: var(--primary-color);
            background: rgba(52, 152, 219, 0.1);
            width: 70px;
            height: 70px;
            line-height: 70px;
            border-radius: 50%;
            display: inline-block;
        }
        
        .section-title {
            margin-bottom: 1.5rem;
            position: relative;
            padding-left: 1rem;
            font-weight: 600;
        }
        
        .section-title::before {
            content: "";
            position: absolute;
            left: 0;
            top: 0;
            height: 100%;
            width: 4px;
            background: var(--primary-color);
            border-radius: 2px;
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .btn-primary:hover {
            background-color: #2980b9;
            border-color: #2980b9;
        }
        
        .health-tip {
            padding: 1rem;
            margin-bottom: 1rem;
            border-radius: var(--border-radius);
            background-color: rgba(46, 204, 113, 0.1);
            border-left: 4px solid var(--secondary-color);
        }
        
        .telemedicine-slot {
            display: flex;
            padding: 1rem;
            margin-bottom: 1rem;
            border-radius: var(--border-radius);
            background-color: white;
            border: 1px solid #e0e0e0;
            align-items: center;
            transition: transform 0.2s;
        }
        
        .telemedicine-slot:hover {
            transform: translateX(5px);
            border-left: 4px solid var(--primary-color);
        }
        
        .slot-time {
            min-width: 100px;
            text-align: center;
            padding: 0.5rem;
            background-color: rgba(52, 152, 219, 0.1);
            border-radius: var(--border-radius);
            margin-right: 1rem;
        }
        
        .medication-card {
            padding: 1rem;
            margin-bottom: 1rem;
            border-radius: var(--border-radius);
            background-color: white;
            border-left: 4px solid var(--accent-color);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        
        .medication-icon {
            font-size: 1.5rem;
            color: var(--accent-color);
            margin-right: 1rem;
        }
        
        .med-schedule {
            display: flex;
            align-items: center;
        }
        
        .med-time {
            background-color: rgba(243, 156, 18, 0.1);
            padding: 0.3rem 0.8rem;
            border-radius: 20px;
            font-size: 0.8rem;
            margin-left: 0.5rem;
        }
        
        .news-card {
            border-radius: var(--border-radius);
            overflow: hidden;
            margin-bottom: 1rem;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s;
        }
        
        .news-card:hover {
            transform: translateY(-5px);
        }
        
        .news-image {
            height: 150px;
            object-fit: cover;
            width: 100%;
        }
        
        .news-content {
            padding: 1rem;
        }
        
        .badge-pill {
            border-radius: 20px;
            padding: 0.4rem 0.8rem;
        }
        
        .health-tracking {
            background: linear-gradient(135deg, #f5f7fa, #c3cfe2);
            border-radius: var(--border-radius);
            padding: 1.5rem;
            margin-bottom: 1rem;
        }
        
        .progress {
            height: 10px;
            border-radius: 5px;
            margin-bottom: 0.5rem;
        }
    </style>
</head>
<body>
    <!-- Dashboard Content -->
    <div class="container dashboard-content">
        <div class="welcome-section">
            <h1><b>Welcome, <?php echo htmlspecialchars($patient_name); ?>!</b></h1>
            <p class="mb-0">Manage your health appointments and medical records all in one place.</p>
        </div>
        
        <div class="row mb-4">
            <div class="col-md-4 mb-3">
                <div class="stats-card">
                    <div class="stats-icon">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <h3><?php echo $upcoming_count; ?></h3>
                    <p class="text-muted mb-0">Upcoming Appointments</p>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="stats-card">
                    <div class="stats-icon">
                        <i class="fas fa-user-md"></i>
                    </div>
                    <h3>24/7</h3>
                    <p class="text-muted mb-0">Doctor Availability</p>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="stats-card">
                    <div class="stats-icon">
                        <i class="fas fa-heartbeat"></i>
                    </div>
                    <h3>Premium</h3>
                    <p class="text-muted mb-0">Health Services</p>
                </div>
            </div>
        </div>
        
        <h2 class="section-title mb-4">Quick Actions</h2>
        <div class="row mb-5">
            <div class="col-md-3 mb-3">
                <div class="card action-card">
                    <div class="card-body">
                        <div class="action-icon">
                            <i class="fas fa-calendar-plus"></i>
                        </div>
                        <h5 class="card-title">Book Appointment</h5>
                        <p class="card-text">Schedule a visit with our specialists</p>
                        <a href="browsedoct.php" class="btn btn-primary">Book Now</a>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card action-card">
                    <div class="card-body">
                        <div class="action-icon">
                            <i class="fas fa-list-alt"></i>
                        </div>
                        <h5 class="card-title">My Appointments</h5>
                        <p class="card-text">View & manage your appointments</p>
                        <a href="appointmentstat.php" class="btn btn-primary">View All</a>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card action-card">
                    <div class="card-body">
                        <div class="action-icon">
                            <i class="fas fa-file-medical"></i>
                        </div>
                        <h5 class="card-title">Medical Records</h5>
                        <p class="card-text">Access your health documents</p>
                        <a href="medical_records.php" class="btn btn-primary">View Records</a>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card action-card">
                    <div class="card-body">
                        <div class="action-icon">
                            <i class="fas fa-user-edit"></i>
                        </div>
                        <h5 class="card-title">Update Profile</h5>
                        <p class="card-text">Edit your personal information</p>
                        <a href="display_patients.php" class="btn btn-primary">Edit Profile</a>
                    </div>
                </div>
            </div>
        </div>
        
        
                        
                        <div class="text-center mt-3">
                            <a href="telemedicine.php" class="btn btn-primary">View All Telemedicine Options</a>
                        </div>
                    </div>
                </div>
                
                
            </div>
            
            
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const userIcon = document.getElementById("user-icon");
            const dropdown = document.getElementById("user-dropdown");
            
            if (userIcon && dropdown) {
                userIcon.addEventListener("click", function (event) {
                    dropdown.style.display = dropdown.style.display === "block" ? "none" : "block";
                    event.stopPropagation();
                });
                
                document.addEventListener("click", function (event) {
                    if (!userIcon.contains(event.target) && !dropdown.contains(event.target)) {
                        dropdown.style.display = "none";
                    }
                });
            }
        });
    </script>
</body>
</html>