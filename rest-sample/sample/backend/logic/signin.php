<?php
session_start();
require_once '../config/dbaccess.php'; // enthält $con

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST['username'] ?? '');
    $passwort = $_POST['passwort'] ?? '';

    $stmt = $con->prepare("SELECT * FROM users WHERE benutzername = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();


    if ($user && password_verify($passwort, $user['passwort'])) {
        $_SESSION['user'] = $user;

         if (isset($_POST['remember'])) {
            $token = bin2hex(random_bytes(32)); // sicherer Zufallswert
            setcookie('login_token', $token, time() + (30 * 24 * 60 * 60), "/");

            $stmt = $con->prepare("UPDATE users SET login_token = ? WHERE id = ?");
            $stmt->bind_param("si", $token, $user['id']);
            $stmt->execute();
        }


        if ($user['ist_admin'] == 1) {
            $_SESSION['success'] = "Hallo Admin!";
            header("Location: /sakurashine/rest-sample/sample/frontend/sites/admin_dashboard.php");
            
        } else {
            $_SESSION['success'] = "Du hast dich erfolgreich angemeldet!";
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
