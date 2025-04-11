
<?php
session_start();
include "config1.php";  // Database connection
include "config.php";   // Razorpay API Keys

require('vendor/autoload.php');  // Razorpay SDK

use Razorpay\Api\Api;

$api = new Api(RAZORPAY_KEY_ID, RAZORPAY_KEY_SECRET);

if (!isset($_SESSION['grand_total'])) {
    echo json_encode(["error" => "Invalid Request"]);
    exit();
}

$orderAmount = round($_SESSION['grand_total'] * 100);  // ₹ to Paise conversion (Fixed Integer Issue)

$orderData = [
    'receipt' => 'ORD_' . uniqid(),
    'amount' => $orderAmount,  // Now always an integer
    'currency' => 'INR',
    'payment_capture' => 1  // Auto-capture payment
];

try {
    $razorpayOrder = $api->order->create($orderData);
    echo json_encode([
        'id' => $razorpayOrder['id'],
        'amount' => $razorpayOrder['amount'],
        'currency' => $razorpayOrder['currency']
    ]);
} catch (Exception $e) {
    echo json_encode(["error" => $e->getMessage()]);
}
?>
