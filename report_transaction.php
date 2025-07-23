<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'server/db.php'; // DB connection

// Query to fetch transaction data
$query = "SELECT transaction_id, type, user, time FROM transactions ORDER BY transaction_id DESC";

$result = $conn->query($query);

if (!$result) {
    die("Query Failed: " . $conn->error);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Transaction Logs - Export to PDF</title>
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
        @media print {
            .print-btn {
                display: none;
            }
        }
    </style>
</head>
<body>

<h2>Transaction Logs</h2>

<table>
    <tr>
        <th>Transaction ID</th>
        <th>Type</th>
        <th>User</th>
        <th>Time</th>
    </tr>

    <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= htmlspecialchars($row['transaction_id']) ?></td>
            <td><?= htmlspecialchars($row['type']) ?></td>
            <td><?= htmlspecialchars($row['user']) ?></td>
            <td><?= htmlspecialchars($row['time']) ?></td>
        </tr>
    <?php endwhile; ?>
</table>

<p>Exported by: <?= htmlspecialchars($_SESSION['fullname']) ?></p>
<p>Date: <?= date('Y-m-d h:i:s A') ?></p>

<script>
    window.onload = function() {
        window.print();
        setTimeout(function() {
            window.location.href = "index.php";
        }, 0); // Delay so print dialog has time
    };
</script>

</body>
</html>
