<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
 
require_once(__DIR__ . "/../../backend/config/dbaccess.php");
require_once __DIR__ . '/../../backend/logic/myaccountfunction.php';
 

if (!isset($_SESSION['user'])) {
    die("Bitte zuerst einloggen.");
}
 
$user_id = $_SESSION['user']['id'];
 
// Bestellungen in Session laden (alternativ hier DB-Abfrage machen)
if (!isset($_SESSION['orders'])) {
    $_SESSION['orders'] = []; // Falls du hier dynamisch laden möchtest, bitte DB-Abfrage ergänzen.
}
 
// Funktion holeLetzteBestellungId muss in myaccountfunction.php definiert sein
$lastBestellungId = holeLetzteBestellungId($con, $user_id);
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <?php include("../includes/header.php"); ?>
    <script src="../js/myAccount.js"></script>
    <title>Daten bearbeiten & Bestellungen</title>
</head>
<body>
    <?php include("../includes/nav.php");?>
 
    <main>
      <div class="container">
 
        <!-- 1) Bestellungen anzeigen -->
        <form action="/sakurashine/rest-sample/sample/backend/logic/myOrders.php" method="post" style="margin-bottom: 2rem;">
          <h1>Meine Bestellungen</h1>
          <?php if (isset($_SESSION['orders']) && count($_SESSION['orders']) > 0): ?>
            <table class="table table-striped">
              <thead>
                <tr>
                  <th>Erstellt am</th>
                  <th>Status</th>
                  <th>Gesamtpreis</th>
                  <th>Name</th>
                  <th>Adresse</th>
                  <th>PLZ</th>
                  <th>Ort</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($_SESSION['orders'] as $order): ?>
                <tr>
                  <td><?= htmlspecialchars($order['erstellt_am']) ?></td>
                  <td><?= htmlspecialchars($order['bestellstatus']) ?></td>
                  <td>€ <?= number_format($order['gesamtpreis'], 2, ',', '.') ?></td>
                  <td><?= htmlspecialchars($order['name']) ?></td>
                  <td><?= htmlspecialchars($order['adresse']) ?></td>
                  <td><?= htmlspecialchars($order['plz']) ?></td>
                  <td><?= htmlspecialchars($order['ort']) ?></td>
                </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          <?php else: ?>
            <p>Keine Bestellungen gefunden.</p>
          <?php endif; ?>
          <input type="submit" class="btn btn-primary" value="Bestellungen aktualisieren">
        </form>
 
        <!-- 2) Rechnung für letzte Bestellung anzeigen -->
        <?php if ($lastBestellungId): ?>
        <form action="../../backend/logic/invoice.php" method="get" target="_blank" style="margin-bottom: 3rem;">
          <input type="hidden" name="bestellung_id" value="<?= htmlspecialchars($lastBestellungId) ?>">
          <button type="submit" class="btn btn-primary">Rechnung für letzte Bestellung anzeigen</button>
        </form>
        <?php else: ?>
          <p>Keine Bestellungen gefunden.</p>
        <?php endif; ?>
 
        <!-- 3) Benutzerdaten bearbeiten -->
        <h2>Meine Daten bearbeiten</h2>
        <form action="../../backend/logic/myaccount.php" method="post" novalidate>
            <!-- Erfolg / Fehler Meldungen -->
            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($_SESSION['error']) ?></div>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>
 
            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success"><?= htmlspecialchars($_SESSION['success']) ?></div>
                <?php unset($_SESSION['success']); ?>
            <?php endif; ?>
 
            <div class="row">
                <div class="col-md-6">
                    <label for="anrede">Anrede:</label>
                    <select id="anrede" class="form-control" name="anrede" required>
                        <option value="" <?= ($_SESSION['user']['anrede'] == '') ? 'selected' : '' ?>></option>
                        <option value="Frau" <?= ($_SESSION['user']['anrede'] == 'Frau') ? 'selected' : '' ?>>Frau</option>
                        <option value="Herr" <?= ($_SESSION['user']['anrede'] == 'Herr') ? 'selected' : '' ?>>Herr</option>
                        <option value="Divers" <?= ($_SESSION['user']['anrede'] == 'Divers') ? 'selected' : '' ?>>Divers</option>
                    </select>
                </div>
            </div>
 
            <div class="row mt-3">
                <div class="form-group col-md-6">
                    <label for="vorname">Vorname:</label>
                    <div id="vorname_display">
                        <span><?= maskiere($_SESSION['user']['vorname']) ?></span>
                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="bearbeiten('vorname')">Bearbeiten</button>
                    </div>
                    <input type="text" name="vorname" id="vorname" class="form-control d-none" value="<?= htmlspecialchars($_SESSION['user']['vorname']) ?>">
                </div>
                <div class="form-group col-md-6">
                    <label for="nachname">Nachname:</label>
                    <div id="nachname_display">
                        <span><?= maskiere($_SESSION['user']['nachname']) ?></span>
                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="bearbeiten('nachname')">Bearbeiten</button>
                    </div>
                    <input type="text" name="nachname" id="nachname" class="form-control d-none" value="<?= htmlspecialchars($_SESSION['user']['nachname']) ?>">
                </div>
            </div>
 
            <div class="form-group mt-3">
                <label for="email">E-Mail:</label>
                <div id="email_display">
                    <span><?= preg_replace('/(?<=.).(?=[^@]*?@)/', '*', $_SESSION['user']['email']) ?></span>
                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="bearbeiten('email')">Bearbeiten</button>
                </div>
                <input type="email" name="email" id="email" class="form-control d-none" value="<?= htmlspecialchars($_SESSION['user']['email']) ?>">
            </div>
 
            <div class="form-group mt-3">
                <label for="adresse">Adresse:</label>
                <div id="adresse_display">
                    <span><?= maskiere($_SESSION['user']['adresse']) ?></span>
                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="bearbeiten('adresse')">Bearbeiten</button>
                </div>
                <input type="text" name="adresse" id="adresse" class="form-control d-none" value="<?= htmlspecialchars($_SESSION['user']['adresse']) ?>">
            </div>
 
            <div class="row mt-3">
                <div class="form-group col-md-6">
                    <label for="plz">PLZ:</label>
                    <div id="plz_display">
                        <span><?= maskiere($_SESSION['user']['plz']) ?></span>
                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="bearbeiten('plz')">Bearbeiten</button>
                    </div>
                    <input type="text" name="plz" id="plz" class="form-control d-none" value="<?= htmlspecialchars($_SESSION['user']['plz']) ?>">
                </div>
 
                <div class="form-group col-md-6">
                    <label for="ort">Ort:</label>
                    <div id="ort_display">
                        <span><?= maskiere($_SESSION['user']['ort']) ?></span>
                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="bearbeiten('ort')">Bearbeiten</button>
                    </div>
                    <input type="text" name="ort" id="ort" class="form-control d-none" value="<?= htmlspecialchars($_SESSION['user']['ort']) ?>">
                </div>
            </div>
 
            <label for="zahlungsinformationen" class="mt-3">Zahlungsmethode:</label>
            <select name="zahlungsinformationen" id="zahlungsinformationen" class="form-control">
                <option value="">Bitte wählen</option>
                <option value="Kreditkarte" <?= (($_SESSION['user']['zahlungsinformationen'] ?? '') === 'Kreditkarte') ? 'selected' : '' ?>>Kreditkarte</option>
                <option value="PayPal" <?= (($_SESSION['user']['zahlungsinformationen'] ?? '') === 'PayPal') ? 'selected' : '' ?>>PayPal</option>
                <option value="Rechnung" <?= (($_SESSION['user']['zahlungsinformationen'] ?? '') === 'Rechnung') ? 'selected' : '' ?>>Rechnung</option>
            </select>
 
            <div class="form-group mt-3">
                <label for="benutzername">Benutzername:</label>
                <div id="benutzername_display">
                    <span><?= maskiere($_SESSION['user']['benutzername']) ?></span>
                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="bearbeiten('benutzername')">Bearbeiten</button>
                </div>
                <input type="text" name="benutzername" id="benutzername" class="form-control d-none" value="<?= htmlspecialchars($_SESSION['user']['benutzername']) ?>">
            </div>
 
            <label for="oldpassword" class="mt-3">Aktuelles Passwort (Pflicht):</label>
            <input type="password" name="oldpassword" class="form-control" placeholder="Aktuelles Passwort" required>
 
            <label for="password" class="mt-3">Neues Passwort (Optional):</label>
            <input type="password" id="password" class="form-control" name="passwort" placeholder="Neues Passwort (optional)">
 
            <label for="wpassword" class="mt-3">Wiederholen Sie das Passwort:</label>
            <input type="password" id="wpassword" class="form-control" name="wpassword" placeholder="Wiederholen">
 
            <div class="mt-4 mb-5">
              <input type="submit" class="btn btn-primary" value="Speichern">
              <input type="reset" class="btn btn-secondary" value="Zurücksetzen">
            </div>
        </form>
 
      </div>
    </main>
 
    <div class="footer text-center py-3">
        <p>&copy;2025 SakuraShine</p>
    </div>
 
</body>
</html>
 
 