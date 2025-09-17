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
            <img src="https://images.unsplash.com/photo-1583324113626-7740974415ea?q=80&w=1974&auto=format&fit=crop" alt="Medicine Delivery" class="img-fluid rounded animated-image">
        </div>
        <div class="col-md-6">
            <h2>Medicine Delivery Service</h2>
            <p>Upload your prescription or list the medicines you need.</p>
            <div class="service-form mt-4">
                <form action="place_order.php" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="service_id" value="5">
                    <div class="mb-3">
                        <label for="prescription" class="form-label">Upload Prescription (optional)</label>
                        <input type="file" id="prescription" name="prescription" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label for="medicines" class="form-label">Medicine Name and Quantity</label>
                        <textarea id="medicines" name="medicines" class="form-control" rows="5" placeholder="e.g., Paracetamol 500mg - 1 strip"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="address" class="form-label">Delivery Address</label>
                        <textarea id="address" name="address" class="form-control" rows="3" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="phone" class="form-label">Phone Number</label>
                        <input type="tel" id="phone" name="phone" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Place Order</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
