<?php
session_start();
include_once __DIR__ . "/../db.php";

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true) ?: $_POST;

$borrow_id = $data['borrow_id'] ?? null;
$item_id = $data['item_id'] ?? null;
$return_type = $data['return_type'] ?? null;
$returned_quantity = $data['returned_quantity'] ?? null;
$return_condition = $data['return_condition'] ?? null;
$return_notes = $data['return_notes'] ?? null;

if (!$borrow_id || !$item_id || !$return_type || !$return_condition) {
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit();
}

try {
    $conn->begin_transaction();

    // Get current borrow record
    $borrow_query = "SELECT quantity, status FROM borrow WHERE borrow_id = ?";
    $stmt = $conn->prepare($borrow_query);
    $stmt->bind_param("i", $borrow_id);
    $stmt->execute();
    $borrow_result = $stmt->get_result();

    if ($borrow_result->num_rows === 0) {
        throw new Exception("Borrow record not found");
    }

    $borrow_data = $borrow_result->fetch_assoc();
    $original_quantity = $borrow_data['quantity'];

    if ($borrow_data['status'] == 'Returned') {
        throw new Exception("This item has already been returned");
    }

    // Process return based on type
    if ($return_type === 'full') {
        $returned_quantity = $original_quantity;
        $new_status = 'Returned';
    } else {
        if (!$returned_quantity || $returned_quantity <= 0 || $returned_quantity > $original_quantity) {
            throw new Exception("Invalid returned quantity");
        }

        $new_status = ($returned_quantity == $original_quantity) ? 'Returned' : 'Partially Returned';
    }

    // Update borrow record
    $update_borrow = "UPDATE borrow
                      SET status = ?, returned_quantity = ?, returned_date = NOW(), 
                          return_condition = ?, return_notes = ?
                      WHERE borrow_id = ?";
    $stmt = $conn->prepare($update_borrow);
    $stmt->bind_param("sissi", $new_status, $returned_quantity, $return_condition, $return_notes, $borrow_id);
    $stmt->execute();

    if (strtolower($return_condition) !== 'Broken') {
        // If condition is excellent, add back to available stock
        $update_item = "UPDATE items
                        SET available = available + ? 
                        WHERE item_id = ?";
        $stmt = $conn->prepare($update_item);
        $stmt->bind_param("ii", $returned_quantity, $item_id);
        $stmt->execute();
    } else {
        // If not excellent, insert new item(s) with the return condition as status
        // Get original item details
        $item_query = "SELECT item_name, category, description, cost_price, low_stock_threshold 
                       FROM items WHERE item_id = ?";
        $stmt = $conn->prepare($item_query);
        $stmt->bind_param("i", $item_id);
        $stmt->execute();
        $item_result = $stmt->get_result();

        if ($item_result->num_rows === 0) {
            throw new Exception("Original item details not found");
        }

        $item = $item_result->fetch_assoc();

        // Insert new item with returned quantity and condition as status
        $insert_new_item = "INSERT INTO items 
            (item_name, category, description, total_quantity, available, status, cost_price, low_stock_threshold, created_at, updated_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())";
        $stmt = $conn->prepare($insert_new_item);
        $stmt->bind_param(
            "sssissdi",
            $item['item_name'],
            $item['category'],
            $item['description'],
            $returned_quantity,
            $returned_quantity,
            $return_condition,
            $item['cost_price'],
            $item['low_stock_threshold']
        );
        $stmt->execute();
    }

    $conn->commit();

    echo json_encode(['success' => true, 'message' => 'Return processed successfully']);

} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
} finally {
    // Optional: change 'user' to the actual logged-in user if available
    $log = $conn->prepare("INSERT INTO transaction(type, user, time) VALUES ('return',?, NOW())");
    $log->bind_param('s',$_SESSION['username']);
    $log->execute();
    header("Location: ../../items.php");
    $conn->close();
}
?>
