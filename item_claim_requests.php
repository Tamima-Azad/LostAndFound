<?php
// filepath: c:\xampp\htdocs\findit\item_claim_requests.php
session_start();
require 'config/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
$user_id = $_SESSION['user_id'];

// Handle claim status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['claim_id'], $_POST['action'])) {
    $claim_id = intval($_POST['claim_id']);
    $action = $_POST['action'];
    if (in_array($action, ['Claimed', 'Rejected'])) {
        $updateStmt = $pdo->prepare("UPDATE claims SET status = ? WHERE id = ?");
        $updateStmt->execute([$action, $claim_id]);
        // Refresh to show updated status
        header("Location: item_claim_requests.php");
        exit();
    }
}

// Fetch claims for lost items posted by this user
$lostClaimsStmt = $pdo->prepare("
    SELECT c.*, u.name AS claimer_name, u.email AS claimer_email, l.item_name AS item_name, l.date AS item_date
    FROM claims c
    JOIN users u ON c.user_id = u.id
    JOIN lost_items l ON c.item_id = l.id
    WHERE c.item_type = 'lost' AND l.user_id = ?
    ORDER BY c.created_at DESC
");
$lostClaimsStmt->execute([$user_id]);
$lostClaims = $lostClaimsStmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch claims for found items posted by this user
$foundClaimsStmt = $pdo->prepare("
    SELECT c.*, u.name AS claimer_name, u.email AS claimer_email, f.item_name AS item_name, f.date AS item_date
    FROM claims c
    JOIN users u ON c.user_id = u.id
    JOIN found_items f ON c.item_id = f.id
    WHERE c.item_type = 'found' AND f.user_id = ?
    ORDER BY c.created_at DESC
");
$foundClaimsStmt->execute([$user_id]);
$foundClaims = $foundClaimsStmt->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Item Claim Requests - FindIt</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body { background: #f8f9fa; }
        .claim-table { background: #fff; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.07); }
        .claim-table th, .claim-table td { vertical-align: middle; }
        .badge-new { background: #007bff; }
        .badge-claimed { background: #28a745; }
        .badge-rejected { background: #dc3545; }
    </style>
</head>
<body>
<div class="container mt-5">
    <h2 class="mb-4">Item Claim Requests</h2>
    <div class="mb-4">
        <a href="dashboard.php" class="btn btn-secondary btn-sm">&larr; Back to Dashboard</a>
    </div>
    <div class="claim-table p-4 mb-5">
        <h4>Claims on Your Lost Items</h4>
        <table class="table table-bordered table-hover mt-3">
            <thead class="thead-light">
                <tr>
                    <th>Item Name</th>
                    <th>Lost Date</th>
                    <th>Claimed By</th>
                    <th>Status</th>
                    <th>Requested At</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
            <?php if (empty($lostClaims)): ?>
                <tr><td colspan="6" class="text-center text-muted">No claim requests for your lost items.</td></tr>
            <?php else: ?>
                <?php foreach ($lostClaims as $claim): ?>
                    <tr>
                        <td><?= htmlspecialchars($claim['item_name']) ?></td>
                        <td><?= htmlspecialchars($claim['item_date']) ?></td>
                        <td>
                            <?= htmlspecialchars($claim['claimer_name']) ?><br>
                            <small class="text-muted"><?= htmlspecialchars($claim['claimer_email']) ?></small>
                        </td>
                        <td>
                            <span class="badge badge-pill badge-<?= strtolower($claim['status']) ?>">
                                <?= htmlspecialchars($claim['status']) ?>
                            </span>
                        </td>
                        <td><?= htmlspecialchars($claim['created_at']) ?></td>
                        <td>
                            <?php if ($claim['status'] === 'New'): ?>
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="claim_id" value="<?= $claim['id'] ?>">
                                    <button type="submit" name="action" value="Claimed" class="btn btn-success btn-sm">Approve</button>
                                    <button type="submit" name="action" value="Rejected" class="btn btn-danger btn-sm">Reject</button>
                                </form>
                            <?php else: ?>
                                <span class="text-muted">No action</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
    <div class="claim-table p-4">
        <h4>Claims on Your Found Items</h4>
        <table class="table table-bordered table-hover mt-3">
            <thead class="thead-light">
                <tr>
                    <th>Item Name</th>
                    <th>Found Date</th>
                    <th>Claimed By</th>
                    <th>Status</th>
                    <th>Requested At</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
            <?php if (empty($foundClaims)): ?>
                <tr><td colspan="6" class="text-center text-muted">No claim requests for your found items.</td></tr>
            <?php else: ?>
                <?php foreach ($foundClaims as $claim): ?>
                    <tr>
                        <td><?= htmlspecialchars($claim['item_name']) ?></td>
                        <td><?= htmlspecialchars($claim['item_date']) ?></td>
                        <td>
                            <?= htmlspecialchars($claim['claimer_name']) ?><br>
                            <small class="text-muted"><?= htmlspecialchars($claim['claimer_email']) ?></small>
                        </td>
                        <td>
                            <span class="badge badge-pill badge-<?= strtolower($claim['status']) ?>">
                                <?= htmlspecialchars($claim['status']) ?>
                            </span>
                        </td>
                        <td><?= htmlspecialchars($claim['created_at']) ?></td>
                        <td>
                            <?php if ($claim['status'] === 'New'): ?>
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="claim_id" value="<?= $claim['id'] ?>">
                                    <button type="submit" name="action" value="Claimed" class="btn btn-success btn-sm">Approve</button>
                                    <button type="submit" name="action" value="Rejected" class="btn btn-danger btn-sm">Reject</button>
                                </form>
                            <?php else: ?>
                                <span class="text-muted">No action</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>