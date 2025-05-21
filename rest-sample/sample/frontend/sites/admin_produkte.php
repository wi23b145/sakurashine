<?php

require_once __DIR__ . '/../../backend/config/dbaccess.php';
session_start();

if (empty($_SESSION['user']) || $_SESSION['user']['ist_admin'] != 1) {
    header('Location: ../sites/login.html');
    exit();
}

$errors = [];

// Kategorien laden
$categories = [];
$res = $con->query("SELECT id, name FROM categories ORDER BY name");
if ($res) {
    while ($row = $res->fetch_assoc()) {
        $categories[] = $row;
    }
}

// Produkt löschen
if (isset($_GET['delete'])) {
    $delId = filter_input(INPUT_GET, 'delete', FILTER_VALIDATE_INT);
    if ($delId) {
        $stmt = $con->prepare("DELETE FROM `Produkte` WHERE id = ?");
        $stmt->bind_param("i", $delId);
        $stmt->execute();
        $stmt->close();
    }
    header('Location: admin_produkte.php');
    exit();
}

// Bearbeitungsmodus
$isEdit  = false;
$product = [
    'id'            => null,
    'name'          => '',
    'beschreibung'  => '',
    'preis'         => '',
    'bestand'       => 0,
    'bild'          => '',
    'category_id'   => null,
];
if (isset($_GET['edit'])) {
    $eid    = filter_input(INPUT_GET, 'edit', FILTER_VALIDATE_INT);
    if ($eid) {
        $stmt = $con->prepare("SELECT * FROM `Produkte` WHERE id = ?");
        $stmt->bind_param("i", $eid);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        if ($row) {
            $product = $row;
            $isEdit  = true;
        }
        $stmt->close();
    }
}

// Speichern/Anlegen
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $isEdit       = !empty($_POST['id']);
    $id           = $isEdit ? intval($_POST['id']) : null;
    $name         = trim($_POST['name']);
    $beschreibung = trim($_POST['beschreibung']);
    $preis        = filter_input(INPUT_POST, 'preis', FILTER_VALIDATE_FLOAT);
    $bestand      = filter_input(INPUT_POST, 'bestand', FILTER_VALIDATE_INT);
    $category_id  = filter_input(INPUT_POST, 'category_id', FILTER_VALIDATE_INT);
    $bildPfad     = $product['bild'];

    // Validierung
    if ($name === '' || mb_strlen($name) > 100) {
        $errors[] = 'Name darf nicht leer sein und max. 100 Zeichen haben.';
    }
    if ($beschreibung === '') {
        $errors[] = 'Beschreibung darf nicht leer sein.';
    }
    if ($preis === false || $preis <= 0) {
        $errors[] = 'Preis muss eine positive Zahl sein.';
    }
    if ($bestand === false || $bestand < 0) {
        $errors[] = 'Bestand muss ≥ 0 sein.';
    }
    if ($category_id === false || $category_id === null) {
        $errors[] = 'Bitte eine gültige Kategorie auswählen.';
    }

    if (isset($_FILES['bild']) && $_FILES['bild']['error'] === UPLOAD_ERR_OK) {
        $allowed = ['image/jpeg','image/png'];
        if (!in_array($_FILES['bild']['type'], $allowed)) {
            $errors[] = 'Nur JPG/PNG-Dateien erlaubt.';
        }
        if ($_FILES['bild']['size'] > 2 * 1024 * 1024) {
            $errors[] = 'Maximal 2 MB pro Bild.';
        }
    } elseif (!$isEdit) {
        $errors[] = 'Bitte ein Bild hochladen.';
    }

    if (empty($errors)) {
        if (isset($_FILES['bild']) && $_FILES['bild']['error'] === UPLOAD_ERR_OK) {
            $ext      = pathinfo($_FILES['bild']['name'], PATHINFO_EXTENSION);
            $fn       = uniqid('prod_') . ".$ext";
            $uDir     = __DIR__ . '/../res/img/';
            if (!is_dir($uDir)) {
                mkdir($uDir, 0755, true);
            }
            move_uploaded_file($_FILES['bild']['tmp_name'], "$uDir$fn");
            $bildPfad = $fn;
        }

        if ($isEdit) {
            $sql = "UPDATE `Produkte`
                       SET name=?, beschreibung=?, preis=?, bestand=?, bild=?, category_id=?
                     WHERE id=?";
            $stmt = $con->prepare($sql);
            $stmt->bind_param(
                "ssdissi",
                $name, $beschreibung, $preis, $bestand, $bildPfad, $category_id, $id
            );
        } else {
            $sql = "INSERT INTO `Produkte`
                      (name, beschreibung, preis, bestand, bild, category_id)
                    VALUES (?,?,?,?,?,?)";
            $stmt = $con->prepare($sql);
            $stmt->bind_param(
                "ssdiss",
                $name, $beschreibung, $preis, $bestand, $bildPfad, $category_id
            );
        }
        $stmt->execute();
        $stmt->close();

        header('Location: admin_produkte.php');
        exit();
    }
}

// Produkte laden
$sql = "SELECT p.*, c.name AS kategorie_name 
        FROM Produkte p 
        LEFT JOIN categories c ON p.category_id = c.id 
        ORDER BY p.erstellt_am DESC";
$result = $con->query($sql);

?>
<!DOCTYPE html>
<html lang="de">
<head>
  <meta charset="UTF-8">
  <?php include("../includes/header.php");?>
  <title>Produkte verwalten</title>
  <style>
    body { font-family: Arial; padding:20px; }
    .errors { color:red; }
    table { width:100%; border-collapse:collapse; margin-top:20px; }
    th, td { border:1px solid #ccc; padding:8px; }
    img.thumb { max-width:80px; }
    form > div { margin-bottom:10px; }
  </style>
</head>
<body>
  <?php include("../includes/nav.php");?>
  
  <div class="container mt-5">
    <h1>Produkte verwalten</h1>

    <?php if ($errors): ?>
      <div class="errors">
        <ul>
        <?php foreach ($errors as $e): ?>
          <li><?= htmlspecialchars($e) ?></li>
        <?php endforeach ?>
        </ul>
      </div>
    <?php endif ?>

    <form method="post" enctype="multipart/form-data">
      <?php if ($isEdit): ?>
        <input type="hidden" name="id" value="<?= $product['id'] ?>">
      <?php endif ?>

      <div>
        <label>Name<br>
          <input type="text" name="name"
                 value="<?= htmlspecialchars($product['name']) ?>"
                 maxlength="100">
        </label>
      </div>
      <div>
        <label>Beschreibung<br>
          <textarea name="beschreibung"
                    rows="4"><?= htmlspecialchars($product['beschreibung']) ?></textarea>
        </label>
      </div>
      <div>
        <label>Kategorie<br>
          <select name="category_id" required>
            <option value="">Bitte wählen</option>
            <?php foreach ($categories as $cat): ?>
              <option value="<?= $cat['id'] ?>" <?= (isset($product['category_id']) && $product['category_id'] == $cat['id']) ? 'selected' : '' ?>>
                <?= htmlspecialchars($cat['name']) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </label>
      </div>
      <div>
        <label>Preis<br>
          <input type="number" name="preis" step="0.01"
                 value="<?= htmlspecialchars($product['preis']) ?>">
        </label>
      </div>
      <div>
        <label>Bestand<br>
          <input type="number" name="bestand"
                 value="<?= htmlspecialchars($product['bestand']) ?>">
        </label>
      </div>
      <div>
        <label>Bild <?= $isEdit ? '(optional)' : '(Pflicht)' ?><br>
          <input type="file" name="bild" accept=".jpg,.jpeg,.png">
        </label>
      </div>
      <?php if ($isEdit && $product['bild']): ?>
        <div>
          <strong>Aktuelles Bild:</strong><br>
          <img src="../<?= htmlspecialchars($product['bild']) ?>" class="thumb" alt="">
        </div>
      <?php endif ?>
      <div>
        <button type="submit"><?= $isEdit ? 'Speichern' : 'Anlegen' ?></button>
        <?php if ($isEdit): ?>
          <a href="admin_produkte.php">Abbrechen</a>
        <?php endif ?>
      </div>
    </form>

    <hr>

    <h2>Bestehende Produkte</h2>
    <table>
      <thead>
        <tr>
          <th>Bild</th>
          <th>Name</th>
          <th>Kategorie</th>
          <th>Preis</th>
          <th>Bestand</th>
          <th>Aktionen</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
          <td>
            <?php if ($row['bild']): ?>
              <img src="../<?= htmlspecialchars($row['bild']) ?>" class="thumb" alt="">
            <?php endif ?>
          </td>
          <td><?= htmlspecialchars($row['name']) ?></td>
          <td><?= htmlspecialchars($row['kategorie_name']) ?></td>
          <td>€ <?= number_format($row['preis'], 2, ',', '.') ?></td>
          <td><?= (int)$row['bestand'] ?></td>
          <td>
            <a href="?edit=<?= $row['id'] ?>">Bearbeiten</a> |
            <a href="?delete=<?= $row['id'] ?>"
               onclick="return confirm('Wirklich löschen?');">Löschen</a>
          </td>
        </tr>
        <?php endwhile ?>
      </tbody>
    </table>
  </div>
</body>
</html>
