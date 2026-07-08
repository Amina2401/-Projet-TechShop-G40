<?php
// logout.php
session_start();
session_destroy();
setcookie('techshop_sn_logged', '', time() - 3600, '/');
header('Location: index.html');
exit;
?>
