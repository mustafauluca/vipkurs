<?php $sf = isset( $_GET[ 'sf' ] ) ? $_GET[ 'sf' ] : 'main'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
	<? include("main/header.php");?>
</head>
<body class="index-page"> <? include("main/navbar.php")?>
	<main class="main">
		<?php
		switch ( $sf ) {
			case 'main':
				include( "main/home.php" );
				break;

			case 'aboutus':
				include( "pages/hakkimizda.php" );
				break;

			case 'courses':
				include( "pages/kurslar.php" );
				break;

			case 'trainers':
				include( "pages/egitmenler.php" );
				break;

			case 'library':
				include( "pages/kutuphane.php" );
				break;

			case 'pricing':
				include( "pages/fiyatlar.php" );
				break;

			case 'contact':
				include( "pages/iletisim.php" );
				break;

			default:
				echo "Ana Sayfa";
				break;
		}
		?>
	</main>
	<? include("main/footer.php");?>
	<!-- Scroll Top -->
	<a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>
	<!-- Preloader -->
	<div id="preloader"></div>
	<!-- Vendor JS Files -->
	<script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
	<script src="assets/vendor/php-email-form/validate.js"></script>
	<script src="assets/vendor/aos/aos.js"></script>
	<script src="assets/vendor/glightbox/js/glightbox.min.js"></script>
	<script src="assets/vendor/purecounter/purecounter_vanilla.js"></script>
	<script src="assets/vendor/swiper/swiper-bundle.min.js"></script>
	<!-- Main JS File -->
	<script src="assets/js/main.js"></script>
</body>
</html>