<?php

require_once __DIR__ . '/../../backend/config/dbaccess.php';
if (empty($_SESSION['user']) || $_SESSION['user']['ist_admin'] != 1) {
    header('Location: ../sites/login.html');
    exit();
}

// 3) Fehler-Array
$errors = [];

// 4) Gutschein löschen
if (isset($_GET['delete'])) {
    $delId = filter_input(INPUT_GET, 'delete', FILTER_VALIDATE_INT);
    if ($delId) {
        $stmt = $con->prepare("DELETE FROM `Gutscheine` WHERE id = ?");
        $stmt->bind_param('i', $delId);
        $stmt->execute();
        $stmt->close();
    }
    header('Location: admin_gutscheine.php');
    exit();
}

// 5) Hilfsfunktion: Code-Generierung
function generateCode($length = 5) {
    $chars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $code = '';
    for ($i = 0; $i < $length; $i++) {
        $code .= $chars[random_int(0, strlen($chars) - 1)];
    }
    return $code;
}

// 6) Gutschein anlegen
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Eingaben
    $codeInput = strtoupper(trim($_POST['code'] ?? ''));
    $type      = $_POST['type'] ?? '';          // 'percent' oder 'fixed'
    $rabatt    = filter_input(INPUT_POST, 'rabatt_prozent', FILTER_VALIDATE_FLOAT);
    $geldwert  = filter_input(INPUT_POST, 'geldwert', FILTER_VALIDATE_FLOAT);
    $gueltig   = $_POST['gueltig_bis'] ?? '';

    // Validierung
    if (!in_array($type, ['percent','fixed'], true)) {
        $errors[] = 'Bitte wählen Sie den Gutscheintyp.';
    }
    if ($type === 'percent') {
        if ($rabatt === false || $rabatt <= 0 || $rabatt > 100) {
            $errors[] = 'Prozent-Rabatt muss zwischen 0 und 100 liegen.';
        }
    } elseif ($type === 'fixed') {
        if ($geldwert === false || $geldwert <= 0) {
            $errors[] = 'Geldwert muss größer als 0 sein.';
        }
    }
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $gueltig)) {
        $errors[] = 'Bitte ein gültiges Ablaufdatum auswählen.';
    }

    // Code validieren oder generieren
    if ($codeInput !== '') {
        if (!preg_match('/^[0-9A-Z]{5}$/', $codeInput)) {
            $errors[] = 'Der Code muss exakt 5 Großbuchstaben/Ziffern sein.';
        } else {
            $check = $con->prepare("SELECT id FROM `Gutscheine` WHERE code = ?");
            $check->bind_param('s', $codeInput);
            $check->execute();
            $exists = $check->get_result()->num_rows > 0;
            $check->close();
            if ($exists) {
                $errors[] = 'Dieser Code existiert bereits.';
            } else {
                $code = $codeInput;
            }
        }
    }
    if (empty($code)) {
        do {
            $code = generateCode();
            $check = $con->prepare("SELECT id FROM `Gutscheine` WHERE code = ?");
            $check->bind_param('s', $code);
            $check->execute();
            $exists = $check->get_result()->num_rows > 0;
            $check->close();
        } while ($exists);
    }

    if (empty($errors)) {
        // Insert
        $stmt = $con->prepare(
            "INSERT INTO `Gutscheine`
             (code, typ, rabatt_prozent, geldwert, gueltig_bis, `eingelöst`)
             VALUES (?, ?, ?, ?, ?, 0)"
        );
        // Nie NULL binden, sondern immer 0 bei nicht-passt
        $bindRabatt = ($type === 'percent' ? $rabatt : 0);
        $bindGeld   = ($type === 'fixed'   ? $geldwert : 0);
        $stmt->bind_param('ssdds',
            $code, $type, $bindRabatt, $bindGeld, $gueltig
        );
        if (!$stmt->execute()) {
            die('DB-Error beim Erstellen: ' . $stmt->error);
        }
        $stmt->close();

        header('Location: admin_gutscheine.php');
        exit();
    }
}

// 7) Gutscheine laden
$sql = "SELECT id, code, typ, rabatt_prozent, geldwert, gueltig_bis, `eingelöst`, erstellt_am
        FROM `Gutscheine` ORDER BY erstellt_am DESC";
$result = $con->query($sql);
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <?php include("../includes/header.php");?>
    <title>Admin Dashboard</title>
  </head> 
<body>
  <?php include("../includes/nav.php");?>
  
</head>
<body>
  <h1>Gutscheine verwalten</h1>
<style>
    body { font-family: Arial; padding:20px; }
    .errors { color:red; }
    table { width:100%; border-collapse:collapse; margin-top:20px; }
    th, td { border:1px solid #ccc; padding:8px; }
    img.thumb { max-width:80px; }
    form > div { margin-bottom:10px; }
  </style>
  <?php if ($errors): ?>
  <div class="errors"><ul>
    <?php foreach ($errors as $e): ?>
      <li><?= htmlspecialchars($e) ?></li>
    <?php endforeach; ?>
  </ul></div>
  <?php endif; ?>

  <form method="post">
    <div>
      <label>Code (5 Zeichen)<br>
        <input type="text" name="code" maxlength="5" pattern="[0-9A-Z]{5}"
               value="<?= htmlspecialchars($_POST['code'] ?? '') ?>">
      </label>
      <small>Leer lassen für automatische Generierung</small>
    </div>
    <div>
      <label>Typ<br>
        <select name="type" required>
          <option value="">-- auswählen --</option>
          <option value="percent" <?= ($_POST['type'] ?? '')==='percent'?'selected':'' ?>>Rabatt (%)</option>
          <option value="fixed" <?= ($_POST['type'] ?? '')==='fixed'?'selected':'' ?>>Geldwert (€)</option>
        </select>
      </label>
    </div>
    <div>
      <label>Rabatt (%)<br>
        <input type="number" name="rabatt_prozent" step="0.01" min="0" max="100"
               value="<?= htmlspecialchars($_POST['rabatt_prozent'] ?? '') ?>"
               <?= (($_POST['type'] ?? '')==='percent')?'required':'' ?> >
      </label>
    </div>
    <div>
      <label>Geldwert (€)<br>
        <input type="number" name="geldwert" step="0.01" min="0"
               value="<?= htmlspecialchars($_POST['geldwert'] ?? '') ?>"
               <?= (($_POST['type'] ?? '')==='fixed')?'required':'' ?> >
      </label>
    </div>
    <div>
      <label>Ablaufdatum<br>
        <input type="date" name="gueltig_bis" required
               value="<?= htmlspecialchars($_POST['gueltig_bis'] ?? '') ?>">
      </label>
    </div>
    <div>
      <button type="submit">Gutschein erstellen</button>
    </div>
  </form>

  <hr>
  <h2>Übersicht aller Gutscheine</h2>
  <table>
    <thead>
      <tr>
        <th>Code</th>
        <th>Typ</th>
        <th>Rabatt (%)</th>
        <th>Geldwert (€)</th>
        <th>Ablaufdatum</th>
        <th>Status</th>
        <th>Aktion</th>
      </tr>
    </thead>
    <tbody>
    <?php
      $today = date('Y-m-d');
      while ($row = $result->fetch_assoc()):
          if ($row['eingelöst']) {
              $status = '<span class="redeemed">Eingelöst</span>';
          } elseif ($row['gueltig_bis'] < $today) {
              $status = '<span class="expired">Abgelaufen</span>';
          } else {
              $status = '<span class="active">Aktiv</span>';
          }
    ?>
      <tr>
        <td><?= htmlspecialchars($row['code']) ?></td>
        <td><?= htmlspecialchars($row['typ']) ?></td>
        <td><?= $row['rabatt_prozent'] !== null ? number_format($row['rabatt_prozent'],2,',','.') . '%' : '' ?></td>
        <td><?= $row['geldwert'] !== null ? '€ ' . number_format($row['geldwert'],2,',','.') : '' ?></td>
        <td><?= htmlspecialchars($row['gueltig_bis']) ?></td>
        <td><?= $status ?></td>
        <td><a href="?delete=<?= $row['id'] ?>" onclick="return confirm('Gutschein wirklich löschen?');">Löschen</a></td>
      </tr>
    <?php endwhile; ?>
    </tbody>
  </table>

</body>
</html>
