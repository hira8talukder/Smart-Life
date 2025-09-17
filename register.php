<?php include 'header.php'; ?>
<?php include 'db.php'; ?>

<div class="form-wrapper">
    <div class="form-card">
        <h2>Create Your Smart Life Account</h2>

        <?php
        $username = $email = $phone = $address = $password = $confirm_password = "";
        $errors = [];

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $username = trim($_POST['username']);
            $email = trim($_POST['email']);
            $phone = trim($_POST['phone']);
            $address = trim($_POST['address']);
            $password = $_POST['password'];
            $confirm_password = $_POST['confirm_password'];

            if (empty($username)) {
                $errors[] = "Username is required.";
            }

            if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = "A valid email is required.";
            }

            if (empty($phone) || !preg_match('/^[0-9]{10,15}$/', $phone)) {
                $errors[] = "A valid phone number is required.";
            }
            
            if (empty($address)) {
                $errors[] = "Address is required.";
            }

            if (empty($password)) {
                $errors[] = "Password is required.";
            } elseif (strlen($password) < 8) {
                $errors[] = "Password must be at least 8 characters long.";
            }

            if ($password !== $confirm_password) {
                $errors[] = "Passwords do not match.";
            }

            if (empty($errors)) {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                $stmt = $conn->prepare("INSERT INTO customers (username, email, phone, address, password) VALUES (?, ?, ?, ?, ?)");
                $stmt->bind_param("sssss", $username, $email, $phone, $address, $hashed_password);

                if ($stmt->execute()) {
                    echo "<p class='success'>🎉 Registration successful! Welcome to the Smart Life community.</p>";
                    // Clear form fields after successful registration
                    $username = $email = $phone = $address = "";
                } else {
                    echo "<p class='error'>❌ Error: " . $stmt->error . "</p>";
                }
                $stmt->close();
            } else {
                foreach ($errors as $error) {
                    echo "<p class='error'>⚠️ " . $error . "</p>";
                }
            }
        }
        ?>

        <form action="register.php" method="post" novalidate>
            <div class="input-group">
                <input type="text" name="username" placeholder="Username" value="<?php echo htmlspecialchars($username); ?>" required>
                <i class="fas fa-user"></i>
            </div>
            <div class="input-group">
                <input type="email" name="email" placeholder="Email" value="<?php echo htmlspecialchars($email); ?>" required>
                <i class="fas fa-envelope"></i>
            </div>
            <div class="input-group">
                <input type="tel" name="phone" placeholder="Phone Number" value="<?php echo htmlspecialchars($phone); ?>" pattern="[0-9]{10,15}" required>
                <i class="fas fa-phone"></i>
            </div>
            <div class="input-group">
                <textarea name="address" placeholder="Address" required><?php echo htmlspecialchars($address); ?></textarea>
                <i class="fas fa-map-marker-alt"></i>
            </div>
            <div class="input-group">
                <input type="password" name="password" placeholder="Password" required>
                <i class="fas fa-lock"></i>
            </div>
            <div class="input-group">
                <input type="password" name="confirm_password" placeholder="Confirm Password" required>
                <i class="fas fa-lock"></i>
            </div>
            <button type="submit">Register</button>
        </form>
        <p class="login-link">Already have an account? <a href="login.php">Login here</a></p>
    </div>
</div>

<?php include 'footer.php'; ?>