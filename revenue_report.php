<?php
include('db_connection.php');

// First query for doctor summary
$summary_sql = "SELECT 
            d.id,
            d.name as doctor_name,
            d.specialization,
            COUNT(ar.id) as total_appointments,
            SUM(p.amount) as total_revenue
        FROM doctorreg d
        LEFT JOIN appointment_requests ar ON d.id = ar.doctor_id
        LEFT JOIN payments p ON ar.id = p.appointment_id
        GROUP BY d.id
        ORDER BY total_revenue DESC";

$summary_result = mysqli_query($conn, $summary_sql);

// Second query for all payment details
$payments_sql = "SELECT 
            p.id as payment_id,
            p.amount,
            p.payment_date,
            p.payment_method,
            p.transaction_id,
            p.status,
            d.id as doctor_id,
            d.name as doctor_name,
            d.specialization,
            pr.name as patient_name,
            ar.appointment_date
        FROM payments p
        JOIN appointment_requests ar ON p.appointment_id = ar.id
        JOIN doctorreg d ON ar.doctor_id = d.id
        JOIN patientreg pr ON ar.user_id = pr.id
        ORDER BY p.payment_date DESC";

$payments_result = mysqli_query($conn, $payments_sql);

// Calculate total revenue
$total_revenue = 0;
if ($summary_result && mysqli_num_rows($summary_result) > 0) {
    while ($row = mysqli_fetch_assoc($summary_result)) {
        $total_revenue += $row['total_revenue'] ?? 0;
    }
    mysqli_data_seek($summary_result, 0);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consolidated Revenue Report - BookMyDoc</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 40px;
            background-color: white;
        }

        .header {
            text-align: center;
            margin-bottom: 40px;
        }

        .header h1 {
            font-size: 24px;
            margin: 0 0 10px 0;
            text-transform: uppercase;
            font-weight: bold;
        }

        .header h2 {
            font-size: 20px;
            margin: 20px 0;
            text-transform: uppercase;
            text-decoration: underline;
        }

        .header p {
            margin: 5px 0;
        }

        .details {
            margin: 20px 0 30px 0;
        }

        .details-row {
            display: grid;
            grid-template-columns: 120px auto;
            margin-bottom: 10px;
        }

        .label {
            font-weight: bold;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            page-break-inside: auto;
        }

        tr {
            page-break-inside: avoid;
            page-break-after: auto;
        }

        th, td {
            border: 1px solid black;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f0f0f0;
        }

        .amount {
            text-align: right;
        }

        .total-row {
            font-weight: bold;
        }

        .section-title {
            margin-top: 40px;
            margin-bottom: 20px;
            font-size: 18px;
            font-weight: bold;
            text-decoration: underline;
        }

        @media print {
            body {
                margin: 20px;
            }
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>BOOKMYDOC HEALTHCARE SERVICES</h1>
        <p>Phone: 7025572282 | Email: info@bookmydoc.com</p>
        <h2>CONSOLIDATED REVENUE REPORT</h2>
    </div>

    <div class="details">
        <div class="details-row">
            <span class="label">Report Period:</span>
            <span><?php echo date('F Y'); ?></span>
        </div>
        <div class="details-row">
            <span class="label">Generated on:</span>
            <span><?php echo date('d-m-Y'); ?></span>
        </div>
    </div>

    <div class="section-title">DOCTOR-WISE REVENUE SUMMARY</div>
    <table>
        <thead>
            <tr>
                <th>Sl.No</th>
                <th>Doctor ID</th>
                <th>Doctor Name</th>
                <th>Specialization</th>
                <th>Total Appointments</th>
                <th>Amount</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $sl_no = 1;
            if (mysqli_num_rows($summary_result) > 0):
                while ($row = mysqli_fetch_assoc($summary_result)): 
            ?>
                <tr>
                    <td><?php echo $sl_no++; ?></td>
                    <td><?php echo $row['id']; ?></td>
                    <td>Dr. <?php echo htmlspecialchars($row['doctor_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['specialization']); ?></td>
                    <td><?php echo $row['total_appointments']; ?></td>
                    <td class="amount"><?php echo number_format($row['total_revenue'] ?? 0, 2); ?>/-</td>
                </tr>
            <?php 
                endwhile;
            endif; 
            ?>
            <tr class="total-row">
                <td colspan="5" style="text-align: right;"><strong>Total Revenue:</strong></td>
                <td class="amount"><?php echo number_format($total_revenue, 2); ?>/-</td>
            </tr>
        </tbody>
    </table>

    <div class="section-title">PAYMENT TRANSACTION DETAILS</div>
    <table>
        <thead>
            <tr>
                <th>Sl.No</th>
                <th>Payment ID</th>
                <th>Date</th>
                <th>Doctor Name</th>
                <th>Patient Name</th>
                <th>Payment Method</th>
                <th>Transaction ID</th>
                <th>Status</th>
                <th>Amount</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $sl_no = 1;
            $total_transactions = 0;
            if ($payments_result && mysqli_num_rows($payments_result) > 0):
                while ($row = mysqli_fetch_assoc($payments_result)): 
                    $total_transactions += $row['amount'];
            ?>
                <tr>
                    <td><?php echo $sl_no++; ?></td>
                    <td><?php echo $row['payment_id']; ?></td>
                    <td><?php echo date('d-m-Y', strtotime($row['payment_date'])); ?></td>
                    <td>Dr. <?php echo htmlspecialchars($row['doctor_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['patient_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['payment_method']); ?></td>
                    <td><?php echo htmlspecialchars($row['transaction_id']); ?></td>
                    <td><?php echo htmlspecialchars($row['status']); ?></td>
                    <td class="amount"><?php echo number_format($row['amount'], 2); ?>/-</td>
                </tr>
            <?php 
                endwhile;
            endif; 
            ?>
            <tr class="total-row">
                <td colspan="8" style="text-align: right;"><strong>Total Transactions:</strong></td>
                <td class="amount"><?php echo number_format($total_transactions, 2); ?>/-</td>
            </tr>
        </tbody>
    </table>

    <div style="margin-top: 50px;">
        <p style="text-align: right; margin-right: 50px;">
            <strong>Grand Total: ₹<?php echo number_format($total_revenue, 2); ?>/-</strong>
        </p>
    </div>

    <div style="margin-top: 100px; display: flex; justify-content: space-between; padding: 0 50px;">
        <div style="text-align: center;">
            <div style="border-top: 1px solid black; width: 150px; padding-top: 5px;">
                Prepared By
            </div>
        </div>
        <div style="text-align: center;">
            <div style="border-top: 1px solid black; width: 150px; padding-top: 5px;">
                Verified By
            </div>
        </div>
        <div style="text-align: center;">
            <div style="border-top: 1px solid black; width: 150px; padding-top: 5px;">
                Administrator
            </div>
        </div>
    </div>

    <div style="margin-top: 50px; text-align: center; font-size: 12px;">
        <p>This is a computer generated report. No signature is required.</p>
        <p>Document ID: REV-<?php echo date('Ymd'); ?>-<?php echo sprintf('%04d', rand(1, 9999)); ?></p>
    </div>

    <div class="no-print" style="text-align: center; margin-top: 30px;">
        <button onclick="window.print()" style="padding: 10px 20px; cursor: pointer;">Print Report</button>
    </div>
</body>
</html>
