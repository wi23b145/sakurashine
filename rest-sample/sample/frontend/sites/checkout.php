<!-- frontend/sites/checkout.html -->
<!DOCTYPE html>
<html lang="de">
  <head>
    <?php include("../includes/header.php");?>
    <title>Zahlung</title>
  </head>
<body>
  <?php include("../includes/nav.php");?>
  <h1>Bestellung abschließen</h1>
  
  <div class="container">
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
          <!-- Dynamisch befüllte Zeilen -->
        </tbody>
      </table>
      
      <h2 id="checkout-summe">Gesamtsumme: €0.00</h2>
    <div class="row checkout">
        <div class="col-md-2"></div>
        <div class="col-8 form">
            <form id="checkout-form">
                <label for="name">Rechnungsname:</label>
                <input  type="text" class="form-control" id="name" name="name" placeholder="Enter Name" required>

                <label for="adress">Rechnungsadresse:</label>
                <input  type="text" class="form-control" id="adress" name="adress" placeholder="Adresse" required>
                <div class="row">
                    <div class="col-6">
                        <label for="plz">PLZ:</label>
                        <input type="text" class="form-control" id="plz" name="plz" placeholder="PLZ" required>
                    </div>
                    <div class="col-6">
                        <label for="ort">Ort:</label>
                        <input type="text" class="form-control" id="ort" name="ort" placeholder="Ort" required>
                    </div>
                </div>

                

                <label for="zahlungsmethode">Zahlungsmethode:</label>
                <select id="zahlungsmethode">
                  <option value="PayPal">PayPal</option>
                  <option value="Kreditkarte">Kreditkarte</option>
                  <option value="Überweisung">Überweisung</option>
                </select>
                
                <br>

                <label for="gutschein">Gutscheincode (optional):</label>
<input type="text" id="gutschein" name="gutschein" class="form-control" placeholder="Code eingeben">

  <br>


                <input type="submit" class="btn btn-primary" value="Checkout" id="checkout">
               
            </form>
        </div>
        <div class="col-md-2"></div>
       
    </div>
</div>

  <script src="../js/checkout.js"></script>
  <div class="footer">
    <p>@2025 SakuraShine</p>
  </div>
</body>
</html>
