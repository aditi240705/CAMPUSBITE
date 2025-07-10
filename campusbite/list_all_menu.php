<?php
header('Content-Type: application/json');

// Database connection
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

// Query to get all food items with canteen names
$sql = "SELECT food_name, price, discount, available, canteen_name FROM menu";
$result = $conn->query($sql);

$menu = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $menu[] = [
            'food_name'     => $row['food_name'],
            'price'         => (float) $row['price'],
            'discount'      => (float) $row['discount'],
            'available'     => (int) $row['available'],
            'canteen_name'  => $row['canteen_name']
        ];
    }

    echo json_encode([
        'status' => 'success',
        'menu'   => $menu
    ]);
} else {
    echo json_encode([
        'status' => 'success',
        'menu'   => []  // empty list if nothing found
    ]);
}

$conn->close();
?>
