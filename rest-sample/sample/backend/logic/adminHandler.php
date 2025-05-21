<?php
// backend/logic/adminHandler.php
// JSON-API für Admin: Kunden & Bestellungen

// 1) Session nur einmal starten
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// 1b) Warnings im Response abschalten
ini_set('display_errors', 0);

// 1c) DB-Verbindung laden
require_once __DIR__ . '/../config/dbaccess.php'; // liefert $con (mysqli)

// 2) JSON-Header
header('Content-Type: application/json');

// 3) Admin-Check
if (empty($_SESSION['user']) || $_SESSION['user']['ist_admin'] != 1) {
    http_response_code(403);
    echo json_encode(['error' => 'Kein Zugriff']);
    exit;
}

// 4) Action auslesen
$action = $_GET['action'] ?? '';

switch ($action) {
    case 'getKunden':
        // Alle Nicht-Admin-User holen (nur existierende Spalten auswählen)
        $sql = "SELECT id, anrede, vorname, nachname, email, benutzername, ist_aktiv AS aktiv
                  FROM users
                 WHERE ist_admin = 0";
        $res = $con->query($sql);
        $kunden = [];
        while ($row = $res->fetch_assoc()) {
            // Casts sicherstellen
            $row['id']    = (int)$row['id'];
            $row['aktiv'] = (bool)$row['aktiv'];
            $kunden[] = $row;
        }
        echo json_encode($kunden);
        break;

    case 'deaktivieren':
        $id = (int)($_GET['id'] ?? 0);
        $stmt = $con->prepare("UPDATE users SET ist_aktiv = NOT ist_aktiv WHERE id = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        echo json_encode(['success' => true]);
        break;

    case 'getBestellungen':
        $userId = (int)($_GET['id'] ?? 0);
        // Bestellungen des Users
        $stmt = $con->prepare(
            "SELECT id, erstellt_am, gesamtpreis
               FROM Bestellungen
              WHERE user_id = ?"
        );
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $orders = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        // Für jede Bestellung Produkte laden
        foreach ($orders as &$o) {
            $o['id']          = (int)$o['id'];
            $o['gesamtpreis'] = (float)$o['gesamtpreis'];
            $stmt2 = $con->prepare(
                "SELECT p.name, bp.menge
                   FROM Bestellpositionen bp
                   JOIN Produkte p ON bp.produkt_id = p.id
                  WHERE bp.bestellung_id = ?"
            );
            $stmt2->bind_param('i', $o['id']);
            $stmt2->execute();
            $o['produkte'] = $stmt2->get_result()->fetch_all(MYSQLI_ASSOC);
            $stmt2->close();
        }

        echo json_encode($orders);
        break;

    default:
        http_response_code(400);
        echo json_encode(['error' => 'Ungültige Aktion']);
        break;
}
?>