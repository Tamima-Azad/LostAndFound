<?php
session_start();
require 'config/database.php';

$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    if (empty($email)) {
        $msg = '<div class="alert alert-danger">Please enter your email address.</div>';
    } else {
        // Check if user exists
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        if ($user) {
            // Generate OTP
            $otp = rand(100000, 999999);
            $expires = date('Y-m-d H:i:s', strtotime('+10 minutes'));

            // Store OTP and expiry in DB (add columns if not exist)
            $pdo->prepare("UPDATE users SET reset_token = ?, reset_expires = ? WHERE id = ?")
                ->execute([$otp, $expires, $user['id']]);

            // Send OTP to email
            $subject = "Your FindIt Password Reset OTP";
            $message = "Your OTP for password reset is: $otp\nThis OTP is valid for 10 minutes.";
            $headers = "From: no-reply@findit.com\r\n";
            // Use mail() function to send the email
            if (mail($email, $subject, $message, $headers)) {
                $msg = '<div class="alert alert-success">An OTP has been sent to your email address.</div>
                <form method="POST" action="reset_password.php">
                    <input type="hidden" name="email" value="' . htmlspecialchars($email) . '">
                    <div class="form-group mt-3">
                        <label>Enter OTP:</label>
                        <input type="text" name="otp" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-success btn-block">Verify OTP</button>
                </form>';
            } else {
                $msg = '<div class="alert alert-danger">Failed to send OTP. Please try again later.</div>';
            }
        } else {
            $msg = '<div class="alert alert-danger">No account found with that email.</div>';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Forgot Password - FindIt</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body { 
            background-image: linear-gradient(rgba(25, 78, 212, 0.964), rgba(0, 0, 0, 0.5)), url('images/homepage_background.jpg');
        }
        .forgot-container {
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
<div class="forgot-container">
    <h3 class="mb-4">Forgot Password</h3>
    <?= $msg ?>
    <?php if (empty($msg) || strpos($msg, 'OTP') === false): ?>
    <form method="POST">
        <div class="form-group">
            <label for="email">Enter your email address:</label>
            <input type="email" name="email" class="form-control" required autofocus>
        </div>
        <button type="submit" class="btn btn-primary btn-block">Send OTP</button>
        <a href="login.php" class="btn btn-link btn-block">Back to Login</a>
    </form>
    <?php endif; ?>
</div>
</body>
</html>