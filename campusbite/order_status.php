<?php
header('Content-Type: application/json');

$host = 'localhost';
$user = 'root';
$pass = '';  // your MySQL password
$dbname = 'campusbite';

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(['error' => 'Database connection failed']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = isset($_POST['user_id']) ? intval($_POST['user_id']) : 0;

    if ($user_id <= 0) {
        http_response_code(400);
        echo json_encode(['error' => 'User ID is required']);
        exit();
    }

    $stmt = $conn->prepare("SELECT order_id, food_name, quantity, total_price, order_status, pickup_time, order_date FROM orders WHERE user_id = ? ORDER BY order_date DESC");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $orders = [];
    while ($row = $result->fetch_assoc()) {
        $orders[] = $row;
    }

    if (count($orders) > 0) {
        echo json_encode(['status' => 'success', 'orders' => $orders]);
    } else {
        echo json_encode(['status' => 'success', 'message' => 'No orders found']);
    }

    $stmt->close();
} else {
    http_response_code(405);
    echo json_encode(['error' => 'Invalid request method']);
}

$conn->close();
?>
