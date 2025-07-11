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

// Validate
if (!$data || !isset($data->complaint_id)) {
    http_response_code(400);
    echo json_encode(["error" => "Missing 'complaint_id'."]);
    exit;
}

// Remove 'CBI' prefix and convert to ID
$complaint_num = (int) str_replace("CBI", "", $data->complaint_id);

// Query the complaint
$sql = "SELECT * FROM complaints WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $complaint_num);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    echo json_encode([
        "complaint_id" => "CBI" . str_pad($row['id'], 4, "0", STR_PAD_LEFT),
        "status" => $row['status'],
        "issue_type" => $row['issue_type'],
        "issue_details" => $row['issue_details'],
        "created_at" => $row['created_at']
    ]);
} else {
    http_response_code(404);
    echo json_encode(["error" => "Complaint not found."]);
}
?>
