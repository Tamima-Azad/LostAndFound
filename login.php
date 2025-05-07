<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FindIt - Login/Signup</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
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

        <form id="login-form">
            <div class="form-group">
                <label for="login-email">Email</label>
                <input type="email" class="form-control" id="login-email" placeholder="Enter email">
            </div>
            <div class="form-group">
                <label for="login-password">Password</label>
                <input type="password" class="form-control" id="login-password" placeholder="Password">
            </div>
            <button type="submit" class="btn btn-primary">Login</button>
            <p class="form-text"><a href="#">Forgot Password?</a></p>
        </form>

        <form id="signup-form" style="display: none;">
            <div class="form-group">
                <label for="signup-name">Name</label>
                <input type="text" class="form-control" id="signup-name" placeholder="Enter name">
            </div>
            <div class="form-group">
                <label for="signup-email">Email</label>
                <input type="email" class="form-control" id="signup-email" placeholder="Enter email">
            </div>
            <div class="form-group">
                <label for="signup-contact">Contact No.</label>
                <input type="tel" class="form-control" id="signup-contact" placeholder="Enter contact number">
            </div>
            <div class="form-group">
                <label for="signup-password">Password</label>
                <input type="password" class="form-control" id="signup-password" placeholder="Password">
            </div>
            <div class="form-group">
                <label for="signup-confirm-password">Confirm Password</label>
                <input type="password" class="form-control" id="signup-confirm-password" placeholder="Confirm Password">
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

        loginForm.addEventListener('submit', (e) => {
            e.preventDefault();
            // Implement login logic here
            console.log("Login submitted");
        });

        signupForm.addEventListener('submit', (e) => {
            e.preventDefault();
            // Implement signup logic here
            console.log("Signup submitted");
        });
    </script>
</body>
</html>