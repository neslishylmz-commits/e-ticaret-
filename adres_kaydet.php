<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

$host   = "localhost";
$dbname = "loba";
$user   = "root";
$pass   = "";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(['basari' => false, 'mesaj' => 'DB hatası: ' . $e->getMessage()]);
    exit;
}

$raw  = file_get_contents('php://input');
$data = json_decode($raw, true);

if (!$data) {
    echo json_encode(['basari' => false, 'mesaj' => 'Veri okunamadı']);
    exit;
}

$kullanici_id = intval($data['kullanici_id'] ?? 0);
$ad           = trim($data['ad'] ?? '');
$soyad        = trim($data['soyad'] ?? '');
$telefon      = trim($data['telefon'] ?? '');
$adres        = trim($data['adres'] ?? '');
$il           = trim($data['il'] ?? '');
$ilce         = trim($data['ilce'] ?? '');
$posta_kodu   = trim($data['posta_kodu'] ?? '');
$baslik       = trim($data['baslik'] ?? 'Ev');

if (!$kullanici_id || !$adres || !$il) {
    echo json_encode(['basari' => false, 'mesaj' => 'Zorunlu alanlar eksik (adres, il)']);
    exit;
}

$stmt = $pdo->prepare("
    INSERT INTO adresler (kullanici_id, ad, soyad, telefon, adres, il, ilce, posta_kodu, baslik)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
");
$stmt->execute([$kullanici_id, $ad, $soyad, $telefon, $adres, $il, $ilce, $posta_kodu, $baslik]);

echo json_encode([
    'basari' => true,
    'mesaj'  => 'Adres kaydedildi',
    'id'     => $pdo->lastInsertId()
]);
