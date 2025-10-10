<?php
session_start();
include 'db.php';

// Check if the user is logged in
if (!isset($_SESSION['user_token'])) {
    header("Location: login.php");
    exit();
}

// Get user ID from token
function get_user_id_from_token($token, $secret = 'secret') {
    list($header, $payload, $signature) = explode('.', $token);
    $payload = json_decode(base64_decode($payload), true);
    return $payload['user_id'];
}

$user_id = get_user_id_from_token($_SESSION['user_token']);

// Check if order_id is provided
if (!isset($_GET['order_id'])) {
    header("Location: order_history.php");
    exit();
}

$order_id = $_GET['order_id'];

// Verify the order belongs to the user and is delivered
$sql = "SELECT id, service_id FROM orders WHERE id = ? AND user_id = ? AND status = 'Delivered'";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $order_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows == 0) {
    // Order not found, not owned by user, or not delivered
    header("Location: order_history.php");
    exit();
}
$order = $result->fetch_assoc();
$service_id = $order['service_id'];

include 'header.php';
?>

<div class="container">
    <h2>Rate and Review Service</h2>
    <form action="submit_review_process.php" method="POST" class="review-form">
        <input type="hidden" name="order_id" value="<?php echo $order_id; ?>">
        <input type="hidden" name="service_id" value="<?php echo $service_id; ?>">
        
        <div class="form-group rating">
            <label>Rating:</label>
            <div class="stars">
                <input type="radio" id="star5" name="rating" value="5" required><label for="star5">&#9733;</label>
                <input type="radio" id="star4" name="rating" value="4"><label for="star4">&#9733;</label>
                <input type="radio" id="star3" name="rating" value="3"><label for="star3">&#9733;</label>
                <input type="radio" id="star2" name="rating" value="2"><label for="star2">&#9733;</label>
                <input type="radio" id="star1" name="rating" value="1"><label for="star1">&#9733;</label>
            </div>
        </div>

        <div class="form-group">
            <label for="review_text">Review:</label>
            <textarea id="review_text" name="review_text" rows="5" placeholder="Share your experience..."></textarea>
        </div>

        <button type="submit" class="btn">Submit Review</button>
    </form>
</div>

<style>
.review-form .form-group {
    margin-bottom: 20px;
}
.review-form label {
    display: block;
    margin-bottom: 5px;
}
.review-form .rating .stars {
    display: inline-block;
    direction: rtl; /* Right to left to make stars select from right */
}
.review-form .rating input[type="radio"] {
    display: none; /* Hide radio buttons */
}
.review-form .rating label {
    font-size: 2.5em;
    color: #ddd;
    cursor: pointer;
    display: inline-block;
}
/* Change color of selected and preceding stars */
.review-form .rating input[type="radio"]:checked ~ label,
.review-form .rating label:hover,
.review-form .rating label:hover ~ label {
    color: #f5b301;
}
.review-form textarea {
    width: 100%;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 4px;
}
.review-form .btn {
    padding: 10px 20px;
    background-color: #007bff;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
}
</style>

<?php include 'footer.php'; ?>
