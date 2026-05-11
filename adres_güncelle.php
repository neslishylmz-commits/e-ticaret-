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
$adres_id     = intval($data['adres_id']     ?? 0);
$kullanici_id = intval($data['kullanici_id'] ?? 0);

if (!$adres_id || !$kullanici_id) {
    echo json_encode(['basari' => false, 'mesaj' => 'Eksik veri']); exit;
}

$ad         = trim($data['ad']         ?? '');
$soyad      = trim($data['soyad']      ?? '');
$telefon    = trim($data['telefon']    ?? '');
$adres      = trim($data['adres']      ?? '');
$il         = trim($data['il']         ?? '');
$ilce       = trim($data['ilce']       ?? '');
$posta_kodu = trim($data['posta_kodu'] ?? '');
$baslik     = trim($data['baslik']     ?? 'Ev');

$stmt = $pdo->prepare("
    UPDATE adresler
    SET ad=?, soyad=?, telefon=?, adres=?, il=?, ilce=?, posta_kodu=?, baslik=?
    WHERE id=? AND kullanici_id=?
");
$stmt->execute([$ad, $soyad, $telefon, $adres, $il, $ilce, $posta_kodu, $baslik, $adres_id, $kullanici_id]);

echo json_encode(['basari' => true, 'mesaj' => 'Adres güncellendi']);
