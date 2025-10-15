<?php
session_start();
include('db.php'); // Database connection included here

// Check if the admin is logged in
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php'); // Redirect to login if not logged in
    exit();
}

// --- Fetch Dashboard Metrics ---

// 1. Total Registered Users
$query_users = "SELECT COUNT(id) AS total_users FROM users"; // users table
$result_users = $conn->query($query_users);
$total_users = $result_users->fetch_assoc()['total_users'] ?? 0;

// 2. Total Services Offered
$query_services = "SELECT COUNT(id) AS total_services FROM services"; // services table
$result_services = $conn->query($query_services);
$total_services = $result_services->fetch_assoc()['total_services'] ?? 0;

// 3. New Orders (Last 7 Days)
// Checks for orders placed in the last 7 days from the current time.
$query_new_orders = "
    SELECT COUNT(orders_id) AS new_orders 
    FROM orders 
    WHERE order_date >= DATE_SUB(NOW(), INTERVAL 7 DAY)
"; // orders table
$result_new_orders = $conn->query($query_new_orders);
$new_orders = $result_new_orders->fetch_assoc()['new_orders'] ?? 0;

// 4. Pending Orders
$query_pending = "SELECT COUNT(orders_id) AS pending_orders FROM orders WHERE status = 'Pending'"; // orders table
$result_pending = $conn->query($query_pending);
$pending_orders = $result_pending->fetch_assoc()['pending_orders'] ?? 0;

// 5. Completed Orders
$query_completed = "SELECT COUNT(orders_id) AS completed_orders FROM orders WHERE status = 'Completed'"; // orders table
$result_completed = $conn->query($query_completed);
$completed_orders = $result_completed->fetch_assoc()['completed_orders'] ?? 0;

// --- End Fetch Dashboard Metrics ---
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
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

        .nav-list a:active {
            background-color: var(--link-active-bg);
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

        /* --- NEW DASHBOARD METRICS STYLES --- */
        .metrics-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .metric-card {
            background-color: var(--card-bg);
            padding: 1.5rem;
            border-radius: 1rem;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            min-height: 120px;
            transition: transform 0.3s, box-shadow 0.3s;
            text-align: center; /* Center content like the image */
        }

        .metric-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
        }

        .metric-card .icon-container {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 1.5rem;
            margin: 0 auto 0.75rem auto; /* Center the icon */
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        /* Icon Colors */
        .metric-card.users .icon-container { background-color: #E0F2FE; color: #0A72B8; } /* Light Blue */
        .metric-card.services .icon-container { background-color: #FFFBEB; color: #F59E0B; } /* Light Amber */
        .metric-card.new-orders .icon-container { background-color: #ECFDF5; color: #059669; } /* Light Green */
        .metric-card.pending .icon-container { background-color: #FEF2F2; color: #EF4444; } /* Light Red */
        .metric-card.completed .icon-container { background-color: #E5FAEE; color: #10B981; } /* Darker Green */


        .metric-card .value {
            font-size: 2.2rem; /* Slightly larger value */
            font-weight: 700;
            color: var(--primary-dark);
            line-height: 1;
            margin-bottom: 0.5rem;
        }

        .metric-card .label {
            font-size: 0.85rem;
            font-weight: 500;
            color: var(--secondary-dark);
        }

        /* --- Responsive Styles --- */
        @media (max-width: 768px) {
            .dashboard-container {
                flex-direction: column;
            }

            .sidebar {
                width: 100%;
                padding: 1.5rem;
                border-right: none;
                border-bottom: 1px solid #E5E7EB;
            }

            .sidebar h2 {
                text-align: center;
            }

            .nav-list {
                flex-direction: row;
                flex-wrap: wrap;
                justify-content: center;
                gap: 0.5rem;
            }
            
            .nav-list a {
                padding: 0.7rem 1rem;
            }

            .main-content {
                padding: 1.5rem;
            }

            .header {
                flex-direction: column;
                align-items: flex-start;
                gap: 1rem;
            }

            .metrics-grid {
                grid-template-columns: 1fr; /* Stack cards on small screens */
            }
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <div class="sidebar">
            <a href="dashboard.php" class="logo">Smart-Life</a>
            <h2>Admin Panel</h2>
            <ul class="nav-list">
                <li><a href="manage_users.php">Manage Users</a></li>
                <li><a href="manage_services.php">Manage Services</a></li>
                <li><a href="manage_transactions.php">Manage Transactions</a></li>
                
            </ul>
        </div>

        <div class="main-content">
            <div class="header">
                <h1>Welcome, <?php echo htmlspecialchars($_SESSION['admin_name'] ?? 'Admin'); ?></h1> 
                <a href="logout.php" class="logout-link">Logout</a>
            </div>
            <div class="content-card" style="background-color: var(--card-bg); border-radius: 1rem; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05); padding: 2rem;margin-bottom: 4rem;">
                <p style="color: var(--secondary-dark);">This is Admin Dashboard</p>
            </div>
            <div class="metrics-grid">
                
                <div class="metric-card users">
                    <div class="icon-container">👥</div>
                    <div class="value"><?php echo number_format($total_users); ?></div>
                    <div class="label">Total Users</div>
                </div>

                <div class="metric-card services">
                    <div class="icon-container">🛠️</div>
                    <div class="value"><?php echo number_format($total_services); ?></div>
                    <div class="label">Total Services</div>
                </div>

                <div class="metric-card new-orders">
                    <div class="icon-container">⭐</div>
                    <div class="value"><?php echo number_format($new_orders); ?></div>
                    <div class="label">New Orders (7 Days)</div>
                </div>

                <div class="metric-card pending">
                    <div class="icon-container">⏳</div>
                    <div class="value"><?php echo number_format($pending_orders); ?></div>
                    <div class="label">Pending Orders</div>
                </div>

                <div class="metric-card completed">
                    <div class="icon-container">✅</div>
                    <div class="value"><?php echo number_format($completed_orders); ?></div>
                    <div class="label">Completed Orders</div>
                </div>
            </div>

            
        </div>
    </div>
</body>
</html>