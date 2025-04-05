<?php 
include('db_connection.php'); 
include('patientheader.php'); 

if (!isset($_SESSION['id'])) {
    echo "<script>alert('Please login first!'); window.location.href='login.php';</script>";
    exit;
}
$user_id = $_SESSION['id'];

// Fetch payment history with all relevant details
$sql = "SELECT 
    p.id AS payment_id,
    p.appointment_id,
    p.amount,
    p.payment_method,
    p.transaction_id,
    p.payment_date,
    p.status AS payment_status,
    d.name AS doctor_name,
    d.specialization AS doctor_specialization,
    d.phone AS doctor_phone,
    d.email AS doctor_email,
    d.address AS doctor_address,
    d.fees AS doctor_fees,
    u.name AS patient_name,
    u.email AS patient_email,
    u.phone AS patient_phone,
    u.age AS patient_age,
    u.dob AS patient_dob,
    u.gender AS patient_gender,
    ar.appointment_date,
    ar.patient_condition,
    da.start_time,
    da.end_time
FROM payments p
JOIN appointment_requests ar ON p.appointment_id = ar.id
JOIN doctorreg d ON ar.doctor_id = d.id
JOIN patientreg u ON ar.user_id = u.id
JOIN doctor_availability da ON ar.slot_id = da.id
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
            max-width: 1050px;
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
        @media print {
            .no-print {
                display: none !important;
            }
            .invoice-box {
                width: 100%;
                margin: 0;
                padding: 20px;
                font-family: Arial, sans-serif;
                font-size: 12pt;
                line-height: 1.5;
                color: #000;
            }
            @page {
                size: A4;
                margin: 1cm;
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
                    <th><i class="fas fa-print me-2"></i>Action</th>
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
                                    if (stripos($method, 'card') !== false) $icon = 'fa-credit-card';
                                    elseif (stripos($method, 'upi') !== false) $icon = 'fa-mobile-alt';
                                    elseif (stripos($method, 'net') !== false) $icon = 'fa-university';
                                    elseif (stripos($method, 'wallet') !== false) $icon = 'fa-wallet';
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
                            <td>
                                <button class="btn btn-sm btn-primary no-print" 
                                        onclick="printInvoice(<?php echo htmlspecialchars(json_encode($row)); ?>)">
                                    <i class="fas fa-print"></i> Print
                                </button>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="no-records">
                            <i class="fas fa-info-circle me-2"></i>No payment records found
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Formal Invoice Template -->
<div id="invoice-template" style="display: none;">
    <div class="invoice-box">
        <table style="width: 100%; border-bottom: 2px solid #000; margin-bottom: 20px;" cellpadding="0" cellspacing="0">
            <tr>
                <td style="width: 50%;">
                    <div style="display: flex; align-items: center;">
                        <img src="images/logo.png" style="max-width: 50px; margin-right: 10px;" alt="Clinic Logo">
                        <div>
                            <strong style="font-size: 14pt;">BOOK MY DOC</strong><br>
                            <small style="font-size: 8pt;">Online Doctor Appointment Booking System</small>
                        </div>
                    </div>
                </td>
                <td style="width: 50%; text-align: right;">
                    <h2 style="margin: 0; font-size: 12pt;">PAYMENT INVOICE</h2>
                    <div style="font-size: 10pt;">
                        Invoice No: INV-<span id="invoice-apt-id"></span><br>
                        Date: <?php echo date('d/m/Y'); ?>
                    </div>
                </td>
            </tr>
        </table>

        <table style="width: 100%; margin-bottom: 20px;" cellpadding="0" cellspacing="0">
            <tr>
                <td style="width: 50%; vertical-align: top;">
                    <strong>From:</strong><br>
                    Dr. <span id="invoice-doctor-name-from"></span><br>
                    <span id="invoice-doctor-address"></span><br>
                    Phone: <span id="invoice-doctor-phone-from"></span><br>
                    Email: <span id="invoice-doctor-email-from"></span>
                </td>
                <td style="width: 50%; vertical-align: top; text-align: right;">
                    <strong>Billed To:</strong><br>
                    <span id="invoice-patient-name"></span><br>
                    <span id="invoice-patient-email"></span><br>
                    <span id="invoice-patient-phone"></span><br>
                    Age: <span id="invoice-patient-age"></span><br>
                    Gender: <span id="invoice-patient-gender"></span>
                </td>
            </tr>
        </table>

        <table style="width: 100%; margin-bottom: 20px;" cellpadding="0" cellspacing="0">
            <tr>
                <td style="width: 50%; vertical-align: top;">
                    <strong>Doctor Details:</strong><br>
                    Dr. <span id="invoice-doctor-name"></span><br>
                    Specialization: <span id="invoice-doctor-specialization"></span><br>
                    Phone: <span id="invoice-doctor-phone"></span><br>
                    Email: <span id="invoice-doctor-email"></span><br>
                    Address: <span id="invoice-doctor-address-details"></span>
                </td>
                <td style="width: 50%; vertical-align: top; text-align: right;">
                    <strong>Appointment Details:</strong><br>
                    Date: <span id="invoice-appointment-date"></span><br>
                    Time: <span id="invoice-start-time"></span> - <span id="invoice-end-time"></span><br>
                    Condition: <span id="invoice-patient-condition"></span>
                </td>
            </tr>
        </table>

        <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;" cellpadding="5">
            <tr style="border-bottom: 1px solid #000;">
                <th style="text-align: left;">Description</th>
                <th style="text-align: right;">Amount (₹)</th>
            </tr>
            <tr style="border-bottom: 1px solid #ccc;">
                <td>Consultation Fee (APT<span id="invoice-apt-id-table"></span>)</td>
                <td style="text-align: right;"><span id="invoice-amount"></span></td>
            </tr>
            <tr>
                <td style="text-align: right; font-weight: bold;">Total</td>
                <td style="text-align: right; font-weight: bold;">₹<span id="invoice-amount-total"></span></td>
            </tr>
        </table>

        <table style="width: 100%; margin-bottom: 20px;" cellpadding="0" cellspacing="0">
            <tr>
                <td>
                    <strong>Payment Details:</strong><br>
                    Transaction ID: <span id="invoice-transaction"></span><br>
                    Date & Time: <span id="invoice-payment-date"></span><br>
                    Payment Method: <span id="invoice-method"></span><br>
                    Status: <span id="invoice-payment-status"></span>
                </td>
            </tr>
        </table>

        <table style="width: 100%; border-top: 1px solid #000; padding-top: 10px;" cellpadding="0" cellspacing="0">
            <tr>
                <td style="font-size: 10pt;">
                    <strong>Terms & Conditions:</strong><br>
                    1. Payment is non-refundable<br>
                    2. Please retain this invoice for your records<br>
                    3. Contact us for any discrepancies within 7 days
                </td>
                <td style="text-align: right; font-size: 10pt;">
                    Authorized Signatory<br>
                    _____________________
                </td>
            </tr>
        </table>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
<script>
function printInvoice(paymentData) {
    document.getElementById('invoice-apt-id').textContent = paymentData.appointment_id;
    document.getElementById('invoice-apt-id-table').textContent = paymentData.appointment_id;
    document.getElementById('invoice-patient-name').textContent = paymentData.patient_name;
    document.getElementById('invoice-patient-email').textContent = paymentData.patient_email;
    document.getElementById('invoice-patient-phone').textContent = paymentData.patient_phone;
    document.getElementById('invoice-patient-age').textContent = paymentData.patient_age;
    document.getElementById('invoice-patient-gender').textContent = paymentData.patient_gender;
    document.getElementById('invoice-doctor-name').textContent = paymentData.doctor_name;
    document.getElementById('invoice-doctor-name-from').textContent = paymentData.doctor_name;
    document.getElementById('invoice-doctor-specialization').textContent = paymentData.doctor_specialization;
    document.getElementById('invoice-doctor-phone').textContent = paymentData.doctor_phone;
    document.getElementById('invoice-doctor-phone-from').textContent = paymentData.doctor_phone;
    document.getElementById('invoice-doctor-email').textContent = paymentData.doctor_email;
    document.getElementById('invoice-doctor-email-from').textContent = paymentData.doctor_email;
    document.getElementById('invoice-doctor-address').textContent = paymentData.doctor_address;
    document.getElementById('invoice-doctor-address-details').textContent = paymentData.doctor_address;
    document.getElementById('invoice-appointment-date').textContent = 
        new Date(paymentData.appointment_date).toLocaleDateString('en-IN', {dateStyle: 'medium'});
    document.getElementById('invoice-start-time').textContent = paymentData.start_time;
    document.getElementById('invoice-end-time').textContent = paymentData.end_time;
    document.getElementById('invoice-patient-condition').textContent = paymentData.patient_condition || 'Not specified';
    document.getElementById('invoice-amount').textContent = Number(paymentData.amount).toFixed(2);
    document.getElementById('invoice-amount-total').textContent = Number(paymentData.amount).toFixed(2);
    document.getElementById('invoice-transaction').textContent = paymentData.transaction_id;
    document.getElementById('invoice-payment-date').textContent = 
        new Date(paymentData.payment_date).toLocaleString('en-IN', {
            dateStyle: 'medium',
            timeStyle: 'short'
        });
    document.getElementById('invoice-method').textContent = paymentData.payment_method;
    document.getElementById('invoice-payment-status').textContent = paymentData.payment_status;

    // Create a new window for printing
    const printWindow = window.open('', '_blank');
    
    // Write the HTML content to the new window with A4-specific styling
    printWindow.document.write(`
        <!DOCTYPE html>
        <html>
        <head>
            <title>Invoice</title>
            <style>
                /* A4 paper styling */
                @page {
                    size: A4;
                    margin: 1.5cm;
                }
                
                body {
                    font-family: Arial, sans-serif;
                    margin: 0;
                    padding: 0;
                    width: 210mm; /* A4 width */
                    height: 297mm; /* A4 height */
                    background-color: white;
                }
                
                .invoice-box {
                    width: 100%;
                    max-width: 210mm;
                    margin: 0 auto;
                    padding: 20px;
                    box-sizing: border-box;
                }
                
                /* Table styling for better alignment */
                table {
                    width: 100%;
                    border-collapse: collapse;
                    margin-bottom: 15px;
                }
                
                td, th {
                    padding: 8px;
                    vertical-align: top;
                }
                
                /* Ensure content fits on A4 */
                .invoice-box * {
                    max-width: 100%;
                    overflow-wrap: break-word;
                }
                
                /* Hide elements with no-print class */
                .no-print {
                    display: none !important;
                }
                
                /* Ensure proper page breaks */
                .page-break {
                    page-break-after: always;
                }
            </style>
        </head>
        <body>
            <div class="invoice-box">
                ${document.getElementById('invoice-template').innerHTML}
            </div>
        </body>
        </html>
    `);
    
    printWindow.document.close();
    
    // Wait for resources to load before printing
    setTimeout(function() {
        printWindow.focus();
        printWindow.print();
        // Close the window after printing (or if printing is cancelled)
        setTimeout(function() {
            printWindow.close();
        }, 1000);
    }, 500);
}
</script>
</body>
</html>