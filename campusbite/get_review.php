<?php
session_start();

// Database connection
$host = 'localhost';
$user = 'root';
$pass = ''; // Replace with your MySQL password if needed
$dbname = 'campusbite';

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Accept from either POST or GET
$food_name = $_REQUEST["food_name"] ?? null;
$canteen_name = $_REQUEST["canteen_name"] ?? null;

// If both are missing, return error
if (!$food_name && !$canteen_name) {
    echo json_encode(["status" => "error", "message" => "Please provide food_name or canteen_name."]);
    exit;
}

// Build dynamic query
$query = "SELECT food_name, canteen_name, rating, review, review_date FROM reviews WHERE ";
$params = [];
$types = "";

if ($food_name) {
    $query .= "food_name = ?";
    $params[] = $food_name;
    $types .= "s";
}
if ($food_name && $canteen_name) {
    $query .= " AND ";
}
if ($canteen_name) {
    $query .= "canteen_name = ?";
    $params[] = $canteen_name;
    $types .= "s";
}

// Prepare and execute
$stmt = $conn->prepare($query);
if ($params) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

// Collect results
$reviews = [];
while ($row = $result->fetch_assoc()) {
    $reviews[] = $row;
}

// Output
echo json_encode([
    "status" => "success",
    "results" => count($reviews),
    "data" => $reviews
]);

// Cleanup
$stmt->close();
$conn->close();
?>
