<?php
session_start();
require_once('../config/dbaccess.php');

if (!isset($_SESSION['id'])) {
    echo "Nicht eingeloggt.";
    exit;
}

$user_id = $_SESSION['id'];

$sql = "SELECT P.name, P.preis, W.menge, (P.preis * W.menge) as gesamt, P.id as produkt_id
        FROM Warenkorb W
        JOIN Produkte P ON W.produkt_id = P.id
        WHERE W.user_id = ?";
$stmt = $con->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$gesamt_summe = 0;
?>

<h2>Ihr Warenkorb</h2>
<table>
    <tr><th>Produkt</th><th>Preis</th><th>Menge</th><th>Gesamt</th><th>Aktion</th></tr>
    <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= htmlspecialchars($row['name']) ?></td>
            <td><?= number_format($row['preis'], 2) ?> €</td>
            <td><?= $row['menge'] ?></td>
            <td><?= number_format($row['gesamt'], 2) ?> €</td>
            <td>
                <form method="POST" action="../logic/remove_from_cart_db.php">
                    <input type="hidden" name="produkt_id" value="<?= $row['produkt_id'] ?>">
                    <button type="submit">Entfernen</button>
                </form>
            </td>
        </tr>
        <?php $gesamt_summe += $row['gesamt']; ?>
    <?php endwhile; ?>
</table>
<p><strong>Gesamtsumme: <?= number_format($gesamt_summe, 2) ?> €</strong></p>
