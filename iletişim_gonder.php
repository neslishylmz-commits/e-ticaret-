<?php
if (PHP_OS === 'WINNT' || PHP_OS === 'Windows') {
    $conn = new mysqli('localhost', 'root', '', 'loba');
} else {
    ini_set('mysqli.default_socket', '/Applications/XAMPP/xamppfiles/var/mysql/mysql.sock');
    $conn = new mysqli('localhost', 'root', '', 'loba');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ad      = htmlspecialchars(trim($_POST['ad'] ?? ''));
    $soyad   = htmlspecialchars(trim($_POST['soyad'] ?? ''));
    $email   = htmlspecialchars(trim($_POST['email'] ?? ''));
    $telefon = htmlspecialchars(trim($_POST['telefon'] ?? ''));
    $konu    = htmlspecialchars(trim($_POST['konu'] ?? ''));
    $mesaj   = htmlspecialchars(trim($_POST['mesaj'] ?? ''));

    if (empty($ad) || empty($soyad) || empty($email) || empty($mesaj)) {
        echo json_encode(['durum' => 'hata', 'mesaj' => 'Zorunlu alanları doldurun.']);
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['durum' => 'hata', 'mesaj' => 'Geçerli bir e-posta girin.']);
        exit;
    }

    if ($conn->connect_error) {
        echo json_encode(['durum' => 'hata', 'mesaj' => 'DB: ' . $conn->connect_error]);
        exit;
    }

    $stmt = $conn->prepare("INSERT INTO iletisim (ad, soyad, email, telefon, konu, mesaj) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param('ssssss', $ad, $soyad, $email, $telefon, $konu, $mesaj);

    if ($stmt->execute()) {
        echo json_encode(['durum' => 'basarili', 'mesaj' => 'Mesajınız alındı.']);
    } else {
        echo json_encode(['durum' => 'hata', 'mesaj' => 'Kayıt hatası.']);
    }

    $stmt->close();
    $conn->close();
}
?>
