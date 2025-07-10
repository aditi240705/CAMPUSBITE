<?php
header('Content-Type: application/json');

$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'campusbite';

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    echo json_encode(["status" => "error", "message" => "Connection failed"]);
    exit;
}

$email = trim($_POST['email'] ?? '');

if (empty($email)) {
    echo json_encode(["status" => "error", "message" => "Email is required"]);
    exit;
}

// Check if email exists in auth table
$stmt = $conn->prepare("SELECT * FROM auth WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(["status" => "error", "message" => "No user found with that email"]);
    exit;
}

// Generate a reset token
$token = bin2hex(random_bytes(16));
$expires_at = date('Y-m-d H:i:s', strtotime('+15 minutes'));

// Store the token in password_resets table
$stmt = $conn->prepare("INSERT INTO password_resets (email, token, expires_at) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $email, $token, $expires_at);
$stmt->execute();

// In real app, send the $reset_link via email instead of returning it directly
$reset_link = "http://localhost:8080/campusbite/auth/reset_password.php?email=$email&token=$token";

echo json_encode([
    "status" => "success",
    "message" => "Reset link has been generated",
    "reset_link" => $reset_link
]);

$conn->close();
