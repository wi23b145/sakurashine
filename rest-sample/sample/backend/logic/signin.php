<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
 
require_once '../config/dbaccess.php';
 
if (!$con) {
    die("DB-Verbindung fehlgeschlagen: " . mysqli_connect_error());
}
 
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $login = trim($_POST['username'] ?? '');
    $passwort = $_POST['passwort'] ?? '';
 
    if (!$login || !$passwort) {
        $_SESSION['error'] = "Bitte Benutzername und Passwort eingeben.";
        header("Location: /sakurashine/rest-sample/sample/frontend/sites/login.php");
        exit;
    }
 
    if (filter_var($login, FILTER_VALIDATE_EMAIL)) {
        $stmt = $con->prepare("SELECT * FROM users WHERE email = ?");
    } else {
        $stmt = $con->prepare("SELECT * FROM users WHERE benutzername = ?");
    }
 
    $stmt->bind_param("s", $login);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
 
    if ($user) {
        if (password_verify($passwort, $user['passwort'])) {
            $_SESSION['user'] = $user;
 
            if (isset($_POST['remember'])) {
                $token = bin2hex(random_bytes(32));
                setcookie('login_token', $token, time() + (30 * 24 * 60 * 60), "/");
 
                $stmt = $con->prepare("UPDATE users SET login_token = ? WHERE id = ?");
                $stmt->bind_param("si", $token, $user['id']);
                $stmt->execute();
            }
 
            if ($user['ist_admin'] == 1) {
                header("Location: /sakurashine/rest-sample/sample/frontend/sites/admin_dashboard.php");
            } else {
                header("Location: /sakurashine/rest-sample/sample/frontend/index.php");
            }
            exit;
        } else {
            $_SESSION['error'] = "Passwort ist falsch.";
        }
    } else {
        $_SESSION['error'] = "Benutzername oder E-Mail nicht gefunden.";
    }
    header("Location: /sakurashine/rest-sample/sample/frontend/sites/login.php");
    exit;
}
?>
 
 