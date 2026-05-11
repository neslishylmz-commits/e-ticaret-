<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

$host   = "localhost";
$dbname = "loba";
$user   = "root";
$pass   = "";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(['basari' => false, 'mesaj' => 'DB hatası']);
    exit;
}

$user_id = intval($_GET['user_id'] ?? 0);
if (!$user_id) {
    echo json_encode(['basari' => false, 'mesaj' => 'user_id gerekli']);
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM adresler WHERE kullanici_id = ? ORDER BY id DESC");
$stmt->execute([$user_id]);
$adresler = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode(['basari' => true, 'adresler' => $adresler]);
