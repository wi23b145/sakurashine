<?php
session_start();
require_once "../config/dbaccess.php";

// Nur eingeloggte Benutzer
if (!isset($_SESSION['user'])) {
    header("Location: ../../frontend/sites/login.php");
    exit;
}

$user_id = $_SESSION['user']['id'];

// Aktuelles Passwort prüfen
$oldpassword = $_POST['oldpassword'] ?? '';
$stmt = $con->prepare("SELECT passwort FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user || !password_verify($oldpassword, $user['passwort'])) {
    $_SESSION['error'] = "Aktuelles Passwort ist falsch.";
    header("Location: ../../frontend/sites/myAccount.php");
    exit;
}

// Neue Daten abholen
$felder = ['anrede', 'vorname', 'nachname', 'email', 'adresse', 'plz', 'ort', 'benutzername'];
$updates = [];
$params = [];
$types = "";

// Felder vergleichen
foreach ($felder as $feld) {
    $alt = $_SESSION['user'][$feld] ?? '';
    $neu = $_POST[$feld] ?? '';
    if ($alt !== $neu) {
        $updates[] = "$feld = ?";
        $params[] = $neu;
        $types .= "s";
        $_SESSION['user'][$feld] = $neu; // Session aktualisieren
    }
}

// Passwort optional ändern
$neues_passwort = $_POST['passwort'] ?? '';
$wpasswort = $_POST['wpassword'] ?? '';

if (!empty($neues_passwort) || !empty($wpasswort)) {
    if ($neues_passwort !== $wpasswort) {
        $_SESSION['error'] = "Neue Passwörter stimmen nicht überein.";
        header("Location: ../../frontend/sites/myAccount.php");
        exit;
    }

    $hash = password_hash($neues_passwort, PASSWORD_DEFAULT);
    $updates[] = "passwort = ?";
    $params[] = $hash;
    $types .= "s";
}

// Wenn es Änderungen gibt → Update
if (!empty($updates)) {
    $updates_sql = implode(", ", $updates);
    $params[] = $user_id;
    $types .= "i";

    $stmt = $con->prepare("UPDATE users SET $updates_sql WHERE id = ?");
    $stmt->bind_param($types, ...$params);

    if (!$stmt->execute()) {
        $_SESSION['error'] = "Fehler beim Speichern: " . $stmt->error;
        header("Location: ../../frontend/sites/myAccount.php");
        exit;
    }
}

// Neue Zahlungsmethode hinzufügen, falls ausgewählt
$zahlung = trim($_POST['zahlung'] ?? '');
if (!empty($zahlung)) {
    $check = $con->prepare("SELECT id FROM zahlungsinformationen WHERE user_id = ? AND methode = ?");
    $check->bind_param("is", $user_id, $zahlung);
    $check->execute();
    $check->store_result();

    if ($check->num_rows == 0) {
        $insert = $con->prepare("INSERT INTO zahlungsinformationen (user_id, methode) VALUES (?, ?)");
        $insert->bind_param("is", $user_id, $zahlung);
        $insert->execute();
        $insert->close();
    }
    $check->close();
}

$_SESSION['success'] = "Daten erfolgreich gespeichert.";
header("Location: ../../frontend/sites/myAccount.php");
exit;
?>
