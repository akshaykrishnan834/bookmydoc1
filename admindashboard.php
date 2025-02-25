<?php
session_start();
// Add authentication check
if (!isset($_SESSION['id'])) {
    header("Location: adminlog.php");
    exit();
}
include ('db_connection.php');
include('adminheader.php');


// Fetch summary statistics
$total_doctors_query = "SELECT COUNT(*) as total FROM doctorreg";
$total_patients_query = "SELECT COUNT(*) as total FROM patientreg";
$total_appointments_query = "SELECT COUNT(*) as total FROM appointment_requests";
$pending_appointments_query = "SELECT COUNT(*) as total FROM appointment_requests WHERE status = 'pending'";

$doctors_result = mysqli_query($conn, $total_doctors_query);
$patients_result = mysqli_query($conn, $total_patients_query);
$appointments_result = mysqli_query($conn, $total_appointments_query);
$pending_result = mysqli_query($conn, $pending_appointments_query);

$total_doctors = mysqli_fetch_assoc($doctors_result)['total'];
$total_patients = mysqli_fetch_assoc($patients_result)['total'];
$total_appointments = mysqli_fetch_assoc($appointments_result)['total'];
$pending_appointments = mysqli_fetch_assoc($pending_result)['total'];

// Fetch recent appointments
$recent_appointments_query = "SELECT a.*, d.name as doctor_name, p.name as patient_name 
                            FROM appointment_requests a 
                            JOIN doctorreg d ON a.doctor_id = d.id 
                            JOIN patientreg p ON a.user_id = p.id";
$recent_appointments = mysqli_query($conn, $recent_appointments_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - BookMyDoc</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-light">
    <div class="container-fluid py-4">
        <div class="row mb-4">
            <div class="col-12">
                <h2 class="admin-title">Dashboard Overview</h2>
                <p class="text-muted">Welcome back, Admin!</p>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row g-4 mb-4">
            <div class="col-xl-3 col-md-6">
                <div class="stat-card">
                    <div class="stat-card-info">
                        <h6>Total Doctors</h6>
                        <h2><?php echo $total_doctors; ?></h2>
                    </div>
                    <div class="stat-card-icon">
                        <i class="fas fa-user-md"></i>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="stat-card">
                    <div class="stat-card-info">
                        <h6>Total Patients</h6>
                        <h2><?php echo $total_patients; ?></h2>
                    </div>
                    <div class="stat-card-icon">
                        <i class="fas fa-users"></i>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="stat-card">
                    <div class="stat-card-info">
                        <h6>Total Appointments</h6>
                        <h2><?php echo $total_appointments; ?></h2>
                    </div>
                    <div class="stat-card-icon">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="stat-card">
                    <div class="stat-card-info">
                        <h6>Pending Appointments</h6>
                        <h2><?php echo $pending_appointments; ?></h2>
                    </div>
                    <div class="stat-card-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card quick-actions">
                    <div class="card-body">
                        <h5 class="card-title mb-4">Quick Actions</h5>
                        <div class="action-buttons">
                            <a href="adminmanagedoct.php" class="action-btn">
                                <i class="fas fa-user-md"></i>
                                Manage Doctors
                            </a>
                            <a href="adminmanagepatient.php" class="action-btn">
                                <i class="fas fa-users"></i>
                                Manage Patients
                            </a>
                            <a href="" class="action-btn">
                                <i class="fas fa-calendar-check"></i>
                                View Appointments
                            </a>
                            <a href="" class="action-btn">
                                <i class="fas fa-chart-bar"></i>
                                Generate Reports
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Appointments -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Recent Appointments</h5>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Doctor</th>
                                        <th>Patient</th>
                                        <th>Status</th>
                                        
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while($appointment = mysqli_fetch_assoc($recent_appointments)): ?>
                                    <tr>
                                        <td><?php echo date('M d, Y', strtotime($appointment['appointment_date'])); ?></td>
                                        <td><?php echo htmlspecialchars($appointment['doctor_name']); ?></td>
                                        <td><?php echo htmlspecialchars($appointment['patient_name']); ?></td>
                                        <td>
                                            <span class="status-badge status-<?php echo strtolower($appointment['status']); ?>">
                                                <?php echo ucfirst($appointment['status']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            
                                            
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
       /* Dashboard Styles */
.admin-title {
    color: #1a237e;
    font-weight: 700;
    margin-bottom: 0.75rem;
    font-size: 2.2rem;
    letter-spacing: -0.5px;
}

/* Stat Cards */
.stat-card {
    background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
    border-radius: 20px;
    padding: 1.75rem;
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.08);
    display: flex;
    justify-content: space-between;
    align-items: center;
    transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
    border: 1px solid rgba(255, 255, 255, 0.8);
}

.stat-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 15px 30px rgba(0, 0, 0, 0.12);
}

.stat-card-info h6 {
    color: #5c6bc0;
    font-size: 1rem;
    margin-bottom: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.stat-card-info h2 {
    color: #1a237e;
    margin-bottom: 0;
    font-weight: 700;
    font-size: 2.5rem;
    line-height: 1;
}

.stat-card-icon {
    width: 60px;
    height: 60px;
    border-radius: 16px;
    background: linear-gradient(135deg, #3949ab 0%, #1a237e 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    transition: transform 0.3s ease;
}

.stat-card:hover .stat-card-icon {
    transform: scale(1.1);
}

.stat-card-icon i {
    font-size: 1.75rem;
    color: white;
}

/* Quick Actions */
.quick-actions {
    border-radius: 20px;
    border: none;
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.08);
    background: white;
}

.action-buttons {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
    gap: 1.25rem;
}

.action-btn {
    display: flex;
    align-items: center;
    gap: 1.25rem;
    padding: 1.25rem;
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-radius: 15px;
    color: #1a237e;
    text-decoration: none;
    transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
    border: 1px solid rgba(255, 255, 255, 0.8);
}

.action-btn:hover {
    background: linear-gradient(135deg, #3949ab 0%, #1a237e 100%);
    color: white;
    transform: translateY(-5px);
    box-shadow: 0 8px 15px rgba(26, 35, 126, 0.2);
}

.action-btn i {
    font-size: 1.4rem;
    transition: transform 0.3s ease;
}

.action-btn:hover i {
    transform: scale(1.1);
}

/* Status Badges */
.status-badge {
    padding: 0.5rem 1rem;
    border-radius: 50px;
    font-size: 0.9rem;
    font-weight: 600;
    letter-spacing: 0.5px;
    text-transform: uppercase;
}

.status-pending {
    background: #fff3cd;
    color: #856404;
    border: 1px solid #ffeeba;
}

.status-confirmed {
    background: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.status-cancelled {
    background: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}

/* Table Styles */
.table {
    margin-bottom: 0;
}

.table th {
    border-top: none;
    color: #3949ab;
    font-weight: 700;
    text-transform: uppercase;
    font-size: 0.9rem;
    letter-spacing: 0.5px;
    padding: 1.25rem 1rem;
}

.table td {
    vertical-align: middle;
    padding: 1.25rem 1rem;
    color: #2a3f54;
    font-weight: 500;
}

/* Card Styles */
.card {
    border-radius: 20px;
    border: none;
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.08);
    background: white;
    overflow: hidden;
}

.card-body {
    padding: 2rem;
}

.card-title {
    color: #1a237e;
    font-weight: 700;
    margin-bottom: 1.75rem;
    font-size: 1.5rem;
    letter-spacing: -0.5px;
}

/* Button Styles */
.btn-info {
    background: linear-gradient(135deg, #29b6f6 0%, #0288d1 100%);
    border: none;
    border-radius: 8px;
    padding: 0.5rem 1rem;
    font-weight: 600;
    transition: all 0.3s ease;
}

.btn-info:hover {
    background: linear-gradient(135deg, #0288d1 0%, #01579b 100%);
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(2, 136, 209, 0.2);
}

/* Responsive Adjustments */
@media (max-width: 768px) {
    .stat-card {
        margin-bottom: 1.25rem;
    }

    .action-buttons {
        grid-template-columns: 1fr;
    }

    .card-body {
        padding: 1.5rem;
    }

    .admin-title {
        font-size: 1.8rem;
    }

    .stat-card-info h2 {
        font-size: 2rem;
    }
}
    </style>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>