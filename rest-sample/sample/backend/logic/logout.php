<?php
session_start();
require_once "../config/dbaccess.php"; // enthält $con

// 1. Login-Token aus DB löschen, falls vorhanden
if (isset($_SESSION['user'])) {
    $stmt = $con->prepare("UPDATE users SET login_token = NULL WHERE id = ?");
    $stmt->bind_param("i", $_SESSION['user']['id']);
    $stmt->execute();
}

// 2. Login-Cookie löschen
setcookie('login_token', '', time() - 3600, "/");

// 3. Session beenden
unset($_SESSION);
session_destroy();

// 4. Weiterleitung
header("Location: /sakurashine/rest-sample/sample/frontend/index.php");
exit();
?>

