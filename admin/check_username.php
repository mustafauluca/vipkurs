<?php
// Veritabanı bağlantısı
require_once '../ulc/db.php'; // Veritabanı bağlantı dosyasını dahil et

$db = new DB();

// Kullanıcı adını al
$kadi = $_POST['kadi'];
//$kadi = $_GET['kadi'];

// Kullanıcı adının veritabanında olup olmadığını kontrol et
$result = $db->getVar("SELECT COUNT(*) FROM kullanicilar WHERE kadi = ?", [$kadi]);

if ($result > 0) {
    echo 'exists'; // Kullanıcı adı zaten varsa
} else {
    echo 'available'; // Kullanıcı adı uygun ise
}
?>