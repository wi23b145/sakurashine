<?php
session_start();
require_once("../../backend/db_connect.php");

if (!isset($_SESSION['user'])) {
    die("Bitte zuerst einloggen.");
}

$user_id = $_SESSION['user']['id'];
$bestellung_id = isset($_GET['bestellung_id']) ? (int)$_GET['bestellung_id'] : 0;
if ($bestellung_id <= 0) {
    die("Ungültige Bestell-ID.");
}

// Bestellung prüfen
$stmt = $pdo->prepare("SELECT * FROM bestellungen WHERE id = ? AND user_id = ?");
$stmt->execute([$bestellung_id, $user_id]);
$bestellung = $stmt->fetch();

if (!$bestellung) {
    die("Bestellung nicht gefunden oder keine Zugriffsberechtigung.");
}

// Rechnungsnummer prüfen oder erzeugen
$stmt = $pdo->prepare("SELECT * FROM rechnungen WHERE bestellung_id = ?");
$stmt->execute([$bestellung_id]);
$rechnung = $stmt->fetch();

if (!$rechnung) {
    $jahr = date('Y');
    $random = random_int(1000, 9999);
    $rechnungsnummer = "RE" . $jahr . $random;
    $insert = $pdo->prepare("INSERT INTO rechnungen (bestellung_id, rechnungsnummer) VALUES (?, ?)");
    $insert->execute([$bestellung_id, $rechnungsnummer]);
} else {
    $rechnungsnummer = $rechnung['rechnungsnummer'];
}

// Bestellpositionen holen
$stmt = $pdo->prepare("SELECT produktname, menge, einzelpreis FROM bestellpositionen WHERE bestellung_id = ?");
$stmt->execute([$bestellung_id]);
$positionen = $stmt->fetchAll();

?>

<!DOCTYPE html>
<html lang="de">
<head>
<meta charset="UTF-8" />
<title>Rechnung <?=htmlspecialchars($rechnungsnummer)?></title>
<style>
  body { font-family: Arial, sans-serif; margin: 2rem; }
  table { width: 100%; border-collapse: collapse; margin-top: 1rem; }
  th, td { border: 1px solid #333; padding: 0.5rem; text-align: left; }
  th { background: #eee; }
  @media print {
    button#printBtn { display: none; }
  }
</style>
</head>
<body>

<button id="printBtn" onclick="window.print()">Rechnung drucken / Als PDF speichern</button>

<h1>Rechnung <?=htmlspecialchars($rechnungsnummer)?></h1>
<p><strong>Datum:</strong> <?=date('d.m.Y')?></p>

<h3>Rechnung an:</h3>
<p>
  <?=htmlspecialchars($bestellung['name'])?><br>
  <?=nl2br(htmlspecialchars($bestellung['adresse']))?><br>
  <?=htmlspecialchars($bestellung['plz'] . " " . $bestellung['ort'])?>
</p>

<h3>Bestellung Nr. <?=htmlspecialchars($bestellung_id)?></h3>

<table>
  <thead>
    <tr><th>Produkt</th><th>Menge</th><th>Einzelpreis</th><th>Gesamtpreis</th></tr>
  </thead>
  <tbody>
    <?php
    $gesamt = 0;
    foreach ($positionen as $pos) {
        $preis = $pos['menge'] * $pos['einzelpreis'];
        $gesamt += $preis;
        echo "<tr>";
        echo "<td>" . htmlspecialchars($pos['produktname']) . "</td>";
        echo "<td>" . htmlspecialchars($pos['menge']) . "</td>";
        echo "<td>" . number_format($pos['einzelpreis'], 2) . " €</td>";
        echo "<td>" . number_format($preis, 2) . " €</td>";
        echo "</tr>";
    }
    ?>
    <tr>
      <td colspan="3" style="text-align:right"><strong>Gesamt:</strong></td>
      <td><strong><?=number_format($gesamt, 2)?> €</strong></td>
    </tr>
  </tbody>
</table>

<p>Vielen Dank für Ihren Einkauf!</p>

</body>
</html>
