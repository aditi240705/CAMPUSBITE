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

// Key inputs
$canteen_name = isset($_POST['canteen_name']) ? trim($_POST['canteen_name']) : '';
$food_name    = isset($_POST['food_name']) ? trim($_POST['food_name']) : '';
$discount     = isset($_POST['discount']) ? floatval($_POST['discount']) : null;

// Validation
if (empty($canteen_name) || empty($food_name) || $discount === null) {
    http_response_code(400);
    echo json_encode(['error' => 'canteen_name, food_name, and discount are required']);
    exit();
}

// Update the discount
$sql = "UPDATE vendor SET discount = ? WHERE canteen_name = ? AND food_name = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("dss", $discount, $canteen_name, $food_name);

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        echo json_encode(['status' => 'success', 'message' => 'Discount updated successfully']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Food item not found or discount unchanged']);
    }
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to update discount']);
}

$stmt->close();
$conn->close();
?>
