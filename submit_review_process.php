<?php
session_start();
include 'db.php';

// Check if the user is logged in
if (!isset($_SESSION['user_token'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    // Only allow POST requests
    header("Location: order_history.php");
    exit();
}

// Get user ID from token
function get_user_id_from_token($token, $secret = 'secret') {
    list($header, $payload, $signature) = explode('.', $token);
    $payload = json_decode(base64_decode($payload), true);
    return $payload['user_id'];
}

$user_id = get_user_id_from_token($_SESSION['user_token']);

// Validate and sanitize input
$order_id = filter_input(INPUT_POST, 'order_id', FILTER_VALIDATE_INT);
$service_id = filter_input(INPUT_POST, 'service_id', FILTER_VALIDATE_INT);
$rating = filter_input(INPUT_POST, 'rating', FILTER_VALIDATE_INT);
$review_text = filter_input(INPUT_POST, 'review_text', FILTER_SANITIZE_STRING);

if (!$order_id || !$service_id || !$rating || $rating < 1 || $rating > 5) {
    // Invalid input
    header("Location: order_history.php?error=invalid_input");
    exit();
}

// 1. Verify the order belongs to the user and is delivered
$order_sql = "SELECT id FROM orders WHERE id = ? AND user_id = ? AND status = 'Delivered'";
$order_stmt = $conn->prepare($order_sql);
$order_stmt->bind_param("ii", $order_id, $user_id);
$order_stmt->execute();
$order_result = $order_stmt->get_result();

if ($order_result->num_rows == 0) {
    // Order not found, not owned, or not delivered
    header("Location: order_history.php?error=invalid_order");
    exit();
}

// 2. Check if a review has already been submitted for this order
$review_sql = "SELECT id FROM reviews WHERE order_id = ?";
$review_stmt = $conn->prepare($review_sql);
$review_stmt->bind_param("i", $order_id);
$review_stmt->execute();
$review_result = $review_stmt->get_result();

if ($review_result->num_rows > 0) {
    // Review already exists
    header("Location: order_history.php?error=already_reviewed");
    exit();
}

// 3. Insert the new review
$insert_sql = "INSERT INTO reviews (order_id, user_id, service_id, rating, review_text) VALUES (?, ?, ?, ?, ?)";
$insert_stmt = $conn->prepare($insert_sql);
$insert_stmt->bind_param("iiiis", $order_id, $user_id, $service_id, $rating, $review_text);

if ($insert_stmt->execute()) {
    // Success
    header("Location: order_history.php?review=success");
    exit();
} else {
    // Database error
    header("Location: order_history.php?error=db_error");
    exit();
}

?>