<?php
session_start();
require_once '../db.php'; // Adjust path if needed

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $item_id = $_POST['item_id'];
    $broken_quantity = intval($_POST['broken_quantity']);
     $brokenNotes = trim($_POST['broken_notes']);

     echo 'this is broken note'.$brokenNotes;

    if ($broken_quantity <= 0) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid quantity.']);
        exit;
    }

    // Get original item
    $stmt = $conn->prepare("SELECT * FROM items WHERE item_id = ?");
    $stmt->bind_param("i", $item_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $original_item = $result->fetch_assoc();
    $stmt->close();

    if (!$original_item) {
        echo json_encode(['status' => 'error', 'message' => 'Item not found.']);
        exit;
    }

    // Check if enough available
    if ($broken_quantity > $original_item['available']) {
        echo json_encode(['status' => 'error', 'message' => 'Not enough available items.']);
        exit;
    }
     $broken_quantity1 = 0;

    // Begin transaction
    $conn->begin_transaction();

    try {
        // 1. Update original item
        $new_available = $original_item['available'] - $broken_quantity;

        $update_stmt = $conn->prepare("UPDATE items SET available = ?, updated_at = NOW() WHERE item_id = ?");
        $update_stmt->bind_param("ii", $new_available, $item_id);
        $update_stmt->execute();
        $update_stmt->close();

        $newDescription = "Old Description ".$original_item['description']."New Description ".$brokenNotes;

        // 2. Insert broken item
        $insert_stmt = $conn->prepare("
            INSERT INTO items (item_name, category, description, total_quantity, available, status, cost_price, low_stock_threshold, created_at, updated_at)
            VALUES (?, ?, ?, ?, ?, 'Broken', ?, ?, NOW(), NOW())
        ");
        $insert_stmt->bind_param(
            "sssiiid",
            $original_item['item_name'],
            $original_item['category'],
            $newDescription,
            $broken_quantity,
            $broken_quantity1,
            $original_item['cost_price'],
            $original_item['low_stock_threshold']
        );
        $insert_stmt->execute();
        $log = $conn->prepare("INSERT INTO transaction(type, user, time) VALUES ('update (broken)',?, NOW())")
        $log->bind_param('s',$_SESSION['username']);
        $log->execute();
        $insert_stmt->close();

        $conn->commit();

        header("Location: ../../items.php?success=2");
    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode(['status' => 'error', 'message' => 'Transaction failed: ' . $e->getMessage()]);
    }

} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request.']);
}
?>
