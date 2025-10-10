<?php
session_start();
include('db.php');

// Check if the admin is logged in
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit();
}

// Check if the form was submitted
if (isset($_POST['update_service'])) {
    $service_id = $_POST['service_id'];
    $service_name = $_POST['service_name'];
    $service_description = $_POST['service_description'];
    $service_price = $_POST['service_price'];

    // Validate input
    if (empty($service_id) || empty($service_name) || empty($service_description) || !is_numeric($service_price)) {
        header('Location: manage_services.php?message=error');
        exit();
    }

    // Securely update the service
    $stmt = $conn->prepare("UPDATE services SET name = ?, description = ?, price = ? WHERE id = ?");
    $stmt->bind_param("ssdi", $service_name, $service_description, $service_price, $service_id);

    if ($stmt->execute()) {
        header('Location: manage_services.php?message=success');
    } else {
        header('Location: manage_services.php?message=error');
    }

    $stmt->close();
    $conn->close();
} else {
    // Redirect if accessed directly
    header('Location: manage_services.php');
    exit();
}
?>