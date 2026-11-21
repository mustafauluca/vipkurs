
<?
error_reporting(E_ALL);
ini_set('display_errors', 1);
?><!-- Cropper.js CSS -->
<link href="css/cropper.min.css" rel="stylesheet">

<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database and user initialization
$editMode = (isset($_GET['md']) && $_GET['md'] == 'edit');
$userData = null;

if ($editMode && isset($_GET['id'])) {
    $userId = $_GET['id'];
    $userData = $db->getRow("SELECT * FROM kullanicilar WHERE id = ?", [$userId]);
    
    if (!$userData) {
        echo "<div class='alert alert-danger'>Kullanıcı bulunamadı!</div>";
        $editMode = false;
    }
}

// Form submission handling
// Form submission handling
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $file_name = $editMode ? $userData->foto : '';
    $photoAction = $_POST['photo_action'] ?? 'keep';

    // Handle photo upload/delete
    if (isset($_POST['foto']) && $_POST['foto'] != '' && strpos($_POST['foto'], 'data:image') !== false) {
        // Process new photo upload only if it's a valid base64 image
        $base64_image = $_POST['foto'];
        $image_parts = explode(";base64,", $base64_image);
        
        if (count($image_parts) == 2) {
            $image_type_aux = explode("image/", $image_parts[0]);
            $image_type = $image_type_aux[1];
            $image_base64 = base64_decode($image_parts[1]);
            
            if ($image_base64 !== false) {
                $file_name = $_POST['kadi'] . '.jpg';
                $file_path = '../edusys/media/users/' . $file_name;
                
                // Create directory if not exists
                if (!file_exists('../edusys/media/users')) {
                    mkdir('../edusys/media/users', 0755, true);
                }

                // Save new photo
                if (file_put_contents($file_path, $image_base64)) {
                    // Archive old photo if exists
                    if ($editMode && !empty($userData->foto)) {
                        archivePhoto($userData->foto);
                    }
                } else {
                    $error = error_get_last();
                    die("Dosya yazma hatası: " . $error['message']);
                }
            }
        }
    } 
    elseif ($photoAction == 'delete') {
        // Handle photo deletion
        if ($editMode && !empty($userData->foto)) {
            archivePhoto($userData->foto);
            $file_name = '';
        }
    }
    else {
        // Keep existing photo (edit mode only)
        if ($editMode && !empty($userData->foto)) {
            $file_name = $userData->foto;
        } else {
            $file_name = '';
        }
    }

    // Process other form data
    $tcno = $_POST['tcno'];
    $kadi = $_POST['kadi'];
    $adi = $_POST['adi'];
    $sadi = $_POST['sadi'];
    $gsm = $_POST['gsm'];
    $foto = $file_name;
    $eposta = $_POST['eposta'];
    $gorev = $_POST['gorev'];
    $yetki = $_POST['yetki'];
    $aktif = $_POST['aktif'];
    $firmaid = $db->getVar("SELECT id FROM ayarlar WHERE adminusername = ?", [$adminusername]);

    if ($editMode) {
        // Update existing user
        $userId = $_POST['user_id'];
        $updateQuery = "UPDATE kullanicilar SET 
                        tcno = ?, kadi = ?, adi = ?, sadi = ?, gsm = ?, 
                        foto = ?, eposta = ?, gorev = ?, yetki = ?, aktif = ?";
        
        $params = [$tcno, $kadi, $adi, $sadi, $gsm, $foto, $eposta, $gorev, $yetki, $aktif];
        
        if (!empty($_POST['parola'])) {
            $updateQuery .= ", parola = ?";
            $params[] = password_hash($_POST['parola'], PASSWORD_DEFAULT);
        }
        
        $updateQuery .= " WHERE id = ?";
        $params[] = $userId;
        
        if ($db->query($updateQuery, $params)) {
            echo "<div class='alert alert-success'>Kullanıcı başarıyla güncellendi!</div>";
        } else {
            echo "<div class='alert alert-error'>Kullanıcı güncellenemedi!</div>";
        }
    } else {
        // Create new user
        if ($db->getVar("SELECT COUNT(*) FROM kullanicilar WHERE kadi = ?", [$kadi]) > 0) {
            echo "<div class='alert alert-error'>Bu kullanıcı adı zaten kayıtlı!</div>";
        } else {
            $parola = password_hash($_POST['parola'], PASSWORD_DEFAULT);
            $query = "INSERT INTO kullanicilar (firmaid, tcno, kadi, adi, sadi, gsm, foto, eposta, gorev, parola, yetki, aktif) 
                      VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            
            if ($db->query($query, [$firmaid, $tcno, $kadi, $adi, $sadi, $gsm, $foto, $eposta, $gorev, $parola, $yetki, $aktif])) {
                echo "<div class='alert alert-success'>Kullanıcı başarıyla eklendi!</div>";
            } else {
                echo "<div class='alert alert-error'>Kullanıcı eklenemedi!</div>";
            }
        }
    }
}



/**
 * Archives a photo to the _arsiv directory
 */
function archivePhoto($filename) {
    $source = '../edusys/media/users/' . $filename;
    $archive_dir = '../edusys/media/users/_arsiv/';
    
    if (!file_exists($source)) {
        error_log("Arşivlenecek dosya bulunamadı: $source");
        return false;
    }
    
    if (!file_exists($archive_dir)) {
        mkdir($archive_dir, 0755, true);
    }
    
    $pathinfo = pathinfo($filename);
    $counter = 1;
    $archive_path = $archive_dir . $filename;
    
    while (file_exists($archive_path)) {
        $archive_path = $archive_dir . $pathinfo['filename'] . '_' . $counter . '.' . $pathinfo['extension'];
        $counter++;
    }
    
    if (!rename($source, $archive_path)) {
        error_log("Fotoğraf arşivlenemedi: $source -> $archive_path");
        return false;
    }
    
    return true;
}

 $userData = $db->getRow("SELECT * FROM kullanicilar WHERE id = ?", [$userId]);

?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title><?= $editMode ? 'Kullanıcı Düzenle' : 'Kullanıcı Ekle' ?></title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/cropper.min.css" rel="stylesheet">
    <style>
        .profile-picture { width: 150px; height: 150px; border-radius: 50%; object-fit: cover; }
        .modal-content { padding: 20px; }
        #cropImage { max-width: 100%; max-height: 400px; }
        .cropper-container { width: 100% !important; height: 500px !important; }
        .cropper-canvas { width: 100% !important; height: 100% !important; }
        .modal-body { padding: 0; }
        .modal-dialog { max-width: 800px; }
        .cv-view { border: 1px solid #ddd; padding: 20px; border-radius: 10px; background-color: #f9f9f9; }
    </style>
</head>
	


<body>
	<div class="container mt-5">
        <h2 class="mb-4"><?= $editMode ? 'Kullanıcı Düzenle' : 'Kullanıcı Ekle' ?></h2>
        <div class="cv-view">
            <div class="text-center">
                <img src="../edusys/media/users/<? if($userData->foto<>'') { echo $userData->foto;} else {echo  '../blank-profile.png';} ?>" 
                     alt="Profil Fotoğrafı" class="profile-picture" id="profilePicture">
                <div class="mt-2">
                    <input type="file" id="fotoUpload" style="display: none;" accept="image/*">
                    <button type="button" class="btn btn-secondary btn-sm" id="selectPhoto">Fotoğraf Yükle</button>
                    <button type="button" class="btn btn-danger btn-sm" onclick="deletePhoto()">Fotoğrafı Sil</button>
                </div>
            </div>

            <div class="modal fade" id="cropModal" tabindex="-1" aria-labelledby="cropModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg">
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

            <form method="POST" action="" enctype="multipart/form-data">
                <?php if ($editMode): ?>
                    <input type="hidden" name="user_id" value="<?= $userData->id ?>">
                <?php endif; ?>
                
                <input type="hidden" id="foto" name="foto" value="">
                <input type="hidden" id="photo_action" name="photo_action" value="keep">
				
				
				
				
                
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="tcno">TC No</label>
                        <input type="text" class="form-control" id="tcno" name="tcno" 
                               pattern="\d{11}" 
                               maxlength="11" 
                               title="TC Kimlik Numarası 11 haneli olmalı ve sadece rakam içermelidir." 
                               value="<?= $editMode ? $userData->tcno : '' ?>" required>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="kadi">Kullanıcı Adı (Türkçe karakter kullanmayınız, en az 3 karakter)</label>
                        <input type="text" class="form-control" id="kadi" name="kadi" 
                               pattern="[a-zA-Z0-9]{3,}" 
                               oninput="this.value = this.value.replace(/[^a-zA-Z0-9]/g, '')" 
                               minlength="3"
                               title="Kullanıcı adı yalnızca İngilizce harfler ve rakamlar içermeli, en az 3 karakter olmalıdır." 
                               value="<?= $editMode ? $userData->kadi : '' ?>" <?= $editMode ? 'readonly' : '' ?> required>
                        <small id="kadiHelp" class="form-text text-muted"></small>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="adi">Adı</label>
                        <input type="text" class="form-control" id="adi" name="adi" value="<?= $editMode ? $userData->adi : '' ?>" required>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="sadi">Soyadı</label>
                        <input type="text" class="form-control" id="sadi" name="sadi" value="<?= $editMode ? $userData->sadi : '' ?>" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="gsm">GSM</label>
                        <input type="text" class="form-control" id="gsm" name="gsm" value="<?= $editMode ? $userData->gsm : '' ?>" required>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="eposta">E-Posta</label>
                        <input type="email" class="form-control" id="eposta" name="eposta" value="<?= $editMode ? $userData->eposta : '' ?>" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="gorev">Görev</label>
                        <input type="text" class="form-control" id="gorev" name="gorev" value="<?= $editMode ? $userData->gorev : '' ?>" required>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="parola">Parola <?= $editMode ? '(Değiştirmek istemiyorsanız boş bırakın)' : '' ?></label>
                        <input type="password" class="form-control" id="parola" name="parola" <?= $editMode ? '' : 'required' ?>>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="yetki">Yetki</label>
                        <select class="form-control" id="yetki" name="yetki" required>
                            <option value="10" <?= ($editMode && $userData->yetki == 10) ? 'selected' : '' ?>>Müdür</option>
                            <option value="9" <?= ($editMode && $userData->yetki == 9) ? 'selected' : '' ?>>Bilgi İşlem</option>
                            <option value="8" <?= ($editMode && $userData->yetki == 8) ? 'selected' : '' ?>>Öğrenci İşleri</option>
							<option value="7" <?= ($editMode && $userData->yetki == 7) ? 'selected' : '' ?>>Öğrenci Kayıt</option>
                            <option value="6" <?= ($editMode && $userData->yetki == 6) ? 'selected' : '' ?>>Muhasebe</option>                       						
                            <option value="5" <?= ($editMode && $userData->yetki == 5) ? 'selected' : '' ?>>Raporlar</option>
                            <option value="4" <?= ($editMode && $userData->yetki == 4) ? 'selected' : '' ?>>Sınav</option>
                            <option value="3" <?= ($editMode && $userData->yetki == 3) ? 'selected' : '' ?>>Kütüphane</option>
                            <option value="2" <?= ($editMode && $userData->yetki == 2) ? 'selected' : '' ?>>Mağaza</option>
                            <option value="1" <?= ($editMode && $userData->yetki == 1) ? 'selected' : '' ?>>Kantin</option>
                        </select>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="aktif">Aktif</label>
                        <select class="form-control" id="aktif" name="aktif" required>
                            <option value="1" <?= ($editMode && $userData->aktif == 1) ? 'selected' : '' ?>>Aktif</option>
                            <option value="0" <?= ($editMode && $userData->aktif == 0) ? 'selected' : '' ?>>Pasif</option>
                        </select>
                    </div>
                </div>
              <button type="submit" class="btn btn-primary"><?= $editMode ? 'Güncelle' : 'Ekle' ?></button>
                <a href="?sf=users" class="btn btn-secondary">İptal</a>
            </form>
        </div>
    </div>

    <script src="js/jquery-3.6.0.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/cropper.min.js"></script>

    <script>
    $(document).ready(function() {
        let cropper;
        const fotoUpload = document.getElementById("fotoUpload");
        const selectPhoto = document.getElementById("selectPhoto");
        const cropModal = new bootstrap.Modal(document.getElementById("cropModal"));
        const cropImage = document.getElementById("cropImage");
        const profilePicture = document.getElementById("profilePicture");
        const fotoInput = document.getElementById("foto");
        const photoAction = document.getElementById("photo_action");

        // Initialize with existing photo if in edit mode
        <?php if ($editMode && !empty($userData->foto)): ?>
            $("#foto").val("<?= $userData->foto ?>");
        <?php endif; ?>

        // Photo selection handler
        selectPhoto.addEventListener("click", function() {
            fotoUpload.click();
        });

        // File selection handler
        fotoUpload.addEventListener("change", function(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
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

        // Crop and save handler
        document.getElementById("cropButton").addEventListener("click", function() {
            if (cropper) {
                const croppedCanvas = cropper.getCroppedCanvas({
                    width: 512,
                    height: 512,
                });

                if (croppedCanvas) {
                    const croppedImageBase64 = croppedCanvas.toDataURL("image/jpeg", 0.9);
                    profilePicture.src = croppedImageBase64;
                    document.getElementById("foto").value = croppedImageBase64;
                    document.getElementById("photo_action").value = "new";
                    cropModal.hide();
                }
            }
        });

        // Delete photo handler
        window.deletePhoto = function() {
            if (confirm("Fotoğrafı silmek istediğinize emin misiniz?")) {
                document.getElementById('profilePicture').src = '../edusys/media/blank-profile.png';
                document.getElementById('foto').value = '';
                document.getElementById('photo_action').value = 'delete';
            }
        };

        // Username availability check (for new users only)
        <?php if (!$editMode): ?>
            $('#kadi').on('input', function() {
                var kadi = $(this).val();
                $.ajax({
                    url: 'check_username.php',
                    type: 'POST',
                    data: { kadi: kadi },
                    success: function(response) {
                        if (response === 'exists') {
                            $('#kadiHelp').text('Bu kullanıcı adı zaten kayıtlı!')
                                .removeClass('text-muted').addClass('text-danger');
                        } else {
                            $('#kadiHelp').text('Bu kullanıcı adı kullanılabilir.')
                                .removeClass('text-danger').addClass('text-success');
                        }
                    }
                });
            });
        <?php endif; ?>
    });

    // TC No validation
    document.getElementById('tcno').addEventListener('input', function(e) {
        const tcno = e.target.value;
        const isValid = /^\d{11}$/.test(tcno);
        e.target.setCustomValidity(isValid ? '' : 'TC Kimlik Numarası 11 haneli olmalı ve sadece rakam içermelidir.');
    });
    </script>
</body>
</html>