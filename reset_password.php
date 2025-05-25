<?php
// filepath: c:\xampp\htdocs\findit\reset_password.php
session_start();
require 'config/database.php';

$msg = '';
$showForm = true;
$showPasswordForm = false;
$user = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $otp = isset($_POST['otp']) ? trim($_POST['otp']) : '';
    $new_password = isset($_POST['new_password']) ? $_POST['new_password'] : '';
    $confirm_password = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';

    // If password fields are set, verify OTP and reset password
    if (!empty($new_password) && !empty($confirm_password) && !empty($otp)) {
        // Fetch user by email and OTP
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? AND reset_token = ?");
        $stmt->execute([$email, $otp]);
        $user = $stmt->fetch();

        if (!$user) {
            $msg = '<div class="alert alert-danger">Invalid OTP or email.</div>';
        } elseif (strtotime($user['reset_expires']) < time()) {
            $msg = '<div class="alert alert-danger">OTP has expired. Please request a new one.</div>';
        } elseif ($new_password !== $confirm_password) {
            $msg = '<div class="alert alert-danger">Passwords do not match.</div>';
            $showPasswordForm = true;
        } elseif (strlen($new_password) < 6) {
            $msg = '<div class="alert alert-danger">Password must be at least 6 characters.</div>';
            $showPasswordForm = true;
        } else {
            // Update password and clear token
            $hashed = password_hash($new_password, PASSWORD_BCRYPT);
            $pdo->prepare("UPDATE users SET password = ?, reset_token = NULL, reset_expires = NULL WHERE id = ?")
                ->execute([$hashed, $user['id']]);
            $msg = '<div class="alert alert-success">Password reset successful! <a href="login.php">Login now</a>.</div>';
            $showForm = false;
        }
    }
    // If only OTP is set, verify OTP and show password form
    elseif (!empty($otp)) {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? AND reset_token = ?");
        $stmt->execute([$email, $otp]);
        $user = $stmt->fetch();

        if (!$user) {
            $msg = '<div class="alert alert-danger">Invalid OTP or email.</div>';
        } elseif (strtotime($user['reset_expires']) < time()) {
            $msg = '<div class="alert alert-danger">OTP has expired. Please request a new one.</div>';
        } else {
            $showPasswordForm = true;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reset Password - FindIt</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background-image: linear-gradient(rgba(25, 78, 212, 0.964), rgba(0, 0, 0, 0.5)), url('images/homepage_background.jpg');
            background-size: cover;
            background-position: center;
            color: white;
        }
        .reset-container {
            max-width: 400px;
            margin: 60px auto;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 16px rgba(0,0,0,0.09);
            padding: 30px;
        }
    </style>
</head>
<body>
<div class="reset-container">
    <h3 class="mb-4">Reset Password</h3>
    <?= $msg ?>
    <?php if ($showForm): ?>
        <?php if (!$showPasswordForm): ?>
            <!-- OTP Verification Form -->
            <form method="POST">
                <input type="hidden" name="email" value="<?= isset($email) ? htmlspecialchars($email) : '' ?>">
                <div class="form-group">
                    <label for="otp">Enter OTP sent to your email:</label>
                    <input type="text" name="otp" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary btn-block">Verify OTP</button>
            </form>
        <?php else: ?>
            <!-- Password Reset Form -->
            <form method="POST">
                <input type="hidden" name="email" value="<?= isset($email) ? htmlspecialchars($email) : '' ?>">
                <input type="hidden" name="otp" value="<?= isset($otp) ? htmlspecialchars($otp) : '' ?>">
                <div class="form-group">
                    <label for="new_password">New Password:</label>
                    <input type="password" name="new_password" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="confirm_password">Confirm New Password:</label>
                    <input type="password" name="confirm_password" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-success btn-block">Reset Password</button>
            </form>
        <?php endif; ?>
    <?php endif; ?>
</div>
</body>
</html>