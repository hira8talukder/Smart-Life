<?php
session_start();
include 'db.php';

// Function to generate a simple JWT
function generate_jwt($payload, $secret = 'secret') {
    // Header
    $header = json_encode(['typ' => 'JWT', 'alg' => 'HS256']);
    $header = base64_encode($header);

    // Payload
    $payload = json_encode($payload);
    $payload = base64_encode($payload);

    // Signature
    $signature = hash_hmac('sha256', "$header.$payload", $secret, true);
    $signature = base64_encode($signature);

    return "$header.$payload.$signature";
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            // Generate token
            $payload = ['user_id' => $user['id'], 'email' => $user['email']];
            $token = generate_jwt($payload);

            // Update user's token in the database
            $update_sql = "UPDATE users SET token = ? WHERE id = ?";
            $update_stmt = $conn->prepare($update_sql);
            $update_stmt->bind_param("si", $token, $user['id']);
            $update_stmt->execute();

            // Set session and redirect to home page
            $_SESSION['user_token'] = $token;
            header("Location: home.php");
            exit();
        } else {
            echo "Invalid password";
        }
    } else {
        echo "No user found with this email";
    }
}
?>
