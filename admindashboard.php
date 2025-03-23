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

// Query to get appointment status distribution
$appointment_status_query = "SELECT status, COUNT(*) as count FROM appointment_requests GROUP BY status";
$appointment_status_result = mysqli_query($conn, $appointment_status_query);

$status_data = [];
while($status = mysqli_fetch_assoc($appointment_status_result)) {
    $status_data[$status['status']] = $status['count'];
}

// Convert to JSON for charts
$chart_data = json_encode([
    'doctors' => $total_doctors,
    'patients' => $total_patients
]);

$status_chart_data = json_encode($status_data);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - BookMyDoc</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Add Chart.js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
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

       

        <!-- Analytics Section - Charts -->
        <div class="row">
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Doctors vs Patients Analysis</h5>
                        <div class="chart-container">
                            <canvas id="userComparisonChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Appointment Status Distribution</h5>
                        <div class="chart-container">
                            <canvas id="appointmentStatusChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Additional Stats Cards Row - ALIGNED BLOCKS -->
        <div class="row mt-4">
            <div class="col-md-4">
                <div class="card equal-height-card">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title">Patient-Doctor Ratio</h5>
                        <div class="metric-content text-center flex-grow-1 d-flex flex-column justify-content-center">
                            <span class="ratio-number">
                                <?php 
                                    $ratio = $total_doctors > 0 ? round($total_patients / $total_doctors, 1) : 0;
                                    echo $ratio; 
                                ?>
                            </span>
                            <p class="text-muted mb-0">Patients per Doctor</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card equal-height-card">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title">System Growth</h5>
                        <div class="metric-content flex-grow-1 d-flex flex-column justify-content-center">
                            <div class="progress-info">
                                <span>Patients</span>
                                <span><?php echo $total_patients; ?></span>
                            </div>
                            <div class="progress mb-3">
                                <div class="progress-bar bg-primary" role="progressbar" style="width: 100%"></div>
                            </div>
                            <div class="progress-info">
                                <span>Doctors</span>
                                <span><?php echo $total_doctors; ?></span>
                            </div>
                            <div class="progress">
                                <div class="progress-bar bg-success" role="progressbar" style="width: <?php echo ($total_doctors/$total_patients*100); ?>%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card equal-height-card">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title">Appointments Overview</h5>
                        <div class="metric-content appointments-overview flex-grow-1 d-flex flex-column justify-content-center">
                            <div class="d-flex justify-content-between">
                                <div class="appointment-metric">
                                    <div class="metric-value"><?php echo $total_appointments; ?></div>
                                    <div class="metric-label">Total</div>
                                </div>
                                <div class="appointment-metric">
                                    <div class="metric-value"><?php echo $pending_appointments; ?></div>
                                    <div class="metric-label">Pending</div>
                                </div>
                                <div class="appointment-metric">
                                    <div class="metric-value"><?php echo $total_appointments - $pending_appointments; ?></div>
                                    <div class="metric-label">Processed</div>
                                </div>
                            </div>
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

/* Card Styles */
.card {
    border-radius: 20px;
    border: none;
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.08);
    background: white;
    overflow: hidden;
    margin-bottom: 1.5rem;
}

.equal-height-card {
    height: 100%;
    display: flex;
    flex-direction: column;
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

/* Chart Styles */
.chart-container {
    position: relative;
    height: 300px;
    width: 100%;
}

/* Metric Content - For consistent height */
.metric-content {
    height: 200px;
}

/* Ratio Display */
.ratio-number {
    font-size: 4rem;
    font-weight: 700;
    color: #1a237e;
    display: block;
}

/* Progress Styles */
.progress-info {
    display: flex;
    justify-content: space-between;
    margin-bottom: 0.5rem;
    font-weight: 500;
}

.progress {
    height: 12px;
    border-radius: 6px;
    margin-bottom: 1.25rem;
}

/* Appointments Overview */
.appointments-overview {
    text-align: center;
}

.appointment-metric {
    padding: 0.5rem;
}

.metric-value {
    font-size: 2.5rem;
    font-weight: 700;
    color: #1a237e;
}

.metric-label {
    color: #5c6bc0;
    font-weight: 600;
    text-transform: uppercase;
    font-size: 0.85rem;
    letter-spacing: 0.5px;
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
    
    .chart-container {
        height: 250px;
    }
    
    .metric-content {
        height: auto;
        min-height: 150px;
    }
}
    </style>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Parse PHP data for charts
        const userData = <?php echo $chart_data; ?>;
        const statusData = <?php echo $status_chart_data; ?>;
        
        // Initialize charts when DOM is loaded
        document.addEventListener('DOMContentLoaded', function() {
            // Users Comparison Chart (Bar Chart)
            const userCtx = document.getElementById('userComparisonChart').getContext('2d');
            const userChart = new Chart(userCtx, {
                type: 'bar',
                data: {
                    labels: ['Doctors', 'Patients'],
                    datasets: [{
                        label: 'Number of Users',
                        data: [userData.doctors, userData.patients],
                        backgroundColor: [
                            'rgba(54, 162, 235, 0.8)',
                            'rgba(75, 192, 192, 0.8)'
                        ],
                        borderColor: [
                            'rgba(54, 162, 235, 1)',
                            'rgba(75, 192, 192, 1)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return context.parsed.y + ' users';
                                }
                            }
                        }
                    }
                }
            });
            
            // Appointment Status Chart (Doughnut)
            const statusLabels = Object.keys(statusData);
            const statusValues = Object.values(statusData);
            const statusColors = [
                'rgba(255, 99, 132, 0.8)',
                'rgba(54, 162, 235, 0.8)',
                'rgba(255, 206, 86, 0.8)',
                'rgba(75, 192, 192, 0.8)'
            ];
            
            const statusCtx = document.getElementById('appointmentStatusChart').getContext('2d');
            const statusChart = new Chart(statusCtx, {
                type: 'doughnut',
                data: {
                    labels: statusLabels.map(label => label.charAt(0).toUpperCase() + label.slice(1)),
                    datasets: [{
                        data: statusValues,
                        backgroundColor: statusColors.slice(0, statusLabels.length),
                        borderColor: statusColors.map(color => color.replace('0.8', '1')),
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'right'
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const label = context.label || '';
                                    const value = context.parsed || 0;
                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    const percentage = Math.round((value / total) * 100);
                                    return `${label}: ${value} (${percentage}%)`;
                                }
                            }
                        }
                    }
                }
            });
        });
    </script>
</body>
</html>