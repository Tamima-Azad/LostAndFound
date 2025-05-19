<?php
// filepath: c:\xampp\htdocs\findit\messages.php
session_start();
require 'config/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
$user_id = $_SESSION['user_id'];

// Handle reply
$reply_msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reply_message'], $_POST['exchange_id'], $_POST['receiver_id'], $_POST['parent_id'])) {
    $reply = trim($_POST['reply_message']);
    $exchange_id = intval($_POST['exchange_id']);
    $receiver_id = intval($_POST['receiver_id']);
    $parent_id = intval($_POST['parent_id']);
    if ($reply) {
        $stmt = $pdo->prepare("INSERT INTO messages (exchange_id, sender_id, receiver_id, message, parent_id) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$exchange_id, $user_id, $receiver_id, $reply, $parent_id]);
        $reply_msg = '<div class="alert alert-success">Reply sent!</div>';
    } else {
        $reply_msg = '<div class="alert alert-danger">Reply cannot be empty.</div>';
    }
}

// Handle delete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_message_id'])) {
    $delete_id = intval($_POST['delete_message_id']);
    // Only allow delete if user is sender or receiver
    $stmt = $pdo->prepare("DELETE FROM messages WHERE id = ? AND (sender_id = ? OR receiver_id = ?)");
    $stmt->execute([$delete_id, $user_id, $user_id]);
    $reply_msg = '<div class="alert alert-success">Message deleted!</div>';
}

// Fetch all messages for this user (as sender or receiver), with parent_id for threading
$stmt = $pdo->prepare("
    SELECT m.*, 
           ei.item_name, 
           u1.name AS sender_name, 
           u1.email AS sender_email,
           u2.name AS receiver_name,
           u2.email AS receiver_email
    FROM messages m
    JOIN exchange_items ei ON m.exchange_id = ei.id
    JOIN users u1 ON m.sender_id = u1.id
    JOIN users u2 ON m.receiver_id = u2.id
    WHERE m.sender_id = ? OR m.receiver_id = ?
    ORDER BY m.exchange_id, m.parent_id, m.sent_at ASC
");
$stmt->execute([$user_id, $user_id]);
$all_messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Group messages by exchange and parent_id for threading
$threads = [];
foreach ($all_messages as $msg) {
    $threads[$msg['exchange_id']][$msg['parent_id']][] = $msg;
}

// Helper to recursively display a thread
function render_thread($exchange_id, $parent_id, $threads, $user_id) {
    if (empty($threads[$exchange_id][$parent_id])) return;
    foreach ($threads[$exchange_id][$parent_id] as $msg) {
        ?>
        <div class="message-card ml-<?= $parent_id ? 5 : 0 ?>">
            <div class="msg-meta">
                <i class="fas fa-box-open"></i>
                <strong>Item:</strong> <?= htmlspecialchars($msg['item_name']) ?><br>
                <span>
                    <?= $msg['sender_id'] == $user_id ? '<span class="msg-sent"><i class="fas fa-paper-plane"></i> You sent</span>' : '<span class="msg-received"><i class="fas fa-inbox"></i> You received</span>' ?>
                    to <b><?= htmlspecialchars($msg['receiver_name']) ?></b>
                    <span class="msg-email">(<?= htmlspecialchars($msg['receiver_email']) ?>)</span>
                    <br>
                    from <b><?= htmlspecialchars($msg['sender_name']) ?></b>
                    <span class="msg-email">(<?= htmlspecialchars($msg['sender_email']) ?>)</span>
                    <span class="msg-date"><i class="far fa-clock"></i> <?= date('M d, Y H:i', strtotime($msg['sent_at'])) ?></span>
                </span>
            </div>
            <div class="msg-content"><?= nl2br(htmlspecialchars($msg['message'])) ?></div>
            <!-- Reply form: only show if you are the receiver -->
            <?php if ($msg['receiver_id'] == $user_id): ?>
                <form method="POST" class="reply-form mt-2">
                    <input type="hidden" name="exchange_id" value="<?= $msg['exchange_id'] ?>">
                    <input type="hidden" name="receiver_id" value="<?= $msg['sender_id'] ?>">
                    <input type="hidden" name="parent_id" value="<?= $msg['id'] ?>">
                    <div class="form-group mb-2">
                        <textarea name="reply_message" class="form-control" rows="2" placeholder="Type your reply..." required></textarea>
                    </div>
                    <button type="submit"><i class="fas fa-reply"></i> Reply</button>
                </form>
            <?php endif; ?>
            <?php if ($msg['sender_id'] == $user_id || $msg['receiver_id'] == $user_id): ?>
                <form method="POST" class="d-inline-block mt-2" onsubmit="return confirm('Are you sure you want to delete this message?');">
                    <input type="hidden" name="delete_message_id" value="<?= $msg['id'] ?>">
                    <button type="submit" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i> Delete</button>
                </form>
            <?php endif; ?>
            <?php
            // Render replies to this message
            render_thread($exchange_id, $msg['id'], $threads, $user_id);
            ?>
        </div>
        <?php
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Your Messages - Lost And Found</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body { background: linear-gradient(135deg, #f8f9fa 0%, #e0e7ef 100%); min-height: 100vh; }
        .messages-container {
            max-width: 900px;
            margin: 40px auto;
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 8px 32px rgba(44,62,80,0.08);
            padding: 40px 32px 32px 32px;
        }
        .message-card {
            background: linear-gradient(90deg, #e3f6fc 0%, #f9f9f9 100%);
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(44,62,80,0.06);
            padding: 24px 28px 18px 28px;
            margin-bottom: 18px;
            border-left: 6px solid #00bfa5;
            transition: box-shadow 0.2s;
            position: relative;
        }
        .message-card:hover {
            box-shadow: 0 6px 24px rgba(44,62,80,0.13);
        }
        .msg-meta {
            font-size: 1.05em;
            color: #555;
            margin-bottom: 10px;
        }
        .msg-sent { color: #007bff; font-weight: 600; }
        .msg-received { color: #28a745; font-weight: 600; }
        .msg-email {
            font-size: 0.97em;
            color: #888;
        }
        .msg-content {
            font-size: 1.13em;
            color: #222;
            margin-bottom: 10px;
            padding: 10px 0 0 0;
        }
        .reply-form textarea {
            border-radius: 8px;
            border: 1px solid #b2dfdb;
            resize: vertical;
        }
        .reply-form button {
            background: #00bfa5;
            border: none;
            color: #fff;
            border-radius: 6px;
            padding: 6px 22px;
            font-weight: 600;
            transition: background 0.2s;
        }
        .reply-form button:hover {
            background: #00897b;
        }
        .no-messages {
            text-align: center;
            color: #888;
            font-size: 1.2em;
            margin-top: 60px;
        }
        .messages-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 32px;
        }
        .messages-header h2 {
            font-weight: 700;
            color: #00bfa5;
            margin-bottom: 0;
        }
        .messages-header a {
            font-size: 1em;
            color: #007bff;
            text-decoration: none;
            transition: color 0.2s;
        }
        .messages-header a:hover {
            color: #0056b3;
            text-decoration: underline;
        }
        .msg-date {
            font-size: 0.95em;
            color: #aaa;
            margin-left: 4px;
        }
        .ml-5 { margin-left: 40px !important; }
        @media (max-width: 600px) {
            .messages-container { padding: 16px 4px; }
            .message-card { padding: 16px 8px 12px 12px; }
            .ml-5 { margin-left: 16px !important; }
        }
    </style>
</head>
<body>
<div class="messages-container">
    <div class="messages-header">
        <h2><i class="fas fa-comments mr-2"></i>Your Exchange Messages</h2>
        <a href="dashboard.php"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>
    </div>
    <?= $reply_msg ?>
    <?php
    if (!empty($threads)) {
        foreach ($threads as $exchange_id => $thread) {
            // Show only top-level messages (parent_id = 0)
            if (!empty($thread[0])) {
                foreach ($thread[0] as $msg) {
                    render_thread($exchange_id, 0, $threads, $user_id);
                    break; // Only need to call once per exchange_id
                }
            }
        }
    } else {
        ?>
        <div class="no-messages">
            <i class="far fa-comment-dots fa-2x mb-2"></i><br>
            You have no messages yet.
        </div>
    <?php } ?>
</div>
</body>
</html>