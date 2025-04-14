<?php
session_start();
include '../db.php';

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Hardcoded admin credentials
    $admin_username = "admin";
    $admin_password = "password";  // In a production environment, you would hash passwords!

    // Check if the provided credentials match the admin credentials
    if ($username === $admin_username && $password === $admin_password) {
        // Admin login, set session variables for admin
        $_SESSION['user_id'] = 1;  // Hardcoded user_id for admin
        $_SESSION['role'] = 'authority';  // Role is 'authority' for the admin

        // Redirect to the authority dashboard
        header("Location: /magicqr/authority/dashboard.php");
        exit();
    } else {
        // Check for customer credentials in the database
        $stmt = $conn->prepare("SELECT id, password, role FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($user_id, $hashed_password, $role);
        $stmt->fetch();

        // Check if the credentials match
        if ($stmt->num_rows > 0 && password_verify($password, $hashed_password)) {
            // Set session variables for customer
            $_SESSION['user_id'] = $user_id;
            $_SESSION['role'] = $role;  // Role will be 'customer'

            // Redirect to customer dashboard if the user is a customer
            if ($role === 'customer') {
                header("Location: /magicqr/customer/dashboard.php");
                exit();
            }
        } else {
            // If login fails for both admin and customer
            echo "Invalid username or password.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="../styles.css">
</head>
<body>
    <div class="container">
        <h2>Login</h2>
        
        <form method="POST" action="login.php">
            <label for="username">Username:</label>
            <input type="text" name="username" required>

            <label for="password">Password:</label>
            <input type="password" name="password" required>

            <button type="submit">Login</button>
            
        </form>
       
        <form action="https://localhost/magicqr/auth/register.php">
            <button type="submit">Register</button>
        </form>
            
    </div>
</body>
</html>
