<?php
$host = "localhost";
$user = "root";
$password = "";
$db = "findit_db";

// Create connection
$conn = new mysqli($host, $user, $password, $db);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Retrieve POST data
$name = $_POST['name'];
$email = $_POST['email'];
$contact = $_POST['contact'];
$password = password_hash($_POST['password'], PASSWORD_BCRYPT); // hash password

$sql = "INSERT INTO users (name, email, contact, password) VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssss", $name, $email, $contact, $password);

if ($stmt->execute()) {
    setcookie('remember_email', $email, time() + (86400 * 30), "/"); // 30 days
    echo "Signup successful!";
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>

<input type="email" name="email" class="form-control" 
    value="<?= isset($_COOKIE['remember_email']) ? htmlspecialchars($_COOKIE['remember_email']) : '' ?>" 
    required>
