<?php
session_start();

// Database connection
$host = 'localhost';
$user = 'root';
$pass = ''; // Replace with your MySQL password
$dbname = 'campusbite';

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email']);
    $password = trim($_POST['password']);

    $stmt = $conn->prepare("SELECT user_id, username, password, role FROM auth WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
        $stmt->bind_result($user_id, $username, $stored_password, $role);
        $stmt->fetch();

        if ($password === $stored_password) {
            // Login success
            $_SESSION['user_id'] = $user_id;
            $_SESSION['username'] = $username;
            $_SESSION['email'] = $email;   // Added this line
            $_SESSION['role'] = $role;

            echo "Login successful. Welcome, $username!";
            // Optionally, redirect to dashboard/homepage:
            // header("Location: dashboard.php");
            // exit();
        } else {
            echo "Incorrect password.";
        }
    } else {
        echo "Email not found.";
    }

    $stmt->close();
}

$conn->close();
?>
