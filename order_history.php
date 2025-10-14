<?php
session_start();
include 'db.php';

// Check if the user is logged in
if (!isset($_SESSION['user_token'])) {
    header("Location: login.php");
    exit();
}

// Get user ID from token (assuming token payload has user_id)
function get_user_id_from_token($token, $secret = 'secret') {
    list($header, $payload, $signature) = explode('.', $token);
    $payload = json_decode(base64_decode($payload), true);
    return $payload['user_id'];
}

$user_id = get_user_id_from_token($_SESSION['user_token']);

// Fetch user's orders
$sql = "SELECT orders.orders_id, services.name, orders.order_date, orders.status 
        FROM orders 
        JOIN services ON orders.service_id = services.id 
        WHERE orders.user_id = ? 
        ORDER BY orders.order_date DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

include 'header.php';
?>

<div class="container">
    <?php if (isset($_GET['order']) && $_GET['order'] == 'success'): ?>
        <div class="success-message">Order placed successfully!</div>
    <?php endif; ?>
    <h2>Order History</h2>
    <div class="order-history">
        <table>
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Service Name</th>
                    <th>Order Date</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['orders_id']; ?></td>
                            <td><?php echo $row['name']; ?></td>
                            <td><?php echo $row['order_date']; ?></td>
                            <td><?php echo $row['status']; ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4">No orders found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<style>
.order-history table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
}
.order-history th, .order-history td {
    border: 1px solid #ddd;
    padding: 8px;
    text-align: left;
}
.order-history th {
    background-color: #f2f2f2;
}
</style>

<?php include 'footer.php'; ?>
