<?php
session_start();
include('db.php'); // Database connection

// check if the admin is logged in
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php'); // Redirect to login if not logged in
    exit();
}

// Handle User Deletion ---
$message = '';
$message_class = 'success'; // Default message class for styling

if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    
    // --- FIX START: Use Transaction to handle Foreign Key Constraints ---
    // This ensures all steps (delete details, delete orders, delete user) succeed or all fail.
    $conn->begin_transaction();

    try {
        // 1. Get all orders_id associated with the user
        $order_ids = [];
        $stmt_select_orders = $conn->prepare("SELECT orders_id FROM orders WHERE user_id = ?");
        $stmt_select_orders->bind_param("i", $delete_id);
        $stmt_select_orders->execute();
        $result_orders = $stmt_select_orders->get_result();
        while ($row = $result_orders->fetch_assoc()) {
            $order_ids[] = $row['orders_id'];
        }
        $stmt_select_orders->close();
        
        // 2. Delete dependent rows in order_details (must be done before deleting orders)
        if (!empty($order_ids)) {
            // Build the IN clause dynamically for secure prepared statement
            $in_clause = implode(',', array_fill(0, count($order_ids), '?'));
            $types = str_repeat('i', count($order_ids));
            
            $stmt_details = $conn->prepare("DELETE FROM order_details WHERE order_id IN ($in_clause)");
            // Use splat operator to pass array elements as separate arguments
            $stmt_details->bind_param($types, ...$order_ids);
            
            if (!$stmt_details->execute()) {
                throw new Exception("Error deleting order details: " . $stmt_details->error);
            }
            $stmt_details->close();
        }

        // 3. Delete dependent rows in orders (must be done before deleting user)
        $stmt_orders = $conn->prepare("DELETE FROM orders WHERE user_id = ?");
        $stmt_orders->bind_param("i", $delete_id);
        if (!$stmt_orders->execute()) {
            throw new Exception("Error deleting user's orders: " . $stmt_orders->error);
        }
        $stmt_orders->close();
        
        // 4. Delete the user (parent row)
        $stmt_user = $conn->prepare("DELETE FROM users WHERE id = ?");
        $stmt_user->bind_param("i", $delete_id);
        if (!$stmt_user->execute()) {
            throw new Exception("Error deleting user: " . $stmt_user->error);
        }
        $stmt_user->close();
        
        // Commit the transaction
        $conn->commit();
        $message = "User and all associated data deleted successfully!";
        
    } catch (Exception $e) {
        // Rollback on any failure
        $conn->rollback();
        $message_class = 'error';
        $message = "Error deleting user: The operation failed due to a database issue. Please check logs for details or contact support.";
    }
    // --- FIX END ---
}

// Handle Search Query (NEW EXPLICIT LOGIC) ---
$search_term = trim($_GET['search'] ?? '');
$search_by = $_GET['search_by'] ?? 'full_name'; // Default to fuzzy name search

// Base SQL query
$query = "SELECT id, full_name, email, created_at FROM users";
$params = [];
$types = '';

if (!empty($search_term)) {
    // Determine the field and operator based on search_by selection
    $field_name = '';
    $operator = '';
    $bind_value = $search_term;
    
    switch ($search_by) {
        case 'id':
            
            $field_name = 'id';
            $operator = '=';
            break;
        case 'email':
            
            $field_name = 'email';
            $operator = '=';
            break;
        case 'full_name':
        default:
            
            $field_name = 'full_name';
            $operator = 'LIKE';
            $bind_value = "%" . $search_term . "%";
            break;
    }

    // Construct the WHERE clause with the selected criteria
    $query .= " WHERE $field_name $operator ?";
    // Use a temporary variable for bind_param to pass by reference
    $temp_bind_value = $bind_value; 
    $params[] = &$temp_bind_value;
    $types = 's'; // Assuming the final bound value is treated as a string for safety
}

$query .= " ORDER BY created_at DESC";

// Prepare the statement
$stmt = $conn->prepare($query);

// Bind parameters if search term is present
if (!empty($search_term)) {
    // Note: The use of call_user_func_array is generally needed when types/params are dynamic. 
    // Since we only have one potential parameter here, simple bind_param can also be used, 
    // but the existing dynamic logic is retained and cleaned up.
    if (!empty($params)) {
        // Resetting $types to match the actual binding type. If 'id' is selected, it should be 'i'.
        $types = ($search_by == 'id' && is_numeric($search_term)) ? 'i' : 's'; 
        
        // Since we are only binding one parameter, we can simplify this
        $stmt->bind_param($types, $temp_bind_value);
    }
}

$stmt->execute();
$result = $stmt->get_result();
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
            --primary-orange: #FF8C00; 
            --primary-dark: #1F2937;
            --secondary-dark: #4B5563;
            --background-light: #F8F9FA;
            --card-bg: #FFFFFF;
            --link-hover-bg: #F3F4F6;
            --link-active-bg: #E5E7EB;
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
            color: var(--primary-orange); 
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
        
        /* Message box styles updated to include error state */
        .message-box {
            text-align: center;
            font-size: 0.9rem;
            font-weight: 500;
            padding: 1rem;
            border-radius: 0.75rem;
            margin-bottom: 1.5rem;
        }
        
        .message-box.success {
            color: #065F46; /* Dark Green */
            background-color: #D1FAE5; /* Light Green */
            border: 1px solid #A7F3D0;
        }

        .message-box.error {
            color: #991B1B; /* Dark Red */
            background-color: #FEE2E2; /* Light Red */
            border: 1px solid #FCA5A5;
        }

        /* Search Form Styling */
        .search-form {
            display: flex;
            flex-direction: column; /* Stack on mobile */
            gap: 0.5rem;
            margin-bottom: 1.5rem;
        }

        .search-controls {
            display: flex;
            gap: 0.5rem;
        }
        
        .search-form .search-select {
            padding: 0.75rem 1rem;
            border: 1px solid var(--border-color);
            border-radius: 0.75rem;
            font-size: 1rem;
            background-color: white;
            cursor: pointer;
            transition: border-color 0.3s;
        }

        .search-form input[type="text"] {
            flex-grow: 1;
            padding: 0.75rem 1rem;
            border: 1px solid var(--border-color);
            border-radius: 0.75rem;
            font-size: 1rem;
            transition: border-color 0.3s;
        }

        .search-form input[type="text"]:focus, .search-form .search-select:focus {
            outline: none;
            border-color: var(--primary-blue);
        }

        .search-form button {
            background-color: var(--primary-blue);
            color: white;
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 0.75rem;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.3s, transform 0.3s;
        }

        .search-form button:hover {
            background-color: #085A8A;
            transform: translateY(-1px);
        }

        /* Desktop layout for search */
        @media (min-width: 640px) {
            .search-form {
                flex-direction: row;
            }
            .search-controls {
                flex-grow: 1;
            }
            .search-form .search-select {
                flex-shrink: 0;
            }
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

        /* Button styles */
        .btn {
            display: inline-block;
            padding: 0.5rem 1rem;
            margin: 0.25rem;
            border-radius: 0.5rem;
            font-weight: 500;
            text-align: center;
            text-decoration: none;
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
                border-bottom: none;
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
        <div class="sidebar">
            
            <a href="dashboard.php" class="logo">Smart-Life</a>
            <h2>Admin Panel</h2> 
            <ul class="nav-list">
                <li><a href="#" class="active">Manage Users</a></li>
                <li><a href="manage_services.php">Manage Services</a></li>
                <li><a href="manage_transactions.php">Manage Transactions</a></li>
            </ul>
        </div>

        <div class="main-content">
            <div class="header">
                <h1>Manage Users</h1>
                <a href="logout.php" class="logout-link">Logout</a>
            </div>

            <div class="content-card">
                <h3>User List</h3>
                <?php if (!empty($message)) { ?>
                    <div class="message-box <?php echo $message_class; ?>"><?php echo $message; ?></div>
                <?php } ?>

                <form method="GET" class="search-form">
                    <div class="search-controls">
                        <select name="search_by" class="search-select">
                            <option value="id" <?php if ($search_by == 'id') echo 'selected'; ?>>ID</option>
                            <option value="email" <?php if ($search_by == 'email') echo 'selected'; ?>>Email</option>
                            <option value="full_name" <?php if ($search_by == 'full_name') echo 'selected'; ?>>Name</option>
                        </select>
                        <input 
                            type="text" 
                            name="search" 
                            placeholder="Enter search term..." 
                            value="<?php echo htmlspecialchars($search_term); ?>"
                        >
                    </div>
                    <button type="submit">Search</button>
                    <?php if (!empty($search_term)) { ?>
                        <a href="manage_users.php" class="btn btn-view" style="background-color: #6B7280;">Clear</a>
                    <?php } ?>
                </form>

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
                                        <a href="manage_users.php?delete_id=<?php echo htmlspecialchars($user['id']); ?>" class="btn btn-danger" onclick="return confirm('WARNING: Are you sure you want to delete this user? This will permanently delete all associated orders and order details.');">Delete</a>
                                    </td>
                                </tr>
                            <?php }
                            } else { ?>
                                <tr>
                                    <td colspan="5" style="text-align: center; color: var(--secondary-dark);">
                                        <?php if (!empty($search_term)) { ?>
                                            No users found matching your search criteria.
                                        <?php } else { ?>
                                            No users found.
                                        <?php } ?>
                                    </td>
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