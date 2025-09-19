<?php
session_start();
include('db.php'); // Database connection

if (isset($_POST['register'])) {
    $email = $_POST['email'];
    $password = $_POST['password']; // Plain-text password
    $name = $_POST['name']; // Admin name

    // Regular expression to check if the password is strong
    $password_pattern = "/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/";

    // Check if the password matches the pattern
    if (!preg_match($password_pattern, $password)) {
        $error = "Password must be at least 8 characters long, and include at least one uppercase letter, one lowercase letter, one number, and one special character.";
    } else {
        // Insert into the database (without password hashing)
        $query = "INSERT INTO admin_users (email, password, name) VALUES ('$email', '$password', '$name')";
        if (mysqli_query($conn, $query)) {
            $_SESSION['message'] = "Registration successful! You can now log in.";
            header('Location: login.php'); // Redirect to login page
        } else {
            $error = "Registration failed!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Admin</title>
</head>
<body>
    <h2>Register Admin</h2>
    <form method="POST">
        <label for="name">Name:</label>
        <input type="text" name="name" required><br>

        <label for="email">Email:</label>
        <input type="email" name="email" required><br>

        <label for="password">Password:</label>
        <input type="password" name="password" required><br>

        <button type="submit" name="register">Register</button>
    </form>

    <?php if (isset($error)) { echo "<p>$error</p>"; } ?>
    <p>Already have an account? <a href="login.php">Login here</a></p>
</body>
</html>
