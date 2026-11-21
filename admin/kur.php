<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

$p = isset( $_GET[ 'p' ] ) ? $_GET[ 'p' ] : 'step1';



session_start();
include '../ulc/db.php';
$db = new db();

// Oturum kontrolü
//echo $_SERVER[ 'PHP_SELF'];


if (isset($_GET['token'])) {
    $hash = $_GET['token'];
    $record = $db->getRow("SELECT * FROM install WHERE hash = ? and aktif = 1", [$hash]);
	
	//$db->debugt();
	//echo $record->id;

    if (!$record) {
        exit("🔒 Geçersiz link.");
    }

    if ($record->used) {
        exit("⚠️ Bu link zaten kullanılmış.");
    }

    // Kullanılmamışsa işaretle
    $ip = $_SERVER['REMOTE_ADDR'];
    $db->query("UPDATE install SET used = 1, usedtime = NOW(), ip = ? WHERE id = ?", [$ip, $record->id]);
	
	//$db->debugt();
	
   //echo "UPDATE ET";
}



if (isset($_SESSION['logged'])==1) {

$isLoggedIn = isset( $_SESSION[ 'logged' ] ); // Oturum açılmış mı?

 $adminusername = $_SESSION['adminusername'];
$admin = $db->getRow("select * from ayarlar where adminusername = ?", [$adminusername]);
}
else {
	$isLoggedIn =0;
	
}

?>
<!DOCTYPE html>
<html lang="tr">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Kurulum Sihirbazı</title>
	<!-- Bootstrap CSS -->
	<link href="css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

	<div class="container mt-5">
		<div class="container mt-5">
			<div style="width: 100%; text-align: right">
				<?php if ($isLoggedIn==1): ?>
				<!-- Oturum açılmışsa "Çıkış Yap" bağlantısı -->
				<a href="logout.php">Çıkış Yap</a>
				<?php else: ?>
				<!-- Oturum açılmamışsa "Oturum Açın" bağlantısı -->
				Zaten kayıtlı firmanız var mı? <a href="login.php">Oturum Açın</a>
				<?php endif; ?>
			</div>
		</div>
		<div class="card">
			<div class="card-header">
				<ul class="nav nav-tabs card-header-tabs" id="wizardTabs" role="tablist">
					<li class="nav-item">
						<a class="nav-link active" id="step1-tab" data-toggle="tab" href="#step1" role="tab">1. Admin Kaydı</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" id="step2-tab" data-toggle="tab" href="#step2" role="tab">2. Firma Bilgileri</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" id="step3-tab" data-toggle="tab" href="#step3" role="tab">3. Varsayılan Ayarlar</a>
					</li>
				</ul>
			</div>
			<div class="card-body">
				<div class="tab-content" id="wizardContent">
					<!-- Adım 1: Admin Kaydı -->
					<div class="tab-pane fade show active" id="step1" role="tabpanel">
						
						<? include("step1.php")?>
						
					</div>

					<!-- Adım 2: Firma Bilgileri -->
					<div class="tab-pane fade" id="step2" role="tabpanel">
						<? include("step2.php")?>
						
					</div>

					<!-- Adım 3: Varsayılan Ayarlar -->
					<div class="tab-pane fade" id="step3" role="tabpanel">
						
						<? include("step3.php")?>
						
						
					</div>
				</div>
			</div>
		</div>
	</div>

	<!-- Bootstrap JS ve Bağımlılıkları -->
	<script src="js/jquery-3.5.1.min.js"></script>
	<script src="js/bootstrap.min.js"></script>
	<script>
		// Admin kullanıcı adı kontrolü
		function checkAdminUsername( username ) {
			$.ajax( {
				url: 'check_availability.php',
				type: 'POST',
				data: {
					type: 'adminusername',
					value: username
				},
				success: function ( response ) {
					if ( response === 'exists' ) {
						$( '#adminusernameFeedback' ).html( '<span style="color:red;">Bu kullanıcı adı zaten kayıtlı!</span>' );
					} else {
						$( '#adminusernameFeedback' ).html( '<span style="color:green;">Bu kullanıcı adı kullanılabilir.</span>' );
					}
				}
			} );
		}

		// Firma adı kontrolü
		function checkKurumAdi( kurumadi ) {
			$.ajax( {
				url: 'check_availability.php',
				type: 'POST',
				data: {
					type: 'kurumadi',
					value: kurumadi
				},
				success: function ( response ) {
					if ( response === 'exists' ) {
						$( '#kurumadiFeedback' ).html( '<span style="color:red;">Bu firma adı zaten kayıtlı!</span>' );
					} else {
						$( '#kurumadiFeedback' ).html( '<span style="color:green;">Bu firma adı kullanılabilir.</span>' );
					}
				}
			} );
		}
</script>
	<script>
        // PHP'den gelen $p değerini JavaScript'e aktar
        var currentStep = "<?php echo $p; ?>";

        // Sayfa yüklendiğinde ilgili tab'ı aç
        $(document).ready(function() {
            if (currentStep === "step2") {
                nextStep(2); // Step 2'yi aç
            } else if (currentStep === "step3") {
                nextStep(3); // Step 3'ü aç
            } else {
                nextStep(1); // Varsayılan olarak Step 1'i aç
            }
        });

        function nextStep(step) {
            $(`#wizardTabs .nav-link`).removeClass('active');
            $(`#wizardContent .tab-pane`).removeClass('show active');
            $(`#step${step}-tab`).addClass('active');
            $(`#step${step}`).addClass('show active');
        }

        function prevStep(step) {
            $(`#wizardTabs .nav-link`).removeClass('active');
            $(`#wizardContent .tab-pane`).removeClass('show active');
            $(`#step${step}-tab`).addClass('active');
            $(`#step${step}`).addClass('show active');
        }
    </script>
	


	<script>
		$( document ).ready( function () {
			$( '#ayarlarForm' ).on( 'submit', function ( e ) {
				e.preventDefault();
				var formData = $( this ).serialize();

				// Taksit sayısı ve taksit günü kontrolü
				if ( $( '#teksitsayisidefault' ).val() < 1 || $( '#teksitsayisidefault' ).val() > 99 ) {
					alert( 'Taksit sayısı 1 ile 99 arasında olmalıdır.' );
					return;
				}
				if ( $( '#taksitgunudefault' ).val() < 1 || $( '#taksitgunudefault' ).val() > 31 ) {
					alert( 'Taksit günü 1 ile 31 arasında olmalıdır.' );
					return;
				}


				
		


			} );
		} );
		
		
		

		
	</script>
	
	
	<script>
	$(document).ready(function() {
    $('#adminForm').on('submit', function(e) {
        // Admin kullanıcı adı kontrolü
        var adminUsernameFeedback = $('#adminusernameFeedback').text();
        if (adminUsernameFeedback.includes('zaten kayıtlı')) {
            alert('Lütfen farklı bir kullanıcı adı seçin.');
            e.preventDefault(); // Formu gönderme
            return;
        }

        // Formu gönder
        // this.submit(); // Bu satırı kaldırın, form otomatik olarak gönderilecek
    });

    $('#firmaForm').on('submit', function(e) {
        // Firma adı kontrolü
        var kurumAdiFeedback = $('#kurumadiFeedback').text();
        if (kurumAdiFeedback.includes('zaten kayıtlı')) {
            alert('Lütfen farklı bir firma adı seçin.');
            e.preventDefault(); // Formu gönderme
            return;
        }

        // Formu gönder
        // this.submit(); // Bu satırı kaldırın, form otomatik olarak gönderilecek
    });
});
	</script>
</body>
</html>