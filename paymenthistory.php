<?php include('db_connection.php'); include('patientheader.php'); 

// Assume user is logged in, fetch their ID
if (!isset($_SESSION['id'])) {
    echo "<script>alert('Please login first!'); window.location.href='login.php';</script>";
    exit;
}
$user_id = $_SESSION['id'];

// Fetch payment history
$sql = "SELECT p.appointment_id, p.amount, p.payment_method, p.transaction_id, p.payment_date,
                d.name AS doctor_name
        FROM payments p
        JOIN appointment_requests ar ON p.appointment_id = ar.id
        JOIN doctorreg d ON ar.doctor_id = d.id
        WHERE ar.user_id = ?
        ORDER BY p.payment_date DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment History</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f0f2f5;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .payment-container {
            max-width: 950px;
            margin: 40px auto;
            background-color: #fff;
            border-radius: 12px;
            padding: 30px;
            
        }
        .payment-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 8px;
            background: linear-gradient(to right, #3498db, #2ac8dd);
        }
        .page-title {
            color: #2d3748;
            font-weight: 700;
            margin-bottom: 25px;
            padding-bottom: 15px;
            position: relative;
        }
        .page-title i {
            margin-right: 10px;
            color: #38a169;
        }
        .table {
            margin-top: 20px;
            border-radius: 8px;
            overflow: hidden;
            border-collapse: separate;
            border-spacing: 0;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }
        .table thead th {
            background: linear-gradient(to right, #3498db, #2ac8dd);
            color: white;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 14px;
            letter-spacing: 0.5px;
            padding: 15px;
            border: none;
        }
        .table tbody tr {
            transition: all 0.2s ease;
        }
        .table tbody tr:hover {
            background-color: #f8fafb;
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(0,0,0,0.05);
        }
        .table td {
            padding: 15px;
            vertical-align: middle;
            border-top: 1px solid #edf2f7;
            font-size: 14px;
        }
        .appointment-id {
            font-weight: 600;
            color: #3182ce;
        }
        .doctor-name {
            font-weight: 600;
            color: #2d3748;
        }
        .amount {
            font-weight: 600;
            color: #38a169;
        }
        .payment-method {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 20px;
            background-color: #ebf8ff;
            color: #3182ce;
            font-size: 13px;
            font-weight: 500;
        }
        .transaction-id {
            font-family: monospace;
            font-size: 13px;
            color: #718096;
        }
        .payment-date {
            color: #718096;
            font-size: 13px;
        }
        .no-records {
            text-align: center;
            padding: 30px;
            color: #718096;
        }
        @media (max-width: 768px) {
            .payment-container {
                padding: 20px 15px;
                margin: 20px 10px;
            }
            .table-responsive {
                border-radius: 8px;
                overflow: hidden;
            }
        }
    </style>
</head>
<body>

<div class="payment-container">
    <h2 class="page-title"><i class="fas fa-history"></i>Payment History</h2>
    
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th><i class="fas fa-bookmark me-2"></i>Appointment ID</th>
                    <th><i class="fas fa-user-md me-2"></i>Doctor</th>
                    <th><i class="fas fa-rupee-sign me-2"></i>Amount</th>
                    <th><i class="fas fa-credit-card me-2"></i>Method</th>
                    <th><i class="fas fa-hashtag me-2"></i>Transaction ID</th>
                    <th><i class="fas fa-calendar-alt me-2"></i>Date & Time</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td class="appointment-id">APT<?php echo $row['appointment_id']; ?></td>
                            <td class="doctor-name">Dr. <?php echo htmlspecialchars($row['doctor_name']); ?></td>
                            <td class="amount">₹<?php echo number_format($row['amount'], 2); ?></td>
                            <td>
                                <span class="payment-method">
                                    <?php 
                                    $method = htmlspecialchars($row['payment_method']);
                                    $icon = 'fa-credit-card';
                                    
                                    if (stripos($method, 'card') !== false) {
                                        $icon = 'fa-credit-card';
                                    } elseif (stripos($method, 'upi') !== false) {
                                        $icon = 'fa-mobile-alt';
                                    } elseif (stripos($method, 'net') !== false) {
                                        $icon = 'fa-university';
                                    } elseif (stripos($method, 'wallet') !== false) {
                                        $icon = 'fa-wallet';
                                    }
                                    ?>
                                    <i class="fas <?php echo $icon; ?> me-1"></i> <?php echo $method; ?>
                                </span>
                            </td>
                            <td class="transaction-id"><?php echo htmlspecialchars($row['transaction_id']); ?></td>
                            <td class="payment-date">
                                <i class="far fa-clock me-1"></i>
                                <?php echo date('d M Y', strtotime($row['payment_date'])); ?>
                                <br>
                                <small><?php echo date('h:i A', strtotime($row['payment_date'])); ?></small>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="no-records">
                            <i class="fas fa-info-circle me-2"></i>No payment records found
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>