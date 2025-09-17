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
            <img src="https://images.unsplash.com/photo-1621905251189-08b45d6a269e?q=80&w=2070&auto=format&fit=crop" alt="Electrician Service" class="img-fluid rounded animated-image">
        </div>
        <div class="col-md-6">
            <h2>Electrician Service</h2>
            <p>From faulty wiring to new installations, our certified electricians can handle it all. Book a service call today.</p>
            <div class="service-form mt-4">
                <form action="place_order.php" method="POST">
                    <input type="hidden" name="service_id" value="3">
                    <div class="mb-3">
                        <label for="issue_type" class="form-label">Type of Issue</label>
                        <input type="text" id="issue_type" name="issue_type" class="form-control" placeholder="e.g., Faulty wiring, New installation" required>
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
