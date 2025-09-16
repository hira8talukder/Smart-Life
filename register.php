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
