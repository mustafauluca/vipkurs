<?php
session_start();
include '../ulc/db.php';
$db = new db();

error_log("save.php dosyası çalıştı."); // Log ekleyerek dosyanın çalıştığını kontrol et

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    error_log("POST verileri: " . print_r($_POST, true)); // Gelen POST verilerini logla

    // Admin bilgilerini al
    $adminusername = $_POST['adminusername'];
    $adminpassword = password_hash($_POST['adminpassword'], PASSWORD_DEFAULT); // Şifreyi hashle
    $adminemail = $_POST['adminemail'];
    $admingsm = $_POST['admingsm'];

    // Admin bilgilerini veritabanına ekle
    $sql = "INSERT INTO ayarlar (adminusername, adminpassword, adminemail, admingsm) 
            VALUES (:adminusername, :adminpassword, :adminemail, :admingsm)";
    $result = $db->query($sql, [
        'adminusername' => $adminusername,
        'adminpassword' => $adminpassword,
        'adminemail' => $adminemail,
        'admingsm' => $admingsm
    ]);

    if ($result) {
        // Oturum başlat
        $_SESSION['admin'] = [
            'adminusername' => $adminusername,
            'adminemail' => $adminemail,
            'admingsm' => $admingsm
        ];

        // Başarılı yanıt döndür
        echo json_encode(['status' => 'success', 'message' => 'Admin kaydı başarıyla oluşturuldu ve oturum açıldı.']);
    } else {
        // Hata yanıtı döndür
        echo json_encode(['status' => 'error', 'message' => 'Admin kaydı oluşturulamadı.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Geçersiz istek methodu.']);
}