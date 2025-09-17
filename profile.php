<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_token'])) {
    header("Location: login.php");
    exit();
}

// Include header
include 'header.php';
?>

<div class="container">
    <h2>User Profile</h2>
    <p>User profile information will be displayed here.</p>
</div>

<?php
// Include footer
include 'footer.php';
?>
