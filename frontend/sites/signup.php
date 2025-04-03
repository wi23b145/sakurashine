<!DOCTYPE html>
<html lang="en">
<head>
    <title>Registrierung</title>
</head>

<body>
<header>
    <h1>Registrierung</h1>
</header>
<?php include("includes/nav.php");?>

<main>

    <div class="container">
        <div class="row">
            <div class="col-md-4"></div>
            <div class="col-md-4">
                <?php
                    if (isset($_SESSION['error'])) {
                        echo '<div class="message-error">' . $_SESSION['error'] . '</div>';
            unset($_SESSION['error']); // Löscht die Nachricht nach der Anzeige
            }
            ?>
        </div>
        <div class="col-md-4"></div>
    </div>
    <div class="row registrationrow">
        <div class="col-md-4"></div>
        <div class="col-4 form">
            <form action="php/registration.php" method="post">
                <div class="row">
                    <div class="col-md-6">
                        <label for="anrede">Anrede:</label>

                        <select id="anrede" class="form-control" name="anr" required>
                            <option></option>
                            <option>Frau</option>
                            <option>Herr</option>
                            <option>Divers</option>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <label for="vorname">Vorname:</label>
                        <input  type="text" id="vorname" class="form-control" name="vorname" style="padding-right: 0;" placeholder="Vorname" required>
                    </div>
                    <div class="col-md-6">
                        <label for="nachname">Nachname:</label>
                        <input  type="text" id="nachname" class="form-control" name="nachname" style="padding-right: 0;" placeholder="Nachname" required>
                    </div>
                </div>

                <label for="email">Email:</label>
                <input  type="email" id="email" class="form-control" name="email" placeholder="Email" required>
                
                
                <label for="adresse">Adresse:</label>
                <input  type="adresse" id="adresse" class="form-control" name="adresse" placeholder="Adresse" required>

                
                <label for="plz">PLZ:</label>
                <input  type="plz" id="plz" class="form-control" name="plz" placeholder="PLZ" required>
               
                <label for="ort">Ort:</label>
                <input  type="ort" id="ort" class="form-control" name="ort" placeholder="Ort" required>
               
                <label for="benutzername">Username:</label>
                <input  type="text" id="benutzername" class="form-control" name="benutzername" placeholder="Benutzername" required>

                <label for="passwort">Passwort:</label>
                <input  type="password" id="passwort" class="form-control" name="passwort" placeholder="Passwort" required>

                <label for="wpassword">Wiederholen Sie das Passwort:</label>
                <input  type="password" id="wpassword" class="form-control" name="wpassword" placeholder="Repeat password" required>

                <input type="submit" class="btn btn-primary" value="Submit" id="regis">
                <input type="reset" class="btn btn-primary" value="Reset" id="reset">


            </form>
        </div>
        <div class="col-md-4"></div>
    </div>
    </div>
</main>



<div class="footer">
    <p>@ 2024 Zum Goldenen Eichhörnchen</p>
</div>
</body>
</html>