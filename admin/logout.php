
<?php
session_start();
session_destroy(); // Oturumu sonlandır

if(isset($_GET['r'])){header('Location:'.$_GET['r']);}
else {header('Location:index.php');}
 // Giriş sayfasına yönlendir

exit;
?>