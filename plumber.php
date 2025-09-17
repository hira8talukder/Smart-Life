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
            <img src="https://images.unsplash.com/photo-1581092919534-99a9f420b56a?q=80&w=2070&auto=format&fit=crop" alt="Plumber Service" class="img-fluid rounded animated-image">
        </div>
        <div class="col-md-6">
            <h2>Plumber Service</h2>
            <p>Leaky faucet? Clogged drain? Our expert plumbers are here to help. Book a service call today.</p>
            <div class="service-form mt-4">
                <form action="place_order.php" method="POST">
                    <input type="hidden" name="service_id" value="2">
                    <div class="mb-3">
                        <label for="issue_type" class="form-label">Type of Issue</label>
                        <input type="text" id="issue_type" name="issue_type" class="form-control" placeholder="e.g., Leaky faucet, Clogged drain" required>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description of Issue</label>
                        <textarea id="description" name="description" class="form-control" rows="5" required></textarea>
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
