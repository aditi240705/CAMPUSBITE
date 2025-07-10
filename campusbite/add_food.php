<?php
header('Content-Type: application/json');

// Database connection
$conn = new mysqli("localhost", "root", "", "campusbite");
if ($conn->connect_error) {
    die(json_encode(["error" => "Database connection failed."]));
}

// Collect input data
$vendor_id     = $_POST['vendor_id'] ?? null;
$canteen_name  = $_POST['canteen_name'] ?? null;
$food_name     = $_POST['food_name'] ?? null;
$description   = $_POST['description'] ?? '';
$price         = $_POST['price'] ?? null;
$availability  = $_POST['availability'] ?? null;

// Validate required fields
if (!$vendor_id || !$canteen_name || !$food_name || !$price || $availability === null) {
    echo json_encode(["error" => "Required fields are missing"]);
    exit;
}

// Insert food item
$stmt = $conn->prepare("INSERT INTO vendor (vendor_id, canteen_name, food_name, description, price, availability) VALUES (?, ?, ?, ?, ?, ?)");
$stmt->bind_param("isssdi", $vendor_id, $canteen_name, $food_name, $description, $price, $availability);

if ($stmt->execute()) {
    echo json_encode(["status" => "success", "message" => "Food item added successfully"]);
} else {
    echo json_encode(["error" => "Failed to add food item"]);
}

$stmt->close();
$conn->close();
?>
