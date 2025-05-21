<?php
require 'db.php';

$sql = "SELECT r.*, b.kunde_name 
        FROM rechnungen r
        JOIN bestellungen b ON r.bestellung_id = b.id
        ORDER BY r.rechnung_datum DESC";
$stmt = $pdo->query($sql);

echo "<h2>Rechnungen</h2><table border='1'>
<tr><th>ID</th><th>Rechnung an</th><th>Adresse</th><th>Zahlung</th>
    <th>Gesamt (€)</th><th>Status</th><th>PDF</th><th>Kunde</th></tr>";

while ($row = $stmt->fetch()) {
    echo "<tr>
            <td>{$row['id']}</td>
            <td>{$row['rechnung_name']}</td>
            <td>{$row['rechnung_adresse']}</td>
            <td>{$row['zahlung_methode']}</td>
            <td>{$row['gesamtbetrag']}</td>
            <td>{$row['status']}</td>
            <td><a href='{$row['pdf_pfad']}' target='_blank'>Download</a></td>
            <td>{$row['kunde_name']}</td>
          </tr>";
}
echo "</table>";
?>
