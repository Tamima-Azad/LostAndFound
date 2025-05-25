<?php
require 'config/database.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
$user_id = $_SESSION['user_id'];

// Fetch recent lost items for this user
$lostItemsStmt = $pdo->prepare("SELECT * FROM lost_items WHERE user_id = ? ORDER BY created_at DESC LIMIT 5");
$lostItemsStmt->execute([$user_id]);
$lostItems = $lostItemsStmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch recent found items for this user
$foundItemsStmt = $pdo->prepare("SELECT * FROM found_items WHERE user_id = ? ORDER BY created_at DESC LIMIT 5");
$foundItemsStmt->execute([$user_id]);
$foundItems = $foundItemsStmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch recent claims for this user (with item name)
$claimsStmt = $pdo->prepare("
    SELECT c.*, 
        IF(c.item_type='lost', l.item_name, f.item_name) AS item_name 
    FROM claims c
    LEFT JOIN lost_items l ON c.item_type='lost' AND c.item_id=l.id
    LEFT JOIN found_items f ON c.item_type='found' AND c.item_id=f.id
    WHERE c.user_id = ?
    ORDER BY c.created_at DESC LIMIT 5
");
$claimsStmt->execute([$user_id]);
$claims = $claimsStmt->fetchAll(PDO::FETCH_ASSOC);

// Dashboard stats for this user
$totalFoundItems = $pdo->prepare("SELECT COUNT(*) FROM found_items WHERE user_id = ?");
$totalFoundItems->execute([$user_id]);
$totalFoundItems = $totalFoundItems->fetchColumn();

$totalNewRequests = $pdo->prepare("SELECT COUNT(*) FROM claims WHERE user_id = ? AND status = 'New'");
$totalNewRequests->execute([$user_id]);
$totalNewRequests = $totalNewRequests->fetchColumn();

$totalClaimedRequests = $pdo->prepare("SELECT COUNT(*) FROM claims WHERE user_id = ? AND status = 'Claimed'");
$totalClaimedRequests->execute([$user_id]);
$totalClaimedRequests = $totalClaimedRequests->fetchColumn();

$totalRejectedRequests = $pdo->prepare("SELECT COUNT(*) FROM claims WHERE user_id = ? AND status = 'Rejected'");
$totalRejectedRequests->execute([$user_id]);
$totalRejectedRequests = $totalRejectedRequests->fetchColumn();

$totalUserClaims = $pdo->prepare("SELECT COUNT(*) FROM claims WHERE user_id = ?");
$totalUserClaims->execute([$user_id]);
$totalUserClaims = $totalUserClaims->fetchColumn();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard - Lost And Found</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <style>
        body {
            font-family: 'Nunito', sans-serif; /* More modern font */
            line-height: 1.6;
            background: linear-gradient(135deg, #fdfcfc 0%, #e2e1e1 100%); /* Soft gradient background */
            color: #333; /* Darker text for better readability */
        }

        .sidebar {
            background-color: #2c3e50; /* Darker sidebar */
            color: #fff;
            padding-top: 30px;
            min-height: 100vh;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1); /* Subtle sidebar shadow */
        }

        .sidebar .sidebar-heading {
            padding: 0.875rem 1.25rem;
            font-size: 1rem;
            color: #adb5bd; /* Lighter heading text */
        }

        .sidebar a {
            padding: 12px 20px; /* Increased padding */
            display: block;
            color: #fff;
            text-decoration: none;
            transition: background-color 0.3s ease, color 0.3s ease; /* Smooth transitions */
        }

        .sidebar a:hover {
            background-color: rgba(255, 255, 255, 0.1); /* Lighter hover effect */
            color: #00bfa5; /* Accent color on hover */
        }

        .sidebar .nav-link.active {
            background-color: #00bfa5; /* Active link background */
            color: #fff;
            border-left: 5px solid #fff; /* Highlight active link */
        }

        .main-content {
            padding: 30px;
        }

        .dashboard-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            color: #555;
        }

        .dashboard-header h1 {
            font-size: 2.2rem;
            color: #333;
        }

        .dashboard-card {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05); /* Softer card shadow */
            padding: 25px;
            margin-bottom: 25px;
            border-left: 5px solid #00bfa5; /* Accent border */
            transition: transform 0.2s ease-in-out; /* Subtle hover animation */
        }

        .dashboard-card:hover {
            transform: translateY(-3px);
        }

        .dashboard-card h3 {
            color: #00bfa5;
            margin-bottom: 20px;
            font-size: 1.5rem;
        }

        .item-list {
            list-style: none;
            padding: 0;
        }

        .item-list li {
            border-bottom: 1px solid #eee;
            padding: 15px 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: #666;
        }

        .item-list li:last-child {
            border-bottom: none;
        }

        .item-actions a {
            margin-left: 15px;
            color: #00bfa5;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .item-actions a:hover {
            color: #00897b;
            text-decoration: underline;
        }

        .stat-card {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05);
            padding: 25px;
            margin-bottom: 25px;
            text-align: center;
            border-left: 5px solid #f39c12; /* Different accent color for stats */
            transition: transform 0.2s ease-in-out;
        }

        .stat-card:hover {
            transform: translateY(-3px);
        }

        .stat-card h4 {
            color: #f39c12;
            font-size: 1.2rem;
            margin-bottom: 10px;
        }

        .stat {
            font-size: 2.5rem;
            font-weight: bold;
            color: #333;
        }

        .text-right a {
            color: #00bfa5;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .text-right a:hover {
            color: #00897b;
            text-decoration: underline;
        }
    </style>
</head>
<body>

    <div class="container-fluid">
        <div class="row">
            <nav id="sidebar" class="col-md-3 col-lg-2 d-md-block sidebar">
                <div class="sidebar-sticky">
                    <h4 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted">
                        <span>Dashboard</span>
                    </h4>
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link active" href="#">
                                <i class="fas fa-home mr-2"></i> Overview <span class="sr-only">(current)</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="exchange_items.php">
                                <i class="fas fa-exchange-alt mr-2"></i> Exchanged Items
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="post_exchange.php">
                                <i class="fas fa-plus-circle mr-2"></i> Post for Exchange
                            </a>
                        </li>
                        
                        <li class="nav-item">
                            <a class="nav-link" href="lost/foundItem.php">
                                <i class="fas fa-plus-circle mr-2"></i> Post Lost/Found Item
                            </a>
                        </li>
                         <li class="nav-item">
                            <a class="nav-link" href="#">
                                <i class="fas fa-list-alt mr-2"></i> My Listings
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="browsingItem.php">
                                <i class="fas fa-search mr-2"></i> Browse All Found Item
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="item_claim_requests.php">
                                <i class="fas fa-tasks mr-2"></i> Item Claim Request
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="my_claim.php">
                                <i class="fas fa-clipboard-check mr-2"></i> My Claim
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">
                                <i class="fas fa-chart-bar mr-2"></i> Reports
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="forgot_password.php">
                                <i class="fas fa-key mr-2"></i> Change Password
                            </a>
                        </li>
                         <li class="nav-item">
                            <a class="nav-link" href="messages.php">
                                <i class="fas fa-envelope mr-2"></i> Messages
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="logout.php">
                                <i class="fas fa-sign-out-alt mr-2"></i> Logout
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="index.php">
                                <i class="fas fa-home mr-2"></i> Home Page
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>

            <main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-md-4 main-content">
                <div class="dashboard-header">
                    <h1>Welcome, <?= htmlspecialchars($_SESSION['user_name']) ?>!</h1>
                    <div></div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="stat-card">
                            <h4>Total Found Items</h4>
                            <div class="stat"><?= $totalFoundItems ?></div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stat-card">
                            <h4>New Requests</h4>
                            <div class="stat"><?= $totalNewRequests ?></div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stat-card">
                            <h4>Claimed Requests</h4>
                            <div class="stat"><?= $totalClaimedRequests ?></div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stat-card">
                            <h4>Rejected Requests</h4>
                            <div class="stat"><?= $totalRejectedRequests ?></div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stat-card">
                            <h4>Your Claim Requests</h4>
                            <div class="stat"><?= $totalUserClaims ?></div>
                        </div>
                    </div>
                    </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="dashboard-card">
                            <h3>Your Recent Lost Items</h3>
                            <ul class="item-list">
                                <?php foreach ($lostItems as $item): ?>
                                    <li>
                                        <?= htmlspecialchars($item['item_name']) ?> (Lost on <?= htmlspecialchars(date('M d, Y', strtotime($item['date']))) ?>)
                                        <span class="item-actions">
                                            <a href="view_details.php?type=lost&id=<?= $item['id'] ?>">View</a> | <a href="#">Edit</a>
                                        </span>
                                    </li>
                                <?php endforeach; ?>
                                <?php if (empty($lostItems)): ?>
                                    <li>No lost items found.</li>
                                <?php endif; ?>
                            </ul>
                            <div class="text-right mt-2">
                                <a href="browsingItem.php?type=lost">View All Your Lost Items</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="dashboard-card">
                            <h3>Your Recent Found Items</h3>
                            <ul class="item-list">
                                <?php foreach ($foundItems as $item): ?>
                                    <li>
                                        <?= htmlspecialchars($item['item_name']) ?> (Found on <?= htmlspecialchars(date('M d, Y', strtotime($item['date']))) ?>)
                                        <span class="item-actions">
                                            <a href="view_details.php?type=found&id=<?= $item['id'] ?>">View</a> | <a href="#">Mark as Claimed</a>
                                        </span>
                                    </li>
                                <?php endforeach; ?>
                                <?php if (empty($foundItems)): ?>
                                    <li>No found items found.</li>
                                <?php endif; ?>
                            </ul>
                            <div class="text-right mt-2">
                                <a href="browsingItem.php?type=found">View All Your Found Items</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="dashboard-card">
                            <h3>Your Recent Claims</h3>
                            <ul class="item-list">
                                <?php foreach ($claims as $claim): ?>
                                    <li>
                                        <?= htmlspecialchars($claim['item_name']) ?> (<span class="<?= strtolower($claim['status']) ?>"><?= htmlspecialchars($claim['status']) ?></span>)
                                        <span class="item-actions">
                                            <a href="view_details.php?type=<?= $claim['item_type'] ?>&id=<?= $claim['item_id'] ?>">View</a>
                                        </span>
                                    </li>
                                <?php endforeach; ?>
                                <?php if (empty($claims)): ?>
                                    <li>No claims found.</li>
                                <?php endif; ?>
                            </ul>
                            <div class="text-right mt-2">
                                <a href="item_claim_requests.php">View All Your Claims</a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="dashboard-card">
                    <h3>Messages</h3>
                    <p>You have no new messages.</p>
                    <div class="text-right mt-2">
                        <a href="messages.php">View All Messages</a>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        // Basic script to add a class based on claim status for styling
        document.addEventListener('DOMContentLoaded', function() {
            const statusSpans = document.querySelectorAll('.item-list li span');
            statusSpans.forEach(span => {
                span.classList.add(span.textContent.toLowerCase());
            });
        });
    </script>
</body>
</html>