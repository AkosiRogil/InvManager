<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'server/db.php'; // Your DB connection


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
?>

<!DOCTYPE html>
<html>
<head>
    <title>Items Inventory - Export to PDF</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
        }
        h2 {
            text-align: center;
        }
        table {
            border-collapse: collapse;
            width: 100%;
            font-size: 12px;
        }
        th, td {
            border: 1px solid #444;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #4CAF50;
            color: white;
        }
        .print-btn {
            display: none;
        }
        @media print {
            .print-btn {
                display: none;
            }
        }
    </style>
</head>
<body>

<h2>Items Inventory</h2>

<table>
    <tr>
        <th>Item ID</th>
        <th>Item Name</th>
        <th>Category</th>
        <th>Description</th>
        <th>Total Quantity</th>
        <th>Available</th>
        <th>Status</th>
        <th>Cost Price</th>
        <th>Low Stock Threshold</th>
        <th>Created At</th>
        <th>Updated At</th>
    </tr>

    <?php while ($item = $result->fetch_assoc()): ?>
        <tr>
            <td><?= htmlspecialchars($item['item_id']) ?></td>
            <td><?= htmlspecialchars($item['item_name']) ?></td>
            <td><?= htmlspecialchars($item['category']) ?></td>
            <td><?= htmlspecialchars($item['description']) ?></td>
            <td><?= htmlspecialchars($item['total_quantity']) ?></td>
            <td><?= htmlspecialchars($item['available']) ?></td>
            <td><?= htmlspecialchars($item['status']) ?></td>
            <td><?= number_format($item['cost_price'], 2) ?></td>
            <td><?= htmlspecialchars($item['low_stock_threshold']) ?></td>
            <td><?= htmlspecialchars($item['created_at']) ?></td>
            <td><?= htmlspecialchars($item['updated_at']) ?></td>
        </tr>
    <?php endwhile; ?>
</table>

<p>Export by: <?php  echo $_SESSION['fullname']; ?></p>
<p>Date: <?= date('Y-m-d h:i:s A') ?></p>


<script>
    window.onload = function() {
        window.print();
        // Wait a bit for print to finish, then redirect
        setTimeout(function() {
            window.location.href = "index.php";
        },0); // 2 seconds delay (adjust as needed)
    };
</script>

</body>
</html>
