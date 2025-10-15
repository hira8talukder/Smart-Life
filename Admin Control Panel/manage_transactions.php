<?php
session_start();
include('db.php'); // Database connection

// Check if the admin is logged in
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php'); // Redirect to login if not logged in
    exit();
}

// Initialize search variables
$search_term = '';
$search_where = '';

// Check if a search term was submitted
if (isset($_GET['search']) && !empty(trim($_GET['search']))) {
    $search_term = trim($_GET['search']);
    // Sanitize and create the WHERE clause
    // We search in orders_id (cast to string for LIKE comparison) and user email
    $search_where = $conn->real_escape_string($search_term);
    $search_where = "
        WHERE 
            o.orders_id LIKE '%{$search_where}%' 
            OR u.email LIKE '%{$search_where}%'
    ";
}

// Fetch all orders, joining with users AND services to display customer and service names
$query = "
    SELECT 
        o.orders_id,    /* Confirmed orders table primary key */
        o.total_amount, 
        o.order_date, 
        o.status,
        u.full_name,
        u.email,
        s.name AS service_name /* ADDED: Selects the service name from services table */
    FROM 
        orders o
    JOIN 
        users u ON o.user_id = u.id
    JOIN
        services s ON o.service_id = s.id /* ADDED: Join services table on service_id */
    {$search_where} /* ADDED: Apply search filter */
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
            --border-color: #E5E7EB;
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
            border-right: 1px solid var(--border-color);
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

        .content-card h3 {
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: 1.5rem;
            border-bottom: 1px solid var(--border-color);
            padding-bottom: 0.5rem;
        }
        
        /* Search Bar Styles */
        .search-container {
            margin-bottom: 1.5rem;
            display: flex;
            gap: 1rem;
        }

        .search-container input[type="text"] {
            flex-grow: 1;
            padding: 0.75rem 1rem;
            border: 1px solid var(--border-color);
            border-radius: 0.75rem;
            font-size: 1rem;
            outline: none;
            transition: border-color 0.3s, box-shadow 0.3s;
        }

        .search-container input[type="text"]:focus {
            border-color: var(--primary-blue);
            box-shadow: 0 0 0 3px rgba(10, 114, 184, 0.2);
        }

        .search-container button {
            padding: 0.75rem 1.5rem;
            background-color: var(--primary-blue);
            color: white;
            border: none;
            border-radius: 0.75rem;
            font-weight: 500;
            cursor: pointer;
            transition: background-color 0.3s, transform 0.3s;
        }

        .search-container button:hover {
            background-color: #085A8A;
            transform: translateY(-1px);
        }

        /* Table Styles */
        .order-table-wrapper {
            overflow-x: auto;
        }

        .order-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }

        .order-table th, .order-table td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid var(--border-color);
        }

        .order-table th {
            font-weight: 600;
            color: var(--secondary-dark);
            text-transform: uppercase;
            font-size: 0.8rem;
            background-color: var(--link-hover-bg);
        }

        .order-table tbody tr:last-child td {
            border-bottom: none;
        }

        .order-table tbody tr:hover {
            background-color: var(--link-hover-bg);
        }

        .status-badge {
            display: inline-block;
            padding: 0.3rem 0.75rem;
            border-radius: 0.5rem;
            font-weight: 600;
            font-size: 0.8rem;
            text-transform: uppercase;
        }
        .status-pending { background-color: #FEF3C7; color: #92400E; } /* Yellow */
        .status-processing { background-color: #DBEAFE; color: #1D4ED8; } /* Blue */
        .status-completed { background-color: #D1FAE5; color: #065F46; } /* Green */
        .status-cancelled { background-color: #FEE2E2; color: #991B1B; } /* Red */
        
        .btn {
            display: inline-block;
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            text-decoration: none;
            font-weight: 500;
            transition: background-color 0.3s, opacity 0.3s;
            font-size: 0.875rem;
        }

        .btn-view {
            background-color: var(--primary-blue);
            color: white;
        }

        .btn-view:hover {
            background-color: #085A8A;
        }

        /* Responsive Table */
        @media screen and (max-width: 768px) {
            .sidebar {
                width: 100%;
                padding: 1.5rem;
                border-right: none;
                border-bottom: 1px solid var(--border-color);
            }
            .dashboard-container {
                flex-direction: column;
            }
            .main-content {
                padding: 1.5rem;
            }
            .order-table thead {
                display: none;
            }
            .order-table, .order-table tbody, .order-table tr, .order-table td {
                display: block;
                width: 100%;
            }
            .order-table tr {
                margin-bottom: 1rem;
                border: 1px solid var(--border-color);
                border-radius: 0.75rem;
                background-color: var(--card-bg);
            }
            .order-table td {
                text-align: right;
                padding-left: 50%;
                position: relative;
                border: none;
            }
            .order-table td::before {
                content: attr(data-label);
                position: absolute;
                left: 0;
                width: 50%;
                padding-left: 1rem;
                font-weight: 600;
                text-align: left;
                color: var(--secondary-dark);
            }
            .order-table td:last-child {
                border-bottom: none;
            }

            .search-container {
                flex-direction: column;
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
                <li><a href="manage_transactions.php" class="active">Manage transactions</a></li>
            </ul>
        </div>

        <div class="main-content">
            <div class="header">
                <h1>Manage Orders</h1>
                <a href="logout.php" class="logout-link">Logout</a>
            </div>

            <div class="content-card">
                <h3>All Orders</h3>
                
                <form method="GET" action="manage_transactions.php" class="search-container">
                    <input 
                        type="text" 
                        name="search" 
                        placeholder="Search by Order ID or Customer Email..." 
                        value="<?php echo htmlspecialchars($search_term); ?>"
                    >
                    <button type="submit">Search</button>
                    <?php if (!empty($search_term)): ?>
                        <a href="manage_transactions.php" class="btn" style="background-color: #6B7280; color: white;">Clear</a>
                    <?php endif; ?>
                </form>
                
                <div class="order-table-wrapper">
                    <?php if ($result && $result->num_rows > 0) { ?>
                        <table class="order-table">
                            <thead>
                                <tr>
                                    <th>Order ID</th>
                                    <th>Customer Name</th>
                                    <th>Customer Email</th> <th>Service Name</th> 
                                    <th>Total Amount</th>
                                    <th>Status</th>
                                    <th>Order Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while($order = $result->fetch_assoc()) { 
                                    $status_class = '';
                                    switch ($order['status']) {
                                        case 'Pending':
                                            $status_class = 'status-pending';
                                            break;
                                        case 'Processing':
                                            $status_class = 'status-processing';
                                            break;
                                        case 'Completed':
                                            $status_class = 'status-completed';
                                            break;
                                        case 'Cancelled':
                                            $status_class = 'status-cancelled';
                                            break;
                                        case 'Shipped': // Added Shipped status based on database
                                            $status_class = 'status-processing'; 
                                            break;
                                        default:
                                            $status_class = 'status-pending';
                                    }
                                    // Handle NULL total_amount gracefully
                                    $display_amount = is_null($order['total_amount']) ? 'N/A' : '$' . htmlspecialchars(number_format($order['total_amount'], 2));
                                ?>
                                <tr>
                                    <td data-label="Order ID"><?php echo htmlspecialchars($order['orders_id']); ?></td>
                                    <td data-label="Customer Name"><?php echo htmlspecialchars($order['full_name']); ?></td>
                                    <td data-label="Customer Email"><?php echo htmlspecialchars($order['email']); ?></td> <td data-label="Service Name"><?php echo htmlspecialchars($order['service_name']); ?></td> 
                                    <td data-label="Total Amount"><?php echo $display_amount; ?></td>
                                    <td data-label="Status">
                                        <span class="status-badge <?php echo $status_class; ?>">
                                            <?php echo htmlspecialchars($order['status']); ?>
                                        </span>
                                    </td>
                                    <td data-label="Order Date"><?php echo htmlspecialchars(date("M d, Y H:i", strtotime($order['order_date']))); ?></td>
                                    <td data-label="Actions">
                                        <a href="view_order.php?id=<?php echo htmlspecialchars($order['orders_id']); ?>" class="btn btn-view">View Details</a>

                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                <?php } else { ?>
                    <p style="text-align: center; color: var(--secondary-dark); padding: 20px;">
                        <?php echo empty($search_term) ? "No orders found." : "No orders found matching the search term '<strong>" . htmlspecialchars($search_term) . "</strong>'."; ?>
                    </p>
                <?php } ?>
                </div>
            </div>
        </div>
    </div>
</body>
</html>