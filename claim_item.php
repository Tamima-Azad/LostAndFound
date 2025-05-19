<?php
// filepath: c:\xampp\htdocs\findit\claim_item.php
session_start();
require 'config/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$item_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$item_type = isset($_GET['type']) && in_array($_GET['type'], ['lost','found']) ? $_GET['type'] : '';

if (!$item_id || !$item_type) {
    header("Location: browsingItem.php?error=invalid");
    exit();
}

// Check if already claimed by this user
$stmt = $pdo->prepare("SELECT id FROM claims WHERE user_id = ? AND item_id = ? AND item_type = ?");
$stmt->execute([$user_id, $item_id, $item_type]);
if ($stmt->fetch()) {
    header("Location: browsingItem.php?msg=already_claimed");
    exit();
}

// Insert claim
$stmt = $pdo->prepare("INSERT INTO claims (user_id, item_id, item_type, status) VALUES (?, ?, ?, 'New')");
$stmt->execute([$user_id, $item_id, $item_type]);

header("Location: browsingItem.php?msg=claim_success");
exit();
?>