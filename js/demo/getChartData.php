<?php
// Database connection
include '../../server/db.php';

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Query 1: Get Excellent and Broken items count
$itemStatusQuery = "SELECT 
    SUM(CASE WHEN status = 'Excellent' THEN total_quantity ELSE 0 END) AS excellent_count,
    SUM(CASE WHEN status = 'Broken' THEN total_quantity ELSE 0 END) AS broken_count
FROM items";

$itemStatusResult = $conn->query($itemStatusQuery);
$itemStatusData = $itemStatusResult->fetch_assoc();
$excellentCount = (int)$itemStatusData['excellent_count'];
$brokenCount = (int)$itemStatusData['broken_count'];

// Query 2: Get borrowed items not returned count
$borrowedQuery = "SELECT SUM(quantity) AS borrowed_count FROM borrow WHERE status != 'Returned'";
$borrowedResult = $conn->query($borrowedQuery);
$borrowedData = $borrowedResult->fetch_assoc();
$borrowedCount = (int)$borrowedData['borrowed_count'];

$conn->close();

// Prepare data for chart
$chartData = [
    'labels' => ['Excellent Items', 'Broken Items', 'Borrowed Items'],
    'data' => [$excellentCount, $brokenCount, $borrowedCount],
    'colors' => ['#007bff', '#dc3545', '#ffc107'], // Bootstrap primary, danger, warning
    'hoverColors' => ['#0069d9', '#c82333', '#e0a800'] // darker hover versions
];


// Output as JSON for AJAX call
header('Content-Type: application/json');
echo json_encode($chartData);
?>
