<?php
session_start();

// Database connection
$host = 'localhost';
$user = 'root';
$pass = ''; // Replace with your MySQL password if needed
$dbname = 'campusbite';

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle POST request
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Collect and sanitize inputs
    $food_name = isset($_POST["food_name"]) ? trim($_POST["food_name"]) : "";
    $canteen_name = isset($_POST["canteen_name"]) ? trim($_POST["canteen_name"]) : "";
    $rating = isset($_POST["rating"]) ? intval($_POST["rating"]) : 0;
    $review = isset($_POST["review"]) ? trim($_POST["review"]) : "";

    // Basic validation
    if (empty($food_name) || empty($canteen_name) || $rating < 1 || $rating > 5) {
        echo json_encode([
            "status" => "error",
            "message" => "Please provide valid food name, canteen name, and rating between 1 and 5."
        ]);
        exit;
    }

    // Insert review
    $stmt = $conn->prepare("INSERT INTO reviews (food_name, canteen_name, rating, review) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssis", $food_name, $canteen_name, $rating, $review);

    if ($stmt->execute()) {
        echo json_encode([
            "status" => "success",
            "message" => "Review submitted successfully."
        ]);
    } else {
        echo json_encode([
            "status" => "error",
            "message" => "Database error: " . $stmt->error
        ]);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode([
        "status" => "error",
        "message" => "Invalid request method. Use POST."
    ]);
}
?>
