<?php
session_start();
include_once __DIR__ . "/../db.php";

// Redirect to login if not logged in
session_start();
if (isset($_SESSION['logged']) && $_SESSION['logged'] === true) {
    header("Location: ../../index.php"); 
    exit();
}


// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get and sanitize form inputs
    $username = trim($_POST['user']);
    $password = $_POST['password'];

    // Check if username exists
    $stmt = $conn->prepare("SELECT user_id, user_name, password, f_name, l_name, admin FROM user WHERE user_name = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        
        // Verify password (plain text comparison - NOT recommended for production)
        if ($password === $user['password']) {
        // Set session variables
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['username'] = $user['user_name'];
        $_SESSION['fullname'] = $user['f_name']." ".$user['l_name'];
        $_SESSION['name'] = $user['f_name'];
        $_SESSION['logged'] = true; // Add this line
        $_SESSION['admin'] = $user['admin'];

    // Redirect to dashboard
    header("Location: ../../index.php?success=0");
    exit();
}
 else {
            // Invalid password
            $_SESSION['login_error'] = "Invalid username or password";
            header("Location: ../../login.php?success=0");
            exit();
        }
    } else {
        // User not found
        $_SESSION['login_error'] = "Invalid username or password";
        header("Location: ../../login.php?success=0");
        exit();
    }

    $stmt->close();
}



$conn->close();
?>