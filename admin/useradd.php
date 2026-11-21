<!-- Cropper.js CSS -->
<link href="css/cropper.min.css" rel="stylesheet">


<?php
// Form gönderildiğinde
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Fotoğraf kaydetme işlemi
	
	var_dump($_POST);
	 // Kullanıcı bilgilerini kaydetme işlemi
    $tcno = $_POST['tcno'];
    $kadi = $_POST['kadi'];
    $adi = $_POST['adi'];
    $sadi = $_POST['sadi'];
    $gsm = $_POST['gsm'];
   
    $eposta = $_POST['eposta'];
    $gorev = $_POST['gorev'];
    $parola = password_hash($_POST['parola'], PASSWORD_DEFAULT); // Şifreyi hash'le
    $yetki = $_POST['yetki'];
    $aktif = $_POST['aktif'];
	$firmaid = $db->getVar("select id from ayarlar where adminusername = ?",[$adminusername]) ;

	
	if($db->getVar("SELECT COUNT(*) FROM kullanicilar WHERE kadi = ?", [$kadi])>0)
	{
		
		
		 echo "<div class='alert alert-error'>Kullanıcı eklenemedi! ('.$kadi.' daha önceden kayıtlı)- <a href='?sf=users'>Kullanıcılara Geri Dön</a></div>";
		exit;
		
	}
	
	
if (!empty($_POST['foto'])) {
   
    $base64_image = $_POST['foto'];
    $image_parts = explode(";base64,", $base64_image);
    $image_base64 = base64_decode($image_parts[1]);
    $file_name = $_POST['kadi'] . '.jpg';
    $file_path = '../edusys/media/users/' . $file_name;
    file_put_contents($file_path, $image_base64);
    $foto = $file_name; // Assign the filename
  
} else {
    $foto = NULL; // Set to NULL when no photo
 
}


    // Veritabanına ekleme sorgusu
    $query = "INSERT INTO kullanicilar (firmaid, tcno, kadi, adi, sadi, gsm, foto, eposta, gorev, parola, yetki, aktif) 
              VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    if($db->query($query, [$firmaid, $tcno, $kadi, $adi, $sadi, $gsm, $foto, $eposta, $gorev, $parola, $yetki, $aktif])) {
		
	//	$kullaniciid = $db->getLastInsertId();
		
	//	$db->query("insert into yetkiler set firma_id = ?, yetki = ?, kullanici_id = ?, aktif = ?, olusturan = ?",[$firmaid,$yetki,$kullaniciid,1,$adminusername]);
		
		echo "<meta http-equiv='refresh' content='0;URL=index.php?sf=users'>";
		
		

    echo "<div class='alert alert-success'>Kullanıcı başarıyla eklendi! - <a href='?sf=users'>Kullanıcılara Geri Dön</a></div>";
	//	echo "<meta http-equiv='refresh' content='0;URL=index.php?sf=users'>";
	} else {
		
		if (preg_match('/^\d{11}$/', $tcno)) {     echo "TC Kimlik Numarası geçersiz!";   }
		
		 echo "<div class='alert alert-success'>Kullanıcı eklenemedi! ($kadi) daha önceden kayıtlı)- <a href='?sf=users'>Kullanıcılara Geri Dön</a></div>";
			if ($_SERVER['REMOTE_ADDR'] === '127.0.0.1' || $_SERVER['SERVER_NAME'] === 'localhost') {$db->debug();}
		
		echo "<meta http-equiv='refresh' content='0;URL=index.php?sf=users'>";
	}
		
}
?>

<style>
	
	.profile-picture {
    width: 150px;
    height: 150px;
    border-radius: 50%;
    object-fit: cover;
}

.modal-content {
    padding: 20px;
}

#cropImage {
    max-width: 100%;
    max-height: 400px;
}
	
	/* Cropper container ve canvas boyutları */
.cropper-container {
    width: 100% !important;
    height: 500px !important; /* İstediğiniz yüksekliği ayarlayın */
}

.cropper-canvas {
    width: 100% !important;
    height: 100% !important;
}

/* Modal içeriği */
.modal-body {
    padding: 0;
}

/* Modal boyutu */
.modal-dialog {
    max-width: 800px; /* İstediğiniz genişliği ayarlayın */
}
	</style>
	
	   <style>
        .profile-picture {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 20px;
        }
        .cv-view {
            border: 1px solid #ddd;
            padding: 20px;
            border-radius: 10px;
            background-color: #f9f9f9;
        }
    </style>
	

    <div class="container mt-5">
        <h2 class="mb-4">Kullanıcı Ekle</h2>
        <div class="cv-view">
            <!-- Fotoğraf Alanı -->
             <!-- Fotoğraf Alanı -->
    <div class="text-center">
        <img src="img/x.jpg" alt="Profil Fotoğrafı" class="profile-picture" id="profilePicture">
        <div class="mt-2">
            <input type="file" id="fotoUpload" style="display: none;" accept="image/*">
            <button type="button" class="btn btn-secondary btn-sm" id="selectPhoto">Fotoğraf Yükle</button>
            <button type="button" class="btn btn-danger btn-sm" onclick="deletePhoto()">Fotoğrafı Sil</button>
        </div>

    </div>
			
			
<!-- Kırpma Modalı -->
<div class="modal fade" id="cropModal" tabindex="-1" aria-labelledby="cropModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg"> <!-- modal-lg ile geniş modal -->
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="cropModalLabel">Fotoğrafı Kırp</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <img id="cropImage" style="max-width: 100%;">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Kapat</button>
                <button type="button" class="btn btn-primary" id="cropButton">Kırp ve Kaydet</button>
            </div>
        </div>
    </div>
</div>

            <!-- Kullanıcı Bilgileri Formu -->
            <form method="POST" action="" enctype="multipart/form-data">
				
				        <input type="hidden" id="foto" name="foto">
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="tcno">TC No</label>
    <input type="text" class="form-control" id="tcno" name="tcno" 
           pattern="\d{11}" 
           maxlength="11" 
           title="TC Kimlik Numarası 11 haneli olmalı ve sadece rakam içermelidir." 
           required >
                    </div>
                    <div class="form-group col-md-6">
        <label for="kadi">Kullanıcı Adı (Türkçe karakter kullanmayınız, en az 3 karakter)</label>
<input type="text" class="form-control" id="kadi" name="kadi" 
       pattern="[a-zA-Z0-9]{3,}" 
       oninput="this.value = this.value.replace(/[^a-zA-Z0-9]/g, '')" 
       minlength="3"
       title="Kullanıcı adı yalnızca İngilizce harfler ve rakamlar içermeli, en az 3 karakter olmalıdır." 
       required>
<small id="kadiHelp" class="form-text text-muted"></small>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="adi">Adı</label>
                        <input type="text" class="form-control" id="adi" name="adi" required>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="sadi">Soyadı</label>
                        <input type="text" class="form-control" id="sadi" name="sadi" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="gsm">GSM</label>
                        <input type="text" class="form-control" id="gsm" name="gsm" required>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="eposta">E-Posta</label>
                        <input type="email" class="form-control" id="eposta" name="eposta" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="gorev">Görev</label>
                        <input type="text" class="form-control" id="gorev" name="gorev" required >
                    </div>
                    <div class="form-group col-md-6">
                        <label for="parola">Parola </label>
                        <input type="password" class="form-control" id="parola" name="parola" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="yetki">Yetki</label>
                        <select class="form-control" id="yetki" name="yetki" required>
							<option value="">Kullanıcı Yetkisi Seçiniz</option>
                            <option value="10">Müdür</option>
							<option value="9">Bilgi İşlem</option>
							<option value="8">Öğrenci İşleri</option>
							<option value="7">Öğrenci Kayıt</option>
							<option value="6">Muhasebe</option>
                            <option value="5">Raporlar</option>
							<option value="4">Sınav</option>
							<option value="3">Kütüphane</option>
							<option value="2">Mağaza</option>
							<option value="1">Kantin</option>
							
                        </select>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="aktif">Aktif</label>
                        <select class="form-control" id="aktif" name="aktif" required>
                            <option value="1">Aktif</option>
                            <option value="0">Pasif</option>
                        </select>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Kullanıcı Ekle</button>
            </form>
        </div>
    </div>



    <!-- Bootstrap JS ve bağımlılıkları -->
  <!-- jQuery -->
<script src="js/jquery-3.6.0.min.js"></script>

<!-- Bootstrap JS -->
<script src="js/bootstrap.min.js"></script>



<!-- Cropper.js JS -->
<script src="js/cropper.min.js"></script>
    <!-- Cropper.js -->
    <script>
	document.addEventListener("DOMContentLoaded", function () {
    let cropper;
    const fotoUpload = document.getElementById("fotoUpload");
    const selectPhoto = document.getElementById("selectPhoto");
    const cropModal = new bootstrap.Modal(document.getElementById("cropModal")); // Bootstrap modal
    const cropImage = document.getElementById("cropImage");
    const profilePicture = document.getElementById("profilePicture");
    const fotoInput = document.getElementById("foto");

    // Fotoğraf seç butonuna tıklanınca
    selectPhoto.addEventListener("click", function () {
        fotoUpload.click();
    });

    // Dosya seçildiğinde
    fotoUpload.addEventListener("change", function (event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function (e) {
                // Cropper'ı başlat
                cropImage.src = e.target.result;
                cropModal.show(); // Modal'ı göster

                // Eğer önceki bir cropper örneği varsa temizle
                if (cropper) {
                    cropper.destroy();
                }

                // Yeni Cropper örneği oluştur
                cropper = new Cropper(cropImage, {
                    aspectRatio: 1, // 1:1 oran (Yüz çerçevesi için)
                    viewMode: 1, // Resmin taşmasını engeller (1: Sınırlı, 2: Taşma yok)
                    autoCropArea: 1, // Ekranda görünür olan kırpma alanı (1: Tamamı)
                    minContainerWidth: 500, // Minimum container genişliği
                    minContainerHeight: 500, // Minimum container yüksekliği
                    minCanvasWidth: 500, // Minimum canvas genişliği
                    minCanvasHeight: 500, // Minimum canvas yüksekliği
                });
            };
            reader.readAsDataURL(file);
        }
    });

    // Kırp ve Kaydet butonuna tıklanınca
 document.getElementById("cropButton").addEventListener("click", function () {
    if (cropper) {
        const croppedCanvas = cropper.getCroppedCanvas({
            width: 512,
            height: 512,
        });

        if (croppedCanvas) {
            // Kırpılan resmi base64 formatına çevir
            const croppedImageBase64 = croppedCanvas.toDataURL("image/jpeg", 0.9);

            // Profil resmini güncelle
            profilePicture.src = croppedImageBase64;

            // Gizli input'a base64 değerini ata
            document.getElementById("foto").value = croppedImageBase64;

            // Modal'ı kapat
            cropModal.hide();
        }
    }
});
});
	</script>
	
	<script>
		
	$(document).ready(function () {
    var isUsernameAvailable = false; // Kullanıcı adının uygun olup olmadığını tutar

    // Kullanıcı adı input alanına yazıldığında
    $('#kadi').on('input', function () {
        var kadi = $(this).val(); // Kullanıcı adını al

        // AJAX isteği gönder
        $.ajax({
            url: 'check_username.php', // Kontrol edecek PHP dosyası
            type: 'POST',
            data: { kadi: kadi },
            success: function (response) {
                if (response === 'exists') {
                    // Kullanıcı adı zaten varsa
                    $('#kadiHelp').text('Bu kullanıcı adı zaten kayıtlı!').removeClass('text-muted').addClass('text-danger');
                    isUsernameAvailable = false;
                } else {
                    // Kullanıcı adı uygun ise
                    $('#kadiHelp').text('Bu kullanıcı adı kullanılabilir.').removeClass('text-danger').addClass('text-success');
                    isUsernameAvailable = true;
                }
            },
            error: function () {
                // Hata durumunda
                $('#kadiHelp').text('Bir hata oluştu, lütfen tekrar deneyin.').removeClass('text-muted').addClass('text-danger');
                isUsernameAvailable = false;
            }
        });
    });

    // Form gönderildiğinde
    $('form').on('submit', function (e) {
        if (!isUsernameAvailable) {
            e.preventDefault(); // Form gönderimini engelle
            alert('Lütfen geçerli bir kullanıcı adı seçin!');
        }
    });
});
	
	</script>
	
	<script>
document.getElementById('tcno').addEventListener('input', function (e) {
    const tcno = e.target.value;
    const isValid = /^\d{11}$/.test(tcno); // Sadece 11 rakam olup olmadığını kontrol et

    if (!isValid) {
        e.target.setCustomValidity('TC Kimlik Numarası 11 haneli olmalı ve sadece rakam içermelidir.');
    } else {
        e.target.setCustomValidity('');
    }
});
		
		 // Fotoğraf silme işlevi
        function deletePhoto() {
            document.querySelector('.profile-picture').src = '../media/x.jpg'; // Varsayılan resim
			document.getElementById('foto').value = ''; // Fotoğraf URL'sini temizle
        }
</script>