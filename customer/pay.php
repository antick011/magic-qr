<?php
session_start();
include '../db.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

$customer_id = $_SESSION['user_id']; // Logged-in user ID

// Include Razorpay SDK
require('../razorpay-php/Razorpay.php');
use Razorpay\Api\Api;

// Razorpay API Credentials (Replace with your actual API keys)
$razorpay_key_id = "YOUR_KEY_ID";
$razorpay_key_secret = "YOUR_KEY_SECRET";

// Initialize Razorpay API
$api = new Api($razorpay_key_id, $razorpay_key_secret);

// Define available plans
$plans = [
    "lifetime" => ["price" => 499900, "label" => "₹4999 - Lifetime"],
    "yearly" => ["price" => 149900, "label" => "₹1499 - Yearly"],
    "half_yearly" => ["price" => 99900, "label" => "₹999 - Half-Yearly"]
];

// Check if a plan is selected
if (isset($_POST['plan']) && array_key_exists($_POST['plan'], $plans)) {
    $selected_plan = $_POST['plan'];
    $amount = $plans[$selected_plan]["price"];
    
    // Create a Razorpay Order
    $order = $api->order->create([
        'receipt' => "order_" . $customer_id,
        'amount' => $amount, // Amount in paise (₹4999 = 499900 paise)
        'currency' => 'INR',
        'payment_capture' => 1
    ]);

    $_SESSION['order_id'] = $order->id;
    $_SESSION['selected_plan'] = $selected_plan;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Choose Your Plan</title>
    <link rel="stylesheet" href="../styles.css">
    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: #f5f5f5;
            text-align: center;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 400px;
            margin: auto;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            margin-top: 50px;
        }
        h2 {
            color: #333;
        }
        .plan-btn {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border-radius: 5px;
            border: none;
            font-size: 16px;
            cursor: pointer;
            font-weight: bold;
        }
        .lifetime { background: #28a745; color: white; }
        .yearly { background: #007bff; color: white; }
        .half_yearly { background: #ff5733; color: white; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Choose Your Plan</h2>
        <form method="POST" action="payment.php">
            <button type="submit" name="plan" value="lifetime" class="plan-btn lifetime">₹4999 - Lifetime</button>
            <button type="submit" name="plan" value="yearly" class="plan-btn yearly">₹1499 - Yearly</button>
            <button type="submit" name="plan" value="half_yearly" class="plan-btn half_yearly">₹999 - Half-Yearly</button>
        </form>
        
        <?php if (isset($order)) { ?>
            <script>
                var options = {
                    "key": "<?= $razorpay_key_id ?>",
                    "amount": "<?= $amount ?>",
                    "currency": "INR",
                    "name": "MagicQR Payment",
                    "description": "Payment for <?= $plans[$selected_plan]['label'] ?>",
                    "order_id": "<?= $order->id ?>",
                    "handler": function (response) {
                        window.location.href = "payment_success.php?payment_id=" + response.razorpay_payment_id;
                    },
                    "theme": {
                        "color": "#28a745"
                    }
                };
                var rzp1 = new Razorpay(options);
                rzp1.open();
            </script>
        <?php } ?>
    </div>
</body>
</html>
