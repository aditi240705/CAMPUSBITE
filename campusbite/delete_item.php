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

// Required inputs
$canteen_name = isset($_POST['canteen_name']) ? trim($_POST['canteen_name']) : '';
$food_name    = isset($_POST['food_name']) ? trim($_POST['food_name']) : '';

if (empty($canteen_name) || empty($food_name)) {
    http_response_code(400);
    echo json_encode(['error' => 'canteen_name and food_name are required']);
    exit();
}

// Delete query
$sql = "DELETE FROM vendor WHERE canteen_name = ? AND food_name = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $canteen_name, $food_name);

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        echo json_encode(['status' => 'success', 'message' => 'Food item deleted successfully']);
    } else {
        echo json_encode(['status' => 'success', 'message' => 'Item not found or already deleted']);
    }
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to delete food item']);
}

$stmt->close();
$conn->close();
?>
