<?php
session_start();
require_once('../config/dbaccess.php');

$sql = "SELECT * FROM Produkte";
$result = $con->query($sql);
?>

<h2>Produkte</h2>
<?php while ($row = $result->fetch_assoc()): ?>
    <form method="POST" action="../logic/add_to_cart_db.php">
        <input type="hidden" name="produkt_id" value="<?= $row['id'] ?>">
        <p><strong><?= htmlspecialchars($row['name']) ?></strong> – <?= number_format($row['preis'], 2) ?> €</p>
        <p><?= htmlspecialchars($row['beschreibung']) ?></p>
        <input type="number" name="menge" value="1" min="1">
        <button type="submit">In den Warenkorb</button>
    </form>
    <hr>
<?php endwhile; ?>

