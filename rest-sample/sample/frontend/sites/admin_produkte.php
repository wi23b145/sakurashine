<!DOCTYPE html>
<html lang="de">
<head>
  <meta charset="UTF-8" />
  <title>Produkte verwalten</title>
  <?php include(__DIR__ . '/../includes/header.php'); ?>
  <!-- Bootstrap CSS (falls nicht schon in header.php enthalten) -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.4.0/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body>
  <?php include(__DIR__ . '/../includes/nav.php'); ?>

  <div class="container mt-4">
    <h1>Produkte verwalten</h1>
    
    <table class="table table-striped">
  <thead>
    <tr>
      <th>Bild</th>
      <th>Name</th>
      <th>Beschreibung</th>
      <th>Preis</th>
    </tr>
    <tr>
      <!-- Eingabefelder unter den Spaltenüberschriften -->
      <th>
        <input type="file" id="neuBild" name="neuBild" accept=".jpg,.jpeg,.png" class="form-control form-control-sm" />
      </th>
      <th><input type="text" id="neuName" name="neuName" class="form-control form-control-sm" maxlength="100" /></th>
      <th><input type="text" id="neuBeschreibung" name="neuBeschreibung" class="form-control form-control-sm" /></th>
      <th><input type="number" id="neuPreis" name="neuPreis" class="form-control form-control-sm" min="0" step="0.01" /></th>
      <th>
        <button id="btnNeuSpeichern" class="btn btn-sm btn-success">Neu speichern</button>
      </th>
    </tr>
  </thead>
  <tbody id="produktListe">
    <!-- Hier kommen Produkt-Zeilen per JS -->
  </tbody>
</table>

  </div>

  <!-- Modal für Produktformular -->
  <div class="modal fade" id="produktModal" tabindex="-1" aria-labelledby="modalTitel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <form id="produktForm" class="modal-content" enctype="multipart/form-data">
        <div class="modal-header">
          <h5 class="modal-title" id="modalTitel">Produkt anlegen</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Schließen"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" id="produktId" name="id" />
          <div class="mb-3">
            <label for="name" class="form-label">Name *</label>
            <input type="text" class="form-control" id="name" name="name" required maxlength="100" />
          </div>
          <div class="mb-3">
            <label for="beschreibung" class="form-label">Beschreibung *</label>
            <textarea class="form-control" id="beschreibung" name="beschreibung" required></textarea>
          </div>
          <div class="mb-3">
            <label for="preis" class="form-label">Preis *</label>
            <input type="number" class="form-control" id="preis" name="preis" min="0" step="0.01" required />
          </div>
          <div class="mb-3">
            <label for="bild" class="form-label">Produktbild</label>
            <input type="file" class="form-control" id="bild" name="bild" accept=".jpg,.jpeg,.png" />
            <img id="bildVorschau" src="#" alt="Bildvorschau" class="img-thumbnail mt-2 d-none" style="max-height: 150px;" />
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Speichern</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Abbrechen</button>
        </div>
      </form>
    </div>
  </div>

  
  <script src="../js/admin_produkte.js"></script>
  <div class="footer">
    <p>@2025 SakuraShine</p>
  </div>
</body>
</html>
