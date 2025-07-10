<?php
header('Content-Type: application/json');

// DEBUG: show what's being received (for temporary testing only)
// error_log("GET: " . json_encode($_GET));
// error_log("POST: " . json_encode($_POST));

// Database connection
$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'campusbite';

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode([
        "status" => "error",
        "message" => "Connection failed: " . $conn->connect_error
    ]);
    exit;
}

// Check both GET and POST for 'reference'
$reference = '';
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['reference'])) {
    $reference = trim($_GET['reference']);
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reference'])) {
    $reference = trim($_POST['reference']);
}

if (empty($reference)) {
    http_response_code(400);
    echo json_encode([
        "status" => "error",
        "message" => "Reference is missing"
    ]);
    exit;
}

// Prepare SQL
$sql = "SELECT * FROM payments WHERE payment_reference = ?";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    http_response_code(500);
    echo json_encode([
        "status" => "error",
        "message" => "SQL prepare failed: " . $conn->error
    ]);
    exit;
}

$stmt->bind_param("s", $reference);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $payment = $result->fetch_assoc();
    echo json_encode([
        "status" => "success",
        "payment" => $payment
    ]);
} else {
    http_response_code(404);
    echo json_encode([
        "status" => "error",
        "message" => "Payment not found"
    ]);
}

$stmt->close();
$conn->close();
