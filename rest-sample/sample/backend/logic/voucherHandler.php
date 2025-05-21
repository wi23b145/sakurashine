<?php
// voucherHandler.php
session_start();
require_once __DIR__ . '/../config/dbaccess.php'; // liefert $con (mysqli)
header('Content-Type: application/json');

$code = trim($_GET['code'] ?? '');
if ($code === '') {
    http_response_code(400);
    echo json_encode(['error' => 'Kein Gutscheincode übergeben']);
    exit;
}

// Gutschein aus DB holen
$stmt = $con->prepare("
    SELECT id, typ, rabatt_prozent, geldwert, gueltig_bis, eingelöst
      FROM Gutscheine
     WHERE code = ?
");
$stmt->bind_param('s', $code);
$stmt->execute();
$voucher = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$voucher) {
    http_response_code(404);
    echo json_encode(['error' => 'Gutscheincode nicht gefunden']);
    exit;
}

// Ist er schon eingelöst?
if ((int)$voucher['eingelöst'] === 1) {
    http_response_code(409);
    echo json_encode(['error' => 'Gutschein bereits eingelöst']);
    exit;
}

// Ist er abgelaufen?
$heute = date('Y-m-d');
if ($voucher['gueltig_bis'] < $heute) {
    http_response_code(410);
    echo json_encode(['error' => 'Gutschein abgelaufen']);
    exit;
}

// Alles gut — Rabatt zurückgeben
echo json_encode([
    'success'        => true,
    'typ'            => $voucher['typ'],            // 'percent' oder 'fixed'
    'rabatt_prozent' => (float)$voucher['rabatt_prozent'],
    'geldwert'       => (float)$voucher['geldwert']
]);
