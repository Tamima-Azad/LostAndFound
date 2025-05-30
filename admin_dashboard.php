<?php
session_start();
require 'config/database.php';

// (Optional) Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

// Fetch dashboard stats
$totalUsers = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$totalFoundItems = $pdo->query("SELECT COUNT(*) FROM found_items")->fetchColumn();
$totalClaims = $pdo->query("SELECT COUNT(*) FROM claims")->fetchColumn();
$totalNewRequests = $pdo->query("SELECT COUNT(*) FROM claims WHERE status = 'New'")->fetchColumn();
$totalInProcess = $pdo->query("SELECT COUNT(*) FROM claims WHERE status = 'In-Process'")->fetchColumn();
$totalClaimed = $pdo->query("SELECT COUNT(*) FROM claims WHERE status = 'Claimed'")->fetchColumn();
$totalRejected = $pdo->query("SELECT COUNT(*) FROM claims WHERE status = 'Rejected'")->fetchColumn();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - Lost And Found</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="admin.css">
</head>
<body>
    <nav class="navbar navbar-dark bg-dark">
        <a class="navbar-brand" href="#">Admin Dashboard</a>
        <span class="navbar-text text-white">
            <a href="admin_profile.php" class="text-white mr-3"><i class="fas fa-user-cog"></i> Profile</a>
            <a href="logout.php" class="text-white"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </span>
    </nav>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav class="col-md-2 d-none d-md-block sidebar">
                <div class="sidebar-sticky pt-3">
                    <ul class="nav flex-column">
                        <li class="nav-item"><a class="nav-link active" href="admin_dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                        <li class="nav-item"><a class="nav-link" href="admin_found_items.php"><i class="fas fa-search"></i> Listed Found Items</a></li>
                        <li class="nav-item"><a class="nav-link" href="admin_claims.php"><i class="fas fa-hand-holding"></i> Claims</a></li>
                        <li class="nav-item"><a class="nav-link" href="admin_report.php"><i class="fas fa-chart-bar"></i> Report</a></li>
                        <li class="nav-item"><a class="nav-link" href="admin_bw_users.php"><i class="fas fa-users"></i> B/W Reg Users</a></li>
                        <li class="nav-item"><a class="nav-link" href="admin_bw_claims.php"><i class="fas fa-file-alt"></i> B/W Dates Claim Request</a></li>
                        <li class="nav-item"><a class="nav-link" href="admin_about.php"><i class="fas fa-info-circle"></i> About Us</a></li>
                        <li class="nav-item"><a class="nav-link" href="admin_contact.php"><i class="fas fa-envelope"></i> Contact Us</a></li>
                        <li class="nav-item"><a class="nav-link" href="admin_profile.php"><i class="fas fa-user-cog"></i> Profile</a></li>
                        <li class="nav-item"><a class="nav-link" href="admin_change_password.php"><i class="fas fa-key"></i> Change Password</a></li>
                        <li class="nav-item"><a class="nav-link" href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                    </ul>
                </div>
            </nav>

            <!-- Main Content -->
            <main class="col-md-10 ml-sm-auto px-4">
                <div class="pt-4 pb-2 mb-3 border-bottom">
                    <h2>Dashboard Overview</h2>
                </div>
                <div class="row">
                    <div class="col-md-3">
                        <div class="dashboard-card text-center">
                            <h4>Total Registered Users</h4>
                            <div class="stat"><?= $totalUsers ?></div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="dashboard-card text-center">
                            <h4>Total Listed Found Items</h4>
                            <div class="stat"><?= $totalFoundItems ?></div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="dashboard-card text-center">
                            <h4>Total Claim Requests</h4>
                            <div class="stat"><?= $totalClaims ?></div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="dashboard-card text-center">
                            <h4>New Requests</h4>
                            <div class="stat"><?= $totalNewRequests ?></div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="dashboard-card text-center">
                            <h4>In-Process</h4>
                            <div class="stat"><?= $totalInProcess ?></div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="dashboard-card text-center">
                            <h4>Claimed Items</h4>
                            <div class="stat"><?= $totalClaimed ?></div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="dashboard-card text-center">
                            <h4>Rejected Requests</h4>
                            <div class="stat"><?= $totalRejected ?></div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
</body>
</html>
