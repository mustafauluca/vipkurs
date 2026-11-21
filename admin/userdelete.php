<?php
// Kullanıcı ID'sini al
$id = $_GET['id'];


$admin = $db->getRow("SELECT aktif FROM ayarlar WHERE adminusername = ?", [$adminusername]);
$user = $db->getRow("SELECT * FROM kullanicilar WHERE id = ?", [$id]);


if ($admin->aktif == 1) {
    // Kullanıcı bilgilerini veritabanından çek ve güncelle
    $delete = $db->query("UPDATE kullanicilar SET arsiv = 1, oldkadi = kadi, kadi = '' WHERE id = ?", [$id]);

    if ($delete) {
        // Kullanıcı resminin yolu
        $sourceDir = "../edusys/media/users/";
        $archiveDir = "../edusys/media/users/_arsiv/";
        $fileName = $user->foto; // Kullanıcının profil resmi dosya adı
        $sourcePath = $sourceDir . $fileName;
        $destinationPath = $archiveDir . $fileName;

        // Eğer kaynak dosya varsa taşıma işlemi yap
        if (file_exists($sourcePath)) {
            // Eğer _arsiv dizini yoksa oluştur
            if (!is_dir($archiveDir)) {
                mkdir($archiveDir, 0755, true);
            }

            // Dosya adı çakışmasını önlemek için kontrol yap
            $counter = 1;
            $fileInfo = pathinfo($fileName); // Dosya adı ve uzantısını ayır
            $baseName = $fileInfo['filename']; // Dosya adı (uzantı olmadan)
            $extension = $fileInfo['extension']; // Dosya uzantısı

            // Benzersiz bir dosya adı oluştur
            while (file_exists($destinationPath)) {
                $destinationPath = $archiveDir . $baseName . "_" . $counter . "." . $extension;
                $counter++;
            }

            // Dosyayı taşı
            if (rename($sourcePath, $destinationPath)) {
                echo "<div class='alert alert-success'>Kullanıcı başarıyla arşivlendi ve resmi taşındı! - <br> 
                - <a href='?sf=users'>Kullanıcılara Geri Dön</a></div>";
            } else {
                echo "<div class='alert alert-warning'>Kullanıcı arşivlendi ancak resim taşınamadı!</div>";
            }
        } else {
            echo "<div class='alert alert-success'>Kullanıcı başarıyla arşivlendi ancak resmi bulunamadı! - <br> 
            - <a href='?sf=users'>Kullanıcılara Geri Dön</a></div>";
        }
    }
} else {
    echo "<div class='alert alert-success'>Bu işlemi yapmaya yetkiniz yok - <br> 
    - <a href='?sf=users'>Kullanıcılara Geri Dön</a></div>";
}
?>