<?php
include '../../server/db.php';
if ($conn->connect_error) {
    die(json_encode(["error" => $conn->connect_error]));
}

// Get dates and counts
$sql = "
    SELECT date, 
        SUM(CASE WHEN type = 'Excellent' THEN total ELSE 0 END) AS excellent,
        SUM(CASE WHEN type = 'Broken' THEN total ELSE 0 END) AS broken,
        SUM(CASE WHEN type = 'Borrowed' THEN total ELSE 0 END) AS borrowed
    FROM (
        SELECT DATE(created_at) AS date, status AS type, COUNT(*) AS total
        FROM items
        WHERE status IN ('Excellent', 'Broken')
        GROUP BY DATE(created_at), status

        UNION ALL

        SELECT DATE(borrow_date) AS date, 'Borrowed' AS type, COUNT(*) AS total
        FROM borrow
        GROUP BY DATE(borrow_date)
    ) AS combined
    GROUP BY date
    ORDER BY date ASC
";

$result = $conn->query($sql);
$data = [];

while ($row = $result->fetch_assoc()) {
    $data[] = [
        'date' => $row['date'],
        'Excellent' => (int)$row['excellent'],
        'Broken' => (int)$row['broken'],
        'Borrowed' => (int)$row['borrowed']
    ];
}

echo json_encode($data);
$conn->close();
