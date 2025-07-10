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
    // Get POST inputs
    $user_id = isset($_POST['user_id']) ? intval($_POST['user_id']) : 0;
    $food_name = isset($_POST['food_name']) ? trim($_POST['food_name']) : '';
    $canteen_name = isset($_POST['canteen_name']) ? trim($_POST['canteen_name']) : '';
    $quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 0;
    $unit_price = isset($_POST['total_price']) ? floatval($_POST['total_price']) : 0; // price per unit
    $pickup_time = isset($_POST['pickup_time']) ? trim($_POST['pickup_time']) : '';

    // Basic validation
    if ($user_id <= 0 || empty($food_name) || empty($canteen_name) || $quantity <= 0 || $unit_price <= 0 || empty($pickup_time)) {
        http_response_code(400);
        echo json_encode(['error' => 'All fields are required and quantity, price must be > 0']);
        exit();
    }

    $total_price = $unit_price * $quantity;

    // Insert order into orders table
    $insert_stmt = $conn->prepare("INSERT INTO orders (user_id, food_name, quantity, total_price, order_date, order_status, pickup_time) VALUES (?, ?, ?, ?, NOW(), 'pending', ?)");
    $insert_stmt->bind_param("isids", $user_id, $food_name, $quantity, $total_price, $pickup_time);

    if ($insert_stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Order placed successfully', 'total_price' => $total_price]);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to place order']);
    }

    $insert_stmt->close();
} else {
    http_response_code(405);
    echo json_encode(['error' => 'Invalid request method']);
}

$conn->close();
?>
