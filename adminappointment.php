<?php 
require 'db_connection.php'; // Include your database connection file
include 'adminheader.php';

$sql = "SELECT ar.id, ar.user_id, ar.doctor_id, ar.slot_id, ar.appointment_date, ar.status, ar.created_at,
                u.name AS user_name, d.name AS doctor_name, s.start_time, s.end_time
         FROM appointment_requests ar
        JOIN patientreg u ON ar.user_id = u.id
        JOIN doctorreg d ON ar.doctor_id = d.id
        JOIN doctor_availability s ON ar.slot_id = s.id
        ORDER BY ar.created_at DESC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Appointment Requests</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #4285f4;
            --secondary-color: #34a853;
            --accent-color: #ea4335;
            --light-color: #f8f9fa;
            --dark-color: #202124;
            --gray-color: #5f6368;
            --pending-color: #fbbc05;
            --approved-color: #34a853;
            --rejected-color: #ea4335;
        }
        
       
        
        .container2 {
            max-width: 1200px;
            margin: 0 auto;
            background-color: white;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 30px;
        }
        
        header {
            
            
            border-bottom: 1px solid #e0e0e0;
        }
        
        h2 {
            color: var(--primary-color);
            font-size: 28px;
            font-weight: 600;
        }
        
        .stats {
            display: flex;
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            flex: 1;
            background-color: white;
            border-radius: 8px;
            padding: 15px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
            border-left: 4px solid var(--primary-color);
        }
        
        .stat-card.pending {
            border-left-color: var(--pending-color);
        }
        
        .stat-card.approved {
            border-left-color: var(--approved-color);
        }
        
        .stat-card.rejected {
            border-left-color: var(--accent-color);
        }
        
        .stat-card h3 {
            font-size: 14px;
            color: var(--gray-color);
            margin-bottom: 8px;
        }
        
        .stat-card p {
            font-size: 24px;
            font-weight: 600;
            color: var(--dark-color);
        }
        
        .table-container {
            overflow-x: auto;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            background-color: white;
            border-radius: 8px;
            overflow: hidden;
        }
        
        thead {
            background-color: var(--primary-color);
            color: white;
        }
        
        th, td {
            padding: 16px 20px;
            text-align: left;
        }
        
        th {
            font-weight: 500;
            letter-spacing: 0.5px;
            font-size: 14px;
            text-transform: uppercase;
        }
        
        tbody tr {
            border-bottom: 1px solid #eeeeee;
            transition: all 0.2s ease;
        }
        
        tbody tr:last-child {
            border-bottom: none;
        }
        
        tbody tr:hover {
            background-color: #f5f7ff;
        }
        
        td {
            font-size: 15px;
            color: #333;
        }
        
        .status {
            padding: 6px 12px;
            border-radius: 50px;
            font-size: 13px;
            font-weight: 500;
            text-transform: capitalize;
            display: inline-block;
        }
        
        .status-pending {
            background-color: #fff4db;
            color: #d68102;
        }
        
        .status-approved {
            background-color: #e6f7e9;
            color: #1e7d34;
        }
        
        .status-rejected {
            background-color: #fdeaea;
            color: #c42b1c;
        }
        
        .time-slot {
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        .time-slot i {
            color: var(--gray-color);
        }
        
        .date-field {
            display: flex;
            flex-direction: column;
        }
        
        .date {
            font-weight: 500;
        }
        
        .time {
            font-size: 13px;
            color: var(--gray-color);
        }
        
        .pagination {
            display: flex;
            justify-content: center;
            margin-top: 25px;
            gap: 5px;
        }
        
        .pagination button {
            border: 1px solid #e0e0e0;
            background-color: white;
            color: var(--dark-color);
            padding: 8px 16px;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.2s;
        }
        
        .pagination button:hover, .pagination button.active {
            background-color: var(--primary-color);
            color: white;
            border-color: var(--primary-color);
        }
        
        @media (max-width: 768px) {
            .stats {
                flex-direction: column;
                gap: 10px;
            }
            
            th, td {
                padding: 12px 10px;
                font-size: 13px;
            }
            
            .container {
                padding: 15px;
            }
        }
    </style>
</head>
<body>
    <div class="container2">
        <header>
            <h2><i class="fas fa-calendar-check"></i> Appointment Requests</h2>
            <div class="actions">
                
            </div>
        </header>
        
        <!-- Stats Cards -->
        <?php
        // Count totals for stats
        $total = $result->num_rows;
        $pending = 0;
        $approved = 0;
        $rejected = 0;
        
        // Clone result to count statuses
        $statResult = $conn->query($sql);
        while ($row = $statResult->fetch_assoc()) {
            if (strtolower($row['status']) == 'pending') $pending++;
            else if (strtolower($row['status']) == 'approved') $approved++;
            else if (strtolower($row['status']) == 'rejected') $rejected++;
        }
        ?>
        <br>
        
        <div class="stats">
            <div class="stat-card">
                <h3>Total Appointments</h3>
                <p><?php echo $total; ?></p>
            </div>
            <div class="stat-card pending">
                <h3>Pending</h3>
                <p><?php echo $pending; ?></p>
            </div>
            <div class="stat-card approved">
                <h3>Approved</h3>
                <p><?php echo $approved; ?></p>
            </div>
            <div class="stat-card rejected">
                <h3>Rejected</h3>
                <p><?php echo $rejected; ?></p>
            </div>
        </div>
        
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Patient</th>
                        <th>Doctor</th>
                        <th>Date & Time</th>
                        <th>Status</th>
                        <th>Requested At</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    // Reset pointer for main result
                    $result->data_seek(0);
                    while ($row = $result->fetch_assoc()): 
                        $statusClass = '';
                        switch(strtolower($row['status'])) {
                            case 'pending':
                                $statusClass = 'status-pending';
                                break;
                            case 'approved':
                                $statusClass = 'status-approved';
                                break;
                            case 'rejected':
                                $statusClass = 'status-rejected';
                                break;
                            default:
                                $statusClass = '';
                        }
                    ?>
                        <tr>
                            <td>#<?php echo $row['id']; ?></td>
                            <td><?php echo htmlspecialchars($row['user_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['doctor_name']); ?></td>
                            <td>
                                <div class="date-field">
                                    <span class="date"><?php echo date('d M Y', strtotime($row['appointment_date'])); ?></span>
                                    <span class="time">
                                        <i class="far fa-clock"></i> 
                                        <?php echo date('h:i A', strtotime($row['start_time'])) . ' - ' . date('h:i A', strtotime($row['end_time'])); ?>
                                    </span>
                                </div>
                            </td>
                            <td><span class="status <?php echo $statusClass; ?>"><?php echo $row['status']; ?></span></td>
                            <td><?php echo date('d M Y, h:i A', strtotime($row['created_at'])); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
        
        <!-- Pagination, if needed -->
        <div class="pagination">
            <button><i class="fas fa-chevron-left"></i></button>
            <button class="active">1</button>
            <button>2</button>
            <button>3</button>
            <button><i class="fas fa-chevron-right"></i></button>
        </div>
    </div>
    
    <script>
        // Add any JavaScript functionality if needed
        document.addEventListener('DOMContentLoaded', function() {
            // Example: Add click handlers for the pagination
            const paginationButtons = document.querySelectorAll('.pagination button');
            paginationButtons.forEach(button => {
                button.addEventListener('click', function() {
                    paginationButtons.forEach(btn => btn.classList.remove('active'));
                    this.classList.add('active');
                    // Implement actual pagination logic as needed
                });
            });
        });
    </script>
</body>
</html>

<?php $conn->close(); ?>