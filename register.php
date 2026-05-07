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

if (!$data || !isset($data['ad'], $data['email'], $data['sifre'])) {
    echo json_encode(['basari' => false, 'mesaj' => 'Veri okunamadı', 'raw' => $raw]);
    exit;
}

$ad      = trim($data['ad']);
$soyad   = trim($data['soyad']   ?? '');
$telefon = trim($data['telefon'] ?? '');
$email   = trim($data['email']);
$sifre   =      $data['sifre'];
$adres   = trim($data['adres']   ?? '');
$il      = trim($data['il']      ?? '');
$ilce    = trim($data['ilce']    ?? '');

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['basari' => false, 'mesaj' => 'Geçersiz e-posta']);
    exit;
}

$stmt = $pdo->prepare("SELECT user_id FROM user_data WHERE user_mail = ?");
$stmt->execute([$email]);
if ($stmt->fetch()) {
    echo json_encode(['basari' => false, 'mesaj' => 'Bu e-posta zaten kayıtlı']);
    exit;
}

$sifreHash = password_hash($sifre, PASSWORD_BCRYPT);

$stmt = $pdo->prepare("
    INSERT INTO user_data (user_ad, user_soyad, user_telefon, user_mail, user_sifre, user_adres, user_il, user_ilce)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?)
");
$stmt->execute([$ad, $soyad, $telefon, $email, $sifreHash, $adres, $il, $ilce]);

echo json_encode(['basari' => true, 'mesaj' => 'Kayıt başarılı', 'user_id' => $pdo->lastInsertId()]);
