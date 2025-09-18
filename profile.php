<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_token'])) {
    header("Location: login.php");
    exit();
}

// Include database connection
include 'db.php';

// Get user ID from token
$token_parts = explode('.', $_SESSION['user_token']);
$payload = json_decode(base64_decode($token_parts[1]), true);
$user_id = $payload['user_id'];

// Fetch user data
$sql = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Include header
include 'header.php';
?>

<div class="container">
    <h2>User Profile</h2>
    <?php
    if (isset($_GET['status'])) {
        if ($_GET['status'] == 'success') {
            echo '<div class="alert alert-success">Profile updated successfully!</div>';
        } else {
            echo '<div class="alert alert-danger">Error updating profile.</div>';
        }
    }
    ?>
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Name: <?php echo htmlspecialchars($user['full_name']); ?></h5>
            <p class="card-text">Email: <?php echo htmlspecialchars($user['email']); ?></p>
            <p class="card-text">Phone: <?php echo htmlspecialchars($user['phone']); ?></p>
            <p class="card-text">Age: <?php echo htmlspecialchars($user['age']); ?></p>
            <p class="card-text">Gender: <?php echo htmlspecialchars($user['gender']); ?></p>
            <p class="card-text">Address: <?php echo htmlspecialchars($user['address']); ?></p>
            <a href="account_settings.php?action=edit_profile" class="btn btn-primary">Edit Profile</a>
        </div>
    </div>
</div>

<?php
// Include footer
include 'footer.php';
?>
