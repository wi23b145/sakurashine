<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
header('Content-Type: application/json');

require_once '../config/dbaccess.php';
$pdo = Db::connect();


if (!isset($_SESSION['user'])) {
    echo json_encode(['error' => 'Nicht eingeloggt']);
    exit;
}

$action = $_GET['action'] ?? '';

$pdo = Db::connect();

switch ($action) {

    // Nur Session-Daten ausgeben (Login-Status prüfen)
    case 'session':
        echo json_encode($_SESSION['user']);
        break;

    // Erweiterte Nutzerinfo aus DB
    case 'info':
        $stmt = $pdo->prepare("SELECT anrede, vorname, nachname, adresse, plz, ort, email, benutzername, zahlungsinformationen 
                               FROM users 
                               WHERE id = ?");
        $stmt->execute([$_SESSION['user']['id']]);
        $userData = $stmt->fetch();
        echo json_encode($userData);
        break;

    // Bestellungen ausgeben
    case 'orders':
        $stmt = $pdo->prepare("SELECT id, erstellt_am, gesamtpreis, bestellstatus 
                               FROM Bestellungen 
                               WHERE user_id = ? 
                               ORDER BY erstellt_am DESC");
        $stmt->execute([$_SESSION['user']['id']]);
        echo json_encode($stmt->fetchAll());
        break;

    default:
        echo json_encode(['error' => 'Ungültige Aktion']);
}
?>
