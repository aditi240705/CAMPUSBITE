<?php
header('Content-Type: application/json');
$conn = new mysqli("localhost", "root", "", "campusbite");

// Check DB connection
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(["error" => "Database connection failed"]);
    exit;
}

// Get raw input
$input = file_get_contents("php://input");
$data = json_decode($input);

// Validate input
if (!$data || !isset($data->transaction_id) || !isset($data->issue_type)) {
    http_response_code(400);
    echo json_encode(["error" => "Invalid input. 'transaction_id' and 'issue_type' are required."]);
    exit;
}

$transaction_id = $data->transaction_id;
$issue_type = $data->issue_type;
$issue_details = isset($data->issue_details) ? $data->issue_details : '';

// Prepare and insert
$stmt = $conn->prepare("INSERT INTO complaints (transaction_id, issue_type, issue_details) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $transaction_id, $issue_type, $issue_details);

if ($stmt->execute()) {
    echo json_encode([
        "message" => "Complaint Submitted Successfully",
        "complaint_id" => "CBI" . str_pad($stmt->insert_id, 4, "0", STR_PAD_LEFT),
        "resolution_eta" => "2-3 business days"
    ]);
} else {
    http_response_code(500);
    echo json_encode(["error" => "Failed to submit complaint."]);
}
?>
