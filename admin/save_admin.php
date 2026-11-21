<?php
session_start();
include '../ulc/db.php';
$db = new db();


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Admin bilgilerini al
   $adminusername = $_POST['adminusername'];
$adminadsoyad = $_POST['adminadsoyad'];
 $adminpassword = password_hash($_POST['adminpassword'], PASSWORD_DEFAULT); // Şifreyi hashle
    $adminemail = $_POST['adminemail'];
     $admingsm = $_POST['admingsm'];

    // Admin bilgilerini veritabanına ekle
  
		
		
	$result = $db->query(
    "INSERT INTO ayarlar (adminusername, adminadsoyad, adminpassword, adminemail, admingsm) VALUES (?, ?, ?, ?, ?)",
    [$adminusername, $adminadsoyad, $adminpassword, $adminemail, $admingsm]
);
	
	


    if ($result) {
        // Oturum başlat
        $_SESSION['logged'] = 1;
		$_SESSION['adminusername'] = $adminusername;

        // Step 2'ye yönlendir
        header('Location: kur.php?p=step2');
        exit;
    } else {
        // Hata mesajı göster
        echo "Admin kaydı oluşturulamadı.";
		
		if ($_SERVER['REMOTE_ADDR'] === '127.0.0.1' || $_SERVER['SERVER_NAME'] === 'localhost') {$db->debug();}
	
    }
} else {
    echo "Geçersiz istek methodu.";
	if ($_SERVER['REMOTE_ADDR'] === '127.0.0.1' || $_SERVER['SERVER_NAME'] === 'localhost') {$db->debug();}
}
?>