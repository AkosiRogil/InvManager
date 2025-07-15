<?php
// export_items.php
// Script to export items table to Excel format using $conn (MySQLi connection)

require_once 'server/db.php'; // Make sure this file sets up your $conn variable

// Query to fetch all items
$query = "SELECT 
            item_id,
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
          FROM items 
          ORDER BY item_id";

$result = $conn->query($query);

if (!$result) {
    die("Query Failed: " . $conn->error);
}

// Set headers for Excel download
$filename = 'items_export_' . date('Y-m-d_H-i-s') . '.xls';
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Cache-Control: max-age=0');

// Start output buffering
ob_start();

// Create Excel content
echo '<table border="1">';

// Header row
echo '<tr style="background-color: #4CAF50; color: white; font-weight: bold;">';
echo '<th>Item ID</th>';
echo '<th>Item Name</th>';
echo '<th>Category</th>';
echo '<th>Description</th>';
echo '<th>Total Quantity</th>';
echo '<th>Available</th>';
echo '<th>Status</th>';
echo '<th>Cost Price</th>';
echo '<th>Low Stock Threshold</th>';
echo '<th>Created At</th>';
echo '<th>Updated At</th>';
echo '</tr>';

// Data rows
while ($item = $result->fetch_assoc()) {
    echo '<tr>';
    echo '<td>' . htmlspecialchars($item['item_id']) . '</td>';
    echo '<td>' . htmlspecialchars($item['item_name']) . '</td>';
    echo '<td>' . htmlspecialchars($item['category']) . '</td>';
    echo '<td>' . htmlspecialchars($item['description']) . '</td>';
    echo '<td>' . htmlspecialchars($item['total_quantity']) . '</td>';
    echo '<td>' . htmlspecialchars($item['available']) . '</td>';
    echo '<td>' . htmlspecialchars($item['status']) . '</td>';
    echo '<td>' . number_format($item['cost_price'], 2) . '</td>';
    echo '<td>' . htmlspecialchars($item['low_stock_threshold']) . '</td>';
    echo '<td>' . htmlspecialchars($item['created_at']) . '</td>';
    echo '<td>' . htmlspecialchars($item['updated_at']) . '</td>';
    echo '</tr>';
}

echo '</table>';

// Output the content
$content = ob_get_contents();
ob_end_clean();

echo $content;

// Close the connection if you're done with it
// $conn->close();
?>
