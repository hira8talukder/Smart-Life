<?php
session_start();
include('db.php'); // Database connection

// Check if the admin is logged in
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php'); // Redirect to login if not logged in
    exit();
}

$order = null;
$order_details_array = [];
$error = '';
$message = '';
$order_id = $_GET['id'] ?? null;

// --- 1. Handle Status Update Submission ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $new_status = $_POST['new_status'];
    $order_id_to_update = $_POST['order_id'];

    if (!empty($new_status) && is_numeric($order_id_to_update)) {
        // Prepare the UPDATE statement using the correct 'orders_id' primary key
        $update_stmt = $conn->prepare("UPDATE orders SET status = ? WHERE orders_id = ?");
        $update_stmt->bind_param("si", $new_status, $order_id_to_update);
        
        if ($update_stmt->execute()) {
            $message = "Order #{$order_id_to_update} status updated to **{$new_status}** successfully!";
            // Redirect to the same page to prevent form resubmission and refresh data
            header("Location: view_order.php?id={$order_id_to_update}&msg=" . urlencode($message));
            exit();
        } else {
            $error = "Error updating status: " . $update_stmt->error;
        }
        $update_stmt->close();
    } else {
        $error = "Invalid status or order ID provided for update.";
    }
}

// Check for and display a success message after redirection
if (isset($_GET['msg'])) {
    $message = htmlspecialchars($_GET['msg']);
}

// --- 2. Fetch Order Data (Refreshed after update or initial load) ---
if ($order_id && is_numeric($order_id)) {

    // Query to fetch order data, user data, and order details (JSON)
    $order_stmt = $conn->prepare("
        SELECT 
            o.*, 
            o.orders_id, 
            o.total_amount, 
            u.full_name, 
            u.email,
            u.address as user_address,
            u.phone as user_phone,
            od.details 
        FROM orders o
        JOIN users u ON o.user_id = u.id
        LEFT JOIN order_details od ON o.orders_id = od.order_id
        WHERE o.orders_id = ?
        LIMIT 1
    ");
    
    $order_stmt->bind_param("i", $order_id);
    $order_stmt->execute();
    $order_result = $order_stmt->get_result();
    
    if ($order_result->num_rows > 0) {
        $order = $order_result->fetch_assoc();
        
        // Safely decode the JSON details
        if (!empty($order['details'])) {
            $decoded_details = json_decode($order['details'], true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded_details)) {
                $order_details_array = $decoded_details;
            } else {
                $error .= (empty($error) ? "" : " | ") . "Warning: Failed to parse order details.";
            }
        }
        
    } else {
        $error = "Error: Order #{$order_id} not found.";
    }
    $order_stmt->close();
} else {
    $error = "Invalid order ID provided.";
}

// Determine status class for display
$status_class = '';
$current_status = $order['status'] ?? 'N/A';
if ($order) {
    $status_text = strtolower($current_status);
    if ($status_text == 'processing' || $status_text == 'shipped') {
        $status_class = 'pending';
    } elseif ($status_text == 'completed') {
        $status_class = 'completed';
    } else {
        $status_class = 'cancelled';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Details | #<?php echo htmlspecialchars($order_id ?? 'N/A'); ?></title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap');

        :root {
            --primary-blue: #0A72B8;
            --primary-dark: #1F2937;
            --secondary-dark: #4B5563;
            --background-light: #F8F9FA;
            --card-bg: #FFFFFF;
            --link-hover-bg: #F3F4F6;
            --status-pending: #F59E0B;
            --status-completed: #10B981;
            --status-cancelled: #EF4444;
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
            margin-bottom: 1.5rem;
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
            margin-bottom: 2rem;
            margin-top: 2rem;
        }
        
        /* Message & Error styles */
        .message-box {
            font-size: 0.9rem;
            font-weight: 500;
            padding: 1rem;
            border-radius: 0.75rem;
            margin-bottom: 1.5rem;
        }
        .error-box {
            color: #991B1B;
            background-color: #FEE2E2;
            border: 1px solid #FCA5A5;
        }
        .success-box {
            color: #065F46;
            background-color: #D1FAE5;
            border: 1px solid #34D399;
        }
        .warning-box {
            color: #92400E;
            background-color: #FEF3C7;
            border: 1px solid #FCD34D;
        }

        .btn-back {
            display: inline-flex;
            align-items: center;
            padding: 0.75rem 1.5rem;
            background-color: #E5E7EB;
            color: var(--secondary-dark);
            font-weight: 600;
            border-radius: 0.75rem;
            text-decoration: none;
            transition: background-color 0.3s, transform 0.3s;
            margin-bottom: 2rem;
        }
        .btn-back:hover {
            background-color: #D1D5DB;
            transform: translateY(-1px);
        }

        /* Detail Grid and Cards */
        .grid-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.5rem;
        }

        .detail-card {
            padding: 1.5rem;
            border: 1px solid var(--border-color);
            border-radius: 0.75rem;
            background-color: var(--card-bg);
        }

        .detail-card h4 {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--primary-blue);
            margin-top: 0;
            margin-bottom: 1rem;
            border-bottom: 2px solid var(--link-hover-bg);
            padding-bottom: 0.5rem;
        }

        .detail-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.5rem 0;
            border-bottom: 1px dashed var(--link-hover-bg);
        }
        .detail-item:last-child {
            border-bottom: none;
        }

        .detail-label {
            font-weight: 500;
            color: var(--secondary-dark);
        }

        .detail-value {
            font-weight: 600;
            color: var(--primary-dark);
            text-align: right;
        }
        
        /* Status Badge */
        .status-badge {
            display: inline-block;
            padding: 0.3rem 1rem;
            border-radius: 9999px;
            font-weight: 600;
            font-size: 0.85rem;
            text-transform: uppercase;
        }
        .status-badge.pending {
            background-color: #FEF3C7;
            color: var(--status-pending);
        }
        .status-badge.completed {
            background-color: #D1FAE5;
            color: var(--status-completed);
        }
        .status-badge.cancelled {
            background-color: #FEE2E2;
            color: var(--status-cancelled);
        }

        /* Status Update Form Styling */
        .status-form {
            display: flex;
            gap: 1rem;
            align-items: center;
            margin-top: 1rem;
            padding: 1rem;
            border-top: 1px solid var(--border-color);
        }
        .status-form select, .status-form button {
            padding: 0.6rem 1rem;
            border-radius: 0.5rem;
            font-weight: 500;
            border: 1px solid var(--border-color);
        }
        .status-form select {
            flex-grow: 1;
            background-color: var(--link-hover-bg);
            cursor: pointer;
        }
        .status-form button {
            background-color: var(--primary-blue);
            color: white;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s, transform 0.3s;
        }
        .status-form button:hover {
            background-color: #085A8A;
            transform: translateY(-1px);
        }

        /* Order Items Table */
        .order-items-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }
        .order-items-table th, .order-items-table td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid var(--link-hover-bg);
        }
        .order-items-table th {
            font-weight: 600;
            color: var(--secondary-dark);
            text-transform: uppercase;
            font-size: 0.8rem;
            background-color: var(--link-hover-bg);
        }
        .order-items-table td:last-child {
            text-align: right;
        }
        .order-items-table tfoot td {
            font-weight: 700;
            font-size: 1.1rem;
            border-top: 2px solid var(--primary-blue);
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
                <li><a href="manage_users.php">Manage Users</a></li>
                <li><a href="manage_services.php">Manage Services</a></li>
                <li><a href="manage_transactions.php">Manage Orders</a></li>
                
            </ul>
        </div>

        <!-- Main Content Area -->
        <div class="main-content">
            <div class="header">
                <h1>Order Details: #<?php echo htmlspecialchars($order['orders_id'] ?? 'N/A'); ?></h1>
                <a href="logout.php" class="logout-link">Logout</a>
            </div>

            <a href="manage_transactions.php" class="btn-back">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor" style="width: 1.25rem; height: 1.25rem;">
                    <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                </svg>
                Back to Orders List
            </a>

            <?php if (!empty($error)) { ?>
                <div class="message-box error-box"><?php echo $error; ?></div>
            <?php } elseif (!empty($message)) { ?>
                <div class="message-box success-box"><?php echo $message; ?></div>
            <?php } ?>


            <?php if ($order) { ?>
                
                <!-- Status Update Card -->
                <div class="content-card mb-6">
                    <h3>Update Order Status</h3>
                    <div class="detail-item" style="border-bottom: none; padding-bottom: 0;">
                        <span class="detail-label">Current Status:</span>
                        <span class="detail-value status-badge <?php echo $status_class; ?>">
                            <?php echo htmlspecialchars($current_status); ?>
                        </span>
                    </div>

                    <form method="POST" class="status-form">
                        <input type="hidden" name="order_id" value="<?php echo htmlspecialchars($order['orders_id']); ?>">
                        <select name="new_status" required>
                            <option value="" disabled>-- Select New Status --</option>
                            <?php 
                            $statuses = ['Pending', 'Processing', 'Shipped', 'Completed', 'Cancelled'];
                            foreach ($statuses as $status) {
                                $selected = ($status == $current_status) ? 'selected' : '';
                                echo "<option value='{$status}' {$selected}>{$status}</option>";
                            }
                            ?>
                        </select>
                        <button type="submit" name="update_status">Update Status</button>
                    </form>
                </div>
            
                <div class="grid-container">
                    
                    <!-- Order Summary Card -->
                    <div class="detail-card">
                        <h4>Order Summary</h4>
                        <div class="detail-item">
                            <span class="detail-label">Order ID</span>
                            <span class="detail-value">#<?php echo htmlspecialchars($order['orders_id']); ?></span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Order Date</span>
                            <span class="detail-value"><?php echo htmlspecialchars(date("M d, Y H:i:s", strtotime($order['order_date']))); ?></span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Total Amount</span>
                            <span class="detail-value text-xl" style="color: var(--primary-blue);">$<?php echo htmlspecialchars(number_format($order['total_amount'], 2)); ?></span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Payment Method</span>
                            <span class="detail-value"><?php echo htmlspecialchars($order['payment_method'] ?? 'N/A'); ?></span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Shipping Cost</span>
                            <span class="detail-value">$<?php echo htmlspecialchars(number_format($order['shipping_cost'] ?? 0, 2)); ?></span>
                        </div>
                    </div>

                    <!-- Customer Info Card -->
                    <div class="detail-card">
                        <h4>Customer Information</h4>
                        <div class="detail-item">
                            <span class="detail-label">Customer ID</span>
                            <span class="detail-value"><?php echo htmlspecialchars($order['user_id']); ?></td>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Name</span>
                            <span class="detail-value"><?php echo htmlspecialchars($order['full_name']); ?></span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Email</span>
                            <span class="detail-value"><?php echo htmlspecialchars($order['email']); ?></span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Phone</span>
                            <span class="detail-value"><?php echo htmlspecialchars($order['user_phone'] ?? 'N/A'); ?></span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Shipping Address</span>
                            <span class="detail-value" style="max-width: 60%; white-space: normal; text-align: right;"><?php echo nl2br(htmlspecialchars($order['user_address'] ?? 'N/A')); ?></span>
                        </div>
                    </div>
                </div>

                <!-- Order Items Card -->
                
            <?php } ?>
        </div>
    </div>
</body>
</html>