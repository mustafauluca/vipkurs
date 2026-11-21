<?php
$d = isset( $_GET[ 'd' ] ) ? $_GET[ 'd' ] : 'none';
session_start();
include '../ulc/db.php';
$db = new db();

// Oturum kontrolü
if (!isset($_SESSION['logged'])) {
 die("Yetkisiz erişim!");
}

echo $adminusername = $_SESSION['adminusername'];

if($d=='bilgi') {
	
	
// Formdan gelen verileri al

 $kurumadi = $_POST['kurumadi'];
 $kurumunvan = $_POST['kurumunvan'];
 $kurumadres = $_POST['kurumadres'];
 $kurumtelefon1 = $_POST['kurumtelefon1'];
 $kurumtelefon2 = $_POST['kurumtelefon2'];
 $kurumgsm = $_POST['kurumgsm'];
 $kurumeposta = $_POST['kurumeposta'];
 $kurumweb = $_POST['kurumweb'];
 


// Veritabanını güncelle
$db->query("
 UPDATE ayarlar SET
 kurumadi = ?,
 kurumunvan = ?,
 kurumadres = ?,
 kurumtelefon1 = ?,
 kurumtelefon2 = ?,
 kurumgsm = ?,
 kurumeposta = ?,
 kurumweb = ?
 WHERE adminusername = ?
", [$kurumadi, $kurumunvan, $kurumadres, $kurumtelefon1, $kurumtelefon2, $kurumgsm, $kurumeposta, $kurumweb, $adminusername]);

// Başarılı mesajı ve yönlendirme
echo $_SESSION['message'] = "Firma bilgileri başarıyla güncellendi."; }
	
	
elseif($d=='ayar') {
	
	
		
	
// Formdan gelen verileri al

 
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


// Veritabanını güncelle
$db->query("
 UPDATE ayarlar SET
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
 WHERE adminusername = ?
", [$posbankalari, $kurumhesaplari, $sahsihesaplar, $gelirkategori, $giderkategori, $borckategori, $siniflar, $egitimyillari,
 $taksitsayisidefault, 	 $taksitgunudefault, $odemesekilleri, $adminusername]);

// Başarılı mesajı ve yönlendirme
echo $_SESSION['message'] = "Firma Ayarları başarıyla güncellendi.";
	$db->getError();
	
	
}
	
	elseif ($d=='none') {
		
		echo "nothing";
	}




header('Location: index.php');
exit;