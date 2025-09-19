<?php
session_start();
include('db.php'); // Database connection

// Check if the admin is logged in
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php'); // Redirect to login if not logged in
    exit();
}

// Check if an ID is passed to the page
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Delete the user from the database
    $delete_query = "DELETE FROM admin_users WHERE id = '$id'";
    if (mysqli_query($conn, $delete_query)) {
        header('Location: manage_users.php'); // Redirect to manage_users.php after successful deletion
        exit();
    } else {
        $error = "Failed to delete user.";
    }
} else {
    header('Location: manage_users.php'); // Redirect to manage_users.php if no ID is provided
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete User</title>
</head>
<body>
    <h2>Are you sure you want to delete this user?</h2>
    <p><a href="delete_user.php?id=<?php echo $_GET['id']; ?>">Yes, delete user</a> | <a href="manage_users.php">Cancel</a></p>

    <?php if (isset($error)) { echo "<p>$error</p>"; } ?>
</body>
</html>
