<?php
$host = "localhost";       // or 127.0.0.1
$username = "root";        // your DB username
$password = "";            // your DB password
$database = "inv_db"; // your dat

// Create connection
$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
