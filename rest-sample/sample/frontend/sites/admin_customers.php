
<?php
session_start();
if (empty($_SESSION['user']) || $_SESSION['user']['ist_admin'] != 1) {
    header('Location: ../sites/login.html');
    exit;
}
?>
<!DOCTYPE html>
<html lang="de">
    <head>
        <?php include("../includes/header.php");?>
        <title>Kunden verwalten</title>
    </head>
    
<body>
    <?php include("../includes/nav.php");?>
    
    <div class="container mt-5">
    <h2>Kund*innenübersicht</h2>
    

    <table class="table" id="kunden-tabelle">
      <thead>
        <tr>
          <th>ID</th><th>Vorname</th><th>Nachname</th>
          <th>Email</th><th>Status</th><th>Aktionen</th>
        </tr>
      </thead>
      <tbody></tbody>
    </table>
  </div>

  <div class="container mt-5">
    <h3>Bestellungen des ausgewählten Kunden</h3>
    <table class="table" id="bestellungen-tabelle">
      <thead>
        <tr>
          <th>Bestellung ID</th><th>Datum</th>
          <th>Gesamt</th><th>Produkte</th>
        </tr>
      </thead>
      <tbody>
        <tr><td colspan="4">Wähle oben einen Kunden aus</td></tr>
      </tbody>
    </table>
  </div>

  <div class="footer">
    <p>&copy;2025 SakuraShine</p>
  </div>

   
    <script src="../js/admin_customer.js"></script>
    

</body>
</html>