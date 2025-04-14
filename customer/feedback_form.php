<?php
session_start();
include '../db.php';

$customer_id = $_GET['customer_id'];  // Get customer_id from the URL

// Check if the customer's plan is active or expired
$stmt = $conn->prepare("SELECT expiry_date, status FROM customers WHERE id = ?");
$stmt->bind_param("i", $customer_id);
$stmt->execute();
$result = $stmt->get_result()->fetch_assoc();
$stmt->close();

$expiry_date = $result['expiry_date'];
$status = $result['status'];

// Check if plan has expired
if ($status === 'expired' || (!is_null($expiry_date) && strtotime($expiry_date) < time())) {
    echo "<p style='color: red; font-size: 50px; text-align: center; font-weight: bold;'>🚨 This feedback form has expired!</p>";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $rating = $_POST['rating'];
    $name = $_POST['name'];
    $phone_number = $_POST['phone_number'];
    $comments = $_POST['comments'];

    // Insert feedback into the database
    $stmt = $conn->prepare("INSERT INTO feedback (customer_id, rating, name, phone_number, comments) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("iisss", $customer_id, $rating, $name, $phone_number, $comments);

    if ($stmt->execute()) {
        echo "Feedback submitted successfully.";
    } else {
        echo "Error submitting feedback.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit Your Feedback</title>
    <link rel="stylesheet" href="../styles.css">
    <style>
        /* Design styles as you provided */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #f5f5f5, #dfe9f3);
            text-align: center;
            overflow-x: hidden;
        }
        .header {
            width: 100%;
            height: 300px;
            background-size: cover;
            background-position: center;
            animation: slideshow 12s infinite;
            border-bottom: 5px solid #28a745;
        }
        @keyframes slideshow {
            0% { background-image: url('../images/1.jpg'); }
            33% { background-image: url('../images/2.jpg'); }
            66% { background-image: url('../images/3.jpg'); }
        }
        .review-message {
            margin: 20px;
            font-size: 20px;
            font-weight: bold;
            color: #333;
            background: #fff;
            padding: 15px;
            border-radius: 10px;
            display: inline-block;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
        }
        .overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            display: none;
            z-index: 999;
        }
        .feedback-form {
            width: 90%;
            max-width: 500px;
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
            position: fixed;
            bottom: -100%;
            left: 50%;
            transform: translate(-50%, 0);
            display: block;
            z-index: 1000;
            transition: bottom 0.5s ease-in-out;
        }
        .feedback-form.show {
            bottom: 15%;
        }
        .star {
            font-size: 35px;
            color: grey;
            cursor: pointer;
            transition: color 0.3s;
        }
        .star.selected { color: gold; }
        .star:hover { color: orange; }
        input, textarea {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 16px;
        }
        button {
            width: 100%;
            padding: 12px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 18px;
            font-weight: bold;
        }
        button:disabled { background-color: #ccc; }
    </style>
    <script>
        function handleRating(rating) {
            var stars = document.getElementsByClassName("star");
            for (var i = 0; i < stars.length; i++) {
                stars[i].classList.toggle("selected", i < rating);
            }
            document.getElementById("ratingInput").value = rating;
            if (rating >= 4) {
                var googleReviewLink = "<?php 
                    $stmt2 = $conn->prepare('SELECT google_review_link FROM customers WHERE user_id = ?');
                    $stmt2->bind_param('i', $customer_id);
                    $stmt2->execute();
                    $result = $stmt2->get_result();
                    $customer = $result->fetch_assoc();
                    echo $customer['google_review_link'];
                ?>";
                window.location.href = googleReviewLink;
            } else {
                document.getElementById("additionalFields").style.display = "block";
                document.getElementById("submitFeedback").disabled = false;
            }
        }
        window.onload = function() {
            setTimeout(function() {
                document.getElementById("feedbackForm").classList.add("show");
                document.getElementById("overlay").style.display = "block";
            }, 2000);
        };
    </script>
</head>
<body>
    <div class="header"></div>
    <div class="review-message">We value your feedback! Please rate our service.</div>
    <div id="overlay" class="overlay"></div>
    <div id="feedbackForm" class="feedback-form">
        <h2 style="color: #28a745;">Submit Your Feedback</h2>
        <form method="POST" action="feedback_form.php?customer_id=<?= htmlspecialchars($customer_id) ?>">
            <div>
                <span class="star" onclick="handleRating(1)">★</span>
                <span class="star" onclick="handleRating(2)">★</span>
                <span class="star" onclick="handleRating(3)">★</span>
                <span class="star" onclick="handleRating(4)">★</span>
                <span class="star" onclick="handleRating(5)">★</span>
            </div>
            <input type="hidden" name="rating" id="ratingInput" required>
            <div id="additionalFields" style="display: none;">
                <input type="text" name="name" placeholder="Your Name" required>
                <input type="tel" name="phone_number" placeholder="Your Contact" required>
                <textarea name="comments" placeholder="Your Feedback" required></textarea>
            </div>
            <button type="submit" id="submitFeedback" disabled>Submit Feedback</button>
        </form>
    </div>
</body>
</html>
