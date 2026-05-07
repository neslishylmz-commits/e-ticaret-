<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(200); exit; }

$sock = '/Applications/XAMPP/xamppfiles/var/mysql/mysql.sock';
if (file_exists($sock)) ini_set('mysqli.default_socket', $sock);

$conn = new mysqli('localhost', 'root', '', 'loba');
$conn->set_charset('utf8mb4');
if ($conn->connect_error) {
    echo json_encode(['basari' => false, 'mesaj' => 'DB hatası: ' . $conn->connect_error]); exit;
}

$raw  = file_get_contents('php://input');
$data = json_decode($raw, true);
if (!$data) {
    echo json_encode(['basari' => false, 'mesaj' => 'Veri okunamadı.']); exit;
}

$siparis_no    = trim($data['siparis_no']      ?? '');
$user_email    = trim($data['kullanici_email'] ?? '');
$urunler       = json_encode($data['urunler']  ?? []);
$ara_toplam    = floatval($data['ara_toplam']  ?? 0);
$kargo_ucreti  = floatval($data['kargo_ucreti'] ?? 0);
$kapida_ucret  = floatval($data['kapida_ucret'] ?? 0);
$toplam        = floatval($data['toplam']       ?? 0);
$odeme_yontemi = trim($data['odeme_yontemi']   ?? 'kredi');

if (!$siparis_no || $toplam <= 0) {
    echo json_encode(['basari' => false, 'mesaj' => 'Eksik sipariş verisi.']); exit;
}

$stmt = $conn->prepare("
    INSERT INTO siparisler (siparis_no, user_email, urunler, ara_toplam, kargo_ucreti, kapida_ucret, toplam, odeme_yontemi)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?)
");
$stmt->bind_param('sssdddds', $siparis_no, $user_email, $urunler, $ara_toplam, $kargo_ucreti, $kapida_ucret, $toplam, $odeme_yontemi);

if ($stmt->execute()) {
    echo json_encode(['basari' => true, 'siparis_no' => $siparis_no]);
} else {
    echo json_encode(['basari' => false, 'mesaj' => $stmt->error]);
}
$stmt->close();
$conn->close();
