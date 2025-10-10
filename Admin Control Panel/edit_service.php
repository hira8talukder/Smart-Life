<?php
session_start();
include('db.php');

// Check if the admin is logged in
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit();
}

// Check if ID is provided
if (!isset($_GET['id'])) {
    header('Location: manage_services.php');
    exit();
}

$service_id = $_GET['id'];

// Fetch the service details
$stmt = $conn->prepare("SELECT * FROM services WHERE id = ?");
$stmt->bind_param("i", $service_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    header('Location: manage_services.php');
    exit();
}
$service = $result->fetch_assoc();
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | Edit Service</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="main-content" style="padding: 2.5rem;">
        <div class="header">
            <h1>Edit Service</h1>
            <a href="manage_services.php" class="logout-link" style="background-color: #6c757d;">Back to Services</a>
        </div>

        <div class="content-card">
            <form action="update_service_process.php" method="POST">
                <input type="hidden" name="service_id" value="<?php echo htmlspecialchars($service['id']); ?>">
                <div class="form-group">
                    <label for="service_name">Service Name</label>
                    <input type="text" id="service_name" name="service_name" value="<?php echo htmlspecialchars($service['name']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="service_description">Description</label>
                    <textarea id="service_description" name="service_description" rows="3" required><?php echo htmlspecialchars($service['description']); ?></textarea>
                </div>
                <div class="form-group">
                    <label for="service_price">Price (BDT)</label>
                    <input type="number" step="0.01" id="service_price" name="service_price" value="<?php echo htmlspecialchars($service['price']); ?>" required>
                </div>
                <div class="form-actions">
                    <button type="submit" name="update_service" class="btn btn-primary">Update Service</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
