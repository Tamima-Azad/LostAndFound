<?php
// filepath: c:\xampp\htdocs\findit\contact.php
$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if (!$name || !$email || !$message) {
        $msg = '<div class="alert alert-danger">All fields are required.</div>';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $msg = '<div class="alert alert-danger">Please enter a valid email address.</div>';
    } else {
        // In production, send email to admin/support here
        // mail('admin@findit.com', 'Contact Form: '.$name, $message, "From: $email");
        $msg = '<div class="alert alert-success">Thank you for contacting us! We will get back to you soon.</div>';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Contact Us - FindIt</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #f8f9fa 0%, #e0e7ef 100%);
            min-height: 100vh;
        }
        .contact-container {
            max-width: 500px;
            margin: 60px auto;
            background: #fff;
            border-radius: 14px;
            box-shadow: 0 6px 24px rgba(44,62,80,0.10);
            padding: 36px 28px 28px 28px;
        }
        .contact-container h2 {
            color: #194ed4;
            font-weight: 700;
            margin-bottom: 28px;
            text-align: center;
        }
        .form-control {
            border-radius: 7px;
            border: 1px solid #b2bec3;
        }
        .btn-primary {
            border-radius: 7px;
            font-weight: 600;
            letter-spacing: 0.5px;
        }
        .alert {
            border-radius: 7px;
        }
        @media (max-width: 500px) {
            .contact-container {
                padding: 18px 6px 18px 6px;
            }
        }
    </style>
</head>
<body>
<div class="contact-container">
    <h2><i class="fas fa-envelope mr-2"></i>Contact Us</h2>
    <?= $msg ?>
    <form method="POST" autocomplete="off">
        <div class="form-group">
            <label for="name" class="text-dark">Your Name</label>
            <input type="text" name="name" class="form-control" required value="<?= isset($name) ? htmlspecialchars($name) : '' ?>">
        </div>
        <div class="form-group">
            <label for="email" class="text-dark">Your Email</label>
            <input type="email" name="email" class="form-control" required value="<?= isset($email) ? htmlspecialchars($email) : '' ?>">
        </div>
        <div class="form-group">
            <label for="message" class="text-dark">Message</label>
            <textarea name="message" class="form-control" rows="4" required><?= isset($message) ? htmlspecialchars($message) : '' ?></textarea>
        </div>
        <button type="submit" class="btn btn-primary btn-block">Send Message</button>
        <a href="index.php" class="btn btn-link btn-block">Back to Home</a>
    </form>
</div>
<!-- FontAwesome for icons -->
<script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</body>
</html>