<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(200); exit; }

$host = "localhost"; $dbname = "loba"; $user = "root"; $pass = "";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(['basari' => false, 'mesaj' => 'DB hatası']); exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$adres_id    = intval($data['adres_id']    ?? 0);
$kullanici_id = intval($data['kullanici_id'] ?? 0);

if (!$adres_id || !$kullanici_id) {
    echo json_encode(['basari' => false, 'mesaj' => 'Eksik veri']); exit;
}

// Güvenlik: sadece kendi adresini silebilir
$stmt = $pdo->prepare("DELETE FROM adresler WHERE id = ? AND kullanici_id = ?");
$stmt->execute([$adres_id, $kullanici_id]);

echo json_encode(['basari' => true]);
