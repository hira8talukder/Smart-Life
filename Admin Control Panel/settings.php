<?php
session_start();

// Check if the admin is logged in
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php'); // Redirect to login if not logged in
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings</title>
</head>
<body>
    <h2>Admin Settings</h2>
    <form method="POST" action="update_settings.php">
        <label for="site_name">Site Name:</label>
        <input type="text" name="site_name" required><br>

        <label for="site_email">Site Email:</label>
        <input type="email" name="site_email" required><br>

        <button type="submit">Save Settings</button>
    </form>
</body>
</html>
