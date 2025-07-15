<?php
session_start(); // Start the session (must be called first)

// Unset all session variables
session_unset();

// Destroy the session data on the server
session_destroy();

// Redirect to login page
header("Location: login.php"); // Adjust path if needed
exit();
