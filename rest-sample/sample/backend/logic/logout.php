<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/dbaccess.php';
if (!isset($con) || !($con instanceof mysqli)) {
    die("DB-Verbindung fehlt oder fehlerhaft.");
}

if (!empty($_SESSION['user']['id'])) {
    $stmt = $con->prepare("UPDATE users SET login_token = NULL WHERE id = ?");
    $stmt->bind_param("i", $_SESSION['user']['id']);
    $stmt->execute();
    $stmt->close();
}

// Login-Token-Cookie löschen (Pfad prüfen!)
setcookie('login_token', '', time() - 3600, "/");

// Session-Cookie löschen
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Session leeren & zerstören
$_SESSION = [];
session_destroy();

// Weiterleitung
header("Location: /sakurashine/rest-sample/sample/frontend/index.php");
exit();
