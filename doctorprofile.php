<?php session_start(); 
include('doctorheader.php'); 

// Add authentication check 
if (!isset($_SESSION['id'])) {     
    header("Location: doctorlog.php");     
    exit(); 
} 

// Get doctor information (assuming you have this data in your database)
// This is a placeholder - you'll need to modify based on your actual database structure
$doctor_id = $_SESSION['id'];
$doctor_name = $_SESSION['name'] ?? 'Doctor';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BookMyDoc - Doctor Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #3498db;
            --secondary-color: #2ecc71;
            --accent-color: #f1c40f;
            --dark-color: #2c3e50;
            --light-color: #ecf0f1;
            --danger-color: #e74c3c;
            --border-radius: 8px;
            --box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f7fa;
            margin: 0;
            padding: 0;
        }
        
        .dashboard-container {
            margin-top: 80px;
            padding: 1.5rem;
            max-width: 1400px;
            margin-left: auto;
            margin-right: auto;
        }
        
        .welcome-section {
            background: linear-gradient(to right, #3498db, #2ecc71);
            color: white;
            padding: 2rem;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            margin-bottom: 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .welcome-text h1 {
            margin: 0;
            font-size: 2rem;
        }
        
        .welcome-text p {
            margin-top: 0.5rem;
            font-size: 1.1rem;
            opacity: 0.9;
        }
        
        .stats-overview {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .stat-card {
            background: white;
            padding: 1.5rem;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            transition: transform 0.3s ease;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
        }
        
        .stat-card i {
            font-size: 2rem;
            margin-bottom: 1rem;
            color: var(--primary-color);
        }
        
        .stat-card h3 {
            margin: 0;
            font-size: 2rem;
            font-weight: bold;
        }
        
        .stat-card p {
            margin-top: 0.5rem;
            color: #7f8c8d;
            font-size: 1rem;
        }
        
        .dashboard-card {
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            margin-bottom: 1.5rem;
            overflow: hidden;
        }
        
        .card-header {
            padding: 1.2rem 1.5rem;
            background: var(--light-color);
            border-bottom: 1px solid #ddd;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .card-header h2 {
            margin: 0;
            color: var(--dark-color);
            font-size: 1.3rem;
        }
        
        .card-body {
            padding: 1.5rem;
        }
        
        .appointment {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 0;
            border-bottom: 1px solid #eee;
        }
        
        .appointment:last-child {
            border-bottom: none;
        }
        
        .appointment-info {
            flex: 1;
        }
        
        .appointment-info h4 {
            margin: 0 0 0.5rem 0;
            color: var(--dark-color);
        }
        
        .appointment-info p {
            margin: 0;
            color: #7f8c8d;
        }
        
        .appointment-status {
            padding: 0.5rem 1rem;
            border-radius: 50px;
            color: white;
            font-size: 0.8rem;
            font-weight: bold;
        }
        
        .status-upcoming {
            background-color: var(--primary-color);
        }
        
        .status-completed {
            background-color: var(--secondary-color);
        }
        
        .status-canceled {
            background-color: var(--danger-color);
        }
        
        .quick-actions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
        }
        
        .action-button {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            background: white;
            border: 1px solid #ddd;
            border-radius: var(--border-radius);
            padding: 1.5rem;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .action-button:hover {
            background: var(--primary-color);
            color: white;
            border-color: var(--primary-color);
        }
        
        .action-button:hover i {
            color: white;
        }
        
        .action-button i {
            font-size: 2rem;
            margin-bottom: 1rem;
            color: var(--primary-color);
            transition: color 0.3s ease;
        }
        
        .action-button span {
            font-weight: bold;
        }
        
        @media (max-width: 768px) {
            .welcome-section {
                flex-direction: column;
                text-align: center;
            }
            
            .stats-overview {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <!-- Dashboard Content -->
    <div class="dashboard-container">
        <!-- Welcome Section -->
        <div class="welcome-section">
            <div class="welcome-text">
                <h1>Welcome, Dr. <?php echo htmlspecialchars($doctor_name); ?></h1>
                <p><?php echo date('l, F j, Y'); ?></p>
            </div>
            <div>
                <button class="action-button" style="background: white; color: var(--primary-color); padding: 0.7rem 1.5rem;">
                    <i class="fas fa-calendar-plus"></i> New Appointment
                </button>
            </div>
        </div>
        
        <!-- Stats Overview -->
        <div class="stats-overview">
            <div class="stat-card">
                <i class="fas fa-calendar-check"></i>
                <h3>12</h3>
                <p>Today's Appointments</p>
            </div>
            <div class="stat-card">
                <i class="fas fa-user-plus"></i>
                <h3>3</h3>
                <p>New Patients</p>
            </div>
            <div class="stat-card">
                <i class="fas fa-clipboard-list"></i>
                <h3>48</h3>
                <p>Total Appointments This Week</p>
            </div>
            <div class="stat-card">
                <i class="fas fa-star"></i>
                <h3>4.8</h3>
                <p>Average Rating</p>
            </div>
        </div>
        
        
        
        <!-- Quick Actions -->
        <h2 style="margin-top: 2rem; color: var(--dark-color);">Quick Actions</h2>
        <div class="quick-actions">
            <div class="action-button">
                <i class="fas fa-calendar-alt"></i>
                <span>Manage Schedule</span>
            </div>
            <div class="action-button">
                <i class="fas fa-prescription"></i>
                <span>Write Prescription</span>
            </div>
            <div class="action-button">
                <i class="fas fa-folder-open"></i>
                <span>Patient Records</span>
            </div>
            <div class="action-button">
                <i class="fas fa-chart-line"></i>
                <span>Analytics</span>
            </div>
        </div>
    </div>
    
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