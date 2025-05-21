<?php
<<<<<<< HEAD
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
=======
session_start();
>>>>>>> 342c54d66036d2092c6f831a5ead80ecbc768cdc
require_once "../config/dbaccess.php"; // enthält $con

$oldpassword = $_POST['oldpassword'];

// Hol den gespeicherten Hash
$user_id = $_SESSION['user']['id'];
$stmt = $con->prepare("SELECT passwort FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!password_verify($oldpassword, $user['passwort'])) {
    $_SESSION['error'] = "Aktuelles Passwort ist falsch.";
    header("Location: ../../frontend/sites/editUser.php");
    exit;
}

// Prüfen ob Benutzer eingeloggt ist
if (!isset($_SESSION['user'])) {
    header("Location: ../../frontend/sites/login.php");
    exit;
}

$id = $_SESSION['user']['id'];

// Formulardaten holen
$anrede = $_POST['anrede'];
$vorname = $_POST['firstname'];
$nachname = $_POST['lastname'];
$email = $_POST['email'];
$adresse = $_POST['adresse'];
$plz = $_POST['plz'];
$ort = $_POST['ort'];
$username = $_POST['username'];
$passwort = $_POST['passwort'];
$wpasswort = $_POST['wpassword'];

// Passwort bestätigen
if ($passwort !== $wpasswort) {
    $_SESSION['error'] = "Passwörter stimmen nicht überein.";
    header("Location: ../../frontend/sites/edituser.php");
    exit;
}

$passwort = $_POST['passwort'];
$wpasswort = $_POST['wpassword'];

// Standard-SQL ohne Passwort
$sql = "UPDATE users 
        SET anrede = ?, vorname = ?, nachname = ?, email = ?, adresse = ?, plz = ?, ort = ?, benutzername = ?
        WHERE id = ?";

$params = [$anrede, $vorname, $nachname, $email, $adresse, $plz, $ort, $username, $id];
$types = "ssssssssi";

// Session aktualisieren (wenn Änderung erfolgreich)
$_SESSION['user']['anrede'] = $anrede;
$_SESSION['user']['vorname'] = $vorname;
$_SESSION['user']['nachname'] = $nachname;
$_SESSION['user']['email'] = $email;
$_SESSION['user']['adresse'] = $adresse;
$_SESSION['user']['plz'] = $plz;
$_SESSION['user']['ort'] = $ort;
$_SESSION['user']['benutzername'] = $username;


// Nur wenn beide Passwortfelder ausgefüllt sind → Passwort ändern
if (!empty($passwort) && !empty($wpasswort)) {
    if ($passwort !== $wpasswort) {
        $_SESSION['error'] = "Die Passwörter stimmen nicht überein.";
        header("Location: ../../frontend/sites/editUser.php");
        exit;
    }

    $hash = password_hash($passwort, PASSWORD_DEFAULT);
    $sql = "UPDATE users 
            SET anrede = ?, vorname = ?, nachname = ?, email = ?, adresse = ?, plz = ?, ort = ?, benutzername = ?, passwort = ?
            WHERE id = ?";
    $params = [$anrede, $vorname, $nachname, $email, $adresse, $plz, $ort, $username, $hash, $id];
    $types = "sssssssssi";
}

// Query vorbereiten
$stmt = $con->prepare($sql);
$stmt->bind_param($types, ...$params);

if ($stmt->execute()) {
    $_SESSION['success'] = "Daten erfolgreich aktualisiert.";
    header("Location: ../../frontend/sites/editUser.php");
} else {
    $_SESSION['error'] = "Fehler beim Speichern.";
    header("Location: ../../frontend/sites/editUser.php");
}
exit;

?>
