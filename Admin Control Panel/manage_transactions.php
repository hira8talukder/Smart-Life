<?php
session_start();
include('db.php'); // Database connection

// Check if the admin is logged in
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php'); // Redirect to login if not logged in
    exit();
}

// Fetch all transactions from the transactions table
$query = "SELECT * FROM transactions"; 
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Transactions</title>
</head>
<body>
    <h2>Manage Transactions</h2>
    <table border="1">
        <tr>
            <th>ID</th>
            <th>Amount</th>
            <th>Service</th>
            <th>Transaction Date</th>
            <th>Actions</th>
        </tr>
        <?php while ($transaction = mysqli_fetch_assoc($result)) { ?>
            <tr>
                <td><?php echo $transaction['id']; ?></td>
                <td><?php echo $transaction['amount']; ?></td>
                <td><?php echo $transaction['service']; ?></td>
                <td><?php echo $transaction['transaction_date']; ?></td>
                <td>
                    <a href="view_transaction.php?id=<?php echo $transaction['id']; ?>">View</a> |
                    <a href="delete_transaction.php?id=<?php echo $transaction['id']; ?>">Delete</a>
                </td>
            </tr>
        <?php } ?>
    </table>
    <a href="dashboard.php">Back to Dashboard</a>
</body>
</html>
