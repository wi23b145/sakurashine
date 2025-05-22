<?php
<<<<<<< HEAD
// 1) Fehleranzeige aktivieren
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// 2) Session starten
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 3) DB-Konfiguration einbinden
require_once __DIR__ . '/../config/dbaccess.php';
// Prüfe hier zur Sicherheit:
if (!isset($con) || !($con instanceof mysqli)) {
    die("DB-Verbindung fehlt oder fehlerhaft.");
}

// 4) Login-Token in DB löschen (wenn vorhanden)
if (!empty($_SESSION['user']['id'])) {
    $stmt = $con->prepare("UPDATE users SET login_token = NULL WHERE id = ?");
    $stmt->bind_param("i", $_SESSION['user']['id']);
    $stmt->execute();
    $stmt->close();
}

// 5) Cookie und Session killen
setcookie('login_token', '', time() - 3600, "/");
$_SESSION = [];
session_destroy();

// 6) Auf gültige Ziel-Seite weiterleiten
header("Location: /sakurashine/rest-sample/sample/frontend/index.php");
exit();
=======
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

>>>>>>> registered
