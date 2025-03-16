<?php 
include('db_connection.php'); 
include('patientheader.php'); // Assuming this is the patient dashboard header

// Assuming user ID is stored in session after login
$patient_id = $_SESSION['id'] ?? null;

// Fetch patient's appointment details with payment status
$sql = "SELECT ar.id, d.name AS doctor_name, d.specialization, ar.appointment_date,
                da.start_time, da.end_time, ar.status, ar.created_at, ar.patient_condition,
                (SELECT COUNT(*) FROM payments WHERE appointment_id = ar.id AND status = 'success') AS payment_done
        FROM appointment_requests ar
        JOIN doctorreg d ON ar.doctor_id = d.id
        JOIN doctor_availability da ON ar.slot_id = da.id
        WHERE ar.user_id = ?
        ORDER BY ar.appointment_date DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $patient_id);
$stmt->execute();
$result = $stmt->get_result();

$appointments = [];
while ($row = $result->fetch_assoc()) {
    $appointments[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Appointments</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f5f7fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .appointments-container {
            max-width: 1100px;
            margin: 40px auto;
            position: relative;
        }
        
        .page-title {
            color: #2c3e50;
            font-weight: 700;
            margin-bottom: 30px;
            position: relative;
            display: inline-block;
            padding-bottom: 8px;
        }
        
        .page-title::after {
            content: '';
            position: absolute;
            width: 60%;
            height: 4px;
            background: linear-gradient(90deg, #3498db, #1abc9c);
            bottom: 0;
            left: 0;
            border-radius: 10px;
        }
        
        .appointment-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.08);
            overflow: hidden;
            transition: transform 0.3s ease;
            margin-bottom: 30px;
        }
        
        .appointment-card:hover {
            transform: translateY(-5px);
        }
        
        .card-header {
            background: linear-gradient(135deg, #3498db, #1abc9c);
            color: white;
            font-weight: 600;
            border: none;
            padding: 20px;
        }
        
        .table {
            margin-bottom: 0;
        }
        
        .table thead th {
            background-color: #f8f9fa;
            color: #2c3e50;
            font-weight: 600;
            border-bottom: 2px solid #e9ecef;
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 0.5px;
            padding: 16px;
        }
        
        .table tbody tr {
            transition: all 0.3s ease;
        }
        
        .table tbody tr:hover {
            background-color: #f2f8fd;
        }
        
        .table td {
            padding: 18px 16px;
            vertical-align: middle;
            border-color: #f1f3f6;
        }
        
        .doctor-name {
            font-weight: 600;
            color: #2c3e50;
            display: flex;
            align-items: center;
        }
        
        .doctor-icon {
            background-color: #e8f4fd;
            color: #3498db;
            width: 35px;
            height: 35px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-right: 10px;
            font-size: 15px;
        }
        
        .specialization {
            padding: 4px 12px;
            background-color: #edf7ff;
            color: #3498db;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
            display: inline-block;
        }
        
        .date-time {
            display: flex;
            align-items: center;
        }
        
        .date-icon, .time-icon {
            margin-right: 8px;
            color: #7f8c8d;
        }
        
        .status-pending {
            background-color: #fff9e6;
            color: #f39c12;
            padding: 6px 12px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 12px;
            display: inline-flex;
            align-items: center;
        }
        
        .status-approved {
            background-color: #eafaf1;
            color: #27ae60;
            padding: 6px 12px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 12px;
            display: inline-flex;
            align-items: center;
        }
        
        .status-rejected {
            background-color: #feeaec;
            color: #e74c3c;
            padding: 6px 12px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 12px;
            display: inline-flex;
            align-items: center;
        }
        
        .status-icon {
            margin-right: 5px;
        }
        
        .btn-payment {
            background: linear-gradient(135deg, #3498db, #2980b9);
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 50px;
            font-weight: 500;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            box-shadow: 0 4px 10px rgba(52, 152, 219, 0.3);
        }
        
        .btn-payment:hover {
            background: linear-gradient(135deg, #2980b9, #3498db);
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(52, 152, 219, 0.4);
            color: white;
        }
        
        .payment-done {
            background-color: #27ae60;
            color: white;
            padding: 8px 16px;
            border-radius: 50px;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            box-shadow: 0 4px 10px rgba(39, 174, 96, 0.3);
        }
        
        .payment-icon {
            margin-right: 8px;
        }
        
        .no-appointments {
            padding: 40px;
            text-align: center;
            color: #95a5a6;
        }
        
        .no-appointments i {
            font-size: 50px;
            margin-bottom: 15px;
            color: #bdc3c7;
        }
        
        .created-date {
            color: #7f8c8d;
            font-size: 13px;
            display: flex;
            align-items: center;
        }
        
        .created-icon {
            margin-right: 5px;
        }
        
        @media (max-width: 992px) {
            .table-responsive {
                border-radius: 15px;
                overflow: hidden;
            }
        }
        
        @media (max-width: 768px) {
            .appointments-container {
                margin: 20px 10px;
            }
            
            .table thead {
                display: none;
            }
            
            .table, .table tbody, .table tr, .table td {
                display: block;
                width: 100%;
            }
            
            .table tr {
                margin-bottom: 20px;
                border-bottom: 2px solid #e9ecef;
                padding-bottom: 10px;
            }
            
            .table td {
                text-align: right;
                padding: 12px 15px;
                position: relative;
                border-top: none;
            }
            
            .table td::before {
                content: attr(data-label);
                position: absolute;
                left: 15px;
                width: 50%;
                text-align: left;
                font-weight: 600;
                color: #2c3e50;
            }
        }
        .btn-cancel {
    background: linear-gradient(135deg, #e74c3c, #c0392b);
    color: white;
    border: none;
    padding: 8px 16px;
    border-radius: 50px;
    font-weight: 500;
    transition: all 0.3s;
    display: inline-flex;
    align-items: center;
    box-shadow: 0 4px 10px rgba(231, 76, 60, 0.3);
    margin-left: 10px;
}

.btn-cancel:hover {
    background: linear-gradient(135deg, #c0392b, #e74c3c);
    transform: translateY(-2px);
    box-shadow: 0 6px 12px rgba(231, 76, 60, 0.4);
    color: white;
}

.alert {
    border-radius: 10px;
    margin-bottom: 20px;
}

.alert-success {
    background-color: #d4edda;
    border-color: #c3e6cb;
    color: #155724;
}

.alert-danger {
    background-color: #f8d7da;
    border-color: #f5c6cb;
    color: #721c24;
}
    </style>
</head>
<body>

<div class="appointments-container">
    <h2 class="page-title">My Appointments</h2>
    
    <div class="card appointment-card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <i class="fas fa-calendar-check me-2"></i> Your Appointment Schedule
            </div>
            <div class="d-flex align-items-center">
                <div class="d-flex align-items-center me-3">
                    <span class="status-pending me-2" style="font-size: 10px; padding: 4px 8px;">
                        <i class="fas fa-clock status-icon"></i>
                    </span>
                    <small>Pending</small>
                </div>
                <div class="d-flex align-items-center me-3">
                    <span class="status-approved me-2" style="font-size: 10px; padding: 4px 8px;">
                        <i class="fas fa-check-circle status-icon"></i>
                    </span>
                    <small>Approved</small>
                </div>
                <div class="d-flex align-items-center">
                    <span class="status-rejected me-2" style="font-size: 10px; padding: 4px 8px;">
                        <i class="fas fa-times-circle status-icon"></i>
                    </span>
                    <small>Rejected</small>
                </div>
            </div>
        </div>
        
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover">
                <thead>
    <tr>
        <th>Doctor</th>
        <th>Specialization</th>
        <th>Date</th>
        <th>Time</th>
        <th>Condition</th>
        <th>Status</th>
        <th>Requested On</th>
        <th>condition stat</th>
    </tr>
</thead>
<tbody>
    <?php if (!empty($appointments)): ?>
        <?php foreach ($appointments as $app): ?>
            <tr>
                <td data-label="Doctor">
                    <div class="doctor-name">
                        <span class="doctor-icon">
                            <i class="fas fa-user-md"></i>
                        </span>
                        <?php echo htmlspecialchars($app['doctor_name']); ?>
                    </div>
                </td>
                <td data-label="Specialization">
                    <span class="specialization">
                        <?php echo htmlspecialchars($app['specialization']); ?>
                    </span>
                </td>
                <td data-label="Date">
                    <div class="date-time">
                        <i class="fas fa-calendar-day date-icon"></i>
                        <?php echo date('d M Y', strtotime($app['appointment_date'])); ?>
                    </div>
                </td>
                <td data-label="Time">
                    <div class="date-time">
                        <i class="fas fa-clock time-icon"></i>
                        <?php echo $app['start_time'] . " - " . $app['end_time']; ?>
                    </div>
                </td>
                <td data-label="Status">
                    <?php
                        if ($app['status'] === 'Pending') {
                            echo "<span class='status-pending'>
                                    <i class='fas fa-clock status-icon'></i> Waiting Approval
                                  </span>";
                         } elseif ($app['status'] === 'Approved') {
                            echo "<span class='status-approved'>
                                    <i class='fas fa-check-circle status-icon'></i> Approved
                                  </span>";
                                  
                                } elseif ($app['status'] === 'Expired') {
                                    echo "<span class='status-rejected'>
                                           <i class='fas fa-times-circle status-icon'></i> Expired
                                          </span>";
                                          
                                }
                                  else {
                            echo "<span class='status-rejected'>
                                    <i class='fas fa-times-circle status-icon'></i> Rejected
                                  </span>";
                        }
                    ?>
                </td>
                <td data-label="Requested On">
                    <div class="created-date">
                        <i class="fas fa-history created-icon"></i>
                        <?php 
                            $createdDate = $app['created_at'] ?? 'N/A';
                            if ($createdDate !== 'N/A') {
                                echo date('d M Y', strtotime($createdDate));
                            } else {
                                echo $createdDate;
                            }
                        ?>
                    </div>
                </td>
                <td data-label="Action">
    <?php if ($app['status'] === 'Approved'): ?>
        <?php if ($app['payment_done'] > 0): ?>
            <span class="payment-done">
                <i class="fas fa-check-circle payment-icon"></i> Payment Done
            </span>
        <?php else: ?>
            <div class="d-flex">
                <a href="payment.php?appointment_id=<?php echo $app['id']; ?>" class="btn btn-payment">
                    <i class="fas fa-credit-card payment-icon"></i> Pay Now
                </a>
                
                <!-- Cancel Appointment Button - Only show if payment not done -->
                <a href="cancel_appointment.php?id=<?php echo $app['id']; ?>" 
                   class="btn btn-cancel" 
                   onclick="return confirm('Are you sure you want to cancel this appointment?');">
                    <i class="fas fa-times-circle me-1"></i> Cancel
                </a>
            </div>
        <?php endif; ?>
    <?php else: ?>
        <span class="text-muted fst-italic">No action needed</span>
    <?php endif; ?>
</td>
                <td data-label="Condition">
                    <div class="patient-condition">
                        <?php echo htmlspecialchars($app['patient_condition'] ?? 'Not specified'); ?>
                    </div>
                </td>
            </tr>
        <?php endforeach; ?>
    <?php else: ?>
        <tr>
            <td colspan="8">
                <div class="no-appointments">
                    <i class="fas fa-calendar-times"></i>
                    <h5>No Appointments Found</h5>
                    <p>You haven't scheduled any appointments yet.</p>
                </div>
            </td>
        </tr>
    <?php endif; ?>
</tbody>
                        