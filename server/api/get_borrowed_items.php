<?php
function get_borrowed_items() {
    // Database connection
    include 'server/db1.php'; // ← Fixed missing semicolon

    // Make sure $conn is defined
    if (!isset($conn)) {
        http_response_code(500);
        echo json_encode(['error' => 'Database connection not established.']);
        exit();
    }

    $query = "SELECT b.borrow_id, b.item_id, i.item_name, b.borrower_name, b.status, b.quantity, b.borrow_date, b.return_date, b.returned_date, b.returned_quantity
              FROM borrow b
              JOIN items i ON b.item_id = i.item_id
              ORDER BY b.borrow_date DESC";

    $result = $conn->query($query);

    $borrowedItems = [];
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $borrowedItems[] = $row;
        }
    }

    $conn->close();

    return $borrowedItems;
}

// For direct access (like from the table page)
if (isset($_GET['api']) && $_GET['api'] == 'get_borrowed_items') {
    header('Content-Type: application/json');
    echo json_encode(get_borrowed_items());
    exit();
}
?>
