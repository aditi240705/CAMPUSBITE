<?php
header('Content-Type: application/json');

// DB connection
$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'campusbite';

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    echo json_encode(["status" => "error", "message" => "Connection failed"]);
    exit;
}

// Collect POST inputs
$email = trim($_POST['email'] ?? '');
$token = trim($_POST['token'] ?? '');
$new_password = trim($_POST['password'] ?? '');

// Validate inputs
if (empty($email) || empty($token) || empty($new_password)) {
    echo json_encode(["status" => "error", "message" => "All fields are required"]);
    exit;
}

// Check if token is valid and not expired
$stmt = $conn->prepare("SELECT * FROM password_resets WHERE email = ? AND token = ?");
$stmt->bind_param("ss", $email, $token);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(["status" => "error", "message" => "Token not found or email/token mismatch"]);
    exit;
}

$reset = $result->fetch_assoc();
$current_time = date('Y-m-d H:i:s');

if (strtotime($reset['expires_at']) < strtotime($current_time)) {
    echo json_encode(["status" => "error", "message" => "Token has expired"]);
    exit;
}

// Hash new password
$hashed_password = password_hash($new_password, PASSWORD_BCRYPT);

// Update password in auth table
$stmt = $conn->prepare("UPDATE auth SET password = ? WHERE email = ?");
$stmt->bind_param("ss", $hashed_password, $email);
$stmt->execute();

// Delete the token after use
$stmt = $conn->prepare("DELETE FROM password_resets WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();

echo json_encode(["status" => "success", "message" => "Password has been reset"]);

$conn->close();
