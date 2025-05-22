<?php
require_once("../../backend/logic/myaccountfunction.php");

?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <?php include("../includes/header.php");?>
    <script src="../js/myAccount.js"></script>
    
    <title>Daten bearbeiten</title>
  </head>
<body>
    <?php include("../includes/nav.php");?>
<main>
    <div class="container">
        <div id="bestellungen">
            <form action="/sakurashine/rest-sample/sample/backend/logic/myOrders.php" method="post">
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
                <input type="submit" class="btn btn-primary" value="Refresh" id="submitorder"><br><br>

            </form>
        </div>
        <script src="../js/order.js"></script>
        <div class="row registrationrow">
            <h1>Meine Daten</h1>
            <div class="col-md-2"></div>
            <div class="col-8 form">
                <form action="../../backend/logic/myaccount.php" method="post">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <label for="anrede">Anrede:</label>          

                            <select id="anrede" class="form-control" name="anrede" required>
                                <option value="" <?php if ($_SESSION['user']['anrede'] == '') echo 'selected'; ?>></option>
                                <option value="Frau" <?php if ($_SESSION['user']['anrede'] == 'Frau') echo 'selected'; ?>>Frau</option>
                                <option value="Herr" <?php if ($_SESSION['user']['anrede'] == 'Herr') echo 'selected'; ?>>Herr</option>
                                <option value="Divers" <?php if ($_SESSION['user']['anrede'] == 'Divers') echo 'selected'; ?>>Divers</option>
                            </select>

                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group mb-3">
                            <label for="vorname">Vorname:</label>

                            <!-- Maskierte Anzeige -->
                            <div id="vorname_display">
                                <span><?= maskiere($_SESSION['user']['vorname']) ?></span>
                                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="bearbeiten('vorname')">Bearbeiten</button>
                            </div>

                            <!-- Verstecktes Eingabefeld mit Originalwert -->
                            <input type="text"
                                    name="vorname"
                                    id="vorname"
                                    class="form-control d-none"
                                    value="<?= htmlspecialchars($_SESSION['user']['vorname']) ?>">
                    	</div>
                        <div class="form-group mb-3">
                        <label for="nachname">Nachname:</label>
                        <div id="nachname_display">
                            <span><?= maskiere($_SESSION['user']['nachname']) ?></span>
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="bearbeiten('nachname')">Bearbeiten</button>
                        </div>
                        <input type="text"
                                name="nachname"
                                id="nachname"
                                class="form-control d-none"
                                value="<?= htmlspecialchars($_SESSION['user']['nachname']) ?>">
                        </div>
                    </div>
                    
                    <div class="form-group mb-3">
                        <label for="email">E-Mail:</label>
                        <div id="email_display">
                            <span><?= preg_replace('/(?<=.).(?=[^@]*?@)/', '*', $_SESSION['user']['email']) ?></span>
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="bearbeiten('email')">Bearbeiten</button>
                        </div>
                        <input type="email"
                                name="email"
                                id="email"
                                class="form-control d-none"
                                value="<?= htmlspecialchars($_SESSION['user']['email']) ?>">
                     </div>

                    <div class="form-group mb-3">
                        <label for="adresse">Adresse:</label>
                        <div id="adresse_display">
                            <span><?= maskiere($_SESSION['user']['adresse']) ?></span>
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="bearbeiten('adresse')">Bearbeiten</button>
                        </div>
                        <input type="text"
                                name="adresse"
                                id="adresse"
                                class="form-control d-none"
                                value="<?= htmlspecialchars($_SESSION['user']['adresse']) ?>">
                    </div>

                    <div class="row">
                        <div class="form-group col-md-6 mb-3">
                            <label for="plz">PLZ:</label>
                            <div id="plz_display">
                                <span><?= maskiere($_SESSION['user']['plz']) ?></span>
                                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="bearbeiten('plz')">Bearbeiten</button>
                            </div>
                            <input type="text"
                                    name="plz"
                                    id="plz"
                                    class="form-control d-none"
                                    value="<?= htmlspecialchars($_SESSION['user']['plz']) ?>">
                        </div>

                       <div class="form-group col-md-6 mb-3">
                            <label for="ort">Ort:</label>
                            <div id="ort_display">
                                <span><?= maskiere($_SESSION['user']['ort']) ?></span>
                                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="bearbeiten('ort')">Bearbeiten</button>
                            </div>
                            <input type="text"
                                    name="ort"
                                    id="ort"
                                    class="form-control d-none"
                                    value="<?= htmlspecialchars($_SESSION['user']['ort']) ?>">
                        </div>

                    </div>

                    <label for="zahlungsinformationen">Zahlungsmethode:</label>
                        <select name="zahlungsinformationen" id="zahlungsinformationen" class="form-control">
                            <option value="">Bitte wählen</option>
                            <option value="Kreditkarte">Kreditkarte</option>
                            <option value="PayPal">PayPal</option>
                            <option value="Rechnung">Rechnung</option>
                        </select>

                            
                    <div class="form-group mb-3">
                        <label for="benutzername">Benutzername:</label>
                        <div id="benutzername_display">
                            <span><?= maskiere($_SESSION['user']['benutzername']) ?></span>
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="bearbeiten('benutzername')">Bearbeiten</button>
                        </div>
                        <input type="text"
                                name="benutzername"
                                id="benutzername"
                                class="form-control d-none"
                                value="<?= htmlspecialchars($_SESSION['user']['benutzername']) ?>">
                    </div>

                    <label for="oldpassword">Aktuelles Passwort (Pflicht):</label>
                    <input type="password" name="oldpassword" class="form-control" placeholder="Aktuelles Passwort" required>
                    
                    <label for="password">Neues Passwort (Optional):</label>
                    <input  type="password" id="password" class="form-control" name="passwort" placeholder="Neues Passwort (optional)">
                            
                    <label for="wpassword">Wiederholen Sie das Passwort:</label>
                    <input  type="password" id="wpassword" class="form-control" name="wpassword" placeholder="Wiederholen">

                    <input type="submit" class="btn btn-primary" value="Submit" id="submit">
                    <input type="reset" class="btn btn-primary" value="Reset" id="reset">
                </form>
            </div>
            <div class="col-md-2"></div>
        </div>
    </div>
</main>
<div class="footer">
    <p>@2025 SakuraShine</p>
  </div>
</body>
</html>
