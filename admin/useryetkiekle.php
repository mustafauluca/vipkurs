<?php
include '../ulc/db.php';
$db = new db();

// Veritabanı bağlantısı ve session kontrolü
$data = json_decode(file_get_contents('php://input'), true);

// Yetki kontrolü ve validasyon
$insert = $db->query("inster into yetkiler (firma_id, yetki, kullanici_id) VALUES (?,?,?)", [
    $data['firma_id'], $data['yetki'], $data['kullanici_id']]);

//$db->debug();

echo json_encode(['success' => $insert, 'id' => $db->lastInsertId()]);

?>
