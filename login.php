<?php
session_start();
require 'config/database.php';

// (Optional: Enable error reporting for debugging)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'signup') {
        // Signup logic
        $name = $_POST['name'];
        $email = $_POST['email'];
        $contact = $_POST['contact'];
        $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

        $stmt = $pdo->prepare("INSERT INTO users (name, email, contact_no, password) VALUES (?, ?, ?, ?)");
        if ($stmt->execute([$name, $email, $contact, $password])) {
            echo "Signup successful!";
        } else {
            echo "Signup failed!";
        }
    } elseif (isset($_POST['action']) && $_POST['action'] === 'login') {
        // Login logic
        $email = $_POST['email'];
        $password = $_POST['password'];

        // Check if admin
        $adminStmt = $pdo->prepare("SELECT * FROM users WHERE email = ? AND role = 'admin'");
        $adminStmt->execute([$email]);
        $admin = $adminStmt->fetch();

        if ($admin && password_verify($password, $admin['password'])) {
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_name'] = $admin['name'];
            header("Location: admin_dashboard.php");
            exit();
        }

        // Check if normal user
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            header("Location: dashboard.php");
            exit();
        } else {
            echo "Invalid email or password!";
        }
    }
}
?>

<!DOCTYPE html>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FindIt - Login/Signup</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            font-family: 'Montserrat', sans-serif;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
            min-height: 100vh;
            margin: 0;
            background-image: linear-gradient(rgba(25, 78, 212, 0.964), rgba(0, 0, 0, 0.5)), url('images/homepage_background.jpg');
            background-size: cover;
            background-position: center;
            color: white;
        }


    .auth-container {
        background-color: rgba(255, 255, 255, 0.9);
        padding: 40px;
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        width: 400px;
        margin-top: 20px;
        color: black;
    }

    .auth-container h2 {
        text-align: center;
        margin-bottom: 30px;
    }

    .auth-container .form-group {
        margin-bottom: 20px;
    }

    .auth-container .btn-primary {
        width: 100%;
        background-color: #007bff;
        border-color: #007bff;
    }

    .auth-container .form-text {
        text-align: center;
        margin-top: 20px;
    }

    nav.navbar {
        width: 100%;
        background-color: rgba(0,0,0,0.4);
    }

    nav.navbar .navbar-brand {
        color: white;
    }
</style>
</head>
<body>

<nav class="navbar navbar-dark">
    <a class="navbar-brand" href="#">Lost & Found</a>
</nav>

<div class="auth-container">
    <h2 id="auth-title">Login</h2>

    <form id="login-form" method="POST">
        <input type="hidden" name="action" value="login">
        <div class="form-group">
            <label for="login-email">Email</label>
            <input type="email" class="form-control" id="login-email" name="email" placeholder="Enter email">
        </div>
        <div class="form-group">
            <label for="login-password">Password</label>
            <div class="input-group">
                <input type="password" class="form-control" id="login-password" name="password" placeholder="Password">
                <div class="input-group-append">
                    <button class="btn btn-outline-secondary toggle-password" type="button" tabindex="-1">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
            </div>
        </div>
        <button type="submit" class="btn btn-primary">Login</button>
        <p class="form-text"><a href="#">Forgot Password?</a></p>
    </form>

    <form id="signup-form" method="POST" style="display: none;">
        <input type="hidden" name="action" value="signup">
        <div class="form-group">
            <label for="signup-name">Name</label>
            <input type="text" class="form-control" id="signup-name" name="name" placeholder="Enter name">
        </div>
        <div class="form-group">
            <label for="signup-email">Email</label>
            <input type="email" class="form-control" id="signup-email" name="email" placeholder="Enter email">
        </div>
        <div class="form-group">
            <label for="signup-contact">Contact No.</label>
            <input type="tel" class="form-control" id="signup-contact" name="contact" placeholder="Enter contact number">
        </div>
        <div class="form-group">
            <label for="signup-password">Password</label>
            <div class="input-group">
                <input type="password" class="form-control" id="signup-password" name="password" placeholder="Password">
                <div class="input-group-append">
                    <button class="btn btn-outline-secondary toggle-password" type="button" tabindex="-1">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
            </div>
        </div>
        <div class="form-group">
            <label for="signup-confirm-password">Confirm Password</label>
            <div class="input-group">
                <input type="password" class="form-control" id="signup-confirm-password" placeholder="Confirm Password">
                <div class="input-group-append">
                    <button class="btn btn-outline-secondary toggle-password" type="button" tabindex="-1">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
            </div>
        </div>
        <button type="submit" class="btn btn-primary">Signup</button>
    </form>

    <p class="form-text">
        <a href="#" id="toggle-auth">Switch to Signup</a>
    </p>
</div>

<script>
    const loginForm = document.getElementById('login-form');
    const signupForm = document.getElementById('signup-form');
    const toggleAuth = document.getElementById('toggle-auth');
    const authTitle = document.getElementById('auth-title');

    toggleAuth.addEventListener('click', (e) => {
        e.preventDefault();
        if (loginForm.style.display !== 'none') {
            loginForm.style.display = 'none';
            signupForm.style.display = 'block';
            authTitle.textContent = 'Signup';
            toggleAuth.textContent = 'Switch to Login';
        } else {
            loginForm.style.display = 'block';
            signupForm.style.display = 'none';
            authTitle.textContent = 'Login';
            toggleAuth.textContent = 'Switch to Signup';
        }
    });

    document.querySelectorAll('.toggle-password').forEach(btn => {
        btn.addEventListener('click', function() {
            const input = this.parentElement.previousElementSibling;
            if (input.type === "password") {
                input.type = "text";
                this.firstElementChild.classList.remove('fa-eye');
                this.firstElementChild.classList.add('fa-eye-slash');
            } else {
                input.type = "password";
                this.firstElementChild.classList.remove('fa-eye-slash');
                this.firstElementChild.classList.add('fa-eye');
            }
        });
    });
</script>

</body>
</html>
