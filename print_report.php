<?php
session_start();
include('db_connection.php');

// Check if user is logged in
if (!isset($_SESSION['id']) || empty($_SESSION['id'])) {
    header("Location: doctorlog.php");
    exit();
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
        ar.id as booking_id,
        ar.appointment_date,
        pr.name AS patient_name,
        pr.id as patient_id
    FROM 
        appointment_requests ar
        LEFT JOIN payments p ON ar.id = p.appointment_id
        LEFT JOIN patientreg pr ON ar.user_id = pr.id
    WHERE 
        ar.doctor_id = ?
    ORDER BY 
        ar.appointment_date DESC
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
while ($row = $result->fetch_assoc()) {
    if (!empty($row['payment_date'])) {
        $payment_date = new DateTime($row['payment_date']);
        $month_key = $payment_date->format('M Y');
        
        if (!isset($monthly_earnings[$month_key])) {
            $monthly_earnings[$month_key] = 0;
            $monthly_transactions[$month_key] = 0;
        }
        
        $monthly_earnings[$month_key] += floatval($row['amount'] ?? 0);
        $monthly_transactions[$month_key]++;
    }
}

// Calculate total earnings
$result->data_seek(0);
$total_earnings = 0;
$transaction_count = 0;
while ($row = $result->fetch_assoc()) {
    if (!empty($row['amount'])) {
        $total_earnings += floatval($row['amount']);
        $transaction_count++;
    }
}

// Calculate average earnings per transaction
$avg_transaction = ($transaction_count > 0) ? $total_earnings / $transaction_count : 0;

// Calculate year-to-date earnings
$current_year = date('Y');
$ytd_earnings = 0;
$ytd_transactions = 0;

$result->data_seek(0);
while ($row = $result->fetch_assoc()) {
    $payment_date = new DateTime($row['payment_date']);
    if ($payment_date->format('Y') == $current_year) {
        $ytd_earnings += $row['amount'];
        $ytd_transactions++;
    }
}

// Calculate last year earnings for comparison
$last_year = $current_year - 1;
$last_year_earnings = 0;
$last_year_transactions = 0;

$result->data_seek(0);
while ($row = $result->fetch_assoc()) {
    $payment_date = new DateTime($row['payment_date']);
    if ($payment_date->format('Y') == $last_year) {
        $last_year_earnings += $row['amount'];
        $last_year_transactions++;
    }
}

// Calculate year-over-year growth
$yoy_growth = ($last_year_earnings > 0) ? (($ytd_earnings - $last_year_earnings) / $last_year_earnings) * 100 : 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consolidated Financial Report</title>
    <style>
        @page {
            size: A4;
            margin: 0;
        }
        body {
            font-family: 'Times New Roman', Times, serif;
            margin: 0;
            color: #000;
            line-height: 1.6;
            background: #fff;
            width: 210mm;
            min-height: 297mm;
            padding: 20mm 15mm;
            margin: 0 auto;
            box-sizing: border-box;
            font-size: 12pt;
        }
        .report-header {
            text-align: center;
            margin-bottom: 20mm;
            border-bottom: 2px solid #000;
            padding-bottom: 10mm;
        }
        .report-header h1 {
            margin: 0 0 5mm 0;
            font-size: 18pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .report-header h2 {
            margin: 0 0 5mm 0;
            font-size: 14pt;
            font-weight: normal;
        }
        .report-header p {
            margin: 2mm 0;
            font-size: 10pt;
        }
        .report-section {
            margin-bottom: 15mm;
            page-break-inside: avoid;
        }
        .info-grid {
            display: grid;
            grid-template-columns: auto 1fr;
            gap: 3mm;
            margin-bottom: 10mm;
        }
        .info-label {
            font-weight: bold;
            min-width: 40mm;
        }
        .info-value {
            margin-left: 5mm;
        }
        .report-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10mm;
            page-break-inside: avoid;
            font-size: 10pt;
        }
        .report-table th, 
        .report-table td {
            border: 0.5pt solid #000;
            padding: 2mm 3mm;
            text-align: left;
        }
        .report-table th {
            background-color: #f0f0f0;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 10pt;
        }
        .amount-cell {
            text-align: right;
            font-family: 'Courier New', Courier, monospace;
        }
        .total-row {
            font-weight: bold;
            background-color: #f0f0f0 !important;
        }
        .signature-section {
            margin-top: 25mm;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20mm;
            page-break-inside: avoid;
        }
        .signature-box {
            text-align: center;
        }
        .signature-line {
            margin: 15mm auto 3mm;
            border-top: 0.5pt solid #000;
            width: 60mm;
        }
        .signature-title {
            font-size: 10pt;
            font-weight: bold;
        }
        .report-footer {
            margin-top: 15mm;
            text-align: center;
            font-size: 9pt;
            color: #666;
            position: fixed;
            bottom: 15mm;
            left: 0;
            right: 0;
        }
        .summary-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 10mm;
            margin-bottom: 10mm;
        }
        .summary-box {
            border: 0.5pt solid #000;
            padding: 5mm;
            background-color: #f9f9f9;
        }
        .summary-box h3 {
            margin: 0 0 3mm 0;
            font-size: 12pt;
            text-transform: uppercase;
            border-bottom: 0.5pt solid #000;
            padding-bottom: 2mm;
        }
        .value {
            font-size: 14pt;
            font-weight: bold;
            margin: 3mm 0;
        }
        .label {
            font-size: 10pt;
            color: #666;
        }
        @media print {
            body {
                width: 210mm;
                height: 297mm;
                margin: 0;
                padding: 20mm 15mm;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .report-table th {
                background-color: #f0f0f0 !important;
            }
            .report-table tr:nth-child(even) {
                background-color: #f9f9f9 !important;
            }
            .total-row {
                background-color: #f0f0f0 !important;
            }
            .summary-box {
                background-color: #f9f9f9 !important;
            }
            .report-section {
                page-break-inside: avoid;
            }
            .signature-section {
                page-break-inside: avoid;
            }
            .report-footer {
                position: fixed;
                bottom: 15mm;
            }
        }
    </style>
    <script>
        window.onload = function() {
            window.print();
        };
    </script>
</head>
<body>
    <div class="report-header">
        <h1>BOOKMYDOC HEALTHCARE SERVICES</h1>
        <h2>CONSOLIDATED FINANCIAL REPORT</h2>
        <p>Report Generated on: <?php echo date("d M Y, h:i A"); ?></p>
    </div>
    
    <div class="report-section">
        <div class="info-grid">
            <div class="info-label">Doctor Name:</div>
            <div class="info-value">Dr. <?php echo htmlspecialchars($doctor_data['name']); ?></div>
            
            <div class="info-label">Specialization:</div>
            <div class="info-value"><?php echo htmlspecialchars($doctor_data['specialization']); ?></div>
            
            <div class="info-label">Email:</div>
            <div class="info-value"><?php echo htmlspecialchars($doctor_data['email']); ?></div>
            
            <div class="info-label">Contact Number:</div>
            <div class="info-value"><?php echo htmlspecialchars($doctor_data['phone']); ?></div>
        </div>
    </div>

    <div class="report-section">
        <div class="summary-grid">
            <div class="summary-box">
                <h3>Total Earnings</h3>
                <p class="value">₹<?php echo number_format($total_earnings, 2); ?></p>
                <p class="label">Lifetime earnings from all bookings</p>
            </div>
            <div class="summary-box">
                <h3>Current Year Earnings</h3>
                <p class="value">₹<?php echo number_format($ytd_earnings, 2); ?></p>
                <p class="label">Total earnings in <?php echo $current_year; ?></p>
            </div>
        </div>
    </div>
    
    <div class="report-section">
        <table class="report-table">
            <tr>
                <th>Sl.No</th>
                <th>Month</th>
                <th>No. of Bookings</th>
                <th>Total Amount (₹)</th>
                <th>Average per Booking (₹)</th>
            </tr>
            <?php 
            krsort($monthly_earnings);
            $sl_no = 1;
            foreach ($monthly_earnings as $month => $amount): 
                $month_bookings = $monthly_transactions[$month];
                $month_avg = ($month_bookings > 0) ? $amount / $month_bookings : 0;
            ?>
            <tr>
                <td style="text-align: center;"><?php echo $sl_no++; ?></td>
                <td><?php echo $month; ?></td>
                <td style="text-align: center;"><?php echo $month_bookings; ?></td>
                <td class="amount-cell"><?php echo number_format($amount, 2); ?></td>
                <td class="amount-cell"><?php echo number_format($month_avg, 2); ?></td>
            </tr>
            <?php endforeach; ?>
            <tr class="total-row">
                <td colspan="2" style="text-align: center;">Total</td>
                <td style="text-align: center;"><?php echo $transaction_count; ?></td>
                <td class="amount-cell">₹<?php echo number_format($total_earnings, 2); ?></td>
                <td class="amount-cell">₹<?php echo number_format($avg_transaction, 2); ?></td>
            </tr>
        </table>
    </div>

    <div class="report-section">
        <h3 style="margin-bottom: 15px;">Recent Transactions</h3>
        <table class="report-table">
            <tr>
                <th>Sl.No</th>
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
                while (($row = $result->fetch_assoc()) && $count < 10): 
                    $count++;
            ?>
            <tr>
                <td style="text-align: center;"><?php echo $count; ?></td>
                <td>#<?php echo htmlspecialchars($row['booking_id']); ?></td>
                <td><?php echo htmlspecialchars($row['patient_name'] ?? 'N/A'); ?></td>
                <td><?php echo !empty($row['appointment_date']) ? date("d M Y", strtotime($row['appointment_date'])) : 'N/A'; ?></td>
                <td><?php echo !empty($row['payment_date']) ? date("d M Y", strtotime($row['payment_date'])) : 'N/A'; ?></td>
                <td class="amount-cell"><?php echo !empty($row['amount']) ? number_format(floatval($row['amount']), 2) : '0.00'; ?></td>
            </tr>
            <?php 
                endwhile; 
            } else { 
            ?>
            <tr>
                <td colspan="6" style="text-align: center;">No booking records found</td>
            </tr>
            <?php } ?>
        </table>
    </div>

    <div class="signature-section">
        <div class="signature-box">
            <div class="signature-line"></div>
            <div class="signature-title">Doctor's Signature</div>
        </div>
        <div class="signature-box">
            <div class="signature-line"></div>
            <div class="signature-title">Authorized Signatory</div>
        </div>
    </div>

    <div class="report-footer">
        <p>This is a computer-generated report and does not require a physical signature.</p>
        <p>For any queries regarding this report, please contact support.</p>
    </div>
</body>
</html>

<?php 
$stmt->close();
if (isset($doctor_stmt)) $doctor_stmt->close();
$conn->close(); 
?> 