<?php
// Datei: backend/logic/create_order_with_invoice.php

require_once('../config/dbaccess.php');

// Fehleranzeige aktivieren
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');

try {
    // Verbindung zur Datenbank aufbauen
    $conn = getDatabaseConnection();
    $conn->begin_transaction();

    // JSON-Daten empfangen und verarbeiten
    $input = json_decode(file_get_contents("php://input"), true);

    if (!$input) {
        throw new Exception("Keine Eingabedaten erhalten.");
    }

    $user_id = $input['user_id'] ?? null;
    $warenkorb = $input['warenkorb'] ?? [];
    $zahlungsmethode = $input['zahlungsmethode'] ?? 'Unbekannt';
    $rechnungsname = $input['rechnungsname'] ?? 'Unbekannt';
    $rechnungsadresse = $input['rechnungsadresse'] ?? 'Unbekannt';

    if (empty($user_id) || empty($warenkorb)) {
        throw new Exception("Benutzer-ID oder Warenkorb fehlt.");
    }

    // Bestellung speichern
    $gesamtpreis = 0;
    foreach ($warenkorb as $produkt) {
        $gesamtpreis += $produkt['preis'] * $produkt['menge'];
    }

    $stmt_order = $conn->prepare("INSERT INTO bestellungen (user_id, bestellstatus, gesamtpreis, erstellt_am) VALUES (?, 'In Bearbeitung', ?, NOW())");
    $stmt_order->bind_param("id", $user_id, $gesamtpreis);
    $stmt_order->execute();

    $bestellung_id = $stmt_order->insert_id;

    // Einzelne Produkte speichern
    foreach ($warenkorb as $produkt) {
        $produkt_id = $produkt['id'];
        $menge = $produkt['menge'];
        $preis = $produkt['preis'];

        $stmt_detail = $conn->prepare("INSERT INTO bestellpositionen (bestellung_id, produkt_id, menge, einzelpreis) VALUES (?, ?, ?, ?)");
        $stmt_detail->bind_param("iiid", $bestellung_id, $produkt_id, $menge, $preis);
        $stmt_detail->execute();
    }

    // Rechnung erstellen
    $steuersatz = 20.00; // 20% Umsatzsteuer
    $steuerbetrag = round(($gesamtpreis * $steuersatz) / 100, 2);
    $betrag_mit_steuer = $gesamtpreis + $steuerbetrag;

    $stmt_invoice = $conn->prepare("INSERT INTO rechnungen (bestellung_id, rechnungsdatum, zahlungsmethode, rechnungsname, rechnungsadresse, steuersatz, betrag_gesamt, status) VALUES (?, NOW(), ?, ?, ?, ?, ?, 'erstellt')");
    $stmt_invoice->bind_param("isssdd", $bestellung_id, $zahlungsmethode, $rechnungsname, $rechnungsadresse, $steuersatz, $betrag_mit_steuer);
    $stmt_invoice->execute();

    $conn->commit();

    echo json_encode([
        "success" => true,
        "message" => "Bestellung und Rechnung erfolgreich gespeichert.",
        "bestellung_id" => $bestellung_id
    ]);
} catch (Exception $e) {
    if (isset($conn)) {
        $conn->rollback();
    }
    echo json_encode([
        "success" => false,
        "error" => $e->getMessage()
    ]);
}
?>
