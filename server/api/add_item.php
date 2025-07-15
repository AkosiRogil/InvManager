<?php
session_start();
include_once __DIR__ . "/../db.php";

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate and sanitize input
    $item_name = $conn->real_escape_string($_POST['item_name']);
    $category = $conn->real_escape_string($_POST['category']);
    $description = $conn->real_escape_string($_POST['description']);
    $total_quantity = intval($_POST['total_quantity']);
    $status = $conn->real_escape_string($_POST['status']);
    $cost_price = floatval($_POST['cost_price']);
    $low_stock_threshold = intval($_POST['low_stock_threshold']);

    $current_time = date('Y-m-d H:i:s');

    // Prepare SQL statement
    $sql = "INSERT INTO items (
                item_name, 
                category, 
                description, 
                total_quantity,
                available, 
                status, 
                cost_price, 
                low_stock_threshold, 
                created_at, 
                updated_at
            ) VALUES (
                '$item_name', 
                '$category', 
                '$description', 
                $total_quantity,
                $total_quantity, 
                '$status', 
                $cost_price, 
                $low_stock_threshold, 
                '$current_time', 
                '$current_time'
            )";

    // Execute the query and redirect with message
    if ($conn->query($sql) === TRUE) {
        $log = $conn->prepare("INSERT INTO transaction(type, user, time) VALUES ('Add',?, NOW())");
        $log->bind_param('s',$_SESSION['username'])
        $log->execute();
        header('Location: ../../items.php?success=1');
    } else {
        error_log("Error adding item: " . $conn->error);
        header('Location: ../../items.php?success=0');
    }
    exit();
} else {
    header('Location: ../../items.php');
    exit();
}
