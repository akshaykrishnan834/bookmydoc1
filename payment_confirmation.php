<?php
// Include necessary files
include('db_connection.php');
include('patientheader.php');

// Check if appointment ID is provided
if (!isset($_GET['appointment_id']) || empty($_GET['appointment_id'])) {
    echo "<script>alert('No appointment selected!'); window.location.href='my_appointments.php';</script>";
    exit;
}

// Get appointment ID from URL
$appointment_id = $_GET['appointment_id'];

// Fetch the specific appointment and payment details
$sql = "SELECT ar.id, d.name AS doctor_name, d.specialization, ar.appointment_date,
               da.start_time, da.end_time, ar.status, ar.created_at,
               u.name AS patient_name, u.email, u.phone,
               d.fees AS consultation_fee,
               p.transaction_id, p.order_id, p.amount, p.payment_date
        FROM appointment_requests ar
        JOIN doctorreg d ON ar.doctor_id = d.id
        JOIN doctor_availability da ON ar.slot_id = da.id
        JOIN patientreg u ON ar.user_id = u.id
        JOIN payments p ON ar.id = p.appointment_id
        WHERE ar.id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $appointment_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "<script>alert('Payment information not found!'); window.location.href='my_appointments.php';</script>";
    exit;
}

$data = $result->fetch_assoc();

// Format appointment ID with APT prefix
$formatted_apt_id = 'APT' . $appointment_id;
// Format patient ID with PAT prefix
$formatted_pat_id = 'PAT' . $data['id'];
// Format date for display
$payment_date = date('Y-m-d', strtotime($data['payment_date']));
$payment_time = date('h:i A', strtotime($data['payment_date']));
// Format amount
$amount = number_format($data['amount'], 2);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Confirmation</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .container2 {
            max-width: 800px;
            margin-top: 30px;
            margin-left: auto;
            margin-right: auto;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            padding: 30px;
        }
        .success-icon {
            text-align: center;
            margin-bottom: 20px;
        }
        .success-icon svg {
            width: 80px;
            height: 80px;
            color: #38a169;
        }
        .page-header {
            text-align: center;
            margin-bottom: 30px;
        }
        .page-header h1 {
            color: #38a169;
            font-size: 32px;
            font-weight: 600;
        }
        .page-header p {
            color: #6b7280;
            font-size: 16px;
            margin-top: 10px;
        }
        .payment-details {
            background-color: #f0f9f0;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 30px;
        }
        .payment-details h2 {
            font-size: 20px;
            margin-bottom: 15px;
            color: #2d3748;
            font-weight: 600;
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #e2e8f0;
        }
        .detail-row:last-child {
            border-bottom: none;
        }
        .detail-label {
            color: #4a5568;
            font-weight: 500;
        }
        .detail-value {
            font-weight: 600;
            color: #1a202c;
        }
        .appointment-details {
            margin-bottom: 30px;
        }
        .appointment-details h2 {
            font-size: 20px;
            margin-bottom: 15px;
            color: #2d3748;
            font-weight: 600;
        }
        .action-btns {
            display: flex;
            justify-content: space-between;
            margin-top: 30px;
        }
        .action-btn {
            flex: 1;
            margin: 0 10px;
            padding: 12px 20px;
            font-size: 16px;
            font-weight: 500;
            text-align: center;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.2s;
        }
        .btn-primary {
            background-color: #38a169;
            color: white;
            border: none;
        }
        .btn-primary:hover {
            background-color: #2f855a;
        }
        .btn-secondary {
            background-color: #f3f4f6;
            color: #4b5563;
            border: 1px solid #d1d5db;
        }
        .btn-secondary:hover {
            background-color: #e5e7eb;
        }
        .transaction-id {
            font-family: monospace;
            font-size: 14px;
            background-color: #f9fafb;
            padding: 2px 6px;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <div class="container2">
        <div class="success-icon">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                <circle cx="12" cy="12" r="11" stroke-width="2" fill="none" />
            </svg>
        </div>
        
        <div class="page-header">
            <h1>Payment Successful!</h1>
            <p>Your appointment has been confirmed and payment has been processed successfully.</p>
        </div>

        <div class="payment-details">
            <h2>Payment Information</h2>
            <div class="detail-row">
                <div class="detail-label">Transaction ID</div>
                <div class="detail-value transaction-id"><?php echo htmlspecialchars($data['transaction_id']); ?></div>
            </div>
            <div class="detail-row">
                <div class="detail-label">Order ID</div>
                <div class="detail-value transaction-id"><?php echo htmlspecialchars($data['order_id']); ?></div>
            </div>
            <div class="detail-row">
                <div class="detail-label">Amount Paid</div>
                <div class="detail-value">₹<?php echo $amount; ?></div>
            </div>
            <div class="detail-row">
                <div class="detail-label">Payment Date</div>
                <div class="detail-value"><?php echo $payment_date; ?></div>
            </div>
            <div class="detail-row">
                <div class="detail-label">Payment Time</div>
                <div class="detail-value"><?php echo $payment_time; ?></div>
            </div>
        </div>

        <div class="appointment-details">
            <h2>Appointment Details</h2>
            <div class="detail-row">
                <div class="detail-label">Appointment ID</div>
                <div class="detail-value"><?php echo $formatted_apt_id; ?></div>
            </div>
            <div class="detail-row">
                <div class="detail-label">Doctor</div>
                <div class="detail-value">Dr. <?php echo htmlspecialchars($data['doctor_name']); ?></div>
            </div>
            <div class="detail-row">
                <div class="detail-label">Specialization</div>
                <div class="detail-value"><?php echo htmlspecialchars($data['specialization']); ?></div>
            </div>
            <div class="detail-row">
                <div class="detail-label">Date</div>
                <div class="detail-value"><?php echo date('Y-m-d', strtotime($data['appointment_date'])); ?></div>
            </div>
            <div class="detail-row">
                <div class="detail-label">Time</div>
                <div class="detail-value"><?php echo date('h:i A', strtotime($data['start_time'])); ?></div>
            </div>
        </div>

        <div class="action-btns">
            <a href="my_appointments.php" class="action-btn btn-secondary">View All Appointments</a>
            <a href="appointment_details.php?appointment_id=<?php echo $appointment_id; ?>" class="action-btn btn-primary">View Appointment Details</a>
        </div>
    </div>

    <script>
        // You can add any JavaScript you need here
    </script>
</body>
</html>