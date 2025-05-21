<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once("../../backend/config/dbaccess.php");

if (!isset($_SESSION['user'])) {
    die("Bitte zuerst einloggen.");
}

$user_id = $_SESSION['user']['id'];
$bestellung_id = isset($_GET['bestellung_id']) ? (int)$_GET['bestellung_id'] : 0;
if ($bestellung_id <= 0) {
    die("Ungültige Bestell-ID.");
}

// Bestellung prüfen
$stmt = $con->prepare("SELECT * FROM bestellungen WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $bestellung_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();
$bestellung = $result->fetch_assoc();

if (!$bestellung) {
    die("Bestellung nicht gefunden oder keine Zugriffsberechtigung.");
}

// Rechnungsnummer prüfen oder erzeugen
$stmt = $con->prepare("SELECT * FROM rechnungen WHERE bestellung_id = ?");
$stmt->bind_param("i", $bestellung_id);
$stmt->execute();
$result = $stmt->get_result();
$rechnung = $result->fetch_assoc();

if (!$rechnung) {
    $jahr = date('Y');
    $random = random_int(1000, 9999);
    $rechnungsnummer = "RE" . $jahr . $random;
    $insert = $con->prepare("INSERT INTO rechnungen (bestellung_id, rechnungsnummer) VALUES (?, ?)");
    $insert->bind_param("is", $bestellung_id, $rechnungsnummer);
    $insert->execute();
} else {
    $rechnungsnummer = $rechnung['rechnungsnummer'];
}

// Bestellpositionen holen
$stmt = $con->prepare("
    SELECT p.name AS produktname, bp.menge, bp.einzelpreis 
    FROM bestellpositionen bp
    JOIN produkte p ON bp.produkt_id = p.id
    WHERE bp.bestellung_id = ?
");
$stmt->bind_param("i", $bestellung_id);
$stmt->execute();
$result = $stmt->get_result();
$positionen = $result->fetch_all(MYSQLI_ASSOC);

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
        echo "<td>" . number_format($pos['einzelpreis'], 2, ',', '.') . " €</td>";
        echo "<td>" . number_format($preis, 2, ',', '.') . " €</td>";
        echo "</tr>";
    }
    ?>
    <tr>
      <td colspan="3" style="text-align:right"><strong>Gesamt:</strong></td>
      <td><strong><?=number_format($gesamt, 2, ',', '.')?> €</strong></td>
    </tr>
  </tbody>
</table>

<p>Vielen Dank für Ihren Einkauf!</p>

</body>
</html>
