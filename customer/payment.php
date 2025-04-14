<?php
session_start();
include '../db.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

$customer_id = $_SESSION['user_id']; // Logged-in user ID

// Define available plans
$plans = [
    "lifetime" => ["price" => 4999, "label" => "₹4999 - Lifetime", "duration" => NULL],
    "yearly" => ["price" => 1499, "label" => "₹1499 - Yearly", "duration" => "+1 year"],
    "half_yearly" => ["price" => 999, "label" => "₹999 - Half-Yearly", "duration" => "+6 months"]
];

// Handle the payment simulation
if (isset($_POST['plan']) && array_key_exists($_POST['plan'], $plans)) {
    $selected_plan = $_POST['plan'];
    $payment_id = "TEST_PAYMENT_" . rand(1000, 9999);

    // Calculate expiry date
    if ($plans[$selected_plan]["duration"]) {
        $expiry_date = date('Y-m-d', strtotime($plans[$selected_plan]["duration"]));
    } else {
        $expiry_date = NULL; // Lifetime plan never expires
    }

    // Update customer plan in database
    $stmt = $conn->prepare("UPDATE customers SET plan = ?, expiry_date = ?, status = 'active' WHERE id = ?");
    $stmt->bind_param("ssi", $selected_plan, $expiry_date, $customer_id);

    if ($stmt->execute()) {
        $message = "✅ Payment successful! Your plan (" . $plans[$selected_plan]['label'] . ") is now active.";
    } else {
        $message = "❌ Error updating plan. Please contact support.";
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Choose Your Plan</title>
    <link rel="stylesheet" href="../styles.css">
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
        .message {
            font-size: 18px;
            color: green;
            font-weight: bold;
            margin-top: 15px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Choose Your Plan</h2>
        <?php if (isset($message)) { ?>
            <p class="message"><?= $message ?></p>
            <a href="dashboard.php" class="plan-btn lifetime">Go to Dashboard</a>
        <?php } else { ?>
            <form method="POST" action="payment.php">
                <button type="submit" name="plan" value="lifetime" class="plan-btn lifetime">₹4999 - Lifetime</button>
                <button type="submit" name="plan" value="yearly" class="plan-btn yearly">₹1499 - Yearly</button>
                <button type="submit" name="plan" value="half_yearly" class="plan-btn half_yearly">₹999 - Half-Yearly</button>
            </form>
        <?php } ?>
    </div>
</body>
</html>
