<?php
session_start();
include '../db.php';

if ($_SESSION['role'] !== 'authority') {
    header("Location: ../auth/login.php");
    exit();
}

$customer_id = $_GET['customer_id']; 

$stmt = $conn->prepare("SELECT name, google_review_link, status FROM customers WHERE id = ?");
$stmt->bind_param("i", $customer_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "Customer not found!";
    exit();
}

$customer = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $google_review_link = $_POST['google_review_link'];
    $status = $_POST['status'];

    $update_stmt = $conn->prepare("UPDATE customers SET name = ?, google_review_link = ?, status = ? WHERE id = ?");
    $update_stmt->bind_param("sssi", $name, $google_review_link, $status, $customer_id);

    if ($update_stmt->execute()) {
        echo "Customer details updated successfully!";
    } else {
        echo "Error updating customer details.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Update Customer Details</title>
    <link rel="stylesheet" href="../styles.css">
</head>
<body>
    <div class="container">
        <h2>Update Customer Details</h2>

        <form method="POST" action="update_customer.php?customer_id=<?= $customer_id ?>">
            <label for="name">Customer Name:</label>
            <input type="text" name="name" value="<?= htmlspecialchars($customer['name']) ?>" required>

            <label for="google_review_link">Google Review Link:</label>
            <input type="text" name="google_review_link" value="<?= htmlspecialchars($customer['google_review_link']) ?>" required>

            <label for="status">Status:</label>
            <select name="status" required>
                <option value="active" <?= $customer['status'] == 'active' ? 'selected' : '' ?>>Active</option>
                <option value="inactive" <?= $customer['status'] == 'inactive' ? 'selected' : '' ?>>Inactive</option>
            </select>

            <button type="submit">Update Customer</button>
        </form>
    </div>
</body>
</html>
