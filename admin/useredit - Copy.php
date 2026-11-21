<!-- Cropper.js CSS -->
<link href="css/cropper.min.css" rel="stylesheet">

<?php
// Kullanıcı ID'sini al
$id = $_GET['id'];

// Kullanıcı bilgilerini veritabanından çek
$kullanici = $db->getRow("SELECT * FROM kullanicilar WHERE id = ?", [$id]);

var_dump($_POST);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Fotoğrafı varsayılan olarak mevcut fotoğraf olarak ayarla
    $foto = $kullanici->foto;
    
    // Eğer yeni fotoğraf gönderildiyse
    if (isset($_POST['foto']) && $_POST['foto'] != '') {
        $base64_image = $_POST['foto'];
        $image_parts = explode(";base64,", $base64_image);
        $image_base64 = base64_decode($image_parts[1]);
        $file_name = $kullanici->kadi . '_' . time() . '.jpg'; // Kullanıcı adı + timestamp ile dosya adı
        $file_path = '../edusys/media/users/' . $file_name;
        
        // Eğer kullanıcının zaten bir fotoğrafı varsa
        if (!empty($kullanici->foto)) {
            // _arsiv dizinini oluştur (yoksa)
            $arsiv_path = '../edusys/media/users/_arsiv';
            if (!file_exists($arsiv_path)) {
                mkdir($arsiv_path, 0755, true);
            }
            
            // Mevcut fotoğrafın dosya adını ve yolunu al
            $old_file_name = basename($kullanici->foto);
            $old_file_path = '../edusys/media/users/' . $old_file_name;
            
            // Eğer eski dosya varsa, arşivle
            if (file_exists($old_file_path)) {
                $new_arsiv_path = $arsiv_path . '/' . $old_file_name;
                
                // Eğer arşivde aynı dosya varsa, zaman damgası ekle
                if (file_exists($new_arsiv_path)) {
                    $file_info = pathinfo($old_file_name);
                    $timestamp = time();
                    $new_arsiv_path = $arsiv_path . '/' . $file_info['filename'] . '_' . $timestamp . '.' . $file_info['extension'];
                }
                
                rename($old_file_path, $new_arsiv_path);
            }
        }
        
        // Yeni fotoğrafı kaydet
        if (file_put_contents($file_path, $image_base64)) {
            $foto = $file_name;
        }
    } elseif (isset($_POST['foto']) && $_POST['foto'] === '') {
        // Fotoğraf silinmişse
        $foto = 'x.jpg';
    }
    
    // Form verilerini al
    $tcno = $_POST['tcno']; 
    $adi = $_POST['adi'];
    $sadi = $_POST['sadi'];
    $gsm = $_POST['gsm'];    
    $eposta = $_POST['eposta'];
    $gorev = $_POST['gorev'];
    $parola = !empty($_POST['parola']) ? password_hash($_POST['parola'], PASSWORD_DEFAULT) : $kullanici->parola;
    $yetki = $_POST['yetki'];
    $aktif = $_POST['aktif'];

    // Veritabanını güncelle
    $query = "UPDATE kullanicilar SET tcno = ?, adi = ?, sadi = ?, foto = ?, gsm = ?, eposta = ?, gorev = ?, parola = ?, yetki = ?, aktif = ? WHERE id = ?";
    $db->query($query, [$tcno, $adi, $sadi, $foto, $gsm, $eposta, $gorev, $parola, $yetki, $aktif, $id]);

    echo "<div class='alert alert-success'>Kullanıcı başarıyla güncellendi!</div>";
    
    // Debug modda mı kontrol et
    if ($_SERVER['REMOTE_ADDR'] === '127.0.0.1' || $_SERVER['SERVER_NAME'] === 'localhost') {
        $db->debug();
    } else {    
        echo "<meta http-equiv='refresh' content='0;URL=index.php?sf=users'>";
    }
}

$kullanici = $db->getRow("SELECT * FROM kullanicilar WHERE id = ?", [$id]);
?>


<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Kullanıcı Ekle</title>
    <!-- Bootstrap CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
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
	
</head>

<body>
    <div class="container mt-5">
        <h2 class="mb-4">Kullanıcı Ekle</h2>
        <div class="cv-view">
            <!-- Fotoğraf Alanı -->
             <!-- Fotoğraf Alanı -->
    <div class="text-center">
		
		
     

		
		
        <img src="../edusys/media/users/<?php echo $kullanici->foto; ?>" alt="Profil Fotoğrafı" class="profile-picture" id="profilePicture">
        <div class="mt-2">
         
            <input type="file" id="fotoUpload" style="display: none;" accept="image/*">
			<input type="hidden" id="fotoUpload" name="foto" value="">
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
				
				      
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="tcno">TC No</label>
    <input type="text" class="form-control" id="tcno" name="tcno" 
           pattern="\d{11}" 
           maxlength="11" 
           title="TC Kimlik Numarası 11 haneli olmalı ve sadece rakam içermelidir." 
           required value="<?php echo $kullanici->tcno; ?>">
                    </div>
                    <div class="form-group col-md-6">
                        <label for="kadi">Kullanıcı Adı</label>
        <input type="text" class="form-control" id="kadi" name="kadi" required value="<?php echo $kullanici->kadi; ?>" disabled>
        <small id="kadiHelp" class="form-text text-muted"></small>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="adi">Adı</label>
                        <input type="text" class="form-control" id="adi" name="adi" required value="<?php echo $kullanici->adi; ?>">
                    </div>
                    <div class="form-group col-md-6">
                        <label for="sadi">Soyadı</label>
                        <input type="text" class="form-control" id="sadi" name="sadi" required value="<?php echo $kullanici->sadi; ?>">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="gsm">GSM</label>
                        <input type="text" class="form-control" id="gsm" name="gsm" required value="<?php echo $kullanici->gsm; ?>">
                    </div>
                    <div class="form-group col-md-6">
                        <label for="eposta">E-Posta</label>
                        <input type="email" class="form-control" id="eposta" name="eposta" required value="<?php echo $kullanici->eposta; ?>">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="gorev">Görev</label>
                        <input type="text" class="form-control" id="gorev" name="gorev" required value="<?php echo $kullanici->gorev; ?>">
                    </div>
                    <div class="form-group col-md-6">
                    <label for="parola">Parola (Değiştirmek istemiyorsanız boş bırakın)</label>
						
                        <input type="password" class="form-control" id="parola" name="parola">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="yetki">Yetki</label>
                        <select class="form-control" id="yetki" name="yetki" required>
                        <option value="11" <?php echo ($kullanici->yetki == 11) ? 'selected' : ''; ?>>Müdür</option>
							<option value="10" <?php echo ($kullanici->yetki == 10) ? 'selected' : ''; ?>>Öğrenci İşleri</option>
							<option value="9" <?php echo ($kullanici->yetki == 9) ? 'selected' : ''; ?>>İdari İşler</option>
							<option value="8" <?php echo ($kullanici->yetki == 8) ? 'selected' : ''; ?>>Muhasebe</option>
                            <option value="7" <?php echo ($kullanici->yetki == 7) ? 'selected' : ''; ?>>Danışman</option>
							<option value="6" <?php echo ($kullanici->yetki == 6) ? 'selected' : ''; ?>>Öğretmen</option>
							<option value="5" <?php echo ($kullanici->yetki == 5) ? 'selected' : ''; ?>>Kayıt Sorumlusu</option>
							<option value="4" <?php echo ($kullanici->yetki == 4) ? 'selected' : ''; ?>>Sınav Sorumlusu</option>
							<option value="3" <?php echo ($kullanici->yetki == 3) ? 'selected' : ''; ?>>Kütüphane Sorumlusu</option>
							<option value="2" <?php echo ($kullanici->yetki == 2) ? 'selected' : ''; ?>>Kırtasiye Sorumlusu</option>
							<option value="1" <?php echo ($kullanici->yetki == 1) ? 'selected' : ''; ?>>Kantin</option>


                        </select>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="aktif">Aktif</label>
                        <select class="form-control" id="aktif" name="aktif" required>
                      <option value="1" <?php echo ($kullanici->aktif == 1) ? 'selected' : ''; ?>>Aktif</option>
                            <option value="0" <?php echo ($kullanici->aktif == 0) ? 'selected' : ''; ?>>Pasif</option>
                        </select>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Kullanıcı Güncelle</button>
            </form>
        </div>
    </div>



    <!-- Bootstrap JS ve bağımlılıkları -->
  <!-- jQuery -->
<script src="js/jquery-3.6.0.min.js"></script>

<!-- Bootstrap JS -->
<script src="js/bootstrap.min.js"></script>



<!-- Cropper.js JS -->

<!-- Cropper.js JS -->
<script src="js/cropper.min.js"></script>
    <!-- Cropper.js -->
   <script>
document.addEventListener("DOMContentLoaded", function () {
    let cropper;
    const fotoUpload = document.getElementById("fotoUpload");
    const selectPhoto = document.getElementById("selectPhoto");
    const cropModal = new bootstrap.Modal(document.getElementById("cropModal"));
    const cropImage = document.getElementById("cropImage");
    const profilePicture = document.getElementById("profilePicture");
    const fotoInput = document.getElementById("foto");

    // Fotoğraf seç butonuna tıklanınca
    selectPhoto.addEventListener("click", function () {
        fotoUpload.value = ''; // Aynı dosyayı tekrar seçebilmek için
        fotoUpload.click();
    });

    // Dosya seçildiğinde
    fotoUpload.addEventListener("change", function (event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function (e) {
                cropImage.src = e.target.result;
                cropModal.show();

                if (cropper) {
                    cropper.destroy();
                }

                cropper = new Cropper(cropImage, {
                    aspectRatio: 1,
                    viewMode: 1,
                    autoCropArea: 1,
                    minContainerWidth: 500,
                    minContainerHeight: 500,
                    minCanvasWidth: 500,
                    minCanvasHeight: 500,
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
                const croppedImageBase64 = croppedCanvas.toDataURL("image/jpeg", 0.9);
                profilePicture.src = croppedImageBase64;
                document.getElementById("foto").value = croppedImageBase64;
                
                // Modal'ı kapat
                cropModal.hide();
                
                // Cropper'ı temizle
                cropper.destroy();
            }
        }
    });
    
    // Modal kapatıldığında cropper'ı temizle
    $('#cropModal').on('hidden.bs.modal', function () {
        if (cropper) {
            cropper.destroy();
        }
    });
});

// Fotoğraf silme işlevi
function deletePhoto() {
    if (confirm('Fotoğrafı silmek istediğinize emin misiniz?')) {
        document.getElementById('profilePicture').src = '../edusys/media/users/x.jpg';
        document.getElementById('foto').value = '';
    }
}
</script>
	
    <!-- Cropper.js -->
   