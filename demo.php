<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Quick Access</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Arial', sans-serif;
        }
        .quick-access-container {
            max-width: 1200px;
            margin: 40px auto;
        }
        .quick-access-card {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            transition: 0.3s;
            text-align: center;
        }
        .quick-access-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }
        .quick-access-card i {
            font-size: 40px;
            margin-bottom: 10px;
        }
        .btn-quick {
            width: 100%;
            margin-top: 10px;
        }
        .quick-header {
            text-align: center;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>

<div class="container quick-access-container">
    <h2 class="quick-header">⚡ Quick Access Panel</h2>
    <div class="row g-4">
        
        <!-- Approve Doctors -->
        <div class="col-md-4">
            <div class="quick-access-card">
                <i class="fas fa-user-check text-success"></i>
                <h5>Approve Doctors</h5>
                <p>Review and approve new doctor registrations.</p>
                <a href="approve_doctors.php" class="btn btn-success btn-quick">Go</a>
            </div>
        </div>

        <!-- View Pending Appointments -->
        <div class="col-md-4">
            <div class="quick-access-card">
                <i class="fas fa-calendar-check text-primary"></i>
                <h5>Pending Appointments</h5>
                <p>Manage and approve upcoming patient appointments.</p>
                <a href="pending_appointments.php" class="btn btn-primary btn-quick">Go</a>
            </div>
        </div>

        <!-- Manage Payments -->
        <div class="col-md-4">
            <div class="quick-access-card">
                <i class="fas fa-credit-card text-warning"></i>
                <h5>Payments & Transactions</h5>
                <p>Track all payments and pending transactions.</p>
                <a href="payments.php" class="btn btn-warning btn-quick">Go</a>
            </div>
        </div>

        <!-- View Feedback -->
        <div class="col-md-4">
            <div class="quick-access-card">
                <i class="fas fa-star text-info"></i>
                <h5>Manage Feedback</h5>
                <p>Monitor and respond to patient feedback & reviews.</p>
                <a href="feedback.php" class="btn btn-info btn-quick">Go</a>
            </div>
        </div>

        <!-- Send Notifications -->
        <div class="col-md-4">
            <div class="quick-access-card">
                <i class="fas fa-bell text-danger"></i>
                <h5>Send Notifications</h5>
                <p>Notify patients and doctors about important updates.</p>
                <a href="send_notifications.php" class="btn btn-danger btn-quick">Go</a>
            </div>
        </div>

        <!-- Generate Reports -->
        <div class="col-md-4">
            <div class="quick-access-card">
                <i class="fas fa-chart-line text-secondary"></i>
                <h5>Generate Reports</h5>
                <p>View statistics and download reports.</p>
                <a href="reports.php" class="btn btn-secondary btn-quick">Go</a>
            </div>
        </div>

    </div>
</div>

</body>
</html>
