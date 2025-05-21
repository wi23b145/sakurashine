<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once "../config/dbaccess.php";

if (!isset($_SESSION['user'])) {
    header("Location: ../../frontend/sites/login.php");
    exit;
}

$user_id = $_SESSION['user']['id'];
$oldpassword = $_POST['oldpassword'] ?? '';

// Passwortprüfung
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

$felder = ['anrede', 'vorname', 'nachname', 'email', 'adresse', 'plz', 'ort', 'benutzername', 'zahlungsinformationen'];
$updates = [];
$params = [];
$types = "";

foreach ($felder as $feld) {
    $neu = trim($_POST[$feld] ?? '');
    $alt = $_SESSION['user'][$feld] ?? '';

    if ($neu !== '' && $neu !== $alt) {
        if ($feld === 'benutzername') {
            $check = $con->prepare("SELECT id FROM users WHERE benutzername = ? AND id != ?");
            $check->bind_param("si", $neu, $user_id);
            $check->execute();
            $check->store_result();
            if ($check->num_rows > 0) {
                $_SESSION['error'] = "Benutzername bereits vergeben.";
                header("Location: ../../frontend/sites/myAccount.php");
                exit;
            }
            $check->close();
        }
        $updates[] = "$feld = ?";
        $params[] = $neu;
        $types .= "s";
        $_SESSION['user'][$feld] = $neu;
    }
}

$passwort = $_POST['passwort'] ?? '';
$wpasswort = $_POST['wpassword'] ?? '';

if (!empty($passwort) || !empty($wpasswort)) {
    if ($passwort !== $wpasswort) {
        $_SESSION['error'] = "Die neuen Passwörter stimmen nicht überein.";
        header("Location: ../../frontend/sites/myAccount.php");
        exit;
    }
    $hash = password_hash($passwort, PASSWORD_DEFAULT);
    $updates[] = "passwort = ?";
    $params[] = $hash;
    $types .= "s";
}

if (!empty($updates)) {
    $sql = "UPDATE users SET " . implode(", ", $updates) . " WHERE id = ?";
    $params[] = $user_id;
    $types .= "i";
    $stmt = $con->prepare($sql);
    $stmt->bind_param($types, ...$params);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Daten erfolgreich aktualisiert.";
    } else {
        $_SESSION['error'] = "Fehler beim Speichern: " . $stmt->error;
    }
} else {
    $_SESSION['success'] = "Keine Änderungen vorgenommen.";
}

header("Location: ../../frontend/sites/myAccount.php");
exit;
?>
