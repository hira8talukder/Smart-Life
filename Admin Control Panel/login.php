<?php
session_start();
include('db.php'); // Database connection

if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password']; // Plain-text password

    // Query to fetch the admin user based on email
    $query = "SELECT * FROM admin_users WHERE email = '$email'";
    $result = mysqli_query($conn, $query);
    $user = mysqli_fetch_assoc($result);

    if ($user) {
        // Compare the entered password with the stored password (plain text)
        if ($password === $user['password']) {
            // Password is correct, set session variables
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_id'] = $user['id'];
            $_SESSION['admin_email'] = $user['email'];
            $_SESSION['admin_name'] = $user['name']; // Store admin name in session

            // Redirect to admin dashboard
            header('Location: dashboard.php');
            exit();
        } else {
            $error = "Invalid password!";
        }
    } else {
        $error = "No user found with that email!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
</head>
<body>
    <h2>Admin Login</h2>
    <form method="POST">
        <label for="email">Email:</label>
        <input type="email" name="email" required><br>

        <label for="password">Password:</label>
        <input type="password" name="password" required><br>

        <button type="submit" name="login">Login</button>
    </form>

    <?php if (isset($error)) { echo "<p>$error</p>"; } ?>
    <p>Don't have an account? <a href="register.php">Register here</a></p>
</body>
</html>
