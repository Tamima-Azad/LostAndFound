<?php
session_start();
require 'config/database.php'; // Adjust path as needed

$msg = '';
if (isset($_GET['msg'])) {
    if ($_GET['msg'] == 'claim_success') $msg = '<div class="alert alert-success">Claim request sent successfully!</div>';
    if ($_GET['msg'] == 'already_claimed') $msg = '<div class="alert alert-warning">You have already claimed this item.</div>';
    if ($_GET['error'] == 'invalid') $msg = '<div class="alert alert-danger">Invalid claim request.</div>';
}

// Get filters from GET
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$category = isset($_GET['category']) ? trim($_GET['category']) : '';
$type = isset($_GET['type']) ? trim($_GET['type']) : '';
$status = isset($_GET['status']) ? trim($_GET['status']) : '';

// Prepare queries for both tables
$conditions = [];
$params = [];

// Search filter
if ($search !== '') {
    $conditions[] = "(item_name LIKE ? OR category LIKE ? OR location LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
}
// Category filter
if ($category !== '') {
    $conditions[] = "category = ?";
    $params[] = $category;
}
// Status filter
if ($status !== '') {
    $conditions[] = "status = ?";
    $params[] = $status;
}

// LOST ITEMS
$lost_sql = "SELECT *, 'lost' AS type FROM lost_items";
if ($conditions) $lost_sql .= " WHERE " . implode(" AND ", $conditions);

// FOUND ITEMS
$found_sql = "SELECT *, 'found' AS type FROM found_items";
if ($conditions) $found_sql .= " WHERE " . implode(" AND ", $conditions);

// Fetch data
$lost_stmt = $pdo->prepare($lost_sql);
$lost_stmt->execute($params);
$lost_items = $lost_stmt->fetchAll(PDO::FETCH_ASSOC);

$found_stmt = $pdo->prepare($found_sql);
$found_stmt->execute($params);
$found_items = $found_stmt->fetchAll(PDO::FETCH_ASSOC);

// Merge and filter by type if needed
$all_items = array_merge($lost_items, $found_items);
if ($type !== '') {
    $all_items = array_filter($all_items, function($item) use ($type) {
        return $item['type'] === $type;
    });
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lost and Found - Browse Items</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            font-family: 'Nunito', sans-serif;
            background: linear-gradient(135deg, #fdfcfc 0%, #e2e1e1 100%);
            color: #333;
            line-height: 1.6;
        }
        nav.navbar {
            background-color: #2c3e50;
            color: #fff !important;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        nav.navbar .navbar-brand {
            color: #fff;
            font-weight: bold;
        }
        .browse-container {
            max-width: 1200px;
            margin: 40px auto 0 auto;
            padding: 20px;
        }
        .search-filter-container {
            margin-bottom: 30px;
            padding: 25px 30px;
            background-color: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.07);
        }
        .search-filter-container label {
            font-weight: bold;
            color: #555;
        }
        .search-filter-container .form-control {
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 10px;
            font-size: 1rem;
            transition: border-color 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
        }
        .search-filter-container .form-control:focus {
            border-color: #00bfa5;
            box-shadow: 0 0 0 0.2rem rgba(0, 191, 165, 0.25);
        }
        .search-filter-container .btn-primary {
            background-color: #00bfa5;
            border-color: #00bfa5;
            transition: background-color 0.3s ease;
            color: #fff;
        }
        .search-filter-container .btn-primary:hover {
            background-color: #00897b;
            border-color: #00897b;
        }
        .item-card {
            margin-bottom: 30px;
            border-radius: 14px;
            box-shadow: 0 6px 20px rgba(0, 191, 165, 0.10);
            overflow: hidden;
            background: #fff;
            transition: transform 0.25s ease-in-out, box-shadow 0.25s ease-in-out;
            display: flex;
            flex-direction: column;
            height: 100%; /* Ensures cards in the same row have the same height */
        }
        .item-card:hover {
            transform: translateY(-8px) scale(1.02);
            box-shadow: 0 12px 32px rgba(0, 191, 165, 0.20);
        }
        .item-card img {
            width: 100%;
            height: 210px;
            object-fit: cover;
            background: #f4f4f4; /* Placeholder background if image is missing */
        }
        .item-details {
            padding: 20px 18px 18px 18px;
            flex: 1 1 auto; /* Allows this section to grow and fill available space */
            display: flex;
            flex-direction: column;
            justify-content: space-between; /* Pushes buttons to the bottom */
        }
        .item-details-content {
            margin-bottom: 15px; /* Space between content and buttons */
        }
        .item-details h5 {
            margin-bottom: 12px;
            font-weight: bold;
            color: #00bfa5;
            font-size: 1.18rem;
        }
        .item-details p {
            margin-bottom: 7px;
            color: #555;
            font-size: 0.97rem;
        }
        .item-details .badge {
            font-size: 0.85rem;
            font-weight: normal;
            margin-right: 7px;
            padding: 6px 12px;
        }
        .item-details .item-actions .btn-sm {
            font-size: 0.92rem;
            padding: 7px 16px;
            border-radius: 5px;
            margin-top: 8px;
            margin-right: 6px;
        }
        .item-details .btn-outline-info {
            color: #00bfa5;
            border-color: #00bfa5;
        }
        .item-details .btn-outline-info:hover {
            background: #00bfa5;
            color: #fff;
        }
        .no-items {
            text-align: center;
            color: #888;
            margin-top: 50px;
            font-size: 1.2rem;
            width: 100%;
        }
        @media (max-width: 767px) {
            .browse-container {
                padding: 10px;
                 margin-top: 70px;
            }
            .search-filter-container {
                padding: 20px 15px;
            }
            .item-card img {
                height: 180px;
            }
            .search-filter-container .form-row .form-group {
                margin-bottom: 10px !important;
            }
            .search-filter-container .btn-block {
                margin-top: 10px;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <a class="navbar-brand" href="index.php">Lost and Found</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item<?php if(basename($_SERVER['PHP_SELF']) == 'index.php') echo ' active'; ?>">
                    <a class="nav-link" href="index.php">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Exchanged Item</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo isset($_SESSION['user_id']) ? 'lost/foundItem.php' : 'login.php'; ?>">
                        Post Lost/Found Item
                    </a>
                </li>
                <li class="nav-item<?php if(basename($_SERVER['PHP_SELF']) == 'browsingItem.php') echo ' active'; ?>">
                    <a class="nav-link" href="browsingItem.php">Browse Items</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="about_us.php">About Us</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="contact.php">Contact Us</a>
                </li>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="dashboard.php">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">Logout</a>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link" href="login.php">Login/Signup</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>

    <div class="browse-container" style="margin-top: 80px;">
        <?= $msg ?>
        <div class="search-filter-container">
            <form method="GET" action="browsingItem.php" class="mb-4">
                <div class="form-row align-items-end">
                    <div class="form-group col-md-4 mb-2">
                        <label for="searchBar">Search</label>
                        <input type="text" class="form-control" id="searchBar" name="search" placeholder="Search by name, category, location..." value="<?= htmlspecialchars($search) ?>">
                    </div>
                    <div class="form-group col-md-2 mb-2">
                        <label for="searchCategory">Category</label>
                        <select class="form-control" id="searchCategory" name="category">
                            <option value="">All Categories</option>
                            <option value="electronics" <?= $category=='electronics'?'selected':''; ?>>Electronics</option>
                            <option value="documents" <?= $category=='documents'?'selected':''; ?>>Documents</option>
                            <option value="personal" <?= $category=='personal'?'selected':''; ?>>Personal Items</option>
                            <option value="other" <?= $category=='other'?'selected':''; ?>>Other</option>
                        </select>
                    </div>
                    <div class="form-group col-md-2 mb-2">
                        <label for="searchType">Type</label>
                        <select class="form-control" id="searchType" name="type">
                            <option value="">All Types</option>
                            <option value="lost" <?= $type=='lost'?'selected':''; ?>>Lost</option>
                            <option value="found" <?= $type=='found'?'selected':''; ?>>Found</option>
                        </select>
                    </div>
                    <div class="form-group col-md-2 mb-2">
                        <label for="searchClaimed">Status</label>
                        <select class="form-control" id="searchClaimed" name="status">
                            <option value="">All Status</option>
                            <option value="unclaimed" <?= $status=='unclaimed'?'selected':''; ?>>Unclaimed</option>
                            <option value="claimed" <?= $status=='claimed'?'selected':''; ?>>Claimed</option>
                        </select>
                    </div>
                    <div class="form-group col-md-2 mb-2">
                        <button type="submit" class="btn btn-primary btn-block">
                            <i class="fas fa-search mr-2"></i> Search
                        </button>
                    </div>
                </div>
            </form>
        </div>
        
        <div class="row" id="itemsRow">
            <?php if (empty($all_items)): ?>
                <div class="col-12">
                    <p class="no-items">No items have been listed yet.</p>
                </div>
            <?php else: ?>
                <?php foreach ($all_items as $item): ?>
                    <?php
                        $type = $item['type'];
                        $claimedStatus = strtolower($item['status']);
                        $itemId = $item['id'];
                        // Only show claim button if user is not the owner
                        $isOwner = isset($_SESSION['user_id']) && (
                            ($type === 'lost' && isset($item['user_id']) && $item['user_id'] == $_SESSION['user_id']) ||
                            ($type === 'found' && isset($item['user_id']) && $item['user_id'] == $_SESSION['user_id'])
                        );
                    ?>
                    <div class="col-md-4 d-flex item-col">
                        <div class="item-card w-100">
                            <div class="item-details">
                                <div class="item-details-content">
                                    <h5><?= htmlspecialchars($item['item_name']) ?></h5>
                                    <p><i class="fas fa-tag mr-2"></i> Category: <?= htmlspecialchars($item['category']) ?></p>
                                    <p><i class="fas fa-question-circle mr-2"></i> Type: <?= htmlspecialchars(ucfirst($type)) ?></p>
                                    <p><i class="fas fa-map-marker-alt mr-2"></i> Location: <?= htmlspecialchars($item['location']) ?></p>
                                    <p><i class="far fa-calendar-alt mr-2"></i> Status: 
                                        <?php if ($claimedStatus === 'claimed'): ?>
                                            <span class="badge badge-success">Claimed</span>
                                        <?php else: ?>
                                            <span class="badge badge-warning text-dark">Unclaimed</span>
                                        <?php endif; ?>
                                    </p>
                                </div>
                                <div class="item-actions">
                                    <!-- View Details button -->
                                    <a href="<?php
                                        if (isset($_SESSION['user_id'])) {
                                            echo "view_details.php?type=$type&id=$itemId";
                                        } else {
                                            echo "login.php?redirect=" . urlencode("view_details.php?type=$type&id=$itemId");
                                        }
                                    ?>" class="btn btn-sm btn-outline-info">
                                        <i class="fas fa-eye mr-1"></i> View Details
                                    </a>
                                    <!-- Claim button: only for found & unclaimed & not owner -->
                                    <?php if ($type === 'found' && $claimedStatus === 'unclaimed' && !$isOwner): ?>
                                        <?php if (isset($_SESSION['user_id'])): ?>
                                            <a href="claim_item.php?type=found&id=<?= $itemId ?>" class="btn btn-sm btn-primary">
                                                <i class="fas fa-handshake mr-1"></i> Claim
                                            </a>
                                        <?php else: ?>
                                            <a href="login.php?redirect=<?= urlencode("claim_item.php?type=found&id=$itemId") ?>" class="btn btn-sm btn-primary">
                                                <i class="fas fa-sign-in-alt mr-1"></i> Claim
                                            </a>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                    <!-- Claim button: only for lost & unclaimed & not owner -->
                                    <?php if ($type === 'lost' && $claimedStatus === 'unclaimed' && !$isOwner): ?>
                                        <?php if (isset($_SESSION['user_id'])): ?>
                                            <a href="claim_item.php?type=lost&id=<?= $itemId ?>" class="btn btn-sm btn-warning">
                                                <i class="fas fa-handshake mr-1"></i> Claim
                                            </a>
                                        <?php else: ?>
                                            <a href="login.php?redirect=<?= urlencode("claim_item.php?type=lost&id=$itemId") ?>" class="btn btn-sm btn-warning">
                                                <i class="fas fa-sign-in-alt mr-1"></i> Claim
                                            </a>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>