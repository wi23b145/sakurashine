<!-- frontend/sites/warenkorb.html -->
<!DOCTYPE html>
<html lang="de">
  <head>
    <?php include("../includes/header.php");?>
    <title>Warenkorb</title>
  </head> 
<body>
  <?php include("../includes/nav.php");?>
  <h1>Ihr Warenkorb</h1>
  <div class="container cart" id="cart-container">
    <div class="row">
      <div class="col-2"></div>
      <div class="col-8">
        <table id="cart-table">
          <thead>
            <tr>
              <th>Produkt</th>
              <th>Preis</th>
              <th>Menge</th>
              <th>Gesamt</th>
              <th>Aktion</th>
            </tr>
          </thead>
          <tbody>
            <!-- Dynamisch gefüllte Zeilen -->
          </tbody>
        </table>
      </div>
      <div class="col-2"></div>
    </div>
    <div class="row">
      <div class="col-2"></div>
      <div class="col-8">
         <p id="cart-total">Gesamtsumme: €0.00</p>
        <button id="checkout-btn">Zur Kasse</button>
      </div>
      <div class="col-2"></div>
    </div>
  </div>

  <script src="../js/cart.js"></script>
  <div class="footer">
    <p>@2025 SakuraShine</p>
  </div>
</body>
</html>
