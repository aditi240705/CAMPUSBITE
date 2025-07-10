<?php
session_start();

$host = 'localhost';
$user = 'root';
$pass = '';  // Your MySQL password
$dbname = 'campusbite';

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input_email = trim($_POST['email']);
    $input_username = trim($_POST['username']);

    if (isset($_SESSION['email'], $_SESSION['username'])) {
        if ($input_email === $_SESSION['email'] && $input_username === $_SESSION['username']) {
            // Matching session, proceed to logout
            session_unset();
            session_destroy();
            echo "You have been logged out successfully.";
            echo "<br><a href='login.php'>Login again</a>";
            exit();
        } else {
            echo "Email or Username does not match the logged-in user.";
        }
    } else {
        echo "No active session found. You are not logged in.";
    }
}
?>

<form method="POST" action="logout.php">
    <label>Email: <input type="email" name="email" required></label><br><br>
    <label>Username: <input type="text" name="username" required></label><br><br>
    <button type="submit">Logout</button>
</form>
