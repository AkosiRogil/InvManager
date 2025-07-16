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
$new_total_quantity = (int)$_POST['total_quantity'];
$status = $_POST['status'];
$cost_price = (float)$_POST['cost_price'];
$low_stock_threshold = (int)$_POST['low_stock_threshold'];

$fetch = $conn->prepare("SELECT total_quantity FROM items WHERE item_id = ?");
$fetch->bind_param("i", $item_id);
$fetch->execute();
$fetch->bind_result($old_total_quantity);

if ($fetch->fetch()) {
    $fetch->close();

    $available_increment = $new_total_quantity - $old_total_quantity;


    $sql = "UPDATE items SET
                item_name = ?,
                category = ?,
                description = ?,
                available = available + ?,
                total_quantity = ?,
                status = ?,
                cost_price = ?,
                low_stock_threshold = ?,
                updated_at = NOW()
            WHERE item_id = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param(
        "sssissidi", 
        $item_name,
        $category,
        $description,
        $available_increment,
        $new_total_quantity,
        $status,
        $cost_price,
        $low_stock_threshold,
        $item_id
    );

    if ($stmt->execute()) {
        // ✅ 4. Log the transaction
        $log = $conn->prepare("INSERT INTO transaction(type, user, time) VALUES ('update', ?, NOW())");
        $log->bind_param('s', $_SESSION['username']);
        $log->execute();

        header("Location: ../../items.php?success=2");
        exit;
    } else {
        echo "Error updating record: " . $stmt->error;
    }

    $stmt->close();
} else {
    echo "Item not found or failed to fetch.";
}

$conn->close();
?>
