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

            // Show OTP on the page (for demo/testing)
            $msg = '<div class="alert alert-success text-dark text-center" style="font-size:1.1em;">Your OTP is: <b>' . $otp . '</b> <br><small>(In production, this will be sent to your email.)</small></div>
            <form method="POST" action="reset_password.php" class="mt-4">
                <input type="hidden" name="email" value="' . htmlspecialchars($email) . '">
                <div class="form-group">
                    <label class="text-dark">Enter OTP:</label>
                    <input type="text" name="otp" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-success btn-block">Verify OTP</button>
            </form>';
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
            min-height: 100vh;
            background-image: linear-gradient(rgba(25, 78, 212, 0.92), rgba(0, 0, 0, 0.6)), url('images/homepage_background.jpg');
            background-size: cover;
            background-position: center;
            color: #222;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .forgot-container {
            max-width: 400px;
            width: 100%;
            margin: 40px auto;
            background: #fff;
            border-radius: 14px;
            box-shadow: 0 6px 24px rgba(44,62,80,0.13);
            padding: 36px 28px 28px 28px;
        }
        .forgot-container h3 {
            color: #194ed4;
            font-weight: 700;
            margin-bottom: 28px;
            text-align: center;
        }
        .form-control {
            border-radius: 7px;
            border: 1px solid #b2bec3;
        }
        .btn-primary, .btn-success {
            border-radius: 7px;
            font-weight: 600;
            letter-spacing: 0.5px;
        }
        .btn-link {
            color: #194ed4;
        }
        .btn-link:hover {
            color: #0d2c6b;
            text-decoration: underline;
        }
        .alert {
            border-radius: 7px;
        }
        @media (max-width: 500px) {
            .forgot-container {
                padding: 18px 6px 18px 6px;
            }
        }
    </style>
</head>
<body>
<div class="forgot-container">
    <h3>Forgot Password</h3>
    <?= $msg ?>
    <?php if (empty($msg) || strpos($msg, 'OTP') === false): ?>
    <form method="POST">
        <div class="form-group">
            <label for="email" class="text-dark">Enter your email address:</label>
            <input type="email" name="email" class="form-control" required autofocus>
        </div>
        <button type="submit" class="btn btn-primary btn-block">Send OTP</button>
        <a href="login.php" class="btn btn-link btn-block">Back to Login</a>
    </form>
    <?php endif; ?>
</div>
</body>
</html>