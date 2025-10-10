<?php
session_start();
include('db.php'); // Database connection

// Check if the admin is logged in
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php'); // Redirect to login if not logged in
    exit();
}

// Fetch all orders, joining with users to display customer name and email
$query = "
    SELECT 
        o.orders_id,    /* UPDATED: Using correct primary key 'orders_id' from SQL file */
        o.total_amount, /* UPDATED: Using correct amount column 'total_amount' from SQL file */
        o.order_date, 
        o.status,
        u.full_name,
        u.email
    FROM 
        orders o
    JOIN 
        users u ON o.user_id = u.id
    ORDER BY 
        o.order_date DESC
"; 
$result = $conn->query($query);

$message = '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Orders</title>
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
            --status-pending: #F59E0B;
            --status-completed: #10B981;
            --status-cancelled: #EF4444;
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

        .nav-list a[href*="manage_transactions.php"] {
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
        
        /* Status Badges */
        .status-badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px; /* Fully rounded pill shape */
            font-weight: 600;
            font-size: 0.75rem;
            text-transform: uppercase;
        }
        .status-badge.pending {
            background-color: #FEF3C7; /* Yellow background */
            color: var(--status-pending);
        }
        .status-badge.completed {
            background-color: #D1FAE5; /* Green background */
            color: var(--status-completed);
        }
        .status-badge.cancelled {
            background-color: #FEE2E2; /* Red background */
            color: var(--status-cancelled);
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
                border: 1px solid var(--link-hover-bg);
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
                <li><a href="manage_users.php">Manage Users</a></li>
                <li><a href="manage_services.php">Manage Services</a></li>
                <li><a href="manage_transactions.php">Manage Transactions</a></li>
                <li><a href="#"></a></li>
            </ul>
        </div>

        <!-- Main Content Area -->
        <div class="main-content">
            <div class="header">
                <h1>Manage Orders</h1>
                <a href="logout.php" class="logout-link">Logout</a>
            </div>

            <div class="content-card">
                <h3>Order List</h3>
                <?php if (!empty($message)) { ?>
                    <div class="message-box"><?php echo $message; ?></div>
                <?php } ?>
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Customer Name</th>
                                <th>Customer Email</th>
                                <th>Total Amount</th>
                                <th>Status</th>
                                <th>Order Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($result->num_rows > 0) {
                                while($order = $result->fetch_assoc()) { 
                                    // Determine status class
                                    $status_class = strtolower($order['status']);
                                    if ($status_class == 'processing' || $status_class == 'shipped') {
                                        $status_class = 'pending';
                                    } elseif ($status_class == 'completed') {
                                        $status_class = 'completed';
                                    } else {
                                        $status_class = 'cancelled';
                                    }
                                ?>
                                <tr>
                                    <!-- Use 'orders_id' for display and linking -->
                                    <td data-label="Order ID">#<?php echo htmlspecialchars($order['orders_id']); ?></td>
                                    <td data-label="Customer Name"><?php echo htmlspecialchars($order['full_name']); ?></td>
                                    <td data-label="Customer Email"><?php echo htmlspecialchars($order['email']); ?></td>
                                    <!-- Use 'total_amount' for display -->
                                    <td data-label="Total Amount">$<?php echo htmlspecialchars(number_format($order['total_amount'], 2)); ?></td>
                                    <td data-label="Status">
                                        <span class="status-badge <?php echo $status_class; ?>">
                                            <?php echo htmlspecialchars($order['status']); ?>
                                        </span>
                                    </td>
                                    <td data-label="Order Date"><?php echo htmlspecialchars(date("M d, Y H:i", strtotime($order['order_date']))); ?></td>
                                    <td data-label="Actions">
                                        <!-- Use 'orders_id' for the detail link -->
                                        <a href="view_order.php?id=<?php echo htmlspecialchars($order['orders_id']); ?>" class="btn btn-view">View Details</a>
                                        <!-- Update button could be added here later -->
                                    </td>
                                </tr>
                            <?php }
                            } else { ?>
                                <tr>
                                    <td colspan="7" style="text-align: center; color: var(--secondary-dark);">No orders found.</td>
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
