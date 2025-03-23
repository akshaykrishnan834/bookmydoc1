<?php 
session_start();
include('db_connection.php');
include('doctorheader.php');

// Ensure the doctor is logged in
if (!isset($_SESSION['id'])) {     
    header("Location: doctorlog.php");     
    exit(); 
}

$doctor_id = $_SESSION['id'];
$doctor_name = $_SESSION['name'] ?? 'Doctor';

// Fetch total appointments
$sql_appointments = "SELECT COUNT(*) AS total_appointments FROM appointment_requests WHERE doctor_id = ?";
$stmt_appointments = $conn->prepare($sql_appointments);
$stmt_appointments->bind_param("i", $doctor_id);
$stmt_appointments->execute();
$result_appointments = $stmt_appointments->get_result();
$row_appointments = $result_appointments->fetch_assoc();
$total_appointments = $row_appointments['total_appointments'] ?? 0;

// Fetch approved appointments
$sql_approved = "SELECT COUNT(*) AS approved FROM appointment_requests WHERE doctor_id = ? AND status = 'Approved'";
$stmt_approved = $conn->prepare($sql_approved);
$stmt_approved->bind_param("i", $doctor_id);
$stmt_approved->execute();
$result_approved = $stmt_approved->get_result();
$row_approved = $result_approved->fetch_assoc();
$total_approved = $row_approved['approved'] ?? 0;

// Fetch rejected appointments
$sql_rejected = "SELECT COUNT(*) AS rejected FROM appointment_requests WHERE doctor_id = ? AND status = 'Rejected'";
$stmt_rejected = $conn->prepare($sql_rejected);
$stmt_rejected->bind_param("i", $doctor_id);
$stmt_rejected->execute();
$result_rejected = $stmt_rejected->get_result();
$row_rejected = $result_rejected->fetch_assoc();
$total_rejected = $row_rejected['rejected'] ?? 0;

// Fetch pending appointments
$sql_pending = "SELECT COUNT(*) AS pending FROM appointment_requests WHERE doctor_id = ? AND status = 'Pending'";
$stmt_pending = $conn->prepare($sql_pending);
$stmt_pending->bind_param("i", $doctor_id);
$stmt_pending->execute();
$result_pending = $stmt_pending->get_result();
$row_pending = $result_pending->fetch_assoc();
$total_pending = $row_pending['pending'] ?? 0;

// Fetch total earnings (sum of all payments received)
$sql_earnings = "SELECT SUM(amount) AS total_earnings FROM payments WHERE doctor_id = ?";
$stmt_earnings = $conn->prepare($sql_earnings);
$stmt_earnings->bind_param("i", $doctor_id);
$stmt_earnings->execute();
$result_earnings = $stmt_earnings->get_result();
$row_earnings = $result_earnings->fetch_assoc();
$total_earnings = $row_earnings['total_earnings'] ?? 0.00;

// Calculate appointment rate percentage
$approval_rate = ($total_appointments > 0) ? round(($total_approved / $total_appointments) * 100) : 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctor Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #4361ee;
            --secondary-color: #3cb371;
            --danger-color: #e63946;
            --warning-color: #f9a826;
            --info-color: #4cc9f0;
            --dark-color: #2b2d42;
            --text-color: #2b2d42;
            --text-light: #6c757d;
            --border-radius: 12px;
            --box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            --gradient-blue: linear-gradient(135deg, #4361ee, #3a0ca3);
            --gradient-green: linear-gradient(135deg, #3cb371, #2a9d8f);
            --gradient-red: linear-gradient(135deg, #e63946, #d00000);
            --gradient-yellow: linear-gradient(135deg, #f9a826, #f4a261);
            --gradient-purple: linear-gradient(135deg, #7209b7, #560bad);
        }
        
        body {
            font-family: 'Poppins', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
            color: var(--text-color);
        }

        .dashboard-container {
            margin-top: 80px;
            padding: 2rem;
            max-width: 1400px;
            margin-left: auto;
            margin-right: auto;
        }

        .welcome-section {
            text-align: center;
            margin-bottom: 2.5rem;
            position: relative;
            padding-bottom: 1.5rem;
        }

        .welcome-section h2 {
            font-size: 2.2rem;
            margin: 0;
            color: var(--dark-color);
            font-weight: 600;
        }

        .welcome-section p {
            color: var(--text-light);
            margin-top: 0.5rem;
            font-size: 1.1rem;
        }

        .welcome-section::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 4px;
            background: var(--gradient-blue);
            border-radius: 2px;
        }

        .stats-overview {
            display: flex;
            justify-content: space-between;
            gap: 1rem;
            margin-bottom: 2.5rem;
            width: 100%;
            overflow-x: auto;
        }

        .stat-card {
            background: white;
            padding: 1.5rem;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            flex: 1;
            min-width: 180px;
            opacity: 0;
            transform: translateY(20px);
        }

        .stat-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 8px;
            background: var(--gradient-blue);
        }

        .stat-icon {
            margin-bottom: 1rem;
            width: 50px;
            height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            font-size: 1.5rem;
            color: white;
            background: var(--gradient-blue);
        }

        .stat-content {
            width: 100%;
        }

        .stat-title {
            font-size: 0.9rem;
            color: var(--text-light);
            margin-bottom: 0.3rem;
            font-weight: 500;
            white-space: nowrap;
        }

        .stat-value {
            font-size: 1.8rem;
            font-weight: 700;
            margin: 0;
            margin-bottom: 0.3rem;
            color: var(--dark-color);
        }

        .stat-trend {
            display: flex;
            align-items: center;
            font-size: 0.8rem;
            color: var(--secondary-color);
            font-weight: 500;
        }

        .stat-trend i {
            margin-right: 0.3rem;
        }

        /* Specific styling for different cards */
        .appointments-card::before {
            background: var(--gradient-blue);
        }
        .appointments-card .stat-icon {
            background: var(--gradient-blue);
        }

        .earnings-card::before {
            background: var(--gradient-green);
        }
        .earnings-card .stat-icon {
            background: var(--gradient-green);
        }

        .approved-card::before {
            background: var(--gradient-green);
        }
        .approved-card .stat-icon {
            background: var(--gradient-green);
        }

        .rejected-card::before {
            background: var(--gradient-red);
        }
        .rejected-card .stat-icon {
            background: var(--gradient-red);
        }

        .pending-card::before {
            background: var(--gradient-yellow);
        }
        .pending-card .stat-icon {
            background: var(--gradient-yellow);
        }

        .progress-container {
            width: 100%;
            margin-top: 0.8rem;
        }

        .progress-label {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.3rem;
        }

        .progress-label span {
            font-size: 0.75rem;
            color: var(--text-light);
        }

        .progress-bar-bg {
            height: 6px;
            width: 100%;
            background-color: #e9ecef;
            border-radius: 4px;
            overflow: hidden;
        }

        .progress-bar {
            height: 100%;
            background: var(--gradient-green);
            border-radius: 4px;
            transition: width 0.5s ease;
        }

        .quick-actions {
            display: flex;
            justify-content: center;
            margin-top: 2rem;
            gap: 1rem;
            flex-wrap: wrap;
            text-decoration: none;
        }

        .action-btn {
            background: white;
            border: none;
            padding: 0.8rem 1.5rem;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            color: var(--primary-color);
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            text-decoration: none;
        }

        .action-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            text-decoration: none;
        }

        .action-btn.primary {
            background: var(--gradient-blue);
            color: white;
            text-decoration: none;
        }

        @media (max-width: 1200px) {
            .stats-overview {
                flex-wrap: nowrap;
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
                padding-bottom: 10px;
                margin-bottom: 1.5rem;
            }
            
            .stat-card {
                min-width: 180px;
            }
        }

        @media (max-width: 768px) {
            .welcome-section h2 {
                font-size: 1.8rem;
            }
            
            .stat-value {
                font-size: 1.6rem;
            }
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <!-- Welcome Section -->
        <div class="welcome-section">
            <h2>Welcome, Dr. <?php echo htmlspecialchars($doctor_name); ?></h2>
            <p>Here's an overview of your practice performance</p>
        </div>

        <!-- Stats Overview -->
        <div class="stats-overview">
            <div class="stat-card appointments-card">
                <div class="stat-icon">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-title">Total Appointments</div>
                    <h3 class="stat-value"><?php echo $total_appointments; ?></h3>
                    <div class="stat-trend">
                        <i class="fas fa-chart-line"></i> Active
                    </div>
                </div>
            </div>
            
            <div class="stat-card earnings-card">
                <div class="stat-icon">
                    <i class="fas fa-wallet"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-title">Total Earnings</div>
                    <h3 class="stat-value">₹<?php echo number_format($total_earnings, 2); ?></h3>
                    <div class="stat-trend">
                        <i class="fas fa-arrow-up"></i> Growing
                    </div>
                </div>
            </div>
            
            <div class="stat-card approved-card">
                <div class="stat-icon">
                    <i class="fas fa-thumbs-up"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-title">Approved</div>
                    <h3 class="stat-value"><?php echo $total_approved; ?></h3>
                    <div class="stat-trend">
                        <i class="fas fa-check"></i> <?php echo $approval_rate; ?>%
                    </div>
                </div>
            </div>
            
            <div class="stat-card rejected-card">
                <div class="stat-icon">
                    <i class="fas fa-thumbs-down"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-title">Rejected</div>
                    <h3 class="stat-value"><?php echo $total_rejected; ?></h3>
                    <div class="stat-trend">
                        <i class="fas fa-info-circle"></i> <?php echo ($total_appointments > 0) ? round(($total_rejected / $total_appointments) * 100) : 0; ?>%
                    </div>
                </div>
            </div>
            
            <div class="stat-card pending-card">
                <div class="stat-icon">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-title">Pending</div>
                    <h3 class="stat-value"><?php echo $total_pending; ?></h3>
                    <div class="stat-trend">
                        <?php if($total_pending > 0): ?>
                            <i class="fas fa-exclamation-circle"></i> Attention
                        <?php else: ?>
                            <i class="fas fa-check-circle"></i> All clear
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="quick-actions">
            <a href="manageappointments.php" class="action-btn primary">
                <i class="fas fa-calendar-alt"></i> View Appointments
            </a>
            <a href="doctorac.php" class="action-btn">
                <i class="fas fa-user-md"></i> My Profile
            </a>
            <a href="doctorpaymentstat.php" class="action-btn">
                <i class="fas fa-money-bill-wave"></i> Payment History
            </a>
        </div>
    </div>

    <script>
        // Simple animation for stat cards
        document.addEventListener('DOMContentLoaded', function() {
            const cards = document.querySelectorAll('.stat-card');
            
            setTimeout(() => {
                cards.forEach((card, index) => {
                    setTimeout(() => {
                        card.style.opacity = '1';
                        card.style.transform = 'translateY(0)';
                    }, index * 100);
                });
            }, 300);
        });
    </script>
</body>
</html>