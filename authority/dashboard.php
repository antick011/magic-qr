<?php
session_start();
include '../db.php';

if ($_SESSION['role'] !== 'authority') {
    header("Location: ../auth/login.php");
    exit();
}

$stmt = $conn->prepare("SELECT customers.id, customers.name, customers.google_review_link, customers.status, users.username 
                       FROM customers 
                       INNER JOIN users ON customers.user_id = users.id");
$stmt->execute();
$customers = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Authority Dashboard</title>
    <link rel="stylesheet" href="../styles.css">
</head>
<body>
    <div class="container">
        <h2>Authority Dashboard</h2>
        <h3>List of Customers</h3>

        <?php if ($customers->num_rows > 0) { ?>
            <table>
                <tr>
                    <th>Customer Name</th>
                    <th>Username</th>
                    <th>Google Review Link</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
                <?php while ($row = $customers->fetch_assoc()) { ?>
                    <tr>
                        <td><?= htmlspecialchars($row['name']) ?></td>
                        <td><?= htmlspecialchars($row['username']) ?></td>
                        <td><a href="<?= htmlspecialchars($row['google_review_link']) ?>" target="_blank">View Review</a></td>
                        <td><?= htmlspecialchars($row['status']) ?></td>
                        <td>
                            <a href="update_customer.php?customer_id=<?= $row['id'] ?>">Update</a>
                        </td>
                    </tr>
                <?php } ?>
            </table>
        <?php } else { ?>
            <p>No customers found.</p>
        <?php } ?>
    </div>
</body>
</html>

<?php
$stmt->close();
$conn->close();
?>
