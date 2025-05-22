<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../config/dbaccess.php'; // enthält $con

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $login = trim($_POST['username'] ?? '');
    $passwort = $_POST['passwort'] ?? '';

    // Benutzer per E-Mail oder Benutzername suchen
    if (filter_var($login, FILTER_VALIDATE_EMAIL)) {
        $stmt = $con->prepare("SELECT * FROM users WHERE email = ?");
    } else {
        $stmt = $con->prepare("SELECT * FROM users WHERE benutzername = ?");
    }

    $stmt->bind_param("s", $login);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user && password_verify($passwort, $user['passwort'])) {
        $_SESSION['user'] = $user;

<<<<<<< HEAD
         if (isset($_POST['remember'])) {
            $token = bin2hex(random_bytes(32)); // sicherer Zufallswert
=======
        // Login merken (Token)
        if (isset($_POST['remember'])) {
            $token = bin2hex(random_bytes(32));
>>>>>>> registered
            setcookie('login_token', $token, time() + (30 * 24 * 60 * 60), "/");

            $stmt = $con->prepare("UPDATE users SET login_token = ? WHERE id = ?");
            $stmt->bind_param("si", $token, $user['id']);
            $stmt->execute();
        }

<<<<<<< HEAD

=======
        // Weiterleitung
>>>>>>> registered
        if ($user['ist_admin'] == 1) {
            header("Location: /sakurashine/rest-sample/sample/frontend/sites/admin_dashboard.php");
        } else {
            header("Location: /sakurashine/rest-sample/sample/frontend/index.php");
        }
        exit;

    } else {
        $_SESSION['error'] = "Benutzername oder Passwort ist falsch.";
        header("Location: /sakurashine/rest-sample/sample/frontend/sites/login.php");
        exit;
    }
}
?>
