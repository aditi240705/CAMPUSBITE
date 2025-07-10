<?php
// DB connection
$host = 'localhost';
$user = 'root';
$pass = ''; // Use your MySQL password
$dbname = 'campusbite';

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle signup form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username     = trim($_POST['username']);
    $email        = trim($_POST['email']);
    $phone_number = trim($_POST['phone_number']);
    $password     = trim($_POST['password']); // No hashing
    $role         = $_POST['role'] ?? 'user';

    // Check for duplicates
    $check_stmt = $conn->prepare("SELECT * FROM auth WHERE username = ? OR email = ? OR phone_number = ?");
    $check_stmt->bind_param("sss", $username, $email, $phone_number);
    $check_stmt->execute();
    $check_stmt->store_result();

    if ($check_stmt->num_rows > 0) {
        echo "Username, email, or phone number already exists.";
    } else {
        // Insert into auth table
        $stmt = $conn->prepare("INSERT INTO auth (username, email, phone_number, password, role) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $username, $email, $phone_number, $password, $role);

        if ($stmt->execute()) {
            echo "Signup successful.";
        } else {
            echo "Error: " . $stmt->error;
        }
        $stmt->close();
    }

    $check_stmt->close();
}

$conn->close();
?>
