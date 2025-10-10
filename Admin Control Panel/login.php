<?php
session_start();
include('db.php'); // Database connection

$error = '';

if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Use a prepared statement to prevent SQL injection
    $stmt = $conn->prepare("SELECT id, name, password FROM admin_users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();

    if ($user) {
        // Compare the entered password with the stored pasword
        if ($password === $user['password']) {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_id'] = $user['id'];
            $_SESSION['admin_email'] = $email;
            $_SESSION['admin_name'] = $user['name'];

            header('Location: dashboard.php');
            exit();
        } else {
            $error = "Invalid password!";
        }
    } else {
        $error = "No user found with that email!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap');

        :root {
            --primary-blue: #0A72B8;
            --primary-dark: #1F2937;
            --background-light: #F8F9FA;
            --input-bg: #EAEFF4;
            --border-color: #D1D5DB;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #E0EAF1, #B6CCDA);
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 1.5rem;
            color: var(--primary-dark);
        }

        .login-card {
            width: 100%;
            max-width: 28rem;
            background-color: white;
            border-radius: 1rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            padding: 3rem 2.5rem;
            display: flex;
            flex-direction: column;
            gap: 2rem;
            animation: fadeIn 0.8s ease-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .login-card h2 {
            font-size: 2rem;
            font-weight: 600;
            text-align: center;
            color: var(--primary-dark);
            letter-spacing: -0.05em;
        }
        
        #login-form {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }

        .form-group label {
            display: block;
            font-size: 0.9rem;
            font-weight: 500;
            color: var(--primary-dark);
            margin-bottom: 0.5rem;
        }

        .form-group input {
            display: block;
            width: 93%;
            padding: 0.8rem 1rem;
            background-color: var(--input-bg);
            border: 1px solid transparent;
            border-radius: 0.75rem;
            font-size: 1rem;
            transition: border-color 0.3s, box-shadow 0.3s, background-color 0.3s;
        }

        .form-group input:focus {
            outline: none;
            background-color: white;
            border-color: var(--primary-blue);
            box-shadow: 0 0 0 3px rgba(10, 114, 184, 0.2);
        }

        .login-button {
            width: 100%;
            padding: 0.9rem 1.5rem;
            background-color: var(--primary-blue);
            color: white;
            font-weight: 600;
            border-radius: 0.75rem;
            box-shadow: 0 4px 15px rgba(10, 114, 184, 0.3);
            transition: background-color 0.3s, transform 0.3s, box-shadow 0.3s;
            border: none;
            cursor: pointer;
            font-size: 1.1rem;
        }

        .login-button:hover {
            background-color: #085A8A;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(10, 114, 184, 0.4);
        }

        .message-box {
            text-align: center;
            font-size: 0.9rem;
            font-weight: 500;
            padding: 1rem;
            border-radius: 0.75rem;
            transition: all 0.4s ease-in-out;
            transform-origin: top;
        }

        .error {
            background-color: #FEE2E2;
            color: #B91C1C;
        }
        
        .link-text {
            text-align: center;
            color: #6B7280;
            font-size: 0.9rem;
        }

        .link-text a {
            color: var(--primary-blue);
            font-weight: 600;
            text-decoration: none;
            transition: color 0.3s;
        }

        .link-text a:hover {
            color: #085A8A;
            text-decoration: underline;
        }
    </style>
</head>
<body>

    <!-- Login Card -->
    <div class="login-card">
        <h2>Admin Login</h2>
        <form id="login-form" method="POST">
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" name="email" id="email" required placeholder="Enter your email">
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" name="password" id="password" required placeholder="Enter your password">
            </div>

            <button type="submit" name="login" class="login-button">
                Login
            </button>
        </form>

        <?php if (!empty($error)) { ?>
            <div class="message-box error"><?php echo $error; ?></div>
        <?php } ?>

        <p class="link-text">
            Don't have an account? <a href="register.php">Register here</a>
        </p>
    </div>

</body>
</html>
