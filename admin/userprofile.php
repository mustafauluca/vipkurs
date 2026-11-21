<?php
// Veritabanı bağlantısını dahil et
if (!isset($_SESSION['username']) || $_SESSION['username'] !== 'admin') {
    die("Erişim reddedildi. Bu sayfayı görüntüleme yetkiniz yok.");
}

// Tabloları tanımla
$tables = ['gelirler', 'giderler', 'taksitler', 'ogrenciler'];

// Form gönderildiğinde işlemleri yap
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Seçilen tabloları al
    $selectedTables = $_POST['tables'] ?? [];

    if (!empty($selectedTables)) {
        try {
            // Foreign key kontrollerini geçici olarak devre dışı bırak
            $conn->exec("SET FOREIGN_KEY_CHECKS = 0");

            // Seçilen tabloları TRUNCATE et
            foreach ($selectedTables as $table) {
                if (in_array($table, $tables)) { // Güvenlik kontrolü
                    $conn->exec("TRUNCATE TABLE $table");
                    echo "<p>$table tablosu başarıyla temizlendi.</p>";
                }
            }

            // Foreign key kontrollerini tekrar aktif et
            $conn->exec("SET FOREIGN_KEY_CHECKS = 1");
        } catch (PDOException $e) {
            echo "<p style='color: red;'>Hata: " . $e->getMessage() . "</p>";
        }
    } else {
        echo "<p style='color: red;'>Lütfen en az bir tablo seçin.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tablo Temizleme Formu</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        form { max-width: 400px; margin: auto; }
        label { display: block; margin: 10px 0 5px; }
        input[type="submit"] { margin-top: 20px; padding: 10px 20px; background-color: #4CAF50; color: white; border: none; cursor: pointer; }
        input[type="submit"]:hover { background-color: #45a049; }
    </style>
</head>
<body>
    <h1>Tablo Temizleme Formu</h1>
    <form method="POST" action="">
        <?php foreach ($tables as $table): ?>
            <label>
                <input type="checkbox" name="tables[]" value="<?php echo $table; ?>">
                <?php echo ucfirst($table); ?>
            </label>
        <?php endforeach; ?>
        <input type="submit" value="Seçili Tabloları Temizle">
    </form>
</body>
</html>