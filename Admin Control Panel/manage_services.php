<?php
session_start();
include('db.php'); // Database connection

// Check if the admin is logged in
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php'); // Redirect to login if not logged in
    exit();
}

// Fetch all services from the services table
$query = "SELECT * FROM services"; 
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Services</title>
</head>
<body>
    <h2>Manage Services</h2>
    <table border="1">
        <tr>
            <th>ID</th>
            <th>Service Name</th>
            <th>Price</th>
            <th>Actions</th>
        </tr>
        <?php while ($service = mysqli_fetch_assoc($result)) { ?>
            <tr>
                <td><?php echo $service['id']; ?></td>
                <td><?php echo $service['service_name']; ?></td>
                <td><?php echo $service['price']; ?></td>
                <td>
                    <a href="edit_service.php?id=<?php echo $service['id']; ?>">Edit</a> |
                    <a href="delete_service.php?id=<?php echo $service['id']; ?>">Delete</a>
                </td>
            </tr>
        <?php } ?>
    </table>
    <a href="dashboard.php">Back to Dashboard</a>
</body>
</html>
