<?php 
// Define Razorpay API keys
define('RAZORPAY_KEY_ID', 'rzp_test_enBJVcajFSH1Ci');
define('RAZORPAY_KEY_SECRET', '335hWwGIo6uyV9PYp8kXWMej');

include('db_connection.php'); 
include('patientheader.php'); 

// Check if appointment ID is provided
if(!isset($_GET['appointment_id']) || empty($_GET['appointment_id'])) {
    echo "<script>alert('No appointment selected!'); window.location.href='my_appointments.php';</script>";
    exit;
}

// Get appointment ID from URL
$appointment_id = $_GET['appointment_id'];

// Fetch the specific appointment details
$sql = "SELECT ar.id, ar.doctor_id, d.name AS doctor_name, d.specialization, ar.appointment_date,
               da.start_time, da.end_time, ar.status, ar.created_at,
               u.name AS patient_name, u.email, u.phone,
               d.fees AS consultation_fee
        FROM appointment_requests ar
        JOIN doctorreg d ON ar.doctor_id = d.id
        JOIN doctor_availability da ON ar.slot_id = da.id
        JOIN patientreg u ON ar.user_id = u.id
        WHERE ar.id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $appointment_id);
$stmt->execute();
$result = $stmt->get_result();

if($result->num_rows === 0) {
    echo "<script>alert('Appointment not found!'); window.location.href='my_appointments.php';</script>";
    exit;
}

$appointment = $result->fetch_assoc();

// Check if payment is already made
$payment_sql = "SELECT * FROM payments WHERE appointment_id = ? LIMIT 1";
$payment_stmt = $conn->prepare($payment_sql);
$payment_stmt->bind_param("i", $appointment_id);
$payment_stmt->execute();
$payment_result = $payment_stmt->get_result();
$payment_made = $payment_result->num_rows > 0;
$payment_data = $payment_made ? $payment_result->fetch_assoc() : null;

// Calculate tax and total amount
$consultation_fee = $appointment['consultation_fee'] ?? 1500;
$tax = $consultation_fee * 0.10; // 10% tax
$additional_charges = 2; // Additional charges
$total_amount = $consultation_fee + $tax + $additional_charges;

// Initialize variables
$payment_method = '';
$payment_success = false;
$payment_error = '';

// Process payment
if (isset($_POST['process_payment']) && !$payment_made) {
    $payment_method = 'Razorpay';
    $razorpay_payment_id = $_POST['razorpay_payment_id'] ?? '';
    $razorpay_order_id = $_POST['razorpay_order_id'] ?? '';
    $doctor_id = $appointment['doctor_id'];

    if (!empty($razorpay_payment_id) && !empty($razorpay_order_id)) {
        $insert_sql = "INSERT INTO payments (appointment_id, doctor_id, amount, payment_method, transaction_id, order_id, payment_date) 
                      VALUES (?, ?, ?, ?, ?, ?, NOW())";
        $insert_stmt = $conn->prepare($insert_sql);
        $insert_stmt->bind_param("iidsss", $appointment_id, $doctor_id, $total_amount, $payment_method, $razorpay_payment_id, $razorpay_order_id);
        
        if ($insert_stmt->execute()) {
            // Update appointment status
            $update_sql = "UPDATE appointment_requests SET payment_status = 'Paid' WHERE id = ?";
            $update_stmt = $conn->prepare($update_sql);
            $update_stmt->bind_param("i", $appointment_id);
            $update_stmt->execute();
            
            // Set payment success flag
            $payment_success = true;
            
            // Refresh the page to show the payment confirmation
            echo "<script>window.location.href='payment.php?appointment_id=" . $appointment_id . "&success=true';</script>";
            exit;
        } else {
            // Payment insert failed
            $payment_error = "Payment record could not be saved. Please contact support.";
        }
    }
}

// Format appointment ID with APT prefix
$formatted_apt_id = 'APT' . $appointment_id;
// Format patient ID with PAT prefix
$formatted_pat_id = 'PAT' . $appointment['id'];
// Format date for display
$current_date = date('Y-m-d');
$current_time = date('h:i A');

// Check if we need to refresh payment status (after redirect)
if (isset($_GET['success']) && $_GET['success'] == 'true') {
    // Recheck payment status as we might have been redirected
    $payment_stmt->execute();
    $payment_result = $payment_stmt->get_result();
    $payment_made = $payment_result->num_rows > 0;
    $payment_data = $payment_made ? $payment_result->fetch_assoc() : null;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Details</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .container2 {
            max-width: 900px;
            margin-top: 30px;
            margin-left: auto;
            margin-right: auto;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            padding: 30px;
        }
        .page-header {
            border-bottom: 1px solid #dee2e6;
            padding-bottom: 15px;
            margin-bottom: 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .page-header h1 {
            margin: 0;
            font-size: 28px;
            color: #2d3748;
            font-weight: 600;
        }
        .date-time {
            color: #6b7280;
            font-size: 14px;
        }
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 30px;
        }
        .info-card {
            background-color: #f9fafb;
            border-radius: 8px;
            padding: 20px;
        }
        .info-label {
            color: #6b7280;
            font-size: 14px;
            margin-bottom: 5px;
        }
        .info-value {
            font-size: 16px;
            color: #1a202c;
            font-weight: 500;
        }
        .specialization {
            color: #6b7280;
            font-size: 14px;
            margin-top: 5px;
        }
        .payment-summary {
            background-color: #f0f9f0;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 30px;
        }
        .payment-summary h2 {
            font-size: 18px;
            margin-bottom: 15px;
            color: #2d3748;
            font-weight: 600;
        }
        .fee-item {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #e2e8f0;
        }
        .fee-item:last-child {
            border-bottom: none;
        }
        .fee-label {
            color: #4a5568;
        }
        .fee-value {
            font-weight: 500;
            color: #1a202c;
        }
        .payment-total {
            display: flex;
            justify-content: space-between;
            margin-top: 15px;
            font-weight: 600;
            color: #38a169;
            font-size: 18px;
        }
        .payment-methods {
            margin-bottom: 30px;
        }
        .payment-methods h2 {
            font-size: 18px;
            margin-bottom: 15px;
            color: #2d3748;
            font-weight: 600;
        }
        .payment-option {
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 10px;
            cursor: pointer;
            transition: all 0.2s;
        }
        .payment-option:hover {
            border-color: #90cdf4;
            background-color: #f0f7ff;
        }
        .payment-option input {
            margin-right: 10px;
        }
        .pay-button {
            background-color: #38a169;
            color: white;
            border: none;
            border-radius: 8px;
            padding: 12px;
            font-size: 16px;
            font-weight: 500;
            width: 100%;
            cursor: pointer;
            transition: all 0.2s;
        }
        .pay-button:hover {
            background-color: #2f855a;
        }
    </style>
    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <div class="container2">
        <div class="page-header">
            <h1>Payment Details</h1>
            <div class="date-time">
                Date: <?php echo date('Y-m-d'); ?> | Time: <?php echo date('h:i A'); ?>
            </div>
        </div>

        <div class="info-grid">
            <div class="info-card">
                <div class="info-label">Appointment ID</div>
                <div class="info-value"><?php echo $formatted_apt_id; ?></div>
            </div>
            <div class="info-card">
                <div class="info-label">Patient ID</div>
                <div class="info-value"><?php echo $formatted_pat_id; ?></div>
            </div>
            <div class="info-card">
                <div class="info-label">Doctor Name</div>
                <div class="info-value">Dr. <?php echo htmlspecialchars($appointment['doctor_name']); ?></div>
                <div class="specialization">Specialization</div>
                <div class="info-value"><?php echo htmlspecialchars($appointment['specialization']); ?></div>
            </div>
            <div class="info-card">
                <div class="info-label">Patient Name</div>
                <div class="info-value"><?php echo htmlspecialchars($appointment['patient_name']); ?></div>
            </div>
            <div class="info-card">
                <div class="info-label">Appointment Date</div>
                <div class="info-value"><?php echo date('Y-m-d', strtotime($appointment['appointment_date'])); ?></div>
            </div>
            <div class="info-card">
                <div class="info-label">Appointment Time</div>
                <div class="info-value"><?php echo date('h:i A', strtotime($appointment['start_time'])); ?></div>
            </div>
        </div>

        <div class="payment-summary">
            <h2>Payment Summary</h2>
            <div class="fee-item">
                <div class="fee-label">Consultation Fee</div>
                <div class="fee-value">₹<?php echo number_format($consultation_fee, 2); ?></div>
            </div>
            <div class="fee-item">
                <div class="fee-label">Tax</div>
                <div class="fee-value">₹<?php echo number_format($tax, 2); ?></div>
            </div>
            <div class="fee-item">
                <div class="fee-label">Additional Charges</div>
                <div class="fee-value">₹<?php echo number_format($additional_charges, 2); ?></div>
            </div>
            <div class="payment-total">
                <div>Total:</div>
                <div>₹<?php echo number_format($total_amount, 2); ?></div>
            </div>
        </div>

        <?php if($payment_made): ?>
            <div class="payment-methods" style="text-align: center;">
                <div style="background-color: #d4edda; color: #155724; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
                    <h2 style="color: #155724; margin-bottom: 10px;">Payment Confirmed</h2>
                    <p>Your payment has been successfully processed.</p>
                    <p>Transaction ID: <?php echo htmlspecialchars($payment_data['transaction_id']); ?></p>
                    <p>Payment Date: <?php echo date('Y-m-d h:i A', strtotime($payment_data['payment_date'])); ?></p>
                    
                    <!-- Print Button - Only shown when payment is made -->
                    <div style="margin-top: 20px;">
                        <button id="print-button" class="pay-button" style="background-color: #007bff; width: auto; padding: 10px 20px;">
                            Print Payment Receipt
                        </button>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <form id="payment-form" method="POST" action="">
                <div class="payment-methods">
                    <h2>Select Payment Method</h2>
                    <label class="payment-option">
                        <input type="radio" name="payment_method" value="Credit/Debit Card" checked>
                        Credit/Debit Card
                    </label>
                    
                    <label class="payment-option">
                        <input type="radio" name="payment_method" value="Net Banking">
                        Net Banking
                    </label>
                </div>

                <button type="button" id="rzp-button" class="pay-button">
                    Pay ₹<?php echo number_format($total_amount, 2); ?>
                </button>
            </form>
        <?php endif; ?>
    </div>

    <script>
        $(document).ready(function(){
            // Razorpay payment handling
            $('#rzp-button').click(function(e){
                e.preventDefault();
                
                // Create Razorpay order first
                $.ajax({
                    url: 'create_order.php',
                    type: 'POST',
                    data: {
                        amount: <?php echo $total_amount; ?>
                    },
                    success: function(response) {
                        var order = JSON.parse(response);
                        if(order.id) {
                            var options = {
                                "key": "<?php echo RAZORPAY_KEY_ID; ?>",
                                "amount": "<?php echo $total_amount * 100; ?>",
                                "currency": "INR",
                                "name": "Doctor Appointment",
                                "description": "Appointment ID: <?php echo $formatted_apt_id; ?>",
                                "order_id": order.id,
                                "handler": function (response){
                                    console.log('Payment successful:', response);
                                    
                                    // Create form for submission
                                    var form = document.createElement('form');
                                    form.method = 'POST';
                                    form.action = 'payment.php?appointment_id=<?php echo $appointment_id; ?>';
                                    
                                    var hiddenField = document.createElement('input');
                                    hiddenField.type = 'hidden';
                                    hiddenField.name = 'razorpay_payment_id';
                                    hiddenField.value = response.razorpay_payment_id;
                                    form.appendChild(hiddenField);
                                    
                                    var orderField = document.createElement('input');
                                    orderField.type = 'hidden';
                                    orderField.name = 'razorpay_order_id';
                                    orderField.value = response.razorpay_order_id;
                                    form.appendChild(orderField);
                                    
                                    var processField = document.createElement('input');
                                    processField.type = 'hidden';
                                    processField.name = 'process_payment';
                                    processField.value = '1';
                                    form.appendChild(processField);
                                    
                                    document.body.appendChild(form);
                                    form.submit();
                                },
                                "prefill": {
                                    "name": "<?php echo htmlspecialchars($appointment['patient_name']); ?>",
                                    "email": "<?php echo htmlspecialchars($appointment['email']); ?>",
                                    "contact": "<?php echo htmlspecialchars($appointment['phone']); ?>"
                                },
                                "theme": {
                                    "color": "#38a169"
                                }
                            };
                            var rzp1 = new Razorpay(options);
                            rzp1.open();
                        } else {
                            alert('Could not create order. Please try again.');
                        }
                    },
                    error: function() {
                        alert('Could not create order. Please try again.');
                    }
                });
            });

            // Print functionality
            $('#print-button').click(function(){
                // Create a new window for printing
                var printWindow = window.open('', '_blank');
                
                // Get the content we want to print
                var content = `
                    <html>
                    <head>
                        <title>Payment Receipt - <?php echo $formatted_apt_id; ?></title>
                        <style>
                            body {
                                font-family: Arial, sans-serif;
                                margin: 20px;
                            }
                            .receipt-container {
                                max-width: 800px;
                                margin: 0 auto;
                                padding: 20px;
                                border: 1px solid #ccc;
                            }
                            .header {
                                text-align: center;
                                margin-bottom: 20px;
                            }
                            .details-table {
                                width: 100%;
                                border-collapse: collapse;
                                margin-bottom: 20px;
                            }
                            .details-table td {
                                padding: 8px;
                                border: 1px solid #ddd;
                            }
                            .label {
                                font-weight: bold;
                                width: 40%;
                            }
                            .summary-table {
                                width: 60%;
                                margin-left: auto;
                                border-collapse: collapse;
                            }
                            .summary-table td {
                                padding: 8px;
                                border: 1px solid #ddd;
                            }
                        </style>
                    </head>
                    <body>
                        <div class="receipt-container">
                            <div class="header">
                            <h1>BOOK MY DOC - Online Doctor Appointment Booking System</h1>
                                <h2>Payment Receipt</h2>
                                <p>Date: <?php echo date('Y-m-d'); ?> | Time: <?php echo date('h:i A'); ?></p>
                            </div>
                            
                            <table class="details-table">
                                <tr>
                                    <td class="label">Appointment ID</td>
                                    <td><?php echo $formatted_apt_id; ?></td>
                                </tr>
                                <tr>
                                    <td class="label">Patient ID</td>
                                    <td><?php echo $formatted_pat_id; ?></td>
                                </tr>
                                <tr>
                                    <td class="label">Patient Name</td>
                                    <td><?php echo htmlspecialchars($appointment['patient_name']); ?></td>
                                </tr>
                                <tr>
                                    <td class="label">Doctor Name</td>
                                    <td>Dr. <?php echo htmlspecialchars($appointment['doctor_name']); ?></td>
                                </tr>
                                <tr>
                                    <td class="label">Specialization</td>
                                    <td><?php echo htmlspecialchars($appointment['specialization']); ?></td>
                                </tr>
                                <tr>
                                    <td class="label">Appointment Date</td>
                                    <td><?php echo date('Y-m-d', strtotime($appointment['appointment_date'])); ?></td>
                                </tr>
                                <tr>
                                    <td class="label">Appointment Time</td>
                                    <td><?php echo date('h:i A', strtotime($appointment['start_time'])); ?></td>
                                </tr>
                                <?php if($payment_made): ?>
                                <tr>
                                    <td class="label">Transaction ID</td>
                                    <td><?php echo htmlspecialchars($payment_data['transaction_id']); ?></td>
                                </tr>
                                <tr>
                                    <td class="label">Payment Date</td>
                                    <td><?php echo date('Y-m-d h:i A', strtotime($payment_data['payment_date'])); ?></td>
                                </tr>
                                <?php endif; ?>
                            </table>

                            <table class="summary-table">
                                <tr>
                                    <td>Consultation Fee</td>
                                    <td>₹<?php echo number_format($consultation_fee, 2); ?></td>
                                </tr>
                                <tr>
                                    <td>Tax</td>
                                    <td>₹<?php echo number_format($tax, 2); ?></td>
                                </tr>
                                <tr>
                                    <td>Additional Charges</td>
                                    <td>₹<?php echo number_format($additional_charges, 2); ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Total Amount</strong></td>
                                    <td><strong>₹<?php echo number_format($total_amount, 2); ?></strong></td>
                                </tr>
                            </table>
                        </div>
                    </body>
                    </html>
                `;
                
                // Write content to the new window and print
                printWindow.document.write(content);
                printWindow.document.close();
                printWindow.focus();
                printWindow.print();
                // Uncomment the next line if you want the print window to close automatically after printing
                // printWindow.close();
            });
        });
    </script>
</body>
</html>