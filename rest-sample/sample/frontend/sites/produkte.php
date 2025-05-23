<?php
session_start();
 
// DB-Verbindung einbinden (stelle sicher, dass $con in dbaccess.php definiert ist)
require_once __DIR__ . '/../../backend/config/dbaccess.php';
 
// Prüfen, ob DB-Verbindung funktioniert
if (!$con) {
    die("Fehler bei der DB-Verbindung: " . mysqli_connect_error());
}
 
// SQL-Abfrage definieren
$sql = "
  SELECT
    p.id,
    p.name,
    p.beschreibung,
    p.preis,
    p.bild,
    c.name AS kategorie_name
  FROM Produkte p
  JOIN categories c ON p.category_id = c.id
  ORDER BY p.erstellt_am DESC
";
 
// SQL-Abfrage ausführen
$result = $con->query($sql);
 
// Prüfen ob Abfrage erfolgreich war
if (!$result) {
    die("Fehler in der SQL-Abfrage: " . $con->error);
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
  <?php include(__DIR__ . '/../includes/header.php'); ?>
  <title>Produkte – Sakura Shine</title>
</head>
<body class="bg-light">
  <?php include(__DIR__ . '/../includes/nav.php'); ?>
 
  <div class="container py-5">
    <h1 class="mb-4">Alle Produkte</h1>
 
    <!-- Warenkorb-Button -->
    <div class="d-flex justify-content-end mb-3">
      <a href="cart.php" class="btn btn-outline-primary position-relative">
        Zum Warenkorb
        <span id="cart-count"
              class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
          0
        </span>
      </a>
    </div>
 
    <!-- Kategorie-Filter -->
    <div class="mb-3">
      <label for="kategorieFilter" class="form-label">Kategorie:</label>
      <select id="kategorieFilter" class="form-select w-auto">
        <option value="alle">Alle Kategorien</option>
        <option value="figur">Figuren</option>
        <option value="instant-nudeln">Instant-Nudeln</option>
        <option value="snack">Snacks</option>
        <option value="getraenke">Getränke</option>
        <option value="tee">Tee</option>
        <option value="gewuerze">Gewürze</option>
      </select>
    </div>
 
    <!-- Suchfeld -->
    <div class="mb-4">
      <input type="text" id="suchfeld" class="form-control" placeholder="Produkte suchen…">
    </div>
 
    <!-- Produkt-Grid -->
    <div class="row row-cols-1 row-cols-md-3 g-4">
      <?php while ($row = $result->fetch_assoc()):
        // Kategorienamen in slug umwandeln für Filterzwecke (z.B. "Getränke" => "getraenke")
        $slug = strtolower(str_replace(
          [' ', 'ä','ö','ü','ß'],
          ['-','ae','oe','ue','ss'],
          $row['kategorie_name']
        ));
      ?>
        <div class="col product-card" data-kategorie="<?= htmlspecialchars($slug) ?>">
          <div class="card h-100">
            <img src="../res/img/<?= htmlspecialchars($row['bild']) ?>"
                 class="card-img-top product-img"
                 alt="<?= htmlspecialchars($row['name']) ?>">
            <div class="card-body">
              <h5 class="card-title"><?= htmlspecialchars($row['name']) ?></h5>
              <p class="card-text"><?= htmlspecialchars($row['beschreibung']) ?></p>
              <p><strong>Preis: €<?= number_format($row['preis'], 2, ',', '.') ?></strong></p>
              <button class="btn btn-primary add-to-cart"
                      data-id="<?= $row['id'] ?>"
                      data-name="<?= htmlspecialchars($row['name']) ?>"
                      data-preis="<?= $row['preis'] ?>">
                In den Warenkorb
              </button>
            </div>
          </div>
        </div>
      <?php endwhile; ?>
    </div>
  </div>
 
  <div class="footer text-center py-3">
    <p>&copy;2025 SakuraShine</p>
  </div>
 
  <script src="../js/produkte.js"></script>
</body>
</html>
 
 