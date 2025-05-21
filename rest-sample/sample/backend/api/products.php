<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../config/dbaccess.php';

// Admin-Check
if (empty($_SESSION['user']) || $_SESSION['user']['ist_admin'] != 1) {
    http_response_code(403);
    echo json_encode(['error' => 'Zugriff verweigert']);
    exit;
}

// REST-like API je nach HTTP Methode
$method = $_SERVER['REQUEST_METHOD'];

// Hilfsfunktion für JSON-Ausgabe
function sendJson($data, $code = 200) {
    http_response_code($code);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

switch ($method) {
    case 'GET': // Alle Produkte laden oder ein Produkt per id
        $id = $_GET['id'] ?? null;
        if ($id) {
            $stmt = $con->prepare("SELECT * FROM Produkte WHERE id = ?");
            $stmt->bind_param('i', $id);
            $stmt->execute();
            $result = $stmt->get_result()->fetch_assoc();
            $stmt->close();
            if ($result) sendJson($result);
            else sendJson(['error' => 'Produkt nicht gefunden'], 404);
        } else {
            $res = $con->query("SELECT * FROM Produkte ORDER BY erstellt_am DESC");
            $produkte = $res->fetch_all(MYSQLI_ASSOC);
            sendJson($produkte);
        }
        break;

    case 'POST': // Neues Produkt anlegen
        $data = json_decode(file_get_contents('php://input'), true);
        if (!$data) sendJson(['error' => 'Keine Daten'], 400);

        // Validierung (ähnlich wie bisher, gekürzt hier)
        if (empty($data['name']) || empty($data['beschreibung']) || !isset($data['preis'])) {
            sendJson(['error' => 'Pflichtfelder fehlen'], 400);
        }

        $stmt = $con->prepare("INSERT INTO Produkte (name, beschreibung, preis, bestand, bewertung, bild) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param('ssdiis', $data['name'], $data['beschreibung'], $data['preis'], $data['bestand'] ?? 0, $data['bewertung'] ?? 0, $data['bild'] ?? '');
        $stmt->execute();
        if ($stmt->affected_rows) {
            sendJson(['success' => true, 'id' => $stmt->insert_id], 201);
        } else {
            sendJson(['error' => 'Einfügen fehlgeschlagen'], 500);
        }
        $stmt->close();
        break;

    case 'PUT': // Produkt aktualisieren
        parse_str(file_get_contents('php://input'), $put_vars);
        $data = json_decode(file_get_contents('php://input'), true);
        if (!$data || empty($data['id'])) sendJson(['error' => 'Ungültige Daten'], 400);

        $stmt = $con->prepare("UPDATE Produkte SET name=?, beschreibung=?, preis=?, bestand=?, bewertung=?, bild=? WHERE id=?");
        $stmt->bind_param('ssdidsi', $data['name'], $data['beschreibung'], $data['preis'], $data['bestand'], $data['bewertung'], $data['bild'], $data['id']);
        $stmt->execute();
        if ($stmt->affected_rows) {
            sendJson(['success' => true]);
        } else {
            sendJson(['error' => 'Update fehlgeschlagen oder keine Änderung'], 500);
        }
        $stmt->close();
        break;

    case 'DELETE': // Produkt löschen
        $id = $_GET['id'] ?? null;
        if (!$id) sendJson(['error' => 'ID fehlt'], 400);

        $stmt = $con->prepare("DELETE FROM Produkte WHERE id = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        if ($stmt->affected_rows) {
            sendJson(['success' => true]);
        } else {
            sendJson(['error' => 'Löschen fehlgeschlagen'], 500);
        }
        $stmt->close();
        break;

    default:
        sendJson(['error' => 'Methode nicht erlaubt'], 405);
}
