<?php
$sf = isset( $_GET[ 'sf' ] ) ? $_GET[ 'sf' ] : 'main';

session_start();
include '../ulc/db.php';
$db = new db();




// Oturum kontrolü
if ( !isset( $_SESSION[ 'adminlogged' ] ) ) {
	// Oturum açılmamışsa, oturum açma formunu göster
	include 'login.php';
	exit;

}


//print_r( $_SESSION );

$adminusername = $_SESSION[ 'adminusername' ];

// Oturum açılmışsa, admin panelini göster
$ayarlar = $db->getRow( "SELECT * FROM ayarlar WHERE adminusername = ?", [ $adminusername ] );
?>
<!DOCTYPE html>
<html lang="tr">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Admin Paneli</title>
	<link rel="icon" type="image/x-icon" href="../favicon.ico">



	<!-- Bootstrap CSS -->
	  <!--link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css"-->
	<link rel="stylesheet" href="css/bootstrap.min.css">



</head>

<body>
	<div class="container mt-5">
		<div class="card">
			<div class="card-header">
    <div class="d-flex justify-content-between align-items-center">
        <!-- Sol Taraftaki Butonlar -->
        <div>
                  <a href="index.php" class="btn btn-primary">Anasayfa</a>

<a href="?sf=firma" class="btn btn-warning">FİRMA BİLGİLERİNİ GÜNCELLE</a>

<a href="?sf=users" class="btn btn-success">KULLANICILAR</a>

<a href="?sf=permissions" class="btn btn-info">YETKİLER</a>
			<a href="<?php if (strpos($ayarlar->kurumweb, 'http://') !== 0 && strpos($ayarlar->kurumweb, 'https://') !== 0) { $kurumweb = "https://" . $ayarlar->kurumweb;} else {$kurumweb = $ayarlar->kurumweb;} echo $kurumweb."/edusys"; ?>" target="_blank">Siteye Git</a>
        </div>
        <!-- Sağ Taraftaki Butonlar -->
        <div>
            <a href="?sf=adminprofil.php" class="btn btn-secondary">Profil (<?= $adminusername ?>)</a>
            <a href="logout.php" class="btn btn-danger">Çıkış Yap</a>
        </div>
    </div>
</div>

			<? if($sf=='main'):?>


			<? include 'onizleme.php';?>


			<? elseif($sf=='users'):?>
			<? include 'users.php';?>

			<? elseif($sf=='kurulum'):?>
			<? include 'kur.php';?>

			<? elseif($sf=='firma'):?>

			<? include 'firma.php';?>
			
					<? elseif($sf=='permissions'):?>

			<?  include 'permissions.php';?>

			<? else: ?>


			<? endif;?>
		</div>

		<!--script src="https://code.jquery.com/jquery-3.5.1.min.js"></script-->
		<script src="js/jquery-3.5.1.min.js"></script>
		
		<!--script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script-->
<script src="js/bootstrap.min.js"></script>

	
</body>
</html>