<?php
require 'db_connection.php'; // Include your database connection file
include 'adminheader.php';

// Query to fetch all payment details
$sql = "SELECT p.*, a.appointment_date, u.name AS patient_name, d.name AS doctor_name 
        FROM payments p
        JOIN appointment_requests a ON p.appointment_id = a.id
        JOIN patientreg u ON a.user_id = u.id
        JOIN doctorreg d ON p.doctor_id = d.id
        ORDER BY p.payment_date DESC";
$result = $conn->query($sql);

// Calculate total revenue
$totalRevenueSql = "SELECT SUM(amount) as total_revenue FROM payments WHERE status = 'Success'";
$revenueResult = $conn->query($totalRevenueSql);
$totalRevenue = $revenueResult->fetch_assoc()['total_revenue'];

// Calculate revenue by payment method
$paymentMethodSql = "SELECT payment_method, SUM(amount) as method_total FROM payments 
                    WHERE status = 'Success' 
                    GROUP BY payment_method";
$paymentMethodResult = $conn->query($paymentMethodSql);

// Calculate daily revenue for the last 7 days
$dailyRevenueSql = "SELECT DATE(payment_date) as date, SUM(amount) as daily_total 
                    FROM payments 
                    WHERE status = 'Success' AND payment_date >= DATE_SUB(NOW(), INTERVAL 7 DAY) 
                    GROUP BY DATE(payment_date) 
                    ORDER BY date ASC";
$dailyRevenueResult = $conn->query($dailyRevenueSql);

// Prepare data for charts
$dailyLabels = [];
$dailyData = [];
while ($row = $dailyRevenueResult->fetch_assoc()) {
    $dailyLabels[] = date('d M', strtotime($row['date']));
    $dailyData[] = $row['daily_total'];
}

$methodLabels = [];
$methodData = [];
while ($row = $paymentMethodResult->fetch_assoc()) {
    $methodLabels[] = $row['payment_method'];
    $methodData[] = $row['method_total'];
}

// Reset the position of result sets
if ($dailyRevenueResult) $conn->query($dailyRevenueSql);
if ($paymentMethodResult) $conn->query($paymentMethodSql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Details & Revenue</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
    <style>
        :root {
            --primary-color: #2e7d32; /* Green theme for money/payments */
            --primary-light: #4caf50;
            --primary-dark: #1b5e20;
            --secondary-color: #f9a825; /* Gold accent for money theme */
            --light-color: #f8f9fa;
            --dark-color: #202124;
            --gray-color: #5f6368;
            --success-color: #2e7d32;
            --pending-color: #f57c00;
            --failed-color: #c62828;
        }
        
        
        .container2 {
            max-width: 1200px;
            margin: 0 auto;
            background-color: white;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 30px;
        }
        
        
        
        h2 {
            color: var(--primary-color);
            font-size: 28px;
            font-weight: 600;
        }
        
        .filter-controls {
            display: flex;
            gap: 15px;
            margin-bottom: 30px;
            flex-wrap: wrap;
        }
        
        .filter-item {
            flex: 1;
            min-width: 200px;
        }
        
        .filter-item label {
            display: block;
            margin-bottom: 5px;
            font-size: 14px;
            color: var(--gray-color);
        }
        
        .filter-item select, .filter-item input {
            width: 100%;
            padding: 10px;
            border-radius: 6px;
            border: 1px solid #ddd;
            font-size: 15px;
        }
        
        button.filter-btn {
            background-color: var(--primary-color);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 500;
            transition: all 0.2s;
            align-self: flex-end;
        }
        
        button.filter-btn:hover {
            background-color: var(--primary-dark);
        }
        
        .dashboard {
            display: grid;
            grid-template-columns: repeat(12, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .revenue-card {
            grid-column: span 3;
            background-color: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
            border-left: 4px solid var(--primary-color);
            display: flex;
            flex-direction: column;
        }
        
        @media (max-width: 1024px) {
            .revenue-card {
                grid-column: span 6;
            }
        }
        
        @media (max-width: 768px) {
            .revenue-card {
                grid-column: span 12;
            }
        }
        
        .revenue-card h3 {
            font-size: 16px;
            font-weight: 500;
            color: var(--gray-color);
            margin-bottom: 10px;
        }
        
        .revenue-amount {
            font-size: 28px;
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 5px;
        }
        
        .revenue-trend {
            display: flex;
            align-items: center;
            font-size: 14px;
            gap: 5px;
        }
        
        .trend-up {
            color: var(--success-color);
        }
        
        .trend-down {
            color: var(--failed-color);
        }
        
        .charts-container {
            display: grid;
            grid-template-columns: repeat(12, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .chart-card {
            background-color: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        }
        
        .chart-card.revenue-chart {
            grid-column: span 8;
        }
        
        .chart-card.payment-methods {
            grid-column: span 4;
        }
        
        /* Chart height controls - new styles */
        .chart-container {
            position: relative;
            height: 200px; /* Reduced from default height */
        }
        
        @media (max-width: 992px) {
            .chart-card.revenue-chart, .chart-card.payment-methods {
                grid-column: span 12;
            }
        }
        
        .chart-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }
        
        .chart-title {
            font-size: 18px;
            font-weight: 600;
            color: var(--dark-color);
        }
        
        .chart-controls select {
            padding: 6px 10px;
            border-radius: 4px;
            border: 1px solid #ddd;
            font-size: 14px;
        }
        
        .table-container {
            overflow-x: auto;
            margin-top: 20px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
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
        
        .status-success {
            background-color: #e6f7e9;
            color: #1e7d34;
        }
        
        .status-pending {
            background-color: #fff4db;
            color: #d68102;
        }
        
        .status-failed {
            background-color: #fdeaea;
            color: #c42b1c;
        }
        
        .amount {
            font-weight: 600;
            color: var(--primary-color);
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
        
        .export-btn {
            background-color: var(--secondary-color);
            color: var(--dark-color);
            border: none;
            padding: 10px 15px;
            border-radius: 4px;
            cursor: pointer;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        .export-btn:hover {
            background-color: #f0b941;
        }
    </style>
</head>
<body>
    <div class="container2">
        <header>
            <h2><i class="fas fa-credit-card"></i> Payment Details & Revenue</h2>
           
        </header>

        <br>
        
        
        <!-- Revenue Dashboard -->
        <div class="dashboard">
            <div class="revenue-card">
                <h3>Total Revenue</h3>
                <div class="revenue-amount">₹<?php echo number_format($totalRevenue, 2); ?></div>
                <div class="revenue-trend trend-up">
                    <i class="fas fa-arrow-up"></i> 8.5% vs previous period
                </div>
            </div>
            
            <?php
            // Get today's revenue
            $todayRevenueSql = "SELECT SUM(amount) as today_revenue FROM payments 
                              WHERE status = 'Success' AND DATE(payment_date) = CURDATE()";
            $todayResult = $conn->query($todayRevenueSql);
            $todayRevenue = $todayResult->fetch_assoc()['today_revenue'] ?? 0;
            ?>
            <div class="revenue-card">
                <h3>Today's Revenue</h3>
                <div class="revenue-amount">₹<?php echo number_format($todayRevenue, 2); ?></div>
                <div class="revenue-trend trend-up">
                    <i class="fas fa-arrow-up"></i> 12.3% vs yesterday
                </div>
            </div>
            
            <?php
            // Get transaction count
            $transactionCountSql = "SELECT COUNT(*) as count FROM payments WHERE status = 'Success'";
            $countResult = $conn->query($transactionCountSql);
            $transactionCount = $countResult->fetch_assoc()['count'];
            ?>
            <div class="revenue-card">
                <h3>Total Transactions</h3>
                <div class="revenue-amount"><?php echo $transactionCount; ?></div>
                <div class="revenue-trend trend-up">
                    <i class="fas fa-arrow-up"></i> 5.2% vs previous period
                </div>
            </div>
            
            <?php
            // Get average transaction value
            $avgValue = $transactionCount > 0 ? $totalRevenue / $transactionCount : 0;
            ?>
            <div class="revenue-card">
                <h3>Average Transaction</h3>
                <div class="revenue-amount">₹<?php echo number_format($avgValue, 2); ?></div>
                <div class="revenue-trend trend-up">
                    <i class="fas fa-arrow-up"></i> 3.1% vs previous period
                </div>
            </div>
        </div>
        
        <!-- Charts -->
        <div class="charts-container">
            <div class="chart-card revenue-chart">
                <div class="chart-header">
                    <div class="chart-title">Revenue Trend</div>
                    <div class="chart-controls">
                        
                    </div>
                </div>
                <div class="chart-container">
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>
            
            <div class="chart-card payment-methods">
                <div class="chart-header">
                    <div class="chart-title">Payment Methods</div>
                </div>
                <div class="chart-container">
                    <canvas id="methodsChart"></canvas>
                </div>
            </div>
        </div>
        
        <!-- Payment Table -->
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Patient</th>
                        <th>Doctor</th>
                        <th>Amount</th>
                        <th>Payment Method</th>
                        <th>Transaction ID</th>
                        <th>Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    // Ensure result is reset
                    if ($result) $result->data_seek(0);
                    while ($row = $result->fetch_assoc()): 
                        $statusClass = '';
                        switch(strtolower($row['status'])) {
                            case 'success':
                                $statusClass = 'status-success';
                                break;
                            case 'pending':
                                $statusClass = 'status-pending';
                                break;
                            case 'failed':
                                $statusClass = 'status-failed';
                                break;
                            default:
                                $statusClass = 'status-success';
                        }
                    ?>
                        <tr>
                            <td>#<?php echo $row['id']; ?></td>
                            <td><?php echo htmlspecialchars($row['patient_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['doctor_name']); ?></td>
                            <td class="amount">₹<?php echo number_format($row['amount'], 2); ?></td>
                            <td><?php echo htmlspecialchars($row['payment_method']); ?></td>
                            <td><?php echo htmlspecialchars($row['transaction_id']); ?></td>
                            <td><?php echo date('d M Y, h:i A', strtotime($row['payment_date'])); ?></td>
                            <td><span class="status <?php echo $statusClass; ?>"><?php echo $row['status']; ?></span></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="pagination">
            <button><i class="fas fa-chevron-left"></i></button>
            <button class="active">1</button>
            <button>2</button>
            <button>3</button>
            <button><i class="fas fa-chevron-right"></i></button>
        </div>
    </div>
    
    <script>
        // Configure and initialize charts
        document.addEventListener('DOMContentLoaded', function() {
            // Revenue Trend Chart
            const revenueCtx = document.getElementById('revenueChart').getContext('2d');
            const revenueChart = new Chart(revenueCtx, {
                type: 'line',
                data: {
                    labels: <?php echo json_encode($dailyLabels); ?>,
                    datasets: [{
                        label: 'Daily Revenue',
                        data: <?php echo json_encode($dailyData); ?>,
                        backgroundColor: 'rgba(46, 125, 50, 0.2)',
                        borderColor: 'rgba(46, 125, 50, 1)',
                        borderWidth: 2,
                        tension: 0.3,
                        fill: true,
                        pointBackgroundColor: 'rgba(46, 125, 50, 1)',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        pointRadius: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return '₹ ' + context.parsed.y.toFixed(2);
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return '₹' + value;
                                },
                                font: {
                                    size: 10 // Smaller font size for axis labels
                                }
                            }
                        },
                        x: {
                            ticks: {
                                font: {
                                    size: 10 // Smaller font size for axis labels
                                }
                            }
                        }
                    }
                }
            });
            
            // Payment Methods Chart
            const methodsCtx = document.getElementById('methodsChart').getContext('2d');
            const methodsChart = new Chart(methodsCtx, {
                type: 'doughnut',
                data: {
                    labels: <?php echo json_encode($methodLabels); ?>,
                    datasets: [{
                        data: <?php echo json_encode($methodData); ?>,
                        backgroundColor: [
                            'rgba(46, 125, 50, 0.8)',
                            'rgba(76, 175, 80, 0.8)',
                            'rgba(139, 195, 74, 0.8)',
                            'rgba(205, 220, 57, 0.8)',
                            'rgba(249, 168, 37, 0.8)'
                        ],
                        borderColor: '#ffffff',
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                padding: 10, // Reduced padding
                                usePointStyle: true,
                                pointStyle: 'circle',
                                font: {
                                    size: 10 // Smaller font size for legend
                                }
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const label = context.label || '';
                                    const value = context.parsed || 0;
                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    const percentage = Math.round((value / total) * 100);
                                    return `${label}: ₹${value} (${percentage}%)`;
                                }
                            }
                        }
                    },
                    cutout: '70%'
                }
            });
            
            // Example filter functionality
            document.querySelector('.filter-btn').addEventListener('click', function() {
                // In a real application, you would implement AJAX filtering here
                // This is just a placeholder
                alert('Filter applied! In a real implementation, this would refresh the data.');
            });
            
            // Export button functionality
            document.querySelector('.export-btn').addEventListener('click', function() {
                alert('Export functionality would be implemented here. This would generate a CSV or PDF report.');
            });
            
            // Period change for revenue chart
            document.getElementById('revenue-period').addEventListener('change', function() {
                // In a real application, you would fetch new data based on the selected period
                alert('Period changed! In a real implementation, this would update the chart data.');
            });
        });
    </script>
</body>
</html>

<?php $conn->close(); ?>