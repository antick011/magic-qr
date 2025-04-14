<?php
include 'db.php'; // Include database connection

// Expire customers whose expiry_date has passed
$sql = "UPDATE customers SET status = 'expired' WHERE expiry_date IS NOT NULL AND expiry_date < CURDATE()";
$conn->query($sql);

echo "Expired customers updated successfully!";
$conn->close();
?>
