<!DOCTYPE html>
<html lang="de">
    <head>
        <?php include("../includes/header.php");?>
        <title>Konto</title>
    </head>
  
<body>
    <?php include("../includes/nav.php");?>

  <!-- Dynamisches Menü -->
  <script src="js/menu.js"></script>



    <div class="container mt-5">
        <h1 class="mb-4">Mein Konto</h1>

        <div class="card">
            <div class="card-body">
                <div class="card-body">
                    <h5 class="card-title" id="anrede"></h5>
                    <p class="card-text" id="name"></p>
                    <p class="card-text" id="adresse"></p>
                    <p class="card-text" id="email"></p>
                    <p class="card-text" id="benutzername"></p>
                    <p class="card-text" id="zahlung"></p>
                </div>

            </div>
        </div>

        <a href="bestellungen.html" class="btn btn-primary mt-4">Meine Bestellungen ansehen</a>
    </div>


    <script src="../js/konto.js"></script>


  <div class="footer">
    <p>@2025 SakuraShine</p>
  </div>

</body>
</html>