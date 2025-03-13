<?php
// File: razorpay_config.php
// Store Razorpay API keys securely

// Define Razorpay API keys
define('RAZORPAY_KEY_ID', 'rzp_test_enBJVcajFSH1Ci');
define('RAZORPAY_KEY_SECRET', '335hWwGIo6uyV9PYp8kXWMej');

// Flag to indicate if we're in test mode
define('RAZORPAY_TEST_MODE', true);

// Function to create Razorpay order
function createRazorpayOrder($amount) {
    $url = 'https://api.razorpay.com/v1/orders';
    $data = array(
        'amount' => $amount * 100, // Amount in paise
        'currency' => 'INR',
        'receipt' => 'order_' . time()
    );

    $auth = base64_encode(RAZORPAY_KEY_ID . ':' . RAZORPAY_KEY_SECRET);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'Authorization: Basic ' . $auth
    ));
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

    $response = curl_exec($ch);
    curl_close($ch);

    return json_decode($response, true);
}
?>