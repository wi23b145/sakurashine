<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
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

// Summe berechnen
$sumOriginal = 0.0;
foreach ($warenkorb as $p) {
    $sumOriginal += $p['preis'] * $p['menge'];
}

// Gutschein-Rabatt anwenden, falls Gutschein übergeben wurde
if (!empty($gutschein)) {
    $stmtVoucher = $con->prepare("SELECT typ, rabatt_prozent, geldwert, gueltig_bis, eingelöst FROM Gutscheine WHERE code = ?");
    if (!$stmtVoucher) throw new Exception("Prepare Gutschein fehlgeschlagen: " . $con->error);
    $stmtVoucher->bind_param('s', $gutschein);
    $stmtVoucher->execute();
    $voucher = $stmtVoucher->get_result()->fetch_assoc();
    $stmtVoucher->close();

    $heute = date('Y-m-d');
    if ($voucher && $voucher['eingelöst'] == 0 && $voucher['gueltig_bis'] >= $heute) {
        if ($voucher['typ'] === 'percent') {
            $sumFinal = $sumOriginal * (1 - $voucher['rabatt_prozent'] / 100);
        } else {
            $sumFinal = $sumOriginal - $voucher['geldwert'];
        }
        if ($sumFinal < 0) $sumFinal = 0;
    } else {
        // Gutschein ungültig oder abgelaufen
        $sumFinal = $sumOriginal;
    }
} else {
    $sumFinal = $sumOriginal;
}

$con->begin_transaction();

try {
    // Insert in Bestellungen
    $stmt = $con->prepare("INSERT INTO Bestellungen (user_id, name, adresse, plz, ort, bestellstatus, zahlungsmethode, gesamtpreis, erstellt_am) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())");
    if (!$stmt) throw new Exception("Prepare fehlgeschlagen: " . $con->error);

    $bindResult = $stmt->bind_param(
        'issssssd',
        $userId,
        $name,
        $address,
        $plz,
        $ort,
        $bestellstatus,
        $zahlungsmethode,
        $sumFinal
    );
    if (!$bindResult) throw new Exception("bind_param fehlgeschlagen: " . $stmt->error);

    $execResult = $stmt->execute();
    if (!$execResult) throw new Exception("execute fehlgeschlagen: " . $stmt->error);

    $bestellung_id = $stmt->insert_id; // ID der neuen Bestellung
    $stmt->close();

    // Hier kannst du dann die Bestellpositionen speichern...

    $con->commit();

    echo json_encode(['success' => true, 'bestellung_id' => $bestellung_id]);
} catch (Exception $e) {
    $con->rollback();
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Fehler: ' . $e->getMessage()]);
}
