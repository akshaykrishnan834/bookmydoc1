<?php
include('db_connection.php');

// Fetch all doctors with their appointment statistics and availability
$sql = "SELECT 
            d.id,
            d.name,
            d.specialization,
            d.experience,
            d.location,
            d.fees,
            d.created_at,
            d.status,
            COUNT(ar.id) as total_appointments,
            SUM(CASE WHEN ar.status = 'approved' THEN 1 ELSE 0 END) as approved_appointments,
            SUM(CASE WHEN ar.status = 'pending' THEN 1 ELSE 0 END) as pending_appointments,
            SUM(CASE WHEN ar.status = 'rejected' THEN 1 ELSE 0 END) as rejected_appointments,
            SUM(CASE WHEN ar.status = 'expired' THEN 1 ELSE 0 END) as expired_appointments,
            SUM(p.amount) as total_earnings,
            GROUP_CONCAT(DISTINCT CONCAT(TIME_FORMAT(da.start_time, '%H:%i'), '-', TIME_FORMAT(da.end_time, '%H:%i')) ORDER BY da.start_time SEPARATOR ', ') as availability_slots
        FROM doctorreg d
        LEFT JOIN appointment_requests ar ON d.id = ar.doctor_id
        LEFT JOIN payments p ON ar.id = p.appointment_id
        LEFT JOIN doctor_availability da ON d.id = da.doctor_id
        GROUP BY d.id
        ORDER BY d.name ASC";

$result = mysqli_query($conn, $sql);

// Calculate total statistics
$total_stats = [
    'doctors' => 0,
    'appointments' => 0,
    'earnings' => 0
];

if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $total_stats['doctors']++;
        $total_stats['appointments'] += $row['total_appointments'];
        $total_stats['earnings'] += $row['total_earnings'] ?? 0;
    }
    mysqli_data_seek($result, 0); // Reset pointer to beginning
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctors Consolidated Report - BookMyDoc</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 40px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
        }

        .header h1 {
            font-size: 28px;
            margin: 0 0 5px 0;
            text-transform: uppercase;
            font-weight: bold;
        }

        .header h2 {
            font-size: 24px;
            margin: 15px 0 10px 0;
        }

        .header h3 {
            font-size: 18px;
            margin: 5px 0;
            font-weight: normal;
        }

        .header p {
            font-size: 16px;
            margin: 5px 0;
        }

        .report-intro {
            margin: 0 0 20px 0;
            text-align: justify;
            line-height: 1.6;
        }

        .report-intro h4 {
            font-weight: normal;
            margin: 0;
            font-size: 16px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        th, td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
            font-size: 14px;
        }

        th {
            background-color: #f0f0f0;
        }

        .availability {
            font-size: 13px;
            line-height: 1.4;
        }

        .report-summary {
            margin: 30px 0;
            padding: 15px;
            background-color: #f9f9f9;
            border: 1px solid #ddd;
        }

        .signature-section {
            margin-top: 50px;
            display: flex;
            justify-content: space-between;
        }

        .signature-box {
            text-align: center;
            width: 200px;
        }

        .signature-line {
            border-top: 1px solid #000;
            margin-top: 40px;
            padding-top: 5px;
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
        <h1>Book My Doc</h1>
        <h3>Online Doctor Appointment Booking System</h3>
        <p>Phone: 7025572282 | Email: info@bookmydoc.com</p>
        
        
        <p>Report Period: <?php echo date('F Y'); ?></p>
        <p>Generated on: <?php echo date('d-m-Y'); ?></p>
        <br>
        <br>
        <h2 style="margin-top: 20px;"><u>CONSOLIDATED DOCTORS REPORT</u><h2>
        <br>
    </div>

    

    <?php if (mysqli_num_rows($result) > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>Sl.No</th>
                    <th>Doctor Name</th>
                    <th>Specialization</th>
                    <th>Location</th>
                    <th>Experience</th>
                    <th>Consultation Fee</th>
                    <th>Created At</th>
                    <th>Status</th>
                    <th>Availability</th>
                    <th>Total Appointments</th>
                    <th>Status Breakdown</th>
                    <th>Total Earnings</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $sl_no = 1;
                while ($doctor = mysqli_fetch_assoc($result)): 
                ?>
                <tr>
                    <td><?php echo $sl_no++; ?></td>
                    <td>Dr. <?php echo htmlspecialchars($doctor['name']); ?></td>
                    <td><?php echo htmlspecialchars($doctor['specialization']); ?></td>
                    <td><?php echo htmlspecialchars($doctor['location']); ?></td>
                    <td><?php echo $doctor['experience']; ?> years</td>
                    <td>₹<?php echo number_format($doctor['fees'], 2); ?></td>
                    <td style="width: 120px;"><?php echo date('d-m-Y', strtotime($doctor['created_at'])); ?></td>
                    <td><?php echo ucfirst($doctor['status']); ?></td>
                    <td class="availability"><?php echo $doctor['availability_slots'] ? htmlspecialchars($doctor['availability_slots']) : 'No slots defined'; ?></td>
                    <td><?php echo $doctor['total_appointments']; ?></td>
                    <td>
                        Approved: <?php echo $doctor['approved_appointments']; ?>
                        Pending: <?php echo $doctor['pending_appointments']; ?>
                        Rejected: <?php echo $doctor['rejected_appointments']; ?>
                        Expired: <?php echo $doctor['expired_appointments']; ?>
                    </td>
                    <td>₹<?php echo number_format($doctor['total_earnings'] ?? 0, 2); ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <div class="report-summary">
            <h3>Report Summary</h3>
            <p>Total Number of Doctors: <?php echo $total_stats['doctors']; ?></p>
            <p>Total Appointments Handled: <?php echo $total_stats['appointments']; ?></p>
            <p>Total Revenue Generated: ₹<?php echo number_format($total_stats['earnings'], 2); ?></p>
        </div>

    <?php else: ?>
        <p>No doctors found in the system.</p>
    <?php endif; ?>

    <div class="signature-section">
        <div class="signature-box">
            <div class="signature-line">
                Medical Director
            </div>
        </div>
        <div class="signature-box">
            <div class="signature-line">
                Administrative Officer
            </div>
        </div>
        <div class="signature-box">
            <div class="signature-line">
                System Administrator
            </div>
        </div>
    </div>

    <div style="margin-top: 30px; text-align: center; font-size: 12px;">
        <p>This is a computer-generated report. No physical signature is required.</p>
        <p>Document ID: DOC-<?php echo date('Ymd'); ?>-<?php echo rand(1000, 9999); ?></p>
    </div>

    <div class="no-print" style="margin-top: 20px; text-align: center;">
        <button onclick="window.print()">Print Report</button>
    </div>
</body>
</html>
