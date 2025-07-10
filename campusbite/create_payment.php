<?php
$host = 'localhost';
$user = 'root';
$pass = ''; // Replace with your MySQL password if needed
$dbname = 'campusbite';

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Input data
$user_id = intval($_POST['user_id']);
$amount = floatval($_POST['amount']);
$reference = uniqid('pay_', true);

// Insert payment
$sql = "INSERT INTO payments (user_id, amount, payment_reference) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ids", $user_id, $amount, $reference);

if ($stmt->execute()) {
    echo json_encode([
        "status" => "success",
        "message" => "Payment created successfully",
        "payment_reference" => $reference
    ]);
} else {
    echo json_encode([
        "status" => "error",
        "message" => $stmt->error
    ]);
}

$stmt->close();
$conn->close();
?>
