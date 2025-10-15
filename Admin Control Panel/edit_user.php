<?php
session_start();
include('db.php');

// Check if the admin is logged in
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit();
}

$message = '';
$user = null;

// Get the user ID from the URL
if (isset($_GET['id'])) {
    $user_id = $_GET['id'];
    
    // Fetch user data securely
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
    } else {
        $message = 'User not found.';
    }
    $stmt->close();

} else if (isset($_POST['update_user'])) {
    // Handle form submission
    $user_id = $_POST['user_id'];
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $age = $_POST['age'];
    $gender = $_POST['gender'];
    $address = $_POST['address'];
    $phone = $_POST['phone'];
    
    // Update user data securely
    $stmt = $conn->prepare("UPDATE users SET full_name = ?, email = ?, age = ?, gender = ?, address = ?, phone = ? WHERE id = ?");
    $stmt->bind_param("ssisssi", $full_name, $email, $age, $gender, $address, $phone, $user_id);
    
    if ($stmt->execute()) {
        $message = 'success';
    } else {
        $message = 'error';
    }
    $stmt->close();
    
    // Re-fetch updated user data
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();
    
} else {
    $message = 'Invalid request.';
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | Edit User</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap');

        :root {
            --primary-blue: #0A72B8;
            --primary-dark: #1F2937;
            --secondary-dark: #4B5563;
            --background-light: #F8F9FA;
            --card-bg: #FFFFFF;
            --link-hover-bg: #F3F4F6;
            --link-active-bg: #E5E7EB;
            --input-bg: #EAEFF4;
            --border-color: #D1D5DB;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: var(--background-light);
            color: var(--primary-dark);
            line-height: 1.6;
            margin: 0;
            padding: 0;
        }

        .dashboard-container {
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            width: 16rem;
            background-color: var(--card-bg);
            padding: 2.5rem 1.5rem;
            display: flex;
            flex-direction: column;
            border-right: 1px solid #E5E7EB;
        }

        .sidebar h2 {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 2rem;
            color: var(--primary-blue);
        }
         .logo {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 2rem;
            color: orange; 
            text-decoration: none; 
            display: block; 
            transition: color 0.3s;
        }

        .logo:hover {
            color: var(--secondary-dark);
        }
        

        .nav-list {
            list-style: none;
            padding: 0;
            margin: 0;
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .nav-list a {
            display: flex;
            align-items: center;
            padding: 0.8rem 1rem;
            text-decoration: none;
            color: var(--secondary-dark);
            font-weight: 500;
            border-radius: 0.75rem;
            transition: background-color 0.3s, color 0.3s, transform 0.3s, box-shadow 0.3s;
        }

        .nav-list a:hover {
            background-color: rgba(10, 114, 184, 0.1);
            color: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
        }

        .nav-list a.active {
            background-color: var(--primary-blue);
            color: white;
            box-shadow: 0 4px 15px rgba(10, 114, 184, 0.3);
        }
        
        .main-content {
            flex-grow: 1;
            padding: 2.5rem;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }

        .header h1 {
            font-size: 2rem;
            font-weight: 700;
            color: var(--primary-dark);
        }
        
        .logout-link {
            padding: 0.75rem 1.5rem;
            background-color: var(--primary-blue);
            color: white;
            font-weight: 500;
            border-radius: 0.75rem;
            text-decoration: none;
            transition: background-color 0.3s, transform 0.3s, box-shadow 0.3s;
            box-shadow: 0 4px 15px rgba(10, 114, 184, 0.2);
        }

        .logout-link:hover {
            background-color: #085A8A;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(10, 114, 184, 0.3);
        }

        .content-card {
            background-color: var(--card-bg);
            border-radius: 1rem;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            padding: 2rem;
        }
        
        .message-box {
            text-align: center;
            font-size: 0.9rem;
            font-weight: 500;
            padding: 1rem;
            border-radius: 0.75rem;
            margin-bottom: 1.5rem;
        }

        .success {
            background-color: #D1FAE5;
            color: #065F46;
        }

        .error {
            background-color: #FEE2E2;
            color: #B91C1C;
        }

        /* Form styling */
        .form-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 1.5rem;
        }

        @media (min-width: 768px) {
            .form-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }
        
        .form-group {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .form-group label {
            font-weight: 500;
            color: var(--secondary-dark);
        }
        
        .form-group input, .form-group select {
            width: 100%;
            padding: 0.8rem 1rem;
            background-color: var(--input-bg);
            border: 1px solid transparent;
            border-radius: 0.75rem;
            font-size: 1rem;
            transition: border-color 0.3s, box-shadow 0.3s;
            box-sizing: border-box;
        }
        
        .form-group input:focus, .form-group select:focus {
            outline: none;
            background-color: var(--card-bg);
            border-color: var(--primary-blue);
            box-shadow: 0 0 0 3px rgba(10, 114, 184, 0.2);
        }

        .form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 1rem;
            margin-top: 2rem;
        }
        
        .btn {
            padding: 0.75rem 1.5rem;
            font-weight: 500;
            border-radius: 0.75rem;
            text-decoration: none;
            transition: transform 0.3s, box-shadow 0.3s;
            cursor: pointer;
            border: none;
        }
        
        .btn-primary {
            background-color: var(--primary-blue);
            color: white;
            box-shadow: 0 4px 15px rgba(10, 114, 184, 0.2);
        }

        .btn-primary:hover {
            background-color: #085A8A;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(10, 114, 184, 0.3);
        }
        
        .btn-secondary {
            background-color: #E5E7EB;
            color: var(--primary-dark);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        }

        .btn-secondary:hover {
            background-color: #D1D5DB;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.05);
        }

        .back-link {
            text-decoration: none;
            color: var(--primary-dark);
            font-weight: 500;
            transition: color 0.3s;
        }
        
        .back-link:hover {
            color: var(--primary-blue);
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <!-- Sidebar Navigation -->
        <div class="sidebar">
            <a href="dashboard.php" class="logo">Smart-Life</a>
            <h2>Admin Panel</h2>
            <ul class="nav-list">
                <li><a href="manage_users.php" class="active">Manage Users</a></li>
                <li><a href="manage_services.php">Manage Services</a></li>
                <li><a href="manage_transactions.php">Manage Transactions</a></li>
                
            </ul>
        </div>

        <!-- Main Content Area -->
        <div class="main-content">
            <div class="header">
                <h1>Edit User: <?php echo htmlspecialchars($user['full_name'] ?? ''); ?></h1>
                <a href="logout.php" class="logout-link">Logout</a>
            </div>
            
            <?php if ($message): ?>
                <div class="message-box <?php echo $message; ?>">
                    <?php 
                        if ($message == 'success') {
                            echo "User updated successfully!";
                        } else {
                            echo "Error updating user. Please try again.";
                        }
                    ?>
                </div>
            <?php endif; ?>

            <?php if ($user): ?>
                <div class="content-card">
                    <form action="edit_user.php" method="POST">
                        <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($user['id']); ?>">
                        <div class="form-grid">
                            <div class="form-group">
                                <label for="full_name">Full Name</label>
                                <input type="text" id="full_name" name="full_name" value="<?php echo htmlspecialchars($user['full_name']); ?>" required>
                            </div>

                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="age">Age</label>
                                <input type="number" id="age" name="age" value="<?php echo htmlspecialchars($user['age']); ?>">
                            </div>
                            
                            <div class="form-group">
                                <label for="gender">Gender</label>
                                <select id="gender" name="gender">
                                    <option value="">Select...</option>
                                    <option value="male" <?php echo ($user['gender'] === 'male') ? 'selected' : ''; ?>>Male</option>
                                    <option value="female" <?php echo ($user['gender'] === 'female') ? 'selected' : ''; ?>>Female</option>
                                    <option value="other" <?php echo ($user['gender'] === 'other') ? 'selected' : ''; ?>>Other</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="address">Address</label>
                                <input type="text" id="address" name="address" value="<?php echo htmlspecialchars($user['address']); ?>">
                            </div>
                            
                            <div class="form-group">
                                <label for="phone">Phone</label>
                                <input type="tel" id="phone" name="phone" value="<?php echo htmlspecialchars($user['phone']); ?>">
                            </div>
                        </div>

                        <div class="form-actions">
                            <a href="view_user.php?id=<?php echo htmlspecialchars($user['id']); ?>" class="btn btn-secondary">Back to User</a>
                            <a href="manage_users.php" class="btn btn-secondary">Cancel</a>
                            <button type="submit" name="update_user" class="btn btn-primary">Save Changes</button>
                        </div>
                    </form>
                </div>
            <?php else: ?>
                <div class="content-card">
                    <p>No user data to display. Please return to the <a href="manage_users.php">Manage Users</a> page.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
