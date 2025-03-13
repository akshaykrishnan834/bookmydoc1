<?php
include('razorpay_config.php');

if(isset($_POST['amount'])) {
    $amount = $_POST['amount'];
    $order = createRazorpayOrder($amount);
    echo json_encode($order);
} else {
    echo json_encode(['error' => 'Amount not provided']);
}
?> 