<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require 'db.php';

$stmt = $pdo->query("SELECT * FROM bestellungen ORDER BY bestell_datum DESC");
echo "<h2>Bestellungen</h2><table border='1'>
<tr><th>ID</th><th>Name</th><th>Email</th><th>Datum</th><th>Status</th></tr>";

while ($row = $stmt->fetch()) {
    echo "<tr>
            <td>{$row['id']}</td>
            <td>{$row['kunde_name']}</td>
            <td>{$row['kunde_email']}</td>
            <td>{$row['bestell_datum']}</td>
            <td>{$row['status']}</td>
          </tr>";
}
echo "</table>";
?>
