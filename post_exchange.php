<?php
session_start();
require 'config/database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
$user_id = $_SESSION['user_id'];

$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $item_name = trim($_POST['item_name']);
    $description = trim($_POST['description']);
    $category = trim($_POST['category']);
    $exchange_for = trim($_POST['exchange_for']);

    // Handle image upload
    $image_path = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = 'uploads/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        if (in_array($ext, $allowed)) {
            $filename = uniqid('ex_') . '.' . $ext;
            $target = $upload_dir . $filename;
            if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
                $image_path = $filename;
            } else {
                $msg = '<div class="alert alert-danger">Image upload failed.</div>';
            }
        } else {
            $msg = '<div class="alert alert-danger">Invalid image format. Allowed: jpg, jpeg, png, gif.</div>';
        }
    }

    if ($item_name && $description && $category && $exchange_for && empty($msg)) {
        $stmt = $pdo->prepare("INSERT INTO exchange_items (user_id, item_name, description, category, exchange_for, image, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())");
        $stmt->execute([$user_id, $item_name, $description, $category, $exchange_for, $image_path]);
        $msg = '<div class="alert alert-success">Your exchange item has been posted!</div>';
    } elseif (empty($msg)) {
        $msg = '<div class="alert alert-danger">Please fill in all fields.</div>';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Post Exchange Item - FindIt</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body { background: #f8f9fa; }
        .exchange-form-container {
            max-width: 500px;
            margin: 60px auto;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 16px rgba(0,0,0,0.09);
            padding: 30px;
        }
    </style>
</head>
<body>
<div class="exchange-form-container">
    <h3 class="mb-4">Post an Item for Exchange</h3>
    <?= $msg ?>
    <form method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label>Item Name</label>
            <input type="text" name="item_name" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Category</label>
            <select name="category" class="form-control" required>
                <option value="">Select Category</option>
                <option value="electronics">Electronics</option>
                <option value="documents">Documents</option>
                <option value="personal">Personal Items</option>
                <option value="other">Other</option>
            </select>
        </div>
        <div class="form-group">
            <label>Description</label>
            <textarea name="description" class="form-control" rows="3" required></textarea>
        </div>
        <div class="form-group">
            <label>Exchange For (what do you want in return?)</label>
            <input type="text" name="exchange_for" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Image (optional)</label>
            <input type="file" name="image" class="form-control-file" accept="image/*">
        </div>
        <button type="submit" class="btn btn-primary">Post Exchange</button>
        <a href="exchange_items.php" class="btn btn-link">View All Exchange Items</a>
    </form>
</div>
</body>
</html>