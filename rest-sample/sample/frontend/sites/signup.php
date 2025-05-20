<!DOCTYPE html>
<html lang="en">
  <head>
    <?php include("../includes/header.php");?>
    <title>Registrierung</title>
  </head>
<body>
    <?php include("../includes/nav.php");?>
<main>
    <div class="container">
        <div class="row registrationrow">
            <div class="col-md-2"></div>
            <div class="col-8 form">
                <form action="../../backend/logic/signup.php" method="post">
                    <div class="row">
                        <div class="col-md-6">
                            <label for="anrede">Anrede:</label>          

                            <select id="anrede" class="form-control" name="anrede" required>
                                <option></option>
                                <option>Frau</option>
                                <option>Herr</option>
                                <option>Divers</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">       
                            <label for="firstname">Vorname:</label>
                            <input  type="text" id="firstname" class="form-control" name="firstname" style="padding-right: 0;" placeholder="Vorname" required>
                        </div>
                        <div class="col-md-6">
                            <label for="lastname">Nachname:</label>
                            <input  type="text" id="lastname" class="form-control" name="lastname" style="padding-right: 0;" placeholder="Nachname" required>
                        </div>
                    </div>
                    
                    <label for="email">Email:</label>
                    <input  type="email" id="email" class="form-control" name="email" placeholder="Email" required>

                    <label for="adresse">Adresse:</label>
                    <input  type="text" id="adresse" class="form-control" name="adresse" placeholder="Adresse" required>

                    <div class="row">
                        <div class="col-md-6">       
                            <label for="plz">PLZ:</label>
                            <input  type="text" id="plz" class="form-control" name="plz" style="padding-right: 0;" placeholder="PLZ" required>
                        </div>
                        <div class="col-md-6">
                            <label for="ort">Ort:</label>
                            <input  type="text" id="ort" class="form-control" name="ort" style="padding-right: 0;" placeholder="Ort" required>
                        </div>
                    </div>
                            
                    <label for="username">Username:</label>
                    <input  type="text" id="username" class="form-control" name="username" placeholder="Benutzername" required>

                    <label for="password">Passwort:</label>
                    <input  type="password" id="password" class="form-control" name="passwort" placeholder="Passwort" required>
                            
                    <label for="zahlung">Zahlungsmethode:</label>
                        <select name="zahlung" id="zahlung" class="form-control" required>
                            <option value="">Bitte wählen</option>
                            <option value="Kreditkarte">Kreditkarte</option>
                            <option value="PayPal">PayPal</option>
                            <option value="Rechnung">Rechnung</option>
                        </select>


                    <label for="wpassword">Wiederholen Sie das Passwort:</label>
                    <input  type="password" id="wpassword" class="form-control" name="wpassword" placeholder="Wiederhole Passwort" required>

                    <input type="submit" class="btn btn-primary" value="Submit" id="submit">
                    <input type="reset" class="btn btn-primary" value="Reset" id="reset">
                </form>
            </div>
            <div class="col-md-2"></div>
        </div>
    </div>
</main>
<script src="../js/passwordcheck.js"></script>
<div class="footer">
    <p>@2025 SakuraShine</p>
  </div>
</body>
</html>