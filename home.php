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

<div class="container-fluid">
    <div class="row">
        <nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse">
            <div class="position-sticky pt-3">
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="#">
                            <i class="fas fa-home"></i>
                            Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="profile.php">
                            <i class="fas fa-user"></i>
                            Profile
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="order_history.php">
                            <i class="fas fa-history"></i>
                            Order History
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">
                            <i class="fas fa-sign-out-alt"></i>
                            Logout
                        </a>
                    </li>
                </ul>
            </div>
        </nav>

        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Dashboard</h1>
            </div>

            <h2>Our Services</h2>
            <div class="row">
                <div class="col-md-4 mb-4">
                    <div class="card h-100 feature-card">
                        <div class="card-body text-center">
                            <i class="fas fa-broom fa-3x text-primary mb-3"></i>
                            <h5 class="card-title">Home Cleaning</h5>
                            <a href="home_cleaning.php" class="btn btn-primary">Book Now</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card h-100 feature-card">
                        <div class="card-body text-center">
                            <i class="fas fa-wrench fa-3x text-primary mb-3"></i>
                            <h5 class="card-title">Plumber</h5>
                            <a href="plumber.php" class="btn btn-primary">Book Now</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card h-100 feature-card">
                        <div class="card-body text-center">
                            <i class="fas fa-bolt fa-3x text-primary mb-3"></i>
                            <h5 class="card-title">Electrician</h5>
                            <a href="electrician.php" class="btn btn-primary">Book Now</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card h-100 feature-card">
                        <div class="card-body text-center">
                            <i class="fas fa-burn fa-3x text-primary mb-3"></i>
                            <h5 class="card-title">Gas Delivery</h5>
                            <a href="gas_delivery.php" class="btn btn-primary">Order Now</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card h-100 feature-card">
                        <div class="card-body text-center">
                            <i class="fas fa-pills fa-3x text-primary mb-3"></i>
                            <h5 class="card-title">Medicine Delivery</h5>
                            <a href="medicine_delivery.php" class="btn btn-primary">Order Now</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card h-100 feature-card">
                        <div class="card-body text-center">
                            <i class="fas fa-newspaper fa-3x text-primary mb-3"></i>
                            <h5 class="card-title">Daily News</h5>
                            <a href="news.php" class="btn btn-primary">Read Now</a>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<?php include 'footer.php'; ?>