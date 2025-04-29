<?php
// Fehleranzeige aktivieren (nur für Entwicklung)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once '../config/dbaccess.php';
$pdo = Db::connect();

// Nur bei POST-Anfrage fortfahren
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST['username'] ?? '');
    $passwort = $_POST['passwort'] ?? '';

    if (empty($username) || empty($passwort)) {
        $_SESSION['error'] = "Benutzername und Passwort dürfen nicht leer sein.";
        header("Location: ../../frontend/sites/login.html");
        exit;
    }

    try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE benutzername = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($passwort, $user['passwort'])) {
            // Benutzer erfolgreich authentifiziert
            $_SESSION['user'] = [
                'id' => $user['id'],
                'anrede' => $user['anrede'],
                'vorname' => $user['vorname'],
                'nachname' => $user['nachname'],
                'adresse' => $user['adresse'],
                'plz' => $user['plz'],
                'ort' => $user['ort'],
                'email' => $user['email'],
                'benutzername' => $user['benutzername'],
                'zahlungsinformationen' => $user['zahlungsinformationen'],
                'ist_admin' => $user['ist_admin']
            ];

            // Erfolgreich eingeloggt → Weiterleitung zur Startseite
            header("Location: ../../frontend/index.html");
            exit;
        } else {
            // Passwort falsch oder Benutzer nicht gefunden
            $_SESSION['error'] = "Benutzername oder Passwort falsch.";
            header("Location: ../../frontend/sites/login.html");
            exit;
        }

    } catch (PDOException $e) {
        // Datenbankfehler
        $_SESSION['error'] = "Fehler bei der Datenbankabfrage.";
        header("Location: ../../frontend/sites/login.html");
        exit;
    }
} else {
    // Falscher Zugriffstyp (nicht POST)
    header("Location: ../../frontend/sites/login.html");
    exit;
}
?>
