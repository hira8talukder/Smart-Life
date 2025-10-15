<?php
session_start();
include('db.php'); // Database connection

// Check if the admin is logged in
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php'); // Redirect to login if not logged in
    exit();
}

// Check if a user ID is provided in the URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    // Redirect back to the user list if no valid ID is provided
    header('Location: manage_users.php');
    exit();
}

$user_id = $_GET['id'];

// Fetch the user's details using a prepared statement for security
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

// If no user found with that ID
if (!$user) {
    header('Location: manage_users.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | View User</title>
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
        
        .user-details {
            display: grid;
            grid-template-columns: 1fr;
            gap: 1.5rem;
        }
        
        .user-details h3 {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 1.5rem;
        }
        
        .detail-item {
            display: flex;
            flex-direction: column;
            gap: 0.25rem;
        }
        
        .detail-item strong {
            font-size: 0.9rem;
            font-weight: 600;
            color: var(--secondary-dark);
        }
        
        .detail-item span {
            font-size: 1rem;
            color: var(--primary-dark);
        }
        
        .action-buttons {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
        }
        
        .action-buttons .btn {
            padding: 0.75rem 1.5rem;
            font-weight: 500;
            border-radius: 0.75rem;
            text-decoration: none;
            transition: transform 0.3s, box-shadow 0.3s;
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
        
        /* Responsive adjustments */
        @media (min-width: 768px) {
            .user-details {
                grid-template-columns: repeat(2, 1fr);
            }
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
                <h1>User Details</h1>
                <a href="logout.php" class="logout-link">Logout</a>
            </div>

            <div class="content-card">
                <h3><?php echo htmlspecialchars($user['full_name']); ?></h3>
                
                <div class="user-details">
                    <div class="detail-item">
                        <strong>User ID:</strong>
                        <span><?php echo htmlspecialchars($user['id']); ?></span>
                    </div>
                    <div class="detail-item">
                        <strong>Email:</strong>
                        <span><?php echo htmlspecialchars($user['email']); ?></span>
                    </div>
                    <div class="detail-item">
                        <strong>Member Since:</strong>
                        <span><?php echo htmlspecialchars(date("M d, Y", strtotime($user['created_at']))); ?></span>
                    </div>
                    <div class="detail-item">
                        <strong>Age:</strong>
                        <span><?php echo htmlspecialchars($user['age']); ?></span>
                    </div>
                    <div class="detail-item">
                        <strong>Gender:</strong>
                        <span><?php echo htmlspecialchars($user['gender']); ?></span>
                    </div>
                    <div class="detail-item">
                        <strong>Address:</strong>
                        <span><?php echo htmlspecialchars($user['address']); ?></span>
                    </div>
                    <div class="detail-item">
                        <strong>Phone:</strong>
                        <span><?php echo htmlspecialchars($user['phone']); ?></span>
                    </div>
                </div>
                
                <div class="action-buttons">
                    <a href="manage_users.php" class="btn btn-secondary">Back to Users</a>
                    <a href="edit_user.php?id=<?php echo htmlspecialchars($user['id']); ?>" class="btn btn-primary">Edit User</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
