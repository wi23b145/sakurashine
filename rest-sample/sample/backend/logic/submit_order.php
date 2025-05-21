<?php
// submit_order.php
header('Content-Type: application/json');
$data = json_decode(file_get_contents('php://input'), true);

$name     = $data['name'] ?? '';
$address  = $data['address'] ?? '';
$plz      = $data['plz'] ?? '';
$ort      = $data['ort'] ?? '';
$zahlung  = $data['zahlungsmethode'] ?? '';
$warenkorb = $data['warenkorb'] ?? [];
$code     = $data['gutschein'] ?? '';

if (!$name || !$address || !$plz || !$ort || !$zahlung || empty($warenkorb)) {
    echo json_encode(['success'=>false, 'message'=>'Unvollständige Daten']);
    exit;
}

// DB-Verbindung
$pdo = new PDO("mysql:host=localhost;dbname=sakura_shine;charset=utf8mb4","root","");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// 1) Summe berechnen
$summe = 0;
foreach ($warenkorb as $p) {
    $summe += $p['menge'] * $p['preis'];
}

// 2) Gutschein validieren (falls angegeben)
$rabattBetrag = 0.0;
if ($code !== '') {
    $stmt = $pdo->prepare("
      SELECT typ, rabatt_prozent, geldwert, gueltig_bis, eingelöst
        FROM Gutscheine
       WHERE code = ? AND eingelöst = 0
    ");
    $stmt->execute([$code]);
    $g = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$g) {
        echo json_encode(['success'=>false, 'message'=>'Ungültiger oder bereits eingelöster Code']);
        exit;
    }
    if ($g['gueltig_bis'] < date('Y-m-d')) {
        echo json_encode(['success'=>false, 'message'=>'Gutschein abgelaufen']);
        exit;
    }

    if ($g['typ'] === 'percent') {
        $rabattBetrag = $summe * ($g['rabatt_prozent'] / 100);
    } else { // fixed
        $rabattBetrag = (float)$g['geldwert'];
    }
    // nie unter 0
    if ($rabattBetrag > $summe) {
        $rabattBetrag = $summe;
    }
    // Gutschein als eingelöst markieren
    $upd = $pdo->prepare("UPDATE Gutscheine SET eingelöst = 1 WHERE code = ?");
    $upd->execute([$code]);
}

// Finale Summe
$finalSumme = $summe - $rabattBetrag;

// 3) Bestellung in Bestellungen‐Tabelle
$stmt = $pdo->prepare("
  INSERT INTO Bestellungen
    (user_id, bestellstatus, gesamtpreis, erstellt_am, zahlungsmethode)
  VALUES (?, 'offen', ?, NOW(), ?)
");
// hier user_id ggf. aus Session
$userId = $_SESSION['user']['id'] ?? null;
$stmt->execute([$userId, $finalSumme, $zahlung]);
$bid = $pdo->lastInsertId();

// 4) Positionen speichern
$stmtPos = $pdo->prepare("
  INSERT INTO Bestellpositionen
    (bestellung_id, produkt_id, menge, einzelpreis)
  VALUES (?, ?, ?, ?)
");
foreach ($warenkorb as $p) {
    $stmtPos->execute([$bid, $p['id'], $p['menge'], $p['preis']]);
}

echo json_encode([
  'success'    => true,
  'finalSumme' => number_format($finalSumme, 2, ',', '.')
]);
