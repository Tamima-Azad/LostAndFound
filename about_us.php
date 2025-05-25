<?php
// filepath: c:\xampp\htdocs\findit\about_us.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>About Us - FindIt</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #f8f9fa 0%, #e0e7ef 100%);
            min-height: 100vh;
        }
        .about-container {
            max-width: 800px;
            margin: 60px auto;
            background: #fff;
            border-radius: 14px;
            box-shadow: 0 6px 24px rgba(44,62,80,0.10);
            padding: 40px 32px 32px 32px;
        }
        .about-title {
            color: #194ed4;
            font-weight: 700;
            margin-bottom: 24px;
        }
        .about-section {
            margin-bottom: 28px;
        }
        .about-section h4 {
            color: #194ed4;
            font-weight: 600;
            margin-bottom: 10px;
        }
        .about-section p {
            color: #333;
            font-size: 1.08em;
        }
        .about-team {
            margin-top: 30px;
        }
        .about-team h5 {
            color: #194ed4;
            font-weight: 600;
        }
        .about-team ul {
            padding-left: 18px;
        }
    </style>
</head>
<body>
    <div class="about-container">
        <h2 class="about-title text-center"><i class="fas fa-info-circle mr-2"></i>About Lost and found</h2>
        <div class="about-section">
            <h4>Our Mission</h4>
            <p>
                Lost and Found is dedicated to helping people reunite with their lost belongings and connect finders with owners. 
                Our platform makes it easy to report lost or found items, claim ownership, and facilitate safe exchanges in your community.
            </p>
        </div>
        <div class="about-section">
            <h4>How It Works</h4>
            <p>
                <strong>1.</strong> <b>Report Lost or Found:</b> Users can post details and photos of lost or found items.<br>
                <strong>2.</strong> <b>Browse Listings:</b> Search and filter items posted by others.<br>
                <strong>3.</strong> <b>Claim & Exchange:</b> If you find your item, claim it and communicate securely with the finder.<br>
                <strong>4.</strong> <b>Community Safety:</b> We encourage safe, honest exchanges and respect for all users.
            </p>
        </div>
        <div class="about-section">
            <h4>Why Choose FindIt?</h4>
            <ul>
                <li>Simple and user-friendly interface</li>
                <li>Secure messaging and claim process</li>
                <li>Supports both lost and found, and exchange/rent items</li>
                <li>Free to use for everyone</li>
            </ul>
        </div>
        <div class="about-team">
            <h5>Our Team</h5>
            <ul>
                <li>Developers passionate about community service</li>
                <li>Support staff ready to help you</li>
            </ul>
        </div>
        <div class="text-center mt-4">
            <a href="index.php" class="btn btn-primary">&larr; Back to Home</a>
        </div>
    </div>
    <!-- FontAwesome for icons -->
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</body>
</html>