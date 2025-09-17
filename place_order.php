<?php
session_start();
include 'db.php';

// Check if the user is logged in
if (!isset($_SESSION['user_token'])) {
    die("You must be logged in to place an order.");
}

// Function to get user ID from token
function get_user_id_from_token($token, $secret = 'secret') {
    list($header, $payload, $signature) = explode('.', $token);
    $payload = json_decode(base64_decode($payload), true);
    return $payload['user_id'];
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = get_user_id_from_token($_SESSION['user_token']);
    $service_id = $_POST['service_id'];

    // Insert into orders table
    $sql = "INSERT INTO orders (user_id, service_id) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $user_id, $service_id);
    
    if ($stmt->execute()) {
        $order_id = $stmt->insert_id;

        $details = $_POST;
        unset($details['service_id']);

        // Handle file upload
        if (isset($_FILES['prescription']) && $_FILES['prescription']['error'] == 0) {
            $target_dir = "uploads/";
            if (!is_dir($target_dir)) {
                mkdir($target_dir, 0777, true);
            }
            $target_file = $target_dir . basename($_FILES["prescription"]["name"]);
            if (move_uploaded_file($_FILES["prescription"]["tmp_name"], $target_file)) {
                $details['prescription_path'] = $target_file;
            }
        }

        // Insert into order_details table
        $details_sql = "INSERT INTO order_details (order_id, details) VALUES (?, ?)";
        $details_stmt = $conn->prepare($details_sql);
        $json_details = json_encode($details);
        $details_stmt->bind_param("is", $order_id, $json_details);
        $details_stmt->execute();

        // Redirect to order history with success message
        header("Location: order_history.php?order=success");
        exit();

    } else {
        die("Error placing order: " . $stmt->error);
    }
}
?>
