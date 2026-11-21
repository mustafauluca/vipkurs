<?php
include '../ulc/db.php';
$db = new db();

$type = $_POST['type'];
$value = $_POST['value'];

if ($type === 'adminusername') {
    // Admin kullanıcı adı kontrolü
    $result = $db->getVar("SELECT COUNT(*) FROM ayarlar WHERE adminusername = ?", [$value]);
} elseif ($type === 'kurumadi') {
    // Firma adı kontrolü
    $result = $db->getVar("SELECT COUNT(*) FROM ayarlar WHERE kurumadi = ?", [$value]);
}

echo ($result > 0) ? 'exists' : 'available';