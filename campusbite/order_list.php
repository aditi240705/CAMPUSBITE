<?php
header('Content-Type: application/json');

// Database connection
$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'campusbite';

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(['error' => 'Database connection failed']);
    exit();
}

// Input: canteen_name (required)
$canteen_name = isset($_POST['canteen_name']) ? trim($_POST['canteen_name']) : '';

if (empty($canteen_name)) {
    http_response_code(400);
    echo json_encode(['error' => 'canteen_name is required']);
    exit();
}

// Fetch orders related to this canteen
$sql = "SELECT order_id, user_id, food_name, quantity, total_price, pickup_time, order_status, order_date 
        FROM orders 
        WHERE canteen_name = ?
        ORDER BY order_date DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $canteen_name);
$stmt->execute();
$result = $stmt->get_result();

$orders = [];
while ($row = $result->fetch_assoc()) {
    $orders[] = $row;
}

echo json_encode(['status' => 'success', 'orders' => $orders]);

$stmt->close();
$conn->close();
?>
