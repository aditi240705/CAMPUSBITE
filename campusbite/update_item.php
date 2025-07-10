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

// Required inputs to identify the item
$canteen_name    = isset($_POST['canteen_name']) ? trim($_POST['canteen_name']) : '';
$current_name    = isset($_POST['food_name']) ? trim($_POST['food_name']) : '';

if (empty($canteen_name) || empty($current_name)) {
    http_response_code(400);
    echo json_encode(['error' => 'canteen_name and food_name are required to identify the item']);
    exit();
}

// Fields to update
$new_food_name   = isset($_POST['new_food_name']) ? trim($_POST['new_food_name']) : null;
$description     = isset($_POST['description']) ? trim($_POST['description']) : null;
$price           = isset($_POST['price']) ? floatval($_POST['price']) : null;
$availability    = isset($_POST['availability']) ? intval($_POST['availability']) : null;
$discount        = isset($_POST['discount']) ? floatval($_POST['discount']) : null;

// Build dynamic update query
$fields = [];
$params = [];
$types  = '';

if ($new_food_name !== null) {
    $fields[] = "food_name = ?";
    $params[] = $new_food_name;
    $types   .= 's';
}
if ($description !== null) {
    $fields[] = "description = ?";
    $params[] = $description;
    $types   .= 's';
}
if ($price !== null) {
    $fields[] = "price = ?";
    $params[] = $price;
    $types   .= 'd';
}
if ($availability !== null) {
    $fields[] = "availability = ?";
    $params[] = $availability;
    $types   .= 'i';
}
if ($discount !== null) {
    $fields[] = "discount = ?";
    $params[] = $discount;
    $types   .= 'd';
}

if (empty($fields)) {
    http_response_code(400);
    echo json_encode(['error' => 'No fields provided to update']);
    exit();
}

$sql = "UPDATE vendor SET " . implode(", ", $fields) . " WHERE canteen_name = ? AND food_name = ?";
$params[] = $canteen_name;
$params[] = $current_name;
$types   .= 'ss';

$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        echo json_encode(['status' => 'success', 'message' => 'Food item updated successfully']);
    } else {
        echo json_encode(['status' => 'success', 'message' => 'No changes made or item not found']);
    }
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to update food item']);
}

$stmt->close();
$conn->close();
?>
