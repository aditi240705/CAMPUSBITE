<?php
$servername = "localhost";
$username = "root";
$password = "";
$db = "campusbite"; // Replace with your actual database name

$conn = new mysqli($servername, $username, $password, $db);

// Set charset (recommended)
$conn->set_charset("utf8mb4");

$response = array();21\
if ($conn->connect_error) {
    $response['status'] = "error";
    $response['message'] = "Connection failed: " . $conn->connect_error;
} else {
    $response['status'] = "success";
    $response['message'] = "Connected successfully";
}

header('Content-Type: application/json');
echo json_encode($response);
?>
