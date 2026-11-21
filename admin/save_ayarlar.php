<?php
session_start();
include '../ulc/db.php';
$db = new db();

// Oturum kontrolü
if (!isset($_SESSION['logged'])) {
    header('Location: kur.php');
}
 else {
	
	$adminusername = $_SESSION['adminusername'];
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Varsayılan ayarları al
    $posbankalari = $_POST['posbankalari'];
    $kurumhesaplari = $_POST['kurumhesaplari'];
    $sahsihesaplar = $_POST['sahsihesaplar'];
    $gelirkategori = $_POST['gelirkategori'];
    $giderkategori = $_POST['giderkategori'];
    $borckategori = $_POST['borckategori'];
    $siniflar = $_POST['siniflar'];
    $egitimyillari = $_POST['egitimyillari'];
    $taksitsayisidefault = $_POST['taksitsayisidefault'];
    $taksitgunudefault = $_POST['taksitgunudefault'];
	$odemesekilleri = '';
    // Varsayılan ayarları veritabanına ekle
    $result = $db->query("UPDATE ayarlar SET 
            posbankalari = ?, 
            kurumhesaplari = ?, 
            sahsihesaplar = ?, 
            gelirkategori = ?, 
            giderkategori = ?, 
            borckategori = ?, 
            siniflar = ?, 
            egitimyillari = ?, 
            taksitsayisidefault = ?, 
            taksitgunudefault = ?, 
            odemesekilleri = ? 
            WHERE adminusername = ?", [$posbankalari,$kurumhesaplari, $sahsihesaplar, $gelirkategori, $giderkategori, $borckategori, $siniflar, $egitimyillari, $taksitsayisidefault, $taksitgunudefault, $odemesekilleri, $adminusername]);
  
   

    if ($result) {
        //Kurulum tamamlandı, admin paneline yönlendir
       	header('Location: index.php');
        exit;
    } else {
        echo "Varsayılan ayarlar kaydedilemedi.";
		if ($_SERVER['REMOTE_ADDR'] === '127.0.0.1' || $_SERVER['SERVER_NAME'] === 'localhost') {$db->debug();}
		//$db->getError();
    }
} else {
    echo "Geçersiz istek methodu.";
	if ($_SERVER['REMOTE_ADDR'] === '127.0.0.1' || $_SERVER['SERVER_NAME'] === 'localhost') {$db->debug();}
}
?>