<?php
session_start();
include('db.php'); // Database connection

// check if the admin is logged in
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php'); // Redirect to login if not logged in
    exit();
}

// handle user deletion if delete_id is provided
$message = '';
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    
    // Use a prepared statement for secure deletion
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $delete_id);
    
    if ($stmt->execute()) {
        $message = "User deleted successfully!";
    } else {
        $message = "Error deleting user: " . $stmt->error;
    }
    $stmt->close();
}

// fetch all users from the users table using a prepared statement for security
$query = "SELECT id, full_name, email, created_at FROM users ORDER BY created_at DESC";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users</title>
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
            color: #065F46;
            background-color: #D1FAE5;
        }

        /* Responsive table styles */
        .table-responsive {
            overflow-x: auto;
        }
        
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1.5rem;
        }

        .data-table th, .data-table td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid var(--link-hover-bg);
        }

        .data-table th {
            font-weight: 600;
            color: var(--secondary-dark);
            text-transform: uppercase;
            font-size: 0.8rem;
        }
        
        .data-table tr:hover {
            background-color: var(--link-hover-bg);
        }

        .data-table td a {
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s;
        }
        
        /* Button styles */
        .btn {
            display: inline-block;
            padding: 0.5rem 1rem;
            margin: 0.25rem;
            border-radius: 0.5rem;
            font-weight: 500;
            text-align: center;
            transition: transform 0.3s, box-shadow 0.3s, background-color 0.3s;
        }

        .btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        
        .btn-view {
            background-color: var(--primary-blue);
            color: white;
        }

        .btn-view:hover {
            background-color: #085A8A;
        }
        
        .btn-danger {
            background-color: #EF4444;
            color: white;
        }
        
        .btn-danger:hover {
            background-color: #DC2626;
        }

        /* Mobile-first approach for tables */
        @media (max-width: 768px) {
            .data-table thead {
                display: none;
            }

            .data-table, .data-table tbody, .data-table tr, .data-table td {
                display: block;
                width: 100%;
            }

            .data-table tr {
                margin-bottom: 1.5rem;
                border: 1px solid var(--border-color);
                border-radius: 0.75rem;
                overflow: hidden;
            }

            .data-table td {
                text-align: right;
                padding-left: 50%;
                position: relative;
            }

            .data-table td::before {
                content: attr(data-label);
                position: absolute;
                left: 1rem;
                width: calc(50% - 2rem);
                text-align: left;
                font-weight: 600;
                color: var(--secondary-dark);
                text-transform: uppercase;
                font-size: 0.8rem;
            }
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <!-- Sidebar Navigation -->
        <div class="sidebar">
            <h2>Admin Panel</h2>
            <ul class="nav-list">
                <li><a href="#" class="active">Manage Users</a></li>
                <li><a href="manage_services.php">Manage Services</a></li>
                <li><a href="manage_transactions.php">Manage Transactions</a></li>
                
            </ul>
        </div>

        <!-- Main Content Area -->
        <div class="main-content">
            <div class="header">
                <h1>Manage Users</h1>
                <a href="logout.php" class="logout-link">Logout</a>
            </div>

            <div class="content-card">
                <h3>User List</h3>
                <?php if (!empty($message)) { ?>
                    <div class="message-box"><?php echo $message; ?></div>
                <?php } ?>
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Full Name</th>
                                <th>Email</th>
                                <th>Member Since</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($result->num_rows > 0) {
                                while($user = $result->fetch_assoc()) { ?>
                                <tr>
                                    <td data-label="ID"><?php echo htmlspecialchars($user['id']); ?></td>
                                    <td data-label="Full Name"><?php echo htmlspecialchars($user['full_name']); ?></td>
                                    <td data-label="Email"><?php echo htmlspecialchars($user['email']); ?></td>
                                    <td data-label="Member Since"><?php echo htmlspecialchars(date("M d, Y", strtotime($user['created_at']))); ?></td>
                                    <td data-label="Actions">
                                        <a href="view_user.php?id=<?php echo htmlspecialchars($user['id']); ?>" class="btn btn-view">View</a>
                                        <a href="manage_users.php?delete_id=<?php echo htmlspecialchars($user['id']); ?>" class="btn btn-danger" onclick="return confirm('WARNING: Are you sure you want to delete this user? This action cannot be undone.');">Delete</a>
                                    </td>
                                </tr>
                            <?php }
                            } else { ?>
                                <tr>
                                    <td colspan="5" style="text-align: center; color: var(--secondary-dark);">No users found.</td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
