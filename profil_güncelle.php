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

$user_id = intval($data['user_id'] ?? 0);
if (!$user_id) {
    echo json_encode(['basari' => false, 'mesaj' => 'Kullanıcı bulunamadı']); exit;
}

$ad      = trim($data['ad']      ?? '');
$soyad   = trim($data['soyad']   ?? '');
$telefon = trim($data['telefon'] ?? '');
$adres   = trim($data['adres']   ?? '');
$il      = trim($data['il']      ?? '');
$ilce    = trim($data['ilce']    ?? '');

$stmt = $pdo->prepare("
    UPDATE user_data
    SET user_ad=?, user_soyad=?, user_telefon=?, user_adres=?, user_il=?, user_ilce=?
    WHERE user_id=?
");
$stmt->execute([$ad, $soyad, $telefon, $adres, $il, $ilce, $user_id]);

echo json_encode(['basari' => true, 'mesaj' => 'Profil güncellendi']);
