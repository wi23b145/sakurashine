<?php




require_once __DIR__ . '/../../backend/config/dbaccess.php';
if (empty($_SESSION['user']) || $_SESSION['user']['ist_admin'] != 1) {
    header('Location: ../sites/login.html');
    exit();
}

// 3) Array für Fehlermeldungen
$errors = [];

// 4) Löschen eines Produkts
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

// 5) Vorbefüllen im Bearbeitungsmodus
$isEdit  = false;
$product = [
    'id'            => null,
    'name'          => '',
    'beschreibung'  => '',
    'preis'         => '',
    'bestand'       => 0,
    'bewertung'     => 0,
    'bild'          => '',
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

// 6) Anlegen / Speichern
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $isEdit       = !empty($_POST['id']);
    $id           = $isEdit ? intval($_POST['id']) : null;
    $name         = trim($_POST['name']);
    $beschreibung = trim($_POST['beschreibung']);
    $preis        = filter_input(INPUT_POST, 'preis', FILTER_VALIDATE_FLOAT);
    $bestand      = filter_input(INPUT_POST, 'bestand', FILTER_VALIDATE_INT);
    $bewertung    = filter_input(INPUT_POST, 'bewertung', FILTER_VALIDATE_FLOAT);
    $bildPfad     = $product['bild'];

    // --- Validierung ---
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
    if ($bewertung === false || $bewertung < 0 || $bewertung > 5) {
        $errors[] = 'Bewertung muss zwischen 0 und 5 liegen.';
    }

    // Bild-Upload nur bei Neuanlage Pflicht, im Edit optional
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

    // wenn valide: Bild verschieben und DB-Operation
    if (empty($errors)) {
        if (isset($_FILES['bild']) && $_FILES['bild']['error'] === UPLOAD_ERR_OK) {
            $ext      = pathinfo($_FILES['bild']['name'], PATHINFO_EXTENSION);
            $fn       = uniqid('prod_') . ".$ext";
            $uDir     = __DIR__ . '/../res/img/';
            if (!is_dir($uDir)) {
                mkdir($uDir, 0755, true);
            }
            move_uploaded_file($_FILES['bild']['tmp_name'], "$uDir$fn");
            $bildPfad = 'res/img/' . $fn;
        }

        if ($isEdit) {
            $sql = "UPDATE `Produkte`
                       SET name=?, beschreibung=?, preis=?, bestand=?, bewertung=?, bild=?
                     WHERE id=?";
            $stmt = $con->prepare($sql);
            $stmt->bind_param(
                "ssdidsi",
                $name, $beschreibung, $preis, $bestand, $bewertung, $bildPfad, $id
            );
        } else {
            $sql = "INSERT INTO `Produkte`
                      (name, beschreibung, preis, bestand, bewertung, bild)
                    VALUES (?,?,?,?,?,?)";
            $stmt = $con->prepare($sql);
            $stmt->bind_param(
                "ssdids",
                $name, $beschreibung, $preis, $bestand, $bewertung, $bildPfad
            );
        }
        $stmt->execute();
        $stmt->close();

        header('Location: admin_produkte.php');
        exit();
    }
}

// 7) Alle Produkte laden
$result = $con->query("SELECT * FROM `Produkte` ORDER BY erstellt_am DESC");

?>
<!DOCTYPE html>
<html lang="de">
<head>
  <meta charset="UTF-8">
   <head>
        <?php include("../includes/header.php");?>
        <title>Kunden verwalten</title>
    </head>
    
<body>
    <?php include("../includes/nav.php");?>
    
    <div class="container mt-5">
    
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
        <img src="../<?= htmlspecialchars($product['bild']) ?>"
             class="thumb" alt="">
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
            <img src="../<?= htmlspecialchars($row['bild']) ?>"
                 class="thumb" alt="">
          <?php endif ?>
        </td>
        <td><?= htmlspecialchars($row['name']) ?></td>
        <td>€ <?= number_format($row['preis'],2,',','.') ?></td>
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
</body>
</html>
