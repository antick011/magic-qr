<?php
session_start();
include '../db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'customer') {
    header("Location: ../auth/login.php");
    exit();
}

$customer_id = $_SESSION['user_id']; // Get customer ID from session

// Fetch customer details, including plan and expiry date
$stmt = $conn->prepare("SELECT plan, expiry_date, status FROM customers WHERE id = ?");
$stmt->bind_param("i", $customer_id);
$stmt->execute();
$result = $stmt->get_result()->fetch_assoc();
$stmt->close();

$plan = $result['plan'];
$expiry_date = $result['expiry_date'];
$status = $result['status'];

// Check if the plan has expired
if ($status === 'expired' || (!is_null($expiry_date) && strtotime($expiry_date) < time())) {
    $expired = true;
} else {
    $expired = false;
}

// Define the path to the generated QR code image for this customer
$qr_code_image_path = "../generated_qr_codes/feedback_qr_" . $customer_id . ".png";

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Dashboard</title>
    <link rel="stylesheet" href="../styles.css">
    <style>
        /* Mobile-first design */
        body {
            font-family: 'Poppins', sans-serif;
            background: #f5f5f5;
            margin: 0;
            padding: 0;
        }

        .container {
            padding: 20px;
            text-align: center;
        }

        h2 {
            color: #333;
        }

        h3 {
            color: #28a745;
            margin-top: 20px;
        }

        p {
            color: #555;
            font-size: 16px;
        }

        /* Input field and button styles */
        input, button {
            width: 40%;
            padding: 12px;
            margin-top: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
            font-size: 16px;
        }

        button {
            background-color: #28a745;
            color: white;
            cursor: pointer;
            border: none;
        }

        button:disabled {
            background-color: #ddd;
        }

        .qr-container {
            margin-top: 20px;
        }

        .qr-container img {
            width: 100%;
            max-width: 200px;
            border-radius: 10px;
            border: 5px solid #ddd;
        }

        .download-btn {
            display: inline-block;
            margin-top: 10px;
            padding: 10px 15px;
            background: #28a745;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-size: 16px;
        }

        .expired-message {
            color: red;
            font-weight: bold;
            font-size: 18px;
            margin-top: 20px;
        }

        .renew-btn {
            background: #ff5733;
            color: white;
            padding: 10px 15px;
            border-radius: 5px;
            text-decoration: none;
            font-size: 16px;
            display: inline-block;
            margin-top: 15px;
        }

        /* Mobile-friendly feedback table */
        .feedback-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 100%;
        }

        .feedback-card {
            width: 90%;
            background: white;
            border-radius: 10px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
            padding: 15px;
            margin-bottom: 15px;
            text-align: center;
        }

        .feedback-card strong {
            color: #333;
        }

        .feedback-card .rating {
            font-size: 18px;
            font-weight: bold;
            color: #ffa500;
        }

        .feedback-card .created-at {
            font-size: 12px;
            color: #888;
        }

        /* Alternating colors for better readability */
        .feedback-card:nth-child(even) {
            background: #f9f9f9;
        }

        @media screen and (min-width: 600px) {
            .feedback-container {
                max-width: 600px;
                margin: auto;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Customer Dashboard</h2>

        <!-- If expired, show renewal message -->
        <?php if ($expired) { ?>
            <p class="expired-message">🚨 Your plan has expired! Renew now to reactivate your QR Code & Feedback Form.</p>
            <a href="payment.php" class="renew-btn">Renew Plan</a>
        <?php } else { ?>
            <h3>Your Feedback Form</h3>
            <p>Copy the link below and share it with others to gather feedback:</p>
            
            <input style="width: 80%;" type="text" id="feedbackFormUrl" value="http://localhost/magicqr/customer/feedback_form.php?customer_id=<?= htmlspecialchars($customer_id) ?>" readonly>
            <button onclick="copyFeedbackFormLink()">Copy Link</button>

            <h3>QR Code for Feedback Form</h3>
            <div class="qr-container">
                <!-- Display QR code image -->
                <img id="qrCode" src="<?= htmlspecialchars($qr_code_image_path, ENT_QUOTES, 'UTF-8') ?>" alt="QR Code">
                <br>
                <!-- Provide a link to download the QR code -->
                <a href="<?= htmlspecialchars($qr_code_image_path, ENT_QUOTES, 'UTF-8') ?>" class="download-btn" download="feedback_qr_<?= htmlspecialchars($customer_id) ?>.png">Download QR Code</a>
            </div>

            <h3>Your Feedback</h3>
            <?php
            $stmt = $conn->prepare("SELECT * FROM feedback WHERE customer_id = ? ORDER BY created_at DESC");
            $stmt->bind_param("i", $customer_id);
            $stmt->execute();
            $feedbacks = $stmt->get_result();
            ?>

            <div class="feedback-container">
            <?php if ($feedbacks->num_rows > 0) { ?>
                <?php while ($row = $feedbacks->fetch_assoc()) { ?>
                    <div class="feedback-card">
                        <div class="rating">
                            <?= htmlspecialchars($row['rating']) ?> ★
                        </div>
                        <strong>Name:</strong> <?= htmlspecialchars($row['name'], ENT_QUOTES, 'UTF-8') ?><br>
                        <strong>Phone:</strong> <?= htmlspecialchars($row['phone_number'], ENT_QUOTES, 'UTF-8') ?><br>
                        <strong>Comments:</strong> <?= nl2br(htmlspecialchars($row['comments'], ENT_QUOTES, 'UTF-8')) ?><br>
                        <div class="created-at">
                            <?= date('Y-m-d H:i:s', strtotime($row['created_at'])) ?>
                        </div>
                    </div>
                <?php } ?>
            <?php } else { ?>
                <p>No feedback available.</p>
            <?php } ?>
            </div>
        <?php } ?>
    </div>

    <script>
        function copyFeedbackFormLink() {
            var copyText = document.getElementById("feedbackFormUrl");
            navigator.clipboard.writeText(copyText.value).then(() => {
                alert("Feedback form link copied: " + copyText.value);
            }).catch(err => {
                console.error("Failed to copy: ", err);
            });
        }
    </script>
</body>
</html>

<?php
$conn->close();
?>
