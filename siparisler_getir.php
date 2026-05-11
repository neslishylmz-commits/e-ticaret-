<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

$conn = new mysqli('localhost', 'root', '', 'loba');
$conn->set_charset('utf8mb4');
if ($conn->connect_error) {
    echo json_encode(['basari' => false, 'mesaj' => 'DB hatası: ' . $conn->connect_error]); exit;
}

$user_id = intval($_GET['user_id'] ?? 0);

if (!$user_id) {
    echo json_encode(['basari' => false, 'mesaj' => 'user_id gerekli']); exit;
}

$stmt = $conn->prepare("SELECT * FROM siparisler WHERE user_id = ? ORDER BY created_at DESC");
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();

$siparisler = [];
while ($row = $result->fetch_assoc()) {
    // urunler JSON string ise parse et
    if (isset($row['urunler']) && is_string($row['urunler'])) {
        $row['urunler'] = json_decode($row['urunler'], true) ?: [];
    }
    $siparisler[] = $row;
}

$stmt->close();
$conn->close();

echo json_encode(['basari' => true, 'siparisler' => $siparisler], JSON_UNESCAPED_UNICODE);
