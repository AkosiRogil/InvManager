<?php
include_once __DIR__ . "/../db.php";

// Set timezone and tomorrow's date
date_default_timezone_set('Asia/Manila');
$tomorrow = date('Y-m-d', strtotime('+1 day'));
$yesterday = date('Y-m-d', strtotime('-1 day'));
$today = date('Y-m-d', strtotime('+0 day'));

// ===========================
// Borrow Notifications
// ===========================
if (!function_exists('getDueCount')) {
    function getDueCount($conn) {
        $sql = "SELECT COUNT(*) as due_tomorrow_count FROM borrow WHERE status != 'Returned' and return_date <= DATE_ADD(CURDATE(), INTERVAL 1 DAY)";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();
        return $row['due_tomorrow_count'];
    }
}

if (!function_exists('getActCount')) {
    function getActCount($conn) {
        $sql = "SELECT COUNT(*) as act_count FROM transaction WHERE time >= NOW() - INTERVAL 1 DAY";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();
        return $row['act_count'];
    }
}

if (!function_exists('getRecentTransactions')) {
    function getRecentTransactions($conn) {
        $sql = "SELECT transaction_id, type, user, time 
                FROM transaction where time >= NOW() - INTERVAL 1 DAY";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->get_result();
        $transactions = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        return $transactions;
    }
}

if (!function_exists('getDueItems')) {
    function getDueItems($conn, $tomorrow) {
        $today = date('Y-m-d');

        $sql = "
            SELECT 
                borrow.*, 
                items.item_name 
            FROM 
                borrow 
            JOIN 
                items ON borrow.item_id = items.item_id
            WHERE 
                borrow.status != 'Returned'
        ";

        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->get_result();
        $all = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        $dueTomorrow = [];
        $overdue = [];
        $toBeReturn = [];

        foreach ($all as $item) {
            if ($item['return_date'] === $tomorrow && empty($item['returned_at'])) {
                $dueTomorrow[] = $item;
            } elseif ($item['return_date'] < $today && empty($item['returned_at'])) {
                $overdue[] = $item;
            } elseif ($item['return_date'] == $today && empty($item['returned_at'])) {
                $toBeReturn[] = $item;
            }
        }

        return [
            'due_tomorrow' => $dueTomorrow,
            'overdue' => $overdue,
            'toBeReturn' => $toBeReturn 
        ];
    }
}

// ===========================
// Item Status Logic
// ===========================
$items = [];
$statuses = [];

$stmt = $conn->prepare("SELECT * FROM items");
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $items[] = $row;
    $statuses[] = strtolower($row['status']);
}
$stmt->close();

if (!function_exists('get')) {
    function get($param) {
        global $statuses;
        $param = strtolower($param);
        $count = 0;

        foreach ($statuses as $status) {
            if ($status === $param) {
                $count++;
            }
        }

        return $count;
    }
}

if (!function_exists('get_item')) {
    function get_item() {
        global $items;
        return $items;
    }
}

if (!function_exists('get_total_quantity_by_status')) {
    function get_total_quantity_by_status($status) {
        global $conn;

        $stmt = $conn->prepare("SELECT SUM(total_quantity) AS total FROM items WHERE status = ?");
        $stmt->bind_param("s", $status);
        $stmt->execute();

        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        return $row['total'] ?? 0;
    }
}

if (!function_exists('get_total_available')) {
    function get_total_available() {
        global $conn;

        $stmt = $conn->prepare("SELECT SUM(available) AS total FROM items WHERE status = 'Excellent'");
        $stmt->execute();

        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        return $row['total'] ?? 0;
    }
}

if (!function_exists('get_total_quantity')) {
    function get_total_quantity() {
        global $conn;

        $stmt = $conn->prepare("SELECT SUM(total_quantity) AS total FROM items where status = 'Excellent'");
        $stmt->execute();

        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        echo $row['total'] ?? 0;
    }
}

if (!function_exists('get_total_borrowed')) {
    function get_total_borrowed() {
        global $conn;

        $stmt = $conn->prepare("SELECT SUM(quantity - IFNULL(returned_quantity, 0)) AS total FROM borrow WHERE status != 'Returned';");
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        echo $row['total'] ?? 0;
    }
}

// ===========================
// API Endpoint: Get Transactions
// ===========================
if (!function_exists('get_transactions')) {
    function get_transactions() {
        include __DIR__ . "/../db.php";

        $query = "SELECT transaction_id, type, user, time 
                  FROM transaction 
                  ORDER BY time DESC";

        $result = $conn->query($query);

        $transactions = [];
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $transactions[] = $row;
            }
        }

        $conn->close();

        return $transactions;
    }
}

// For direct access (like via fetch or axios)
if (isset($_GET['api']) && $_GET['api'] == 'get_transactions') {
    header('Content-Type: application/json');
    echo json_encode(get_transactions());
    exit();
}
?>
