<?php
include_once __DIR__ . "/../db.php";



// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get and sanitize form inputs
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $username = trim($_POST['user_name']);
    $password = $_POST['password'];
    $repeat_password = $_POST['repeat_password'];

    // Check if passwords match
    if ($password !== $repeat_password) {
        echo "<script>alert('Passwords do not match'); window.history.back();</script>";
        exit;
    }

    // Hash the password

    // Check if username already exists
    $check = $conn->prepare("SELECT user_id FROM user WHERE user_name = ?");
    $check->bind_param("s", $username);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        echo "<script>alert('Username already taken'); window.history.back();</script>";
        exit;
    }

    // Insert into the database
    $stmt = $conn->prepare("INSERT INTO user (user_name, f_name, l_name, password) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $username, $first_name, $last_name, $password);

    if ($stmt->execute()) {
        echo "<script>alert('Registration successful!'); window.location.href = '../../login.php';</script>";
    } else {
        echo "<script>alert('Error: " . $stmt->error . "'); window.history.back();</script>";
    }

    $stmt->close();
    $check->close();
}

$conn->close();
?>
