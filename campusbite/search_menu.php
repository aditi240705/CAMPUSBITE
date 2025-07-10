<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Database connection
$conn = new mysqli("localhost", "root", "", "campusbite");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Use POST to accept body data
if (isset($_POST['canteen_name'])) {
    $canteen = $conn->real_escape_string($_POST['canteen_name']);
    echo "Canteen name: " . $canteen . "<br>"; // Debug

    $sql = "SELECT food_name, price FROM menu WHERE canteen_name LIKE '%$canteen%'";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "Food: " . $row['food_name'] . " - Price: ₹" . $row['price'] . "<br>";
        }
    } else {
        echo "No results found.";
    }
} else {
    echo "canteen_name not set in POST body.";
}

$conn->close();
?>
