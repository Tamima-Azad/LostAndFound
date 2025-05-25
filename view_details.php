<?php
// filepath: c:\xampp\htdocs\findit\view_details.php
session_start();
require 'config/database.php';

// Get item type and id from query string
$type = isset($_GET['type']) && in_array($_GET['type'], ['lost', 'found']) ? $_GET['type'] : '';
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (!$type || !$id) {
    echo "<div class='alert alert-danger'>Invalid item details.</div>";
    exit();
}

// Fetch item details
if ($type === 'lost') {
    $stmt = $pdo->prepare("SELECT l.*, u.id AS owner_id, u.name AS user_name, u.contact_no, u.email FROM lost_items l JOIN users u ON l.user_id = u.id WHERE l.id = ?");
} else {
    $stmt = $pdo->prepare("SELECT f.*, u.id AS owner_id, u.name AS user_name, u.contact_no, u.email FROM found_items f JOIN users u ON f.user_id = u.id WHERE f.id = ?");
}
$stmt->execute([$id]);
$item = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$item) {
    echo "<div class='alert alert-danger'>Item not found.</div>";
    exit();
}

// Fetch claim status from claims table (if any claim exists for this item)
$claimStatus = null;
$claimStmt = $pdo->prepare("SELECT status FROM claims WHERE item_id = ? AND item_type = ? ORDER BY created_at DESC LIMIT 1");
$claimStmt->execute([$id, $type]);
$claimRow = $claimStmt->fetch(PDO::FETCH_ASSOC);
if ($claimRow) {
    $claimStatus = strtolower($claimRow['status']);
} else {
    $claimStatus = strtolower($item['status']);
}

// Image path
$image_base_path = 'uploads/';
$image1 = !empty($item['image1']) ? $image_base_path . htmlspecialchars($item['image1']) : 'https://via.placeholder.com/350x220?text=No+Image';
$image2 = !empty($item['image2']) ? $image_base_path . htmlspecialchars($item['image2']) : '';

// Check if current user is the owner
$isOwner = isset($_SESSION['user_id']) && $_SESSION['user_id'] == $item['owner_id'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Item Details - FindIt</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body { background: #f8f9fa; }
        .details-container {
            max-width: 700px;
            margin: 40px auto;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 16px rgba(0,0,0,0.09);
            padding: 30px;
        }
        .details-img {
            width: 100%;
            max-height: 320px;
            object-fit: cover;
            border-radius: 8px;
            margin-bottom: 18px;
        }
        .details-label {
            font-weight: bold;
            color: #007bff;
        }
        .details-value {
            color: #333;
        }
        .badge-success {
            background: #28a745;
            color: #fff;
        }
        .badge-warning {
            background: #ffc107;
            color: #222;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="details-container">
        <h2 class="mb-4"><?= htmlspecialchars($item['item_name']) ?></h2>
        <img src="<?= $image1 ?>" alt="Item Image" class="details-img">
        <?php if ($image2): ?>
            <img src="<?= $image2 ?>" alt="Item Image 2" class="details-img">
        <?php endif; ?>
        <table class="table table-borderless">
            <tr>
                <td class="details-label">Category:</td>
                <td class="details-value"><?= htmlspecialchars($item['category']) ?></td>
            </tr>
            <tr>
                <td class="details-label">Description:</td>
                <td class="details-value"><?= nl2br(htmlspecialchars($item['description'])) ?></td>
            </tr>
            <tr>
                <td class="details-label">Location:</td>
                <td class="details-value"><?= htmlspecialchars($item['location']) ?></td>
            </tr>
            <tr>
                <td class="details-label"><?= $type === 'lost' ? 'Lost Date:' : 'Found Date:' ?></td>
                <td class="details-value"><?= htmlspecialchars($item['date']) ?></td>
            </tr>
            <?php if ($type === 'found'): ?>
                <tr>
                    <td class="details-label">Found Area:</td>
                    <td class="details-value"><?= htmlspecialchars($item['found_area']) ?></td>
                </tr>
                <tr>
                    <td class="details-label">Found City:</td>
                    <td class="details-value"><?= htmlspecialchars($item['found_city']) ?></td>
                </tr>
                <tr>
                    <td class="details-label">Found State:</td>
                    <td class="details-value"><?= htmlspecialchars($item['found_state']) ?></td>
                </tr>
                <tr>
                    <td class="details-label">Kept Address:</td>
                    <td class="details-value"><?= htmlspecialchars($item['kept_address']) ?></td>
                </tr>
                <tr>
                    <td class="details-label">Kept City:</td>
                    <td class="details-value"><?= htmlspecialchars($item['kept_city']) ?></td>
                </tr>
                <tr>
                    <td class="details-label">Kept State:</td>
                    <td class="details-value"><?= htmlspecialchars($item['kept_state']) ?></td>
                </tr>
                <tr>
                    <td class="details-label">Kept Contact:</td>
                    <td class="details-value"><?= htmlspecialchars($item['kept_contact']) ?></td>
                </tr>
            <?php endif; ?>
            <tr>
                <td class="details-label">Posted By:</td>
                <td class="details-value"><?= htmlspecialchars($item['user_name']) ?></td>
            </tr>
            <tr>
                <td class="details-label">Contact No:</td>
                <td class="details-value"><?= htmlspecialchars($item['contact_no']) ?></td>
            </tr>
            <tr>
                <td class="details-label">Email:</td>
                <td class="details-value"><?= htmlspecialchars($item['email']) ?></td>
            </tr>
            <tr>
                <td class="details-label">Status:</td>
                <td class="details-value">
                    <?php if ($claimStatus === 'claimed'): ?>
                        <span class="badge badge-success">Claimed</span>
                    <?php elseif ($claimStatus === 'rejected'): ?>
                        <span class="badge badge-danger">Rejected</span>
                    <?php else: ?>
                        <span class="badge badge-warning">Unclaimed</span>
                    <?php endif; ?>
                </td>
            </tr>
        </table>
        <div class="mt-4">
            <a href="browsingItem.php" class="btn btn-secondary">Back to Browse</a>
            <?php if (
                $type === 'found' &&
                $claimStatus === 'unclaimed' &&
                isset($_SESSION['user_id']) &&
                !$isOwner
            ): ?>
                <a href="claim_item.php?type=<?= $type ?>&id=<?= $id ?>" class="btn btn-primary ml-2">
                    <i class="fas fa-handshake mr-1"></i> Claim This Item
                </a>
            <?php elseif ($type === 'found' && $claimStatus === 'unclaimed' && !isset($_SESSION['user_id'])): ?>
                <a href="login.php?redirect=view_details.php?type=<?= $type ?>&id=<?= $id ?>" class="btn btn-primary ml-2">
                    <i class="fas fa-sign-in-alt mr-1"></i> Claim This Item
                </a>
            <?php endif; ?>
        </div>
    </div>
</div>
<!-- FontAwesome for icons (optional) -->
<script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</body>
</html>