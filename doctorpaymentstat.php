<?php 
session_start(); 
include('db_connection.php');
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

$doctor_id = $_SESSION['id']; // Assuming doctor ID is stored in session
$total_earnings = 0;

// Fetch payments with patient name
$sql = "
    SELECT p.amount, p.payment_date, ar.id, pr.name AS patient_name
    FROM payments p
    INNER JOIN appointment_requests ar ON p.appointment_id = ar.id
    INNER JOIN patientreg pr ON ar.user_id = pr.id
    WHERE ar.doctor_id = ?
    ORDER BY p.payment_date DESC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $doctor_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctor Payment Statistics</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f5f8fa;
        }
        .dashboard-container {
            max-width: 1000px;
            margin: 20px auto;
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }
        .dashboard-header {
            background: linear-gradient(to right, #4e73df, #224abe);
            color: white;
            padding: 25px;
            border-radius: 15px 15px 0 0;
        }
        .dashboard-title {
            font-size: 1.8rem;
            font-weight: 600;
            margin: 0;
        }
        .dashboard-subtitle {
            font-size: 1rem;
            opacity: 0.9;
            margin-top: 5px;
        }
        .dashboard-body {
            padding: 25px;
        }
        .earnings-summary {
            display: flex;
            justify-content: space-between;
            margin-bottom: 25px;
        }
        .earnings-card {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.03);
            padding: 20px;
            width: 48%;
            border-left: 4px solid #4e73df;
        }
        .earnings-card h3 {
            font-size: 1.1rem;
            color: #5a5c69;
            margin-bottom: 10px;
        }
        .earnings-card .amount {
            font-size: 1.8rem;
            font-weight: 700;
            color: #333;
        }
        .table-responsive {
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.03);
        }
        .table {
            margin-bottom: 0;
        }
        .table thead th {
            background-color: #4e73df;
            color: white;
            border: none;
            padding: 15px;
            font-weight: 500;
        }
        .table tbody tr:hover {
            background-color: #f8f9fc;
        }
        .table td {
            padding: 15px;
            vertical-align: middle;
            border-color: #e3e6f0;
        }
        .badge {
            padding: 8px 12px;
            border-radius: 30px;
            font-weight: 500;
        }
        .badge-success {
            background-color: #1cc88a;
        }
        .transaction-date {
            color: #6c757d;
            font-size: 0.9rem;
        }
        .patient-name {
            font-weight: 500;
            color: #333;
        }
        .empty-state {
            text-align: center;
            padding: 50px 0;
            color: #6c757d;
        }
        .empty-state i {
            font-size: 3rem;
            margin-bottom: 20px;
            color: #e3e6f0;
        }
        .total-footer {
            background-color: #f8f9fc;
            padding: 20px 25px;
            text-align: right;
            border-top: 1px solid #e3e6f0;
            font-size: 1.1rem;
        }
        .total-label {
            color: #5a5c69;
            font-weight: 500;
        }
        .total-amount {
            font-weight: 700;
            color: #4e73df;
            margin-left: 10px;
        }
    </style>
</head>
<body>

<div class="dashboard-container">
    <div class="dashboard-header">
        <h1 class="dashboard-title">Payment Statistics</h1>
        <p class="dashboard-subtitle">Track your earnings and payment history</p>
    </div>
    
    <div class="dashboard-body">
        <div class="earnings-summary">
            <div class="earnings-card">
                <h3><i class="fas fa-wallet me-2"></i>Total Earnings</h3>
                <div class="amount">₹<?php 
                    // Calculate total earnings
                    $total_earnings = 0;
                    $result_copy = $result;
                    while ($row = $result_copy->fetch_assoc()) {
                        $total_earnings += $row['amount'];
                    }
                    echo number_format($total_earnings, 2); 
                ?></div>
            </div>
            <div class="earnings-card">
                <h3><i class="fas fa-calendar-check me-2"></i>Transactions</h3>
                <div class="amount"><?php echo $result->num_rows; ?></div>
            </div>
        </div>
        
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th><i class="fas fa-hashtag me-2"></i>Appointment ID</th>
                        <th><i class="fas fa-user me-2"></i>Patient Name</th>
                        <th><i class="fas fa-rupee-sign me-2"></i>Amount</th>
                        <th><i class="fas fa-calendar me-2"></i>Payment Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $result->data_seek(0); // Reset pointer to beginning of result set
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()): 
                    ?>
                        <tr>
                            <td>#<?php echo $row['id']; ?></td>
                            <td class="patient-name"><?php echo $row['patient_name']; ?></td>
                            <td><span class="badge badge-success">₹<?php echo number_format($row['amount'], 2); ?></span></td>
                            <td class="transaction-date"><?php echo date("d M Y, h:i A", strtotime($row['payment_date'])); ?></td>
                        </tr>
                    <?php 
                        endwhile; 
                    } else { 
                    ?>
                        <tr>
                            <td colspan="4" class="empty-state">
                                <i class="fas fa-file-invoice-dollar d-block"></i>
                                No payment records found
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <div class="total-footer">
        <span class="total-label">Total Earnings:</span>
        <span class="total-amount">₹<?php echo number_format($total_earnings, 2); ?></span>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php 
$stmt->close(); 
$conn->close(); 
?>