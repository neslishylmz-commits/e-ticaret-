<?php
$conn = null;

// Mac için
if (file_exists('/Applications/XAMPP/xamppfiles/var/mysql/mysql.sock')) {
    ini_set('mysqli.default_socket', '/Applications/XAMPP/xamppfiles/var/mysql/mysql.sock');
}

// Bağlantı
$conn = new mysqli('localhost', 'root', '', 'loba');
$conn->set_charset('utf8mb4');

if ($conn->connect_error) {
    die(json_encode(['error' => $conn->connect_error]));
}

header('Content-Type: application/json; charset=utf-8');
$result = $conn->query("SELECT * FROM urunler ORDER BY id ASC");
$products = [];
while ($row = $result->fetch_assoc()) {
    $products[] = $row;
}
echo json_encode($products, JSON_UNESCAPED_UNICODE);
$conn->close();
?>
