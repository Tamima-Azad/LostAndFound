<?php
<?php
require 'config/database.php';
session_start();

// Use session user_id for real login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
$user_id = $_SESSION['user_id'];

// Fetch recent lost items
$lostItemsStmt = $pdo->prepare("SELECT * FROM lost_items WHERE user_id = ? ORDER BY created_at DESC LIMIT 5");
$lostItemsStmt->execute([$user_id]);
$lostItems = $lostItemsStmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch recent found items
$foundItemsStmt = $pdo->prepare("SELECT * FROM found_items WHERE user_id = ? ORDER BY created_at DESC LIMIT 5");
$foundItemsStmt->execute([$user_id]);
$foundItems = $foundItemsStmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch recent exchanged items (example table: exchanged_items)
$exchangedItemsStmt = $pdo->prepare("SELECT * FROM exchanged_items WHERE user_id = ? ORDER BY created_at DESC LIMIT 5");
$exchangedItemsStmt->execute([$user_id]);
$exchangedItems = $exchangedItemsStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard - FindIt</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            font-family: 'Montserrat', sans-serif;
            line-height: 1.6;
            background-color: #f8f9fa;
        }
        .sidebar {
            background-color: #343a40;
            color: white;
            padding-top: 20px;
            min-height: 100vh;
        }
        .sidebar a {
            padding: 10px 20px;
            display: block;
            color: white;
            text-decoration: none;
            transition: background-color 0.3s ease;
        }
        .sidebar a:hover {
            background-color: #495057;
        }
        .content {
            padding: 20px;
        }
        .dashboard-card {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin-bottom: 20px;
        }
        .dashboard-card h3 {
            color: #007bff;
            margin-bottom: 15px;
        }
        .item-list {
            list-style: none;
            padding: 0;
        }
        .item-list li {
            border-bottom: 1px solid #eee;
            padding: 10px 0;
        }
        .item-list li:last-child {
            border-bottom: none;
        }
        .item-actions a {
            margin-left: 10px;
            color: #007bff;
            text-decoration: none;
        }
        .item-actions a:hover {
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
                                <i class="fas fa-home"></i> Overview <span class="sr-only">(current)</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">
                                <i class="fas fa-plus-circle"></i> Post New Lost Item
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">
                                <i class="fas fa-bullhorn"></i> Post New Found Item
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">
                                <i class="fas fa-exchange-alt"></i> Exchanged Items
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">
                                <i class="fas fa-list-alt"></i> My Listings
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">
                                <i class="fas fa-envelope"></i> Messages
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">
                                <i class="fas fa-user-cog"></i> Account Settings
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="logout.php">
                                <i class="fas fa-sign-out-alt"></i> Logout
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>

            <main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">User Dashboard</h1>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="dashboard-card">
                            <h3>Your Recent Lost Items</h3>
                            <ul class="item-list">
                                <?php foreach ($lostItems as $item): ?>
                                    <li>
                                        <?= htmlspecialchars($item['item_name']) ?> (Lost on <?= htmlspecialchars($item['lost_date']) ?>)
                                        <span class="item-actions">
                                            <a href="#">View</a> | <a href="#">Edit</a>
                                        </span>
                                    </li>
                                <?php endforeach; ?>
                                <?php if (empty($lostItems)): ?>
                                    <li>No lost items found.</li>
                                <?php endif; ?>
                            </ul>
                            <div class="text-right">
                                <a href="#">View All Your Lost Items</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="dashboard-card">
                            <h3>Your Recent Found Items</h3>
                            <ul class="item-list">
                                <?php foreach ($foundItems as $item): ?>
                                    <li>
                                        <?= htmlspecialchars($item['item_name']) ?> (Found on <?= htmlspecialchars($item['found_date']) ?>)
                                        <span class="item-actions">
                                            <a href="#">View</a> | <a href="#">Mark as Claimed</a>
                                        </span>
                                    </li>
                                <?php endforeach; ?>
                                <?php if (empty($foundItems)): ?>
                                    <li>No found items found.</li>
                                <?php endif; ?>
                            </ul>
                            <div class="text-right">
                                <a href="#">View All Your Found Items</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="dashboard-card">
                            <h3>Your Recent Exchanged Items</h3>
                            <ul class="item-list">
                                <?php foreach ($exchangedItems as $item): ?>
                                    <li>
                                        <?= htmlspecialchars($item['item_name']) ?> (Exchanged on <?= htmlspecialchars($item['exchanged_date']) ?>)
                                        <span class="item-actions">
                                            <a href="#">View</a>
                                        </span>
                                    </li>
                                <?php endforeach; ?>
                                <?php if (empty($exchangedItems)): ?>
                                    <li>No exchanged items found.</li>
                                <?php endif; ?>
                            </ul>
                            <div class="text-right">
                                <a href="#">View All Your Exchanged Items</a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="dashboard-card">
                    <h3>Messages</h3>
                    <p>You have no new messages.</p>
                    <div class="text-right">
                        <a href="#">View All Messages</a>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>