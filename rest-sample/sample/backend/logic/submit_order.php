<?php
// Datenbankverbindung (ggf. in config.php auslagern)
$pdo = new PDO("mysql:host=localhost;dbname=sakura_shine;charset=utf8mb4", "root", "");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Formulardaten empfangen
$name     = $_POST['name']     ?? '';
$adresse  = $_POST['address']  ?? '';
$plz      = $_POST['plz']      ?? '';
$ort      = $_POST['ort']      ?? '';
$zahlung  = $_POST['zahlung']  ?? '';
$warenkorb = json_decode($_POST['warenkorb'] ?? '', true);

// Validierung
if (!$name || !$adresse || !$plz || !$ort || !$zahlung || !$warenkorb || !is_array($warenkorb)) {
    die("Unvollständige Bestellung.");
}

// Schritt 1: Bestellung einfügen
$gesamtbetrag = 0;
foreach ($warenkorb as $produkt) {
    $gesamtbetrag += $produkt['menge'] * $produkt['preis'];
}

$insertBestellung = $pdo->prepare("
    INSERT INTO bestellungen (user_id, bestellstatus, gesamtpreis, erstellt_am, zahlungsmethode)
    VALUES (?, ?, ?, NOW(), ?)
");
$insertBestellung->execute([$name, 'Neu', $gesamtbetrag, $zahlung]);
$bestellungId = $pdo->lastInsertId();

// Schritt 2: Bestellpositionen speichern
$insertPosition = $pdo->prepare("
    INSERT INTO bestellpositionen (bestellung_id, produkt_id, menge, einzelpreis)
    VALUES (?, ?, ?, ?)
");

foreach ($warenkorb as $produkt) {
    $insertPosition->execute([
        $bestellungId,
        $produkt['id'],
        $produkt['menge'],
        $produkt['preis']
    ]);
}

// Schritt 3: Rechnung erstellen
$insertRechnung = $pdo->prepare("
    INSERT INTO rechnungen (
        bestellung_id, rechnung_datum, rechnung_name,
        rechnung_adresse, zahlung_methode, gesamtbetrag,
        steuern, pdf_pfad, status
    ) VALUES (?, NOW(), ?, ?, ?, ?, 0.00, '', 'offen')
");

$insertRechnung->execute([
    $bestellungId,
    $name,
    "$adresse, $plz $ort",
    $zahlung,
    $gesamtbetrag
]);

// Ausgabe (könnte z. B. auch ein Redirect sein)
echo "<h2>Vielen Dank für Ihre Bestellung, $name!</h2>";
echo "<p>Bestellnummer: $bestellungId</p>";
echo "<p>Gesamtsumme: €" . number_format($gesamtbetrag, 2, ',', '.') . "</p>";
?>
