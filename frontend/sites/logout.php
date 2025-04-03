<?php
session_start();
session_unset();
session_destroy();
setcookie("username", "", time() - 3600, "/"); // Cookie löschen
header("Location:/WEB1/site/login.php");
exit;
?>