<?php
// frontend/sites/checkout.php
session_start();
?>
<!DOCTYPE html>
<html lang="de">
<head>
  <?php include(__DIR__ . '/../includes/header.php'); ?>
  <title>Zahlung</title>
</head>
<body>
  <?php include(__DIR__ . '/../includes/nav.php'); ?>

  <div class="container py-5">
    <h1>Bestellung abschließen</h1>

    <h2>Ihre Bestellung:</h2>
    <table id="checkout-tabelle" class="table">
      <thead>
        <tr>
          <th>Produkt</th>
          <th>Menge</th>
          <th>Preis</th>
          <th>Gesamt</th>
        </tr>
      </thead>
      <tbody>
        <!-- Wird von JS befüllt -->
      </tbody>
    </table>

    <h2 id="checkout-summe">Gesamtsumme: €0.00</h2>

    <form id="checkout-form" class="mt-4">
      <div class="mb-3">
        <label for="name" class="form-label">Rechnungsname:</label>
        <input type="text" id="name" name="name" class="form-control" required>
      </div>
      <div class="mb-3">
        <label for="adress" class="form-label">Rechnungsadresse:</label>
        <input type="text" id="adress" name="adress" class="form-control" required>
      </div>
      <div class="row mb-3">
        <div class="col">
          <label for="plz" class="form-label">PLZ:</label>
          <input type="text" id="plz" name="plz" class="form-control" required>
        </div>
        <div class="col">
          <label for="ort" class="form-label">Ort:</label>
          <input type="text" id="ort" name="ort" class="form-control" required>
        </div>
      </div>
      <div class="mb-3">
        <label for="zahlungsmethode" class="form-label">Zahlungsmethode:</label>
        <select id="zahlungsmethode" name="zahlungsmethode" class="form-select" required>
          <option value="PayPal">PayPal</option>
          <option value="Kreditkarte">Kreditkarte</option>
          <option value="Überweisung">Überweisung</option>
        </select>
      </div>
      <div class="mb-3">
        <label for="gutschein" class="form-label">Gutscheincode (optional):</label>
        <input type="text" id="gutschein" name="gutschein" class="form-control" placeholder="Code eingeben">
      </div>
      <button id="checkout-btn" class="btn btn-primary">Zur Kasse</button>
    </form>
  </div>

  <div class="footer text-center py-3">
    <p>&copy; 2025 SakuraShine</p>
  </div>

  <script src="../js/checkout.js"></script>
</body>
</html>
