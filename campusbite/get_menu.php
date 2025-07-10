<?php
// Set response type
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

// Get input
$canteen_name = isset($_POST['canteen_name']) ? trim($_POST['canteen_name']) : '';

if (empty($canteen_name)) {
    http_response_code(400);
    echo json_encode(['error' => 'canteen_name is required']);
    exit();
}

// Query menu items for the given canteen
$stmt = $conn->prepare("SELECT food_name, description, price, availability, discount FROM vendor WHERE canteen_name = ?");
$stmt->bind_param("s", $canteen_name);
$stmt->execute();
$result = $stmt->get_result();

$menu = [];
while ($row = $result->fetch_assoc()) {
    $menu[] = $row;
}

echo json_encode(['canteen' => $canteen_name, 'menu' => $menu]);

$stmt->close();
$conn->close();
?>
