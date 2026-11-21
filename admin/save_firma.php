<?php
session_start();
include '../ulc/db.php';
$db = new db();

// Oturum kontrolü
if (!isset($_SESSION['logged'])) {
    header('Location: kur.php');
    
} else {
	
	$adminusername = $_SESSION['adminusername'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Firma bilgilerini al
    $kurumadi = $_POST['kurumadi'];
    $kurumunvan = $_POST['kurumunvan'];
    $kurumadres = $_POST['kurumadres'];
    $kurumtelefon1 = $_POST['kurumtelefon1'];
    $kurumtelefon2 = $_POST['kurumtelefon2'];
    $kurumgsm = $_POST['kurumgsm'];
    $kurumeposta = $_POST['kurumeposta'];
    $kurumweb = $_POST['kurumweb'];
	

    // Firma bilgilerini veritabanına ekle
   $result = $db->query("UPDATE ayarlar SET 
            kurumadi = ?, 
            kurumunvan = ?, 
            kurumadres = ?, 
            kurumtelefon1 = ?, 
            kurumtelefon2 = ?, 
            kurumgsm = ?, 
            kurumeposta = ?, 
            kurumweb = ?,
			uid = MD5(now())
            WHERE adminusername = ?",[$kurumadi,$kurumunvan,$kurumadres,$kurumtelefon1,$kurumtelefon2,$kurumgsm,$kurumeposta,$kurumweb,$adminusername]);
	   

    if ($result) {
        // Step 3'e yönlendir
        header('Location: kur.php?p=step3');
        exit;
    } else {
        echo "Firma bilgileri kaydedilemedi.";
			if ($_SERVER['REMOTE_ADDR'] === '127.0.0.1' || $_SERVER['SERVER_NAME'] === 'localhost') {$db->debug();}
    }
} else {
    echo "Geçersiz istek methodu.";
		if ($_SERVER['REMOTE_ADDR'] === '127.0.0.1' || $_SERVER['SERVER_NAME'] === 'localhost') {$db->debug();}
}
?>