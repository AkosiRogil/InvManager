<?php
session_start();
include_once __DIR__ . "/../db.php";

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$item_id = $_POST['item_id'];
$item_name = $_POST['item_name'];
$category = $_POST['category'];
$description = $_POST['description'];
$total_quantity = $_POST['total_quantity'];
$status = $_POST['status'];
$cost_price = $_POST['cost_price'];
$low_stock_threshold = $_POST['low_stock_threshold'];

$sql = "UPDATE items SET
            item_name = ?,
            category = ?,
            description = ?,
            total_quantity = ?,
            status = ?,
            cost_price = ?,
            low_stock_threshold = ?,
            updated_at = NOW()
        WHERE item_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("sssisdii", $item_name, $category, $description, $total_quantity, $status, $cost_price, $low_stock_threshold, $item_id);

if ($stmt->execute()) {
    $log = $conn->prepare("INSERT INTO transaction(type, user, time) VALUES ('update',?, NOW())");
    $log->bind_param('s',$_SESSION['username']);
    $log->execute();

    header("Location: ../../items.php?success=2");
} else {
    echo "Error updating record: " . $conn->error;
}

$conn->close();
?>
