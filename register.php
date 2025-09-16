<?php
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $conn->real_escape_string($_POST['username']);
    $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
    $phone = preg_match('/^[0-9]{10,15}$/', $_POST['phone']) ? $_POST['phone'] : false;
    $address = $conn->real_escape_string($_POST['address']);

    if ($email && $phone) {
        $sql = "INSERT INTO customers (username, email, phone, address)
                VALUES ('$username', '$email', '$phone', '$address')";

        if ($conn->query($sql) === TRUE) {
            echo "<h3>Registration successful!</h3>";
        } else {
            echo "Error: " . $conn->error;
        }
    } else {
        echo "<h3>Invalid email or phone number.</h3>";
    }

    $conn->close();
} else {
    echo "<h3>Invalid request.</h3>";
}
?>
<?php include 'header.php'; ?>
<?php include 'db.php'; ?>

<div class="form-container">
    <h2>Customer Registration</h2>

    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $username = $conn->real_escape_string($_POST['username']);
        $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
        $phone = preg_match('/^[0-9]{10,15}$/', $_POST['phone']) ? $_POST['phone'] : false;
        $address = $conn->real_escape_string($_POST['address']);

        if ($email && $phone) {
            $sql = "INSERT INTO customers (username, email, phone, address)
                    VALUES ('$username', '$email', '$phone', '$address')";

            if ($conn->query($sql) === TRUE) {
                echo "<p class='success'>Registration successful!</p>";
            } else {
                echo "<p class='error'>Error: " . $conn->error . "</p>";
            }
        } else {
            echo "<p class='error'>Invalid email or phone number.</p>";
        }
    }
    ?>

    <form action="register.php" method=":</label>
        <input type="text" name="username" required>

        <label>Email:</label>
        <input type="email" name="email" required>

        <label>Phone Number:</label>
        <input type="tel" name="phone" pattern="[0-9]{10,15}" required>

        <label>Address:</label>
        <textarea name="address" required></textarea>

        <button type="submit">Register</button>
    </form>
</div>

<?php include 'footer.php'; ?>
