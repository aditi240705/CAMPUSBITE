<?php
header('Content-Type: application/json');

// Database connection
$host = 'localhost';
$user = 'root';
$pass = '';  // Your MySQL password
$dbname = 'campusbite';

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(['error' => 'Database connection failed']);
    exit();
}

// Key input
$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$username = isset($_POST['username']) ? trim($_POST['username']) : null;
$phone_number = isset($_POST['phone_number']) ? trim($_POST['phone_number']) : null;
$role = isset($_POST['role']) ? trim($_POST['role']) : null;

if (empty($email)) {
    http_response_code(400);
    echo json_encode(['error' => 'Email is required']);
    exit();
}

// Build dynamic update query based on fields provided
$fields = [];
$params = [];
$types = '';

if ($username !== null) {
    $fields[] = "username = ?";
    $params[] = $username;
    $types .= 's';
}
if ($phone_number !== null) {
    $fields[] = "phone_number = ?";
    $params[] = $phone_number;
    $types .= 's';
}
if ($role !== null) {
    $fields[] = "role = ?";
    $params[] = $role;
    $types .= 's';
}

if (empty($fields)) {
    http_response_code(400);
    echo json_encode(['error' => 'No fields to update']);
    exit();
}

// Update the auth table
$sql = "UPDATE auth SET " . implode(", ", $fields) . " WHERE email = ?";
$params[] = $email;
$types .= 's';

$stmt = $conn->prepare($sql);
if ($stmt === false) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to prepare SQL statement']);
    exit();
}

// Use references for bind_param (necessary for older PHP versions)
$bind_names[] = $types;
for ($i = 0; $i < count($params); $i++) {
    $bind_name = 'bind' . $i;
    $$bind_name = $params[$i];
    $bind_names[] = &$$bind_name;
}

// Bind and execute
call_user_func_array([$stmt, 'bind_param'], $bind_names);

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        echo json_encode(['status' => 'success', 'message' => 'Profile updated successfully']);
    } else {
        echo json_encode(['status' => 'success', 'message' => 'No changes made or user not found']);
    }
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to update profile']);
}

$stmt->close();
$conn->close();
?>
