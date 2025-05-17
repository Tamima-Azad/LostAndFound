<?php
session_start();
require '../config/database.php'; // Adjust path as needed

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $item_type = $_POST['item-type'];
    $item_name = $_POST['item-name'];
    $category = $_POST['item-category'];
    $description = $_POST['item-description'];
    $location = $_POST['item-location'];
    $date = $_POST['item-date'];
    $user_id = $_SESSION['user_id']; // Get the logged-in user's ID

    // Handle file uploads
    $image1 = '';
    $image2 = '';
    if (isset($_FILES['item-image1']) && $_FILES['item-image1']['error'] == 0) {
        $image1 = 'uploads/' . uniqid() . '_' . basename($_FILES['item-image1']['name']);
        move_uploaded_file($_FILES['item-image1']['tmp_name'], '../' . $image1);
    }
    if (isset($_FILES['item-image2']) && $_FILES['item-image2']['error'] == 0) {
        $image2 = 'uploads/' . uniqid() . '_' . basename($_FILES['item-image2']['name']);
        move_uploaded_file($_FILES['item-image2']['tmp_name'], '../' . $image2);
    }

    if ($item_type === 'found') {
        // Found item fields
        $found_area = $_POST['found-area'];
        $found_city = $_POST['found-city'];
        $found_state = $_POST['found-state'];
        $found_date = $_POST['found-date'];
        $kept_address = $_POST['kept-address'];
        $kept_city = $_POST['kept-city'];
        $kept_state = $_POST['kept-state'];
        $kept_contact = $_POST['kept-contact'];

        $stmt = $pdo->prepare("INSERT INTO found_items
            (user_id, item_name, category, description, location, date, image1, image2, found_area, found_city, found_state, found_date, kept_address, kept_city, kept_state, kept_contact)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $user_id, $item_name, $category, $description, $location, $date, $image1, $image2,
            $found_area, $found_city, $found_state, $found_date,
            $kept_address, $kept_city, $kept_state, $kept_contact
        ]);
        echo "<div class='alert alert-success text-center'>Found item submitted successfully!</div>";
    } else {
        $stmt = $pdo->prepare("INSERT INTO lost_items (user_id, item_name, category, description, location, date, image1, image2)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $user_id, $item_name, $category, $description, $location, $date, $image1, $image2
        ]);
        echo "<div class='alert alert-success text-center'>Lost item submitted successfully!</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lost and Found - Post Item</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        body {
            font-family: 'Nunito', sans-serif;
            background: linear-gradient(135deg, #fdfcfc 0%, #e2e1e1 100%);
            color: #333;
            line-height: 1.6;
        }

        .sidebar {
            background-color: #2c3e50 !important;
            color: #fff;
            padding-top: 30px;
            min-height: 100vh;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
        }

        .sidebar .sidebar-heading {
            padding: 0.875rem 1.25rem;
            font-size: 1rem;
            color: #adb5bd;
        }

        .sidebar .nav-link {
            color: #fff;
            padding: 12px 20px;
            display: block;
            text-decoration: none;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            background-color: rgba(255, 255, 255, 0.1);
            color: #00bfa5;
        }

        .sidebar .nav-link.active {
            border-left: 5px solid #fff;
        }

        .post-container {
            max-width: 800px;
            margin: 30px auto; /* Reduced top margin */
            padding: 30px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .post-container h2 {
            text-align: center;
            margin-bottom: 30px;
            color: #00bfa5;
        }

        .post-container .form-group {
            margin-bottom: 20px;
        }

        .post-container label {
            font-weight: bold;
            color: #555;
        }

        .post-container .form-control {
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 10px;
            font-size: 1rem;
        }

        .post-container .form-control:focus {
            border-color: #00bfa5;
            box-shadow: 0 0 0 0.2rem rgba(0, 191, 165, 0.25);
        }

        .post-container .btn-primary {
            width: 100%;
            background-color: #00bfa5;
            border-color: #00bfa5;
            transition: background-color 0.3s ease;
        }

        .post-container .btn-primary:hover {
            background-color: #00897b;
            border-color: #00897b;
        }

        .post-container .form-control-file {
            margin-top: 10px;
        }

        .post-container h5.text-primary {
            color: #00bfa5 !important;
            margin-top: 25px;
            border-bottom: 2px solid #00bfa5;
            padding-bottom: 5px;
        }

        nav.navbar {
            width: 100%;
            background-color: #343a40; /* Match sidebar background */
            color: #fff !important;
        }

        nav.navbar .navbar-brand {
            color: #fff;
        }

        /* Sidebar toggle button style */
        #sidebarToggle {
            background-color: #343a40;
            color: #fff;
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        #sidebarToggle:hover {
            background-color: #23272b;
        }

        /* Adjust main content padding when sidebar is hidden */
        @media (max-width: 767.98px) {
            .main-content {
                padding-left: 15px;
                padding-right: 15px;
            }
        }
    </style>
</head>
<body>

    <button class="btn btn-dark d-md-none" id="sidebarToggle" style="position:fixed;top:15px;left:15px;z-index:1050;">
        <i class="fas fa-bars"></i>
    </button>

    <nav class="navbar navbar-dark bg-dark fixed-top">
        <a class="navbar-brand" href="#">Lost and Found</a>
    </nav>

    <div class="container-fluid">
        <div class="row">
            <nav id="sidebar" class="col-md-3 col-lg-2 d-md-block sidebar bg-light" style="min-height:100vh; border-right:1px solid #eee; margin-top: 56px;">
                <div class="sidebar-sticky pt-3">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link" href="../dashboard.php">
                                <i class="fas fa-home mr-2"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="foundItem.php">
                                <i class="fas fa-plus-circle mr-2"></i> Post Lost/Found Item
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">
                                <i class="fas fa-list-alt mr-2"></i> My Listings
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">
                                <i class="fas fa-hand-holding mr-2"></i> Claims
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">
                                <i class="fas fa-envelope mr-2"></i> Messages
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">
                                <i class="fas fa-user-cog mr-2"></i> Account Settings
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="../logout.php">
                                <i class="fas fa-sign-out-alt mr-2"></i> Logout
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>

            <main class="col-md-9 ml-sm-auto col-lg-10 px-md-4 main-content" style="margin-top: 56px;">
                <div class="post-container">
                    <h2>Post Lost/Found Item</h2>

                    <form method="POST" enctype="multipart/form-data">
                        <div class="form-group">
                            <label for="item-type">Item Type</label>
                            <select class="form-control" id="item-type" name="item-type">
                                <option value="lost">Lost Item</option>
                                <option value="found">Found Item</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="item-name">Item Name</label>
                            <input type="text" class="form-control" id="item-name" name="item-name" placeholder="Enter item name">
                        </div>
                        <div class="form-group">
                            <label for="item-category">Category</label>
                            <select class="form-control" id="item-category" name="item-category">
                                <option value="electronics">Electronics</option>
                                <option value="documents">Documents</option>
                                <option value="personal">Personal Items</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="item-description">Description</label>
                            <textarea class="form-control" id="item-description" name="item-description" rows="4" placeholder="Enter item description"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="item-location">Location</label>
                            <input type="text" class="form-control" id="item-location" name="item-location" placeholder="Enter location">
                        </div>
                        <div class="form-group">
                            <label for="item-date">Date</label>
                            <input type="date" class="form-control" id="item-date" name="item-date">
                        </div>
                        <div class="form-group" id="found-extra-fields" style="display: none;">
                            <h5 class="text-primary mt-4 mb-2">Where Found!!</h5>
                            <label for="found-area">Area</label>
                            <input type="text" class="form-control" id="found-area" name="found-area" placeholder="Area">

                            <label for="found-city" class="mt-2">City</label>
                            <input type="text" class="form-control" id="found-city" name="found-city" placeholder="City">

                            <label for="found-state" class="mt-2">State</label>
                            <input type="text" class="form-control" id="found-state" name="found-state" placeholder="State">

                            <label for="found-date" class="mt-2">Date of Found</label>
                            <input type="date" class="form-control" id="found-date" name="found-date">

                            <h5 class="text-primary mt-4 mb-2">Where Item Kept!!</h5>
                            <label for="kept-address">Address</label>
                            <input type="text" class="form-control" id="kept-address" name="kept-address" placeholder="Kept Address">

                            <label for="kept-city" class="mt-2">City</label>
                            <input type="text" class="form-control" id="kept-city" name="kept-city" placeholder="Kept City">

                            <label for="kept-state" class="mt-2">State</label>
                            <input type="text" class="form-control" id="kept-state" name="kept-state" placeholder="Kept State">

                            <label for="kept-contact" class="mt-2">Contact Person Mobile Number</label>
                            <input type="text" class="form-control" id="kept-contact" name="kept-contact" placeholder="Contact Person Mobile Number">
                        </div>
                        <div class="form-group">
                            <label for="item-image1">Item Image 1</label>
                            <input type="file" class="form-control-file" id="item-image1" name="item-image1">
                        </div>
                        <div class="form-group">
                            <label for="item-image2">Item Image 2</label>
                            <input type="file" class="form-control-file" id="item-image2" name="item-image2">
                        </div>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </form>
                </div>
            </main>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script>
        // Show extra fields only for Found Item
        document.getElementById('item-type').addEventListener('change', function() {
            var foundFields = document.getElementById('found-extra-fields');
            if (this.value === 'found') {
                foundFields.style.display = 'block';
            } else {
                foundFields.style.display = 'none';
            }
        });
        // Trigger on page load in case of browser autofill
        document.getElementById('item-type').dispatchEvent(new Event('change'));
    </script>
    <script>
        // Sidebar toggle for small screens
        document.getElementById('sidebarToggle').addEventListener('click', function() {
            var sidebar = document.getElementById('sidebar');
            if (sidebar.classList.contains('d-md-block')) {
                sidebar.classList.remove('d-md-block');
                sidebar.style.display = 'none';
            } else {
                sidebar.classList.add('d-md-block');
                sidebar.style.display = 'block';
            }
        });

        // Ensure sidebar is visible on desktop resize
        window.addEventListener('resize', function() {
            var sidebar = document.getElementById('sidebar');
            if (window.innerWidth >= 768) {
                sidebar.classList.add('d-md-block');
                sidebar.style.display = 'block';
                  } else {
                sidebar.classList.remove('d-md-block');
                sidebar.style.display = 'none';
            }
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>