<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_token'])) {
    header("Location: login.php");
    exit();
}

// Include header
include 'header.php';
?>

<div class="container mt-5">
    <div class="row">
        <div class="col-md-6">
            <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/a/a4/LPG_in_Bangladesh_01.jpg/1024px-LPG_in_Bangladesh_01.jpg" alt="Gas Delivery" class="img-fluid rounded animated-image">
        </div>
        <div class="col-md-6">
            <h2>Gas Delivery Service</h2>
            <p>Need a gas cylinder? We deliver it to your doorstep. Fast and reliable service.</p>
            <div class="service-form mt-4">
                <form action="place_order.php" method="POST">
                    <input type="hidden" name="service_id" value="4">
                    <div class="mb-3">
                        <label for="cylinders" class="form-label">Number of Cylinders</label>
                        <input type="number" id="cylinders" name="cylinders" class="form-control" min="1" required>
                    </div>
                    <div class="mb-3">
                        <label for="date" class="form-label">Preferred Date</label>
                        <input type="date" id="date" name="date" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="time" class="form-label">Preferred Time</label>
                        <input type="time" id="time" name="time" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="address" class="form-label">Address</label>
                        <textarea id="address" name="address" class="form-control" rows="3" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Order Now</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
