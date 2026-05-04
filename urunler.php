<?php
ini_set('mysqli.default_socket', '/Applications/XAMPP/xamppfiles/var/mysql/mysql.sock');
$conn = new mysqli('localhost', 'root', '', 'loba');
if ($conn->connect_error) {
    die(json_encode(['error' => $conn->connect_error]));
}
header('Content-Type: application/json');
$result = $conn->query("SELECT * FROM urunler ORDER BY id ASC");
$products = [];
while ($row = $result->fetch_assoc()) {
    $products[] = $row;
}
echo json_encode($products);
$conn->close();
?>