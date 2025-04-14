<?php
include '../db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $name = $_POST['name'];
    $google_review_link = $_POST['google_review_link'];

    // Insert into users table
    $stmt = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
    $role = 'customer'; 
    $stmt->bind_param("sss", $username, $password, $role);

    if ($stmt->execute()) {
        $user_id = $stmt->insert_id;

        // Insert into customers table
        $stmt2 = $conn->prepare("INSERT INTO customers (user_id, name, google_review_link) VALUES (?, ?, ?)");
        $stmt2->bind_param("iss", $user_id, $name, $google_review_link);
        
        if ($stmt2->execute()) {
            echo "Customer registered successfully!";
        } else {
            echo "Error inserting customer details.";
        }
    } else {
        echo "Error registering user.";
    }
}
?>

<form method="POST" action="register.php">
    Username: <input type="text" name="username" required>
    Password: <input type="password" name="password" required>
    Name: <input type="text" name="name" required>
    Google Review Link: <input type="text" name="google_review_link" required>
    <button type="submit">Register</button>
</form>
