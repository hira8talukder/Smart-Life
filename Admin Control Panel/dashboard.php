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
    <title>Admin Dashboard</title>
</head>
<body>
    <h2>Welcome, <?php echo $_SESSION['admin_name']; ?></h2> <!-- Display admin's name -->
    <p>This is your admin dashboard.</p>
    <ul>
        <li><a href="manage_users.php">Manage Users</a></li>
        <li><a href="manage_services.php">Manage Services</a></li>
        <li><a href="manage_transactions.php">Manage Transactions</a></li>
        <li><a href="settings.php">Settings</a></li>
    </ul>
    <a href="logout.php">Logout</a>
</body>
</html>
