<?php
require_once("../../backend/logic/myaccountfunction.php");

?>
<!DOCTYPE html>
<html lang="de">
<head>
    <?php include("../includes/header.php"); ?>
    <script src="../js/myAccount.js"></script>
    <title>Mein Konto</title>
</head>
<body>
<?php include("../includes/nav.php"); ?>
<main class="container py-4">
  <h2>Meine Kontoinformationen</h2>

  <?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
  <?php endif; ?>
  <?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
  <?php endif; ?>

  <form action="../../backend/logic/myaccount.php" method="post">

    <!-- Dynamische Felder -->
    <?php foreach (['vorname', 'nachname', 'email', 'adresse', 'plz', 'ort', 'benutzername'] as $feld): ?>
      <div class="form-group mb-3">
        <label for="<?= $feld ?>"><?= ucfirst($feld) ?>:</label>
        <div id="<?= $feld ?>_display">
          <span><?= $feld === 'email'
              ? preg_replace('/(?<=.).(?=[^@]*?@)/', '*', safe($feld))
              : maskiere(safe($feld)) ?></span>
          <button type="button" class="btn btn-sm btn-outline-secondary" onclick="bearbeiten('<?= $feld ?>')">Bearbeiten</button>
        </div>
        <input type="<?= $feld === 'email' ? 'email' : 'text' ?>" name="<?= $feld ?>" id="<?= $feld ?>"
               value="<?= safe($feld) ?>" class="form-control d-none">
      </div>
    <?php endforeach; ?>

    <!-- Anrede -->
    <div class="form-group mb-3">
      <label for="anrede">Anrede:</label>
      <select name="anrede" id="anrede" class="form-control">
        <?php foreach (['', 'Frau', 'Herr', 'Divers'] as $a): ?>
          <option value="<?= $a ?>" <?= safe('anrede') === $a ? 'selected' : '' ?>><?= $a ?></option>
        <?php endforeach; ?>
      </select>
    </div>

    <!-- Neue Zahlungsmethode -->
    <div class="form-group mb-3">
      <label for="zahlung">Neue Zahlungsmethode (optional):</label>
      <select name="zahlung" id="zahlung" class="form-control">
        <option value="">Keine hinzufügen</option>
        <option value="Kreditkarte">Kreditkarte</option>
        <option value="PayPal">PayPal</option>
        <option value="Rechnung">Rechnung</option>
      </select>
    </div>

    <!-- Passwort -->
    <div class="form-group mb-3">
      <label for="oldpassword">Aktuelles Passwort (Pflicht):</label>
      <input type="password" name="oldpassword" class="form-control" required>

      <label for="passwort">Neues Passwort (optional):</label>
      <input type="password" name="passwort" class="form-control">

      <label for="wpassword">Neues Passwort wiederholen:</label>
      <input type="password" name="wpassword" class="form-control">
    </div>

    <button type="submit" class="btn btn-primary">Änderungen speichern</button>
    <button type="reset" class="btn btn-secondary">Zurücksetzen</button>
  </form>
</main>
<div class="footer">
    <p>@2025 SakuraShine</p>
  </div>
</body>
</html>