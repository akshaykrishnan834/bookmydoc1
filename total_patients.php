<?php
include('db_connection.php');

// Fetch all patients with their appointment details
$sql = "SELECT 
            p.id,
            p.name as patient_name,
            p.email,
            p.phone,
            p.gender,
            p.dob,
            ar.appointment_date,
            da.start_time,
            da.end_time,
            d.name as doctor_name,
            d.specialization,
            ar.status,
            ar.patient_condition,
            ar.created_at as booking_date,
            pay.amount as payment_amount,
            pay.payment_method,
            pay.status as payment_status
        FROM patientreg p
        LEFT JOIN appointment_requests ar ON p.id = ar.user_id
        LEFT JOIN doctor_availability da ON ar.slot_id = da.id
        LEFT JOIN doctorreg d ON ar.doctor_id = d.id
        LEFT JOIN payments pay ON ar.id = pay.appointment_id
        ORDER BY p.name ASC, ar.appointment_date DESC";

$result = mysqli_query($conn, $sql);

// Calculate summary statistics
$total_stats = [
    'total_patients' => 0,
    'total_appointments' => 0,
    'total_approved' => 0,
    'total_pending' => 0,
    'total_rejected' => 0,
    'total_payments' => 0
];

$unique_patients = [];
if ($result && mysqli_num_rows($result) > 0) {
    mysqli_data_seek($result, 0); // Reset pointer to beginning
    while ($row = mysqli_fetch_assoc($result)) {
        if (!in_array($row['id'], $unique_patients)) {
            $unique_patients[] = $row['id'];
            $total_stats['total_patients']++;
        }
        if ($row['appointment_date']) {
            $total_stats['total_appointments']++;
            switch(strtolower($row['status'])) {
                case 'approved': $total_stats['total_approved']++; break;
                case 'pending': $total_stats['total_pending']++; break;
                case 'rejected': $total_stats['total_rejected']++; break;
            }
        }
        if ($row['payment_amount']) {
            $total_stats['total_payments'] += $row['payment_amount'];
        }
    }
    mysqli_data_seek($result, 0); // Reset pointer for the main table
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Report - BookMyDoc</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 40px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
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

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 14px;
        }

        th, td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f0f0f0;
        }

        .status-approved { color: green; }
        .status-pending { color: orange; }
        .status-rejected { color: red; }
        .status-expired { color: gray; }

        @media print {
            body { margin: 20px; }
            .no-print { display: none; }
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
        <h2 style="margin-top: 20px;"><u>CONSOLIDATED PATIENTS REPORT</u><h2>
        <br>
    </div>

    <?php if (mysqli_num_rows($result) > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>Sl.No</th>
                    <th>Patient Name</th>
                    <th>Contact Details</th>
                    <th>Appointment Date</th>
                    <th>Time</th>
                    <th>Booking Date</th>
                    <th>Doctor</th>
                    <th>Condition</th>
                    <th>Status</th>
                    <th>Payment</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $sl_no = 1;
                while ($row = mysqli_fetch_assoc($result)): 
                    $status_class = '';
                    switch(strtolower($row['status'])) {
                        case 'approved': $status_class = 'status-approved'; break;
                        case 'pending': $status_class = 'status-pending'; break;
                        case 'rejected': $status_class = 'status-rejected'; break;
                        case 'expired': $status_class = 'status-expired'; break;
                    }
                ?>
                    <tr>
                        <td><?php echo $sl_no++; ?></td>
                        <td>
                            <?php echo htmlspecialchars($row['patient_name']); ?><br>
                            <small>Gender: <?php echo htmlspecialchars($row['gender']); ?><br>
                            DOB: <?php echo htmlspecialchars($row['dob']); ?></small>
                        </td>
                        <td>
                            Email: <?php echo htmlspecialchars($row['email']); ?><br>
                            Phone: <?php echo htmlspecialchars($row['phone']); ?>
                        </td>
                        <td><?php echo $row['appointment_date'] ? date('d-m-Y', strtotime($row['appointment_date'])) : 'Not scheduled'; ?></td>
                        <td>
                            <?php 
                            if ($row['start_time'] && $row['end_time']) {
                                echo date('h:i A', strtotime($row['start_time'])) . ' - ' . 
                                     date('h:i A', strtotime($row['end_time']));
                            } else {
                                echo 'Not scheduled';
                            }
                            ?>
                        </td>
                        <td><?php echo $row['booking_date'] ? date('d-m-Y h:i A', strtotime($row['booking_date'])) : 'N/A'; ?></td>
                        <td>
                            <?php if ($row['doctor_name']): ?>
                                Dr. <?php echo htmlspecialchars($row['doctor_name']); ?><br>
                                <small><?php echo htmlspecialchars($row['specialization']); ?></small>
                            <?php else: ?>
                                Not assigned
                            <?php endif; ?>
                        </td>
                        <td><?php echo htmlspecialchars($row['patient_condition'] ?? 'Not specified'); ?></td>
                        <td class="<?php echo $status_class; ?>"><?php echo htmlspecialchars($row['status'] ?? 'N/A'); ?></td>
                        <td>
                            <?php if ($row['payment_amount']): ?>
                                ₹<?php echo number_format($row['payment_amount'], 2); ?><br>
                                <small>
                                    <?php echo htmlspecialchars($row['payment_method']); ?><br>
                                    <?php echo htmlspecialchars($row['payment_status']); ?>
                                </small>
                            <?php else: ?>
                                No payment
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <div style="margin: 40px 0; padding: 20px;">
            <h2 style="font-size: 20px; margin-bottom: 20px;">Report Summary</h2>
            
            <div style="font-size: 16px; line-height: 2;">
                <?php
                // Get total number of doctors
                $doctor_query = "SELECT COUNT(*) as doctor_count FROM doctorreg";
                $doctor_result = mysqli_query($conn, $doctor_query);
                $doctor_count = mysqli_fetch_assoc($doctor_result)['doctor_count'];
                ?>
                
                <p>Total Number of Doctors: <?php echo $doctor_count; ?></p>
                <p>Total Appointments Handled: <?php echo $total_stats['total_appointments']; ?></p>
                <p>Total Revenue Generated: ₹<?php echo number_format($total_stats['total_payments'], 2); ?></p>
            </div>
        </div>
        
        <div style="margin: 40px 0; text-align: center;">
            <p><strong>Report Generated By:</strong> BookMyDoc Management System</p>
            
        </div>
<br>
        <div style="display: flex; justify-content: space-between; margin-top: 50px; margin-bottom: 30px;">
            <div style="text-align: center; width: 200px;">
                <div style="border-top: 1px solid #000; padding-top: 5px;">
                    Medical Director<br>
                    <small>(Name & Signature)</small>
                </div>
            </div>
            <div style="text-align: center; width: 200px;">
                <div style="border-top: 1px solid #000; padding-top: 5px;">
                    Administrative Officer<br>
                    <small>(Name & Signature)</small>
                </div>
            </div>
            <div style="text-align: center; width: 200px;">
                <div style="border-top: 1px solid #000; padding-top: 5px;">
                    System Administrator<br>
                    <small>(Name & Signature)</small>
                </div>
            </div>
        </div>

        <div style="margin-top: 30px; text-align: center; font-size: 12px;">
            <p>This is a computer-generated report. No physical signature is required.</p>
            <p>Document ID: PAT-<?php echo date('Ymd'); ?>-<?php echo rand(1000, 9999); ?></p>
            
        </div>
    </div>
    <?php else: ?>
        <p>No patients found in the system.</p>
    <?php endif; ?>

    <div class="no-print" style="margin-top: 20px; text-align: center;">
        <button onclick="window.print()">Print Report</button>
    </div>
</body>
</html>
