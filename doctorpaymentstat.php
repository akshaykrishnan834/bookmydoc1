<?php 
session_start(); 
include('db_connection.php');
include('doctorheader.php');

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "bookmydoc";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$doctor_id = $_SESSION['id'];
$total_earnings = 0;

// Get doctor's name and details
$doctor_sql = "SELECT name, specialization, email, phone FROM doctorreg WHERE id = ?";
$doctor_stmt = $conn->prepare($doctor_sql);
$doctor_stmt->bind_param("i", $doctor_id);
$doctor_stmt->execute();
$doctor_result = $doctor_stmt->get_result();
$doctor_data = ($doctor_result->num_rows > 0) ? $doctor_result->fetch_assoc() : 
    ["name" => "Doctor", "specialization" => "Specialist", "email" => "", "phone" => ""];

// Get payment data
$sql = "
    SELECT 
    p.amount,
    p.payment_date,
    ar.id,
    ar.appointment_date,
    pr.name AS patient_name,
    da.start_time,
    da.end_time
FROM 
    payments p
    INNER JOIN appointment_requests ar ON p.appointment_id = ar.id
    INNER JOIN patientreg pr ON ar.user_id = pr.id
    INNER JOIN doctor_availability da ON ar.doctor_id = da.doctor_id 
WHERE 
    ar.doctor_id = ?
ORDER BY 
    p.payment_date DESC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $doctor_id);
$stmt->execute();
$result = $stmt->get_result();

// Calculate monthly earnings
$monthly_earnings = [];
$current_month = date('M Y');
$monthly_transactions = [];

// Calculate earnings by month
$result_copy = $result->data_seek(0);
while ($row = $result->fetch_assoc()) {
    $payment_date = new DateTime($row['payment_date']);
    $month_key = $payment_date->format('M Y');
    
    if (!isset($monthly_earnings[$month_key])) {
        $monthly_earnings[$month_key] = 0;
        $monthly_transactions[$month_key] = 0;
    }
    
    $monthly_earnings[$month_key] += $row['amount'];
    $monthly_transactions[$month_key]++;
}

// Calculate total earnings
$result->data_seek(0);
$total_earnings = 0;
$transaction_count = 0;
while ($row = $result->fetch_assoc()) {
    $total_earnings += $row['amount'];
    $transaction_count++;
}

// Calculate average earnings per transaction
$avg_transaction = ($transaction_count > 0) ? $total_earnings / $transaction_count : 0;
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
        
        /* Report styles - only visible when printing */
        .print-report {
            display: none;
        }
        
        /* Print-specific styles */
        @media print {
            .no-print {
                display: none !important;
            }
            .print-only {
                display: block !important;
            }
            .dashboard-container {
                display: none !important;
            }
            header, nav, .navbar {
                display: none !important;
            }
            body {
                background-color: white !important;
                margin: 0;
                padding: 0;
            }
            
            /* Show print report */
            .print-report {
                display: block !important;
                position: fixed;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
                width: 100%;
                text-align: center;
                font-size: 36px;
                font-weight: bold;
                color: black;
                z-index: 9999;
            }
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
        <div class="no-print mb-3 text-end">
            <button onclick="window.location.href='print_report.php'" class="btn btn-primary">
                Generate Consolidated Report
            </button>
        </div>

        <div class="earnings-summary">
            <div class="earnings-card">
                <h3><i class="fas fa-wallet me-2"></i>Total Earnings</h3>
                <div class="amount">₹<?php echo number_format($total_earnings, 2); ?></div>
            </div>
            <div class="earnings-card">
                <h3><i class="fas fa-calendar-check me-2"></i>Transactions</h3>
                <div class="amount"><?php echo $transaction_count; ?></div>
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
                    $result->data_seek(0);
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()): 
                    ?>
                        <tr>
                            <td>#<?php echo $row['id']; ?></td>
                            <td class="patient-name"><?php echo htmlspecialchars($row['patient_name']); ?></td>
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
        <div class="print-only mt-2">
            <small>Generated on: <?php echo date("d M Y, h:i A"); ?></small>
        </div>
    </div>
    
    <!-- FORMAL REPORT - Only visible when printing -->
    <div class="print-section" id="printSection">
        <div style="display: flex; justify-content: center; align-items: center; min-height: 100vh;">
            <div style="font-size: 24pt; text-transform: uppercase;">
                Demo Book My Doc
            </div>
        </div>
    </div>
        </div>
        
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
<script>
function printReport() {
    // Create a new window for printing
    var printWindow = window.open('', '_blank');
    
    // Get the current date
    var today = new Date();
    var dateStr = today.toLocaleDateString();
    
    // Write the content to the new window
    printWindow.document.write(`
        <!DOCTYPE html>
        <html>
        <head>
            <title>Bookings and Earnings Report</title>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    margin: 20px;
                    color: #333;
                }
                .report-header {
                    text-align: center;
                    margin-bottom: 30px;
                    border-bottom: 2px solid #333;
                    padding-bottom: 20px;
                }
                .report-header h1 {
                    margin: 0;
                    font-size: 24px;
                    font-weight: bold;
                }
                .report-header p {
                    margin: 5px 0 0 0;
                    font-size: 14px;
                }
                .report-summary {
                    margin-bottom: 30px;
                }
                .report-summary h2 {
                    font-size: 18px;
                    font-weight: bold;
                    margin-bottom: 10px;
                    border-bottom: 1px solid #ddd;
                    padding-bottom: 5px;
                }
                .summary-table {
                    width: 100%;
                    border-collapse: collapse;
                    margin-bottom: 20px;
                }
                .summary-table th, .summary-table td {
                    border: 1px solid #ddd;
                    padding: 10px;
                    text-align: left;
                }
                .summary-table th {
                    background-color: #f2f2f2;
                    font-weight: bold;
                }
                .summary-table tr:nth-child(even) {
                    background-color: #f9f9f9;
                }
                .bookings-table {
                    width: 100%;
                    border-collapse: collapse;
                    margin-bottom: 20px;
                }
                .bookings-table th, .bookings-table td {
                    border: 1px solid #ddd;
                    padding: 10px;
                    text-align: left;
                }
                .bookings-table th {
                    background-color: #f2f2f2;
                    font-weight: bold;
                }
                .bookings-table tr:nth-child(even) {
                    background-color: #f9f9f9;
                }
                .report-footer {
                    margin-top: 50px;
                    text-align: center;
                    font-size: 12px;
                    color: #666;
                    border-top: 1px solid #ddd;
                    padding-top: 20px;
                }
                .signature-line {
                    margin-top: 80px;
                    border-top: 1px solid #333;
                    width: 250px;
                    margin-left: auto;
                    margin-right: auto;
                    text-align: center;
                    padding-top: 10px;
                }
                @media print {
                    body {
                        margin: 0;
                        padding: 20px;
                    }
                    .summary-table th, .bookings-table th {
                        background-color: #f2f2f2 !important;
                        -webkit-print-color-adjust: exact;
                        print-color-adjust: exact;
                    }
                    .summary-table tr:nth-child(even), .bookings-table tr:nth-child(even) {
                        background-color: #f9f9f9 !important;
                        -webkit-print-color-adjust: exact;
                        print-color-adjust: exact;
                    }
                }
            </style>
        </head>
        <body>
            <div class="report-header">
                <h1>BOOKINGS AND EARNINGS REPORT</h1>
                <p>Generated on: ${dateStr}</p>
        </div>
        
        <div class="report-summary">
            <h2>Financial Summary</h2>
                <table class="summary-table">
                <tr>
                    <th width="70%">Description</th>
                    <th width="30%">Amount (₹)</th>
                </tr>
                <tr>
                    <td>Total Gross Earnings</td>
                        <td>₹${<?php echo number_format($total_earnings, 2); ?>}</td>
                    </tr>
                    <tr>
                        <td>Average Earnings Per Booking</td>
                        <td>₹${<?php echo number_format($avg_transaction, 2); ?>}</td>
                    </tr>
                    <tr>
                        <td>Current Month Earnings (${<?php echo date('M Y'); ?>})</td>
                        <td>₹${<?php echo number_format($monthly_earnings[$current_month] ?? 0, 2); ?>}</td>
                </tr>
                <tr>
                        <td>Current Month Bookings (${<?php echo date('M Y'); ?>})</td>
                        <td>${<?php echo $monthly_transactions[$current_month] ?? 0; ?>}</td>
                </tr>
                <tr>
                        <td>Total Bookings</td>
                        <td>${<?php echo $transaction_count; ?>}</td>
                </tr>
            </table>
        </div>
        
            <div class="report-summary">
                <h2>Monthly Bookings & Earnings Breakdown</h2>
                <table class="summary-table">
                    <tr>
                        <th width="30%">Month</th>
                        <th width="20%">Bookings</th>
                        <th width="25%">Earnings (₹)</th>
                        <th width="25%">Avg. Per Booking (₹)</th>
                </tr>
                <?php 
                krsort($monthly_earnings); // Sort by month (newest first)
                foreach ($monthly_earnings as $month => $amount): 
                        $month_bookings = $monthly_transactions[$month];
                        $month_avg = ($month_bookings > 0) ? $amount / $month_bookings : 0;
                ?>
                <tr>
                        <td>${<?php echo $month; ?>}</td>
                        <td>${<?php echo $month_bookings; ?>}</td>
                        <td>₹${<?php echo number_format($amount, 2); ?>}</td>
                        <td>₹${<?php echo number_format($month_avg, 2); ?>}</td>
                </tr>
                <?php endforeach; ?>
                <tr>
                    <th>Total</th>
                        <th>${<?php echo $transaction_count; ?>}</th>
                        <th>₹${<?php echo number_format($total_earnings, 2); ?>}</th>
                        <th>₹${<?php echo number_format($avg_transaction, 2); ?>}</th>
                </tr>
            </table>
        </div>
        
            <div class="report-summary">
                <h2>Recent Bookings</h2>
                <table class="bookings-table">
                    <tr>
                        <th>Booking ID</th>
                    <th>Patient Name</th>
                    <th>Appointment Date</th>
                    <th>Payment Date</th>
                    <th>Amount (₹)</th>
                </tr>
                <?php 
                $result->data_seek(0);
                    $count = 0;
                if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc() && $count < 10): 
                            $count++;
                ?>
                <tr>
                        <td>#${<?php echo $row['id']; ?>}</td>
                        <td>${<?php echo htmlspecialchars($row['patient_name']); ?>}</td>
                        <td>${<?php echo date("d M Y", strtotime($row['appointment_date'])); ?>}</td>
                        <td>${<?php echo date("d M Y", strtotime($row['payment_date'])); ?>}</td>
                        <td>₹${<?php echo number_format($row['amount'], 2); ?>}</td>
                </tr>
                <?php 
                    endwhile; 
                } else { 
                ?>
                <tr>
                        <td colspan="5" style="text-align: center;">No booking records found</td>
                </tr>
                <?php } ?>
            </table>
        </div>
        
        <div class="report-footer">
            <div class="signature-line">
                Doctor's Signature
            </div>
            <p>This is a computer-generated report and does not require a physical signature.</p>
        </div>
        </body>
        </html>
    `);
    
    // Close the document
    printWindow.document.close();
    
    // Wait for the content to load
    printWindow.onload = function() {
        // Print the window
        printWindow.print();
    };
}
</script>
</body>
</html>

<?php 
$stmt->close();
if (isset($doctor_stmt)) $doctor_stmt->close();
$conn->close(); 
?>