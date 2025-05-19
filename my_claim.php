<?php
session_start();
require 'config/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
$user_id = $_SESSION['user_id'];

// Fetch all claims made by this user
$stmt = $pdo->prepare("
    SELECT c.*, 
        IF(c.item_type='lost', l.item_name, f.item_name) AS item_name,
        IF(c.item_type='lost', l.category, f.category) AS category,
        IF(c.item_type='lost', l.date, f.date) AS item_date,
        IF(c.item_type='lost', l.status, f.status) AS item_status
    FROM claims c
    LEFT JOIN lost_items l ON c.item_type='lost' AND c.item_id=l.id
    LEFT JOIN found_items f ON c.item_type='found' AND c.item_id=f.id
    WHERE c.user_id = ?
    ORDER BY c.created_at DESC
");
$stmt->execute([$user_id]);
$claims = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Claims - FindIt</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body { background: #f8f9fa; }
        .claims-container { max-width: 900px; margin: 40px auto; }
        .claim-card { background: #fff; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.07); padding: 24px; margin-bottom: 24px; }
        .badge-new { background: #007bff; }
        .badge-claimed { background: #28a745; }
        .badge-rejected { background: #dc3545; }
    </style>
</head>
<body>
<div class="claims-container">
    <h2 class="mb-4">My Claims</h2>
    <a href="dashboard.php" class="btn btn-secondary mb-4">&larr; Back to Dashboard</a>
    <?php if ($claims): ?>
        <?php foreach ($claims as $claim): ?>
            <div class="claim-card">
                <h5><?= htmlspecialchars($claim['item_name']) ?> <small class="text-muted">(<?= htmlspecialchars($claim['category']) ?>)</small></h5>
                <p>
                    <strong>Type:</strong> <?= htmlspecialchars(ucfirst($claim['item_type'])) ?><br>
                    <strong>Date:</strong> <?= htmlspecialchars(date('M d, Y', strtotime($claim['item_date']))) ?><br>
                    <strong>Status:</strong>
                    <span class="badge badge-pill badge-<?= strtolower($claim['status']) ?>">
                        <?= htmlspecialchars($claim['status']) ?>
                    </span>
                </p>
                <p><strong>Claimed At:</strong> <?= date('M d, Y H:i', strtotime($claim['created_at'])) ?></p>
                <a href="view_details.php?type=<?= $claim['item_type'] ?>&id=<?= $claim['item_id'] ?>" class="btn btn-info btn-sm">View Details</a>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="alert alert-info text-center">You have not made any claims yet.</div>
    <?php endif; ?>
</div>
</body>
</html>