<?php
session_start();
require_once __DIR__ . '/../config/dbaccess.php'; // $con

// Rohdaten empfangen
$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Keine Daten empfangen']);
    exit;
}

$userId = $_SESSION['user']['id'] ?? null;
if (!$userId) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Nicht eingeloggt']);
    exit;
}

// Pflichtfelder prüfen
if (empty($data['name']) || empty($data['address']) || empty($data['plz']) || empty($data['ort']) || empty($data['zahlungsmethode']) || empty($data['warenkorb'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Unvollständige Daten']);
    exit;
}

$name = $data['name'];
$address = $data['address'];
$plz = $data['plz'];
$ort = $data['ort'];
$zahlungsmethode = $data['zahlungsmethode'];
$warenkorb = $data['warenkorb'];
$gutschein = $data['gutschein'] ?? '';

$bestellstatus = 'offen'; // Standard-Status

// Summe berechnen
$sumOriginal = 0.0;
foreach ($warenkorb as $p) {
    $sumOriginal += $p['preis'] * $p['menge'];
}
// Gutschein-Rabatt noch nicht implementiert
$sumFinal = $sumOriginal;

$con->begin_transaction();

try {
    $stmt = $con->prepare("INSERT INTO Bestellungen (user_id, name, adresse, plz, ort, bestellstatus, zahlungsmethode, gesamtpreis, erstellt_am) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())");
    if (!$stmt) throw new Exception("Prepare fehlgeschlagen: " . $con->error);

    $bindResult = $stmt->bind_param('issssssd', $userId, $name, $address, $plz, $ort, $bestellstatus, $zahlungsmethode, $sumFinal);
    if (!$bindResult) throw new Exception("bind_param fehlgeschlagen: " . $stmt->error);

    $execResult = $stmt->execute();
    if (!$execResult) throw new Exception("execute fehlgeschlagen: " . $stmt->error);

    $bestellung_id = $stmt->insert_id;
    $stmt->close();

    // Bestellpositionen speichern
    $stmtPos = $con->prepare("INSERT INTO Bestellpositionen (bestellung_id, produkt_id, menge, einzelpreis) VALUES (?, ?, ?, ?)");
    if (!$stmtPos) throw new Exception("Prepare Bestellpositionen fehlgeschlagen: " . $con->error);

    foreach ($warenkorb as $p) {
        $bindPos = $stmtPos->bind_param('iiid', $bestellung_id, $p['id'], $p['menge'], $p['preis']);
        if (!$bindPos) throw new Exception("bind_param Bestellposition fehlgeschlagen: " . $stmtPos->error);

        $execPos = $stmtPos->execute();
        if (!$execPos) throw new Exception("execute Bestellposition fehlgeschlagen: " . $stmtPos->error);
    }
    $stmtPos->close();

    $con->commit();

    echo json_encode(['success' => true, 'finalSumme' => $sumFinal]);
} catch (Exception $e) {
    $con->rollback();
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Fehler: ' . $e->getMessage()]);
}
