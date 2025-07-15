<?php
session_start();
include '../db.php'; // include your DB connection

$item_id = $_POST['item_id'];
$borrower_name = $_POST['borrower_name'];
$quantity = $_POST['borrow_quantity'];
$return_date = $_POST['return_date'];

// Reduce stock
$update = $conn->prepare("UPDATE items SET available = available - ? WHERE item_id = ? AND total_quantity >= ?");
$update->bind_param("iii", $quantity, $item_id, $quantity);
$update->execute();

$insert = $conn->prepare("INSERT INTO borrow (item_id, borrower_name, quantity, return_date, borrow_date, status) VALUES (?, ?, ?, ?, NOW(), ?)");
$status = 'unreturned';
$insert->bind_param("isiss", $item_id, $borrower_name, $quantity, $return_date, $status);
$insert->execute();


// Log in history table
$log = $conn->prepare("INSERT INTO transaction(type, user, time) VALUES ('borrow',?, NOW())");
$log->bind_param('s',$_SESSION['username']);
$log->execute();

header("Location: ../../borrow.php?success=1");
exit();
?>
