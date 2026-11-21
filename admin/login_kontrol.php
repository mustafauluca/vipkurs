<?php

session_start();
include '../ulc/db.php';
$db = new db();

$adminusername = $_POST['adminusername'];
$adminpassword = $_POST['adminpassword'];

$admin = $db->getRow("SELECT * FROM ayarlar WHERE adminusername = ?", [$adminusername]);

if ($admin && password_verify($adminpassword, $admin->adminpassword)) {
    $_SESSION['adminlogged'] = 1;
	$_SESSION['adminusername'] = $adminusername;
	
    header('Location: index.php');
} else {
    echo "Kullanıcı adı veya şifre hatalı!";
	 header('Location: login.php');
	
}