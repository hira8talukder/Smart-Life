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
            <img src="https://images.unsplash.com/photo-1581578731548-c64695cc6952?q=80&w=2070&auto=format&fit=crop" alt="Home Cleaning" class="img-fluid rounded animated-image">
        </div>
        <div class="col-md-6">
            <h2>Home Cleaning Service</h2>
            <p>Our professional home cleaning service will leave your home sparkling clean. We offer various packages to suit your needs.</p>
            <div class="service-form mt-4">
                <form action="place_order.php" method="POST">
                    <input type="hidden" name="service_id" value="1">
                    <div class="mb-3">
                        <label for="cleaning_type" class="form-label">Cleaning Type</label>
                        <select id="cleaning_type" name="cleaning_type" class="form-select">
                            <option value="regular">Regular Cleaning</option>
                            <option value="deep">Deep Cleaning</option>
                            <option value="move_in_out">Move-in/Move-out Cleaning</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="rooms" class="form-label">Number of Rooms</label>
                        <input type="number" id="rooms" name="rooms" class="form-control" min="1" required>
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
                    <button type="submit" class="btn btn-primary w-100">Book Now</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
