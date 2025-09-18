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

$action = $_GET['action'] ?? 'main';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if ($action == 'update_profile') {
        $full_name = $_POST['full_name'];
        $email = $_POST['email'];
        $address = $_POST['address'];

        // Update user data
        $sql = "UPDATE users SET full_name = ?, email = ?, address = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssi", $full_name, $email, $address, $user_id);

        if ($stmt->execute()) {
            header("Location: profile.php?status=success");
        } else {
            header("Location: profile.php?status=error");
        }
        $stmt->close();
        $conn->close();
        exit();
    } elseif ($action == 'change_password') {
        $current_password = $_POST['current_password'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];

        // Fetch user's current password
        $sql = "SELECT password FROM users WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if (password_verify($current_password, $user['password'])) {
            if ($new_password == $confirm_password) {
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                $update_sql = "UPDATE users SET password = ? WHERE id = ?";
                $update_stmt = $conn->prepare($update_sql);
                $update_stmt->bind_param("si", $hashed_password, $user_id);

                if ($update_stmt->execute()) {
                    header("Location: account_settings.php?action=main&status=password_success");
                } else {
                    header("Location: account_settings.php?action=main&status=password_error");
                }
            } else {
                header("Location: account_settings.php?action=main&status=password_mismatch");
            }
        } else {
            header("Location: account_settings.php?action=main&status=current_password_error");
        }
        exit();
    } elseif ($action == 'change_address') {
        $address = $_POST['address'];

        // Update user address
        $sql = "UPDATE users SET address = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $address, $user_id);

        if ($stmt->execute()) {
            header("Location: account_settings.php?action=main&status=address_success");
        } else {
            header("Location: account_settings.php?action=main&status=address_error");
        }
        exit();
    }
}

// Include header
include 'header.php';

if ($action == 'edit_profile') {
    // Fetch user data
    $sql = "SELECT * FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
?>
    <div class="container">
        <h2>Edit Profile</h2>
        <form action="account_settings.php?action=update_profile" method="POST">
            <div class="mb-3">
                <label for="full_name" class="form-label">Full Name</label>
                <input type="text" class="form-control" id="full_name" name="full_name" value="<?php echo htmlspecialchars($user['full_name']); ?>">
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>">
            </div>
            <div class="mb-3">
                <label for="address" class="form-label">Address</label>
                <textarea class="form-control" id="address" name="address" rows="3"><?php echo htmlspecialchars($user['address']); ?></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Update Profile</button>
        </form>
    </div>
<?php
} else { // main
?>
    <div class="container">
        <h2>Account Settings</h2>
        <?php
        if (isset($_GET['status'])) {
            if ($_GET['status'] == 'password_success') {
                echo '<div class="alert alert-success">Password updated successfully!</div>';
            } elseif ($_GET['status'] == 'password_error') {
                echo '<div class="alert alert-danger">Error updating password.</div>';
            } elseif ($_GET['status'] == 'password_mismatch') {
                echo '<div class="alert alert-danger">New passwords do not match.</div>';
            } elseif ($_GET['status'] == 'current_password_error') {
                echo '<div class="alert alert-danger">Incorrect current password.</div>';
            } elseif ($_GET['status'] == 'address_success') {
                echo '<div class="alert alert-success">Address updated successfully!</div>';
            } elseif ($_GET['status'] == 'address_error') {
                echo '<div class="alert alert-danger">Error updating address.</div>';
            }
        }
        ?>
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title">Change Password</h5>
                <form action="account_settings.php?action=change_password" method="POST">
                    <div class="mb-3">
                        <label for="current_password" class="form-label">Current Password</label>
                        <input type="password" class="form-control" id="current_password" name="current_password" required>
                    </div>
                    <div class="mb-3">
                        <label for="new_password" class="form-label">New Password</label>
                        <input type="password" class="form-control" id="new_password" name="new_password" required>
                    </div>
                    <div class="mb-3">
                        <label for="confirm_password" class="form-label">Confirm New Password</label>
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Change Password</button>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Change Address</h5>
                <form action="account_settings.php?action=change_address" method="POST">
                    <div class="mb-3">
                        <label for="address" class="form-label">New Address</label>
                        <textarea class="form-control" id="address" name="address" rows="3" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Update Address</button>
                </form>
            </div>
        </div>
    </div>
<?php
}

// Include footer
include 'footer.php';
?>
