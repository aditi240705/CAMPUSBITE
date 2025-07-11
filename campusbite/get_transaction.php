<?php
$conn = new mysqli("localhost", "root", "", "campusbite");
$sql = "SELECT * FROM transactions ORDER BY transaction_date DESC LIMIT 10";
$result = $conn->query($sql);
$transactions = [];

while($row = $result->fetch_assoc()) {
    $transactions[] = $row;
}

echo json_encode($transactions);
?>
