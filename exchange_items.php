<?php
// filepath: c:\xampp\htdocs\findit\exchange_item.php
session_start();
require 'config/database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
$user_id = $_SESSION['user_id'];

// Fetch all exchange items (do not show the user's own items)
$stmt = $pdo->prepare("SELECT e.*, u.name AS user_name, u.email AS user_email FROM exchange_items e JOIN users u ON e.user_id = u.id WHERE e.user_id != ? ORDER BY e.created_at DESC");
$stmt->execute([$user_id]);
$exchange_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle sending a message to the owner
$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['exchange_id'], $_POST['message'])) {
    $exchange_id = intval($_POST['exchange_id']);
    $message = trim($_POST['message']);
    if ($message) {
        // Get the owner (receiver) id
        $owner_id = null;
        foreach ($exchange_items as $ex_item) {
            if ($ex_item['id'] == $exchange_id) {
                $owner_id = $ex_item['user_id'];
                break;
            }
        }
        if ($owner_id) {
            // Store the message in the messages table
            $stmtMsg = $pdo->prepare("INSERT INTO messages (exchange_id, sender_id, receiver_id, message) VALUES (?, ?, ?, ?)");
            $stmtMsg->execute([$exchange_id, $user_id, $owner_id, $message]);
            $msg = '<div class="alert alert-success">Your message has been sent to the owner!</div>';
        } else {
            $msg = '<div class="alert alert-danger">Could not find the item owner.</div>';
        }
    } else {
        $msg = '<div class="alert alert-danger">Message cannot be empty.</div>';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Available Exchange Items - Lost and Found</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body { background: #f8f9fa; }
        .exchange-container { max-width: 900px; margin: 40px auto; }
        .exchange-card { background: #fff; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.07); padding: 24px; margin-bottom: 24px; }
        .exchange-img { max-width: 180px; max-height: 120px; object-fit: cover; border-radius: 6px; margin-bottom: 10px; }
    </style>
</head>
<body>
<div class="exchange-container">
    <h2 class="mb-4">Available Exchange Items</h2>
    <a href="dashboard.php" class="btn btn-secondary mb-4">&larr; Back to Dashboard</a>
    <?= $msg ?>
    <?php if (!empty($exchange_items)): ?>
        <?php foreach ($exchange_items as $item): ?>
            <div class="exchange-card">
                <?php if (!empty($item['image'])): ?>
                    <img src="uploads/<?= htmlspecialchars($item['image']) ?>" alt="Item Image" class="exchange-img">
                <?php endif; ?>
                <h5><?= htmlspecialchars($item['item_name']) ?> <small class="text-muted">(<?= htmlspecialchars($item['category']) ?>)</small></h5>
                <p><?= nl2br(htmlspecialchars($item['description'])) ?></p>
                <p><strong>Wants in exchange:</strong> <?= htmlspecialchars($item['exchange_for']) ?></p>
                <p class="mb-2"><small>Posted by <?= htmlspecialchars($item['user_name']) ?> (<?= htmlspecialchars($item['user_email']) ?>) on <?= date('M d, Y', strtotime($item['created_at'])) ?></small></p>
                <form method="POST" class="mt-2">
                    <input type="hidden" name="exchange_id" value="<?= $item['id'] ?>">
                    <div class="form-group">
                        <label for="message<?= $item['id'] ?>">Send a message to the owner:</label>
                        <textarea name="message" id="message<?= $item['id'] ?>" class="form-control" rows="2" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm">Send Message</button>
                </form>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="alert alert-warning text-center py-4">
            <i class="fas fa-box-open fa-2x mb-2"></i><br>
            No exchange items available from other users.
        </div>
    <?php endif; ?>
</div>
</body>
</html>