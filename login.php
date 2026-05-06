<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(200); exit; }

$host   = "localhost";
$dbname = "loba";
$user   = "root";
$pass   = "";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(['basari' => false, 'mesaj' => 'DB hatası: ' . $e->getMessage()]);
    exit;
}

$raw  = file_get_contents('php://input');
$data = json_decode($raw, true);

if (!$data || !isset($data['email'], $data['sifre'])) {
    echo json_encode(['basari' => false, 'mesaj' => 'Eksik veri']);
    exit;
}

$email = trim($data['email']);
$sifre = trim($data['sifre']);

$stmt = $pdo->prepare("SELECT * FROM user_data WHERE user_mail = ?");
$stmt->execute([$email]);
$kullanici = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$kullanici) {
    echo json_encode(['basari' => false, 'mesaj' => 'E-posta veya şifre hatalı']);
    exit;
}

if (!password_verify($sifre, $kullanici['user_sifre'])) {
    echo json_encode(['basari' => false, 'mesaj' => 'E-posta veya şifre hatalı']);
    exit;
}

unset($kullanici['user_sifre']);

echo json_encode(['basari' => true, 'kullanici' => $kullanici]);