<?php
session_start();
?>

<!DOCTYPE html>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lost and Found</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            font-family: 'Montserrat', sans-serif;
            line-height: 1.6;
        }

```
    nav.navbar {
        position: sticky;
        top: 0;
        z-index: 100;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    nav.navbar .nav-link {
        transition: color 0.3s ease;
    }

    nav.navbar .nav-link:hover {
        color: #007bff;
    }

    .hero {
        background-image: linear-gradient(rgba(56, 106, 243, 0.96), rgba(0, 0, 0, 0.5)), url('images/homepage_background.jpg');
        background-size: cover;
        background-position: center;
        color: white;
        text-align: center;
        padding: 100px 0;
    }

    .hero h1 {
        font-size: 3.5rem;
        font-weight: 700;
        margin-bottom: 20px;
    }

    .hero p {
        font-size: 1.2rem;
        margin-bottom: 30px;
    }

    .hero .btn {
        padding: 15px 30px;
        font-size: 1.1rem;
        border-radius: 25px;
        margin: 0 10px;
        transition: all 0.3s ease;
    }

    .hero .btn-primary {
        background: linear-gradient(45deg, #007bff, #0056b3);
        border: none;
    }

    .hero .btn-primary:hover {
        background: linear-gradient(45deg, #0056b3, #003f7f);
        transform: translateY(-5px);
    }

    .hero .btn-outline-light:hover {
        background: white;
        color: #007bff;
        transform: translateY(-5px);
    }

    .feature-item {
        padding: 30px;
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .feature-item:hover {
        transform: translateY(-10px);
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
    }

    .feature-icon {
        font-size: 4rem;
        margin-bottom: 20px;
        color: #007bff;
    }

    footer {
        background-color: #343a40;
        color: white;
        padding: 30px 0;
        text-align: center;
    }

    footer p {
        margin: 0;
    }

    footer a {
        color: #007bff;
        text-decoration: none;
    }

    footer a:hover {
        text-decoration: underline;
    }
</style>
```

</head>
<body>

```
<header>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <a class="navbar-brand" href="homepage.php">lost and found</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item active">
                    <a class="nav-link" href="homepage.php">Home <span class="sr-only">(current)</span></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Exchanged Item</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Post Lost Item</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Post Found Item</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Browse Items</a>
                </li>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="dashboard.php">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">Logout</a>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link" href="login.php">Login/Signup</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>
</header>

<section class="hero">
    <div class="container">
        <h1>Reunite with What You've Lost.</h1>
        <p>A community-driven platform to help you find your lost items and connect with others.</p>
       
        <a href="#" class="btn btn-primary btn-lg">Post Lost Item</a>
        <a href="#" class="btn btn-outline-light btn-lg">Browse Found Items</a>
    </div>
</section>

<section class="container my-5">
    <div class="row text-center">
        <div class="col-md-4 feature-item">
            <i class="fas fa-search feature-icon"></i>
            <h3>Advanced Search</h3>
            <p>Easily find items using keywords, categories, and location.</p>
        </div>
        <div class="col-md-4 feature-item">
            <i class="fas fa-edit feature-icon"></i>
            <h3>Easy Posting</h3>
            <p>Quickly post your lost or found items with detailed descriptions.</p>
        </div>
        <div class="col-md-4 feature-item">
            <i class="fas fa-handshake feature-icon"></i>
            <h3>Community Exchange</h3>
            <p>Connect with others to buy, borrow, or exchange items.</p>
        </div>
    </div>
</section>

<section class="container my-5 how-it-works">
    <h2 class="text-center mb-4">How It Works</h2>
    <div class="text-center">
        <img src="your-how-it-works-diagram.png" alt="How It Works Diagram" class="img-fluid">
    </div>
</section>

<footer class="mt-5">
    <div class="container">
        <p>&copy; 2024 FindIt. All rights reserved. <a href="#">Privacy Policy</a> | <a href="#">Terms of Service</a></p>
    </div>
</footer>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
```

</body>
</html>