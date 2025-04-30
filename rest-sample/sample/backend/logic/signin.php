<?php
session_start();
require_once ('../config/dbaccess.php'); 

if($_SERVER["REQUEST_METHOD"] == "POST"){
    $username = $_POST['username'];
    $passwort = $_POST['passwort'];

    $sql = "SELECT * FROM users WHERE benutzername = ?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if($result->num_rows == 1){
        $user = $result->fetch_assoc();
        $hashed_password = $user['passwort'];

        if(password_verify($passwort, $hashed_password)){
            // Session-Variablen setzen
            $_SESSION['benutzername'] = $user["benutzername"];
            $_SESSION['id'] = $user["id"];
            $_SESSION['vorname'] = $user["vorname"];
            $_SESSION['nachname'] = $user["nachname"];
            $_SESSION['email'] = $user["email"];
            $_SESSION['anrede'] = $user["anrede"];
            $_SESSION['ist_admin'] = $user["ist_admin"];  // NEU: Rolle merken

            $_SESSION['success'] = "WILLKOMMEN, " . htmlspecialchars($username) . "!";

            // Weiterleitung zur Startseite
            header("Location:../../frontend/index.html");
            exit;
        } else {
            $_SESSION['error'] = "Das Passwort ist falsch. Bitte versuchen Sie es erneut.";
            header("Location:../../frontend/index.html");
        }
    } else {
        $_SESSION['error'] = "Kein Benutzer mit diesem Usernamen gefunden!";
        header("Location:../../frontend/index.html");
    }

    $con->close();
}
?>
