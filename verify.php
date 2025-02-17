<?php
require 'vendor/autoload.php';

use Razorpay\Api\Api;

$keyId = "rzp_test_RuSwORxstWRyLs";
$keySecret = "PcFKXeXbBWyAPpB4e6E7roQa";

$api = new Api($keyId, $keySecret);

$paymentId = $_POST['payment_id'];
$orderId = $_POST['order_id'];
$signature = $_POST['signature'];

$generatedSignature = hash_hmac('sha256', $orderId . "|" . $paymentId, $keySecret);

if ($generatedSignature === $signature) {
    echo "Payment successful!";
} else {
    echo "Payment verification failed!";
}
?>
