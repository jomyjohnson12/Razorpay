<?php
session_start(); // Start the session

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    die(json_encode(["status" => "error", "message" => "User not logged in."]));
}

// Generate a Razorpay order
$amount = 50000; // Amount in paise (e.g., 50000 paise = ₹500)
$currency = "INR";

// Replace with your Razorpay API credentials
$keyId = "rzp_test_RuSwORxstWRyLs";
$keySecret = "PcFKXeXbBWyAPpB4e6E7roQa";

// Create an order
$data = [
    "amount" => $amount,
    "currency" => $currency,
    "receipt" => "order_" . time(),
    "payment_capture" => 1
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://api.razorpay.com/v1/orders");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_USERPWD, $keyId . ":" . $keySecret);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Content-Type: application/json"
]);

$response = curl_exec($ch);
curl_close($ch);

echo $response;
?>