<?php
// Enable mysqli error reporting for better debugging
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$host = "localhost";
$db = "campusbite";
$user = "root";
$pass = ""; // Update if needed

// Create connection
$conn = new mysqli($host, $user, $pass, $db);
$conn->set_charset("utf8mb4"); // Ensure proper charset

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["image"])) {
    $food_name = $_POST["food_name"] ?? '';
    $canteen_name = $_POST["canteen_name"] ?? '';
    $image = $_FILES["image"];

    // Directory to save images
    $targetDir = "assets/images/";
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0755, true);
    }

    $fileName = basename($image["name"]);
    $fileType = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    $uniqueFileName = uniqid("img_", true) . "." . $fileType;
    $targetFilePath = $targetDir . $uniqueFileName;

    $allowedTypes = array("jpg", "jpeg", "png");

    if (in_array($fileType, $allowedTypes)) {
        if (move_uploaded_file($image["tmp_name"], $targetFilePath)) {
            try {
                // Check connection before executing
                if (!$conn->ping()) {
                    $conn = new mysqli($host, $user, $pass, $db);
                    $conn->set_charset("utf8mb4");
                }

                // Prepare and execute insert statement
                $stmt = $conn->prepare("INSERT INTO assets (food_name, canteen_name, image_path) VALUES (?, ?, ?)");
                $stmt->bind_param("sss", $food_name, $canteen_name, $targetFilePath);
                $stmt->execute();
                echo "✅ Image uploaded and saved successfully.";
                $stmt->close();
            } catch (mysqli_sql_exception $e) {
                echo "❌ Database error: " . $e->getMessage();
            }
        } else {
            echo "❌ Failed to move uploaded file.";
        }
    } else {
        echo "❌ Only JPG and PNG files are allowed.";
    }
} else {
    echo "❌ Invalid request.";
}

// Close the connection
$conn->close();
?>
