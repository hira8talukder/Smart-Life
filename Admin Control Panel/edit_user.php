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

    // Fetch user data by ID
    $query = "SELECT * FROM admin_users WHERE id = '$id'";
    $result = mysqli_query($conn, $query);
    $user = mysqli_fetch_assoc($result);
} else {
    header('Location: manage_users.php'); // Redirect to manage_users.php if no ID is provided
    exit();
}

if (isset($_POST['update'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];

    // Update user details in the database
    $update_query = "UPDATE admin_users SET name = '$name', email = '$email' WHERE id = '$id'";
    if (mysqli_query($conn, $update_query)) {
        header('Location: manage_users.php'); // Redirect to manage_users.php after successful update
        exit();
    } else {
        $error = "Failed to update user information.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User</title>
</head>
<body>
    <h2>Edit User</h2>
    <form method="POST">
        <label for="name">Name:</label>
        <input type="text" name="name" value="<?php echo $user['name']; ?>" required><br>

        <label for="email">Email:</label>
        <input type="email" name="email" value="<?php echo $user['email']; ?>" required><br>

        <button type="submit" name="update">Update</button>
    </form>

    <?php if (isset($error)) { echo "<p>$error</p>"; } ?>
    <a href="manage_users.php">Back to Manage Users</a>
</body>
</html>
