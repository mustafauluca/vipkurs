<?php
include '../ulc/db.php';
$db = new db();

// Veritabanı bağlantısı ve session kontrolü
$id = (int)$_GET['id'];

$delete =  $db->query("Update yetkiler set aktif = 0, arsiv = 1 where id = ?", [$id]);
//$db->debug();

echo json_encode(['success' => $delete]);

?>


	