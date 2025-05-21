<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


require_once(__DIR__ . "/../../backend/config/dbaccess.php");

require_once __DIR__ . '/../../backend/logic/myaccountfunction.php';

if (!isset($_SESSION['user'])) {
    die("Bitte zuerst einloggen.");
}

$user_id = $_SESSION['user']['id'];

// Funktion holeLetzteBestellungId muss in myaccountfunction.php definiert sein
$lastBestellungId = holeLetzteBestellungId($con, $user_id);
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <?php include("../includes/header.php"); ?>
    <script src="../js/myAccount.js"></script>
    <title>Daten bearbeiten</title>
</head>
<body>
    <?php include("../includes/nav.php"); ?>

    <main>
        <div class="container">
            <!-- Erfolg / Fehler Meldungen -->
            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($_SESSION['error']) ?></div>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>

            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success"><?= htmlspecialchars($_SESSION['success']) ?></div>
                <?php unset($_SESSION['success']); ?>
            <?php endif; ?>

            <!-- Rechnung Button -->
            <?php if ($lastBestellungId): ?>
                <div class="mt-3 mb-4">
                <form action="../../backend/logic/invoice.php" method="get" target="_blank">
    <input type="hidden" name="bestellung_id" value="<?= htmlspecialchars($lastBestellungId) ?>">
    <button type="submit" class="btn btn-primary">Rechnung für letzte Bestellung anzeigen</button>
</form>

                </div>
            <?php else: ?>
                <p>Keine Bestellungen gefunden.</p>
            <?php endif; ?>

            <div class="row registrationrow">
                <div class="col-md-2"></div>
                <div class="col-8 form">
                    <form action="../../backend/logic/myaccount.php" method="post" novalidate>
                        <div class="row">
                            <div class="col-md-6">
                                <label for="anrede">Anrede:</label>          
                                <select id="anrede" class="form-control" name="anrede" required>
                                    <option value="" <?= ($_SESSION['user']['anrede'] == '') ? 'selected' : '' ?>></option>
                                    <option value="Frau" <?= ($_SESSION['user']['anrede'] == 'Frau') ? 'selected' : '' ?>>Frau</option>
                                    <option value="Herr" <?= ($_SESSION['user']['anrede'] == 'Herr') ? 'selected' : '' ?>>Herr</option>
                                    <option value="Divers" <?= ($_SESSION['user']['anrede'] == 'Divers') ? 'selected' : '' ?>>Divers</option>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group mb-3">
                                <label for="vorname">Vorname:</label>
                                <div id="vorname_display">
                                    <span><?= maskiere($_SESSION['user']['vorname']) ?></span>
                                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="bearbeiten('vorname')">Bearbeiten</button>
                                </div>
                                <input type="text" name="vorname" id="vorname" class="form-control d-none" value="<?= htmlspecialchars($_SESSION['user']['vorname']) ?>">
                            </div>
                            <div class="form-group mb-3">
                                <label for="nachname">Nachname:</label>
                                <div id="nachname_display">
                                    <span><?= maskiere($_SESSION['user']['nachname']) ?></span>
                                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="bearbeiten('nachname')">Bearbeiten</button>
                                </div>
                                <input type="text" name="nachname" id="nachname" class="form-control d-none" value="<?= htmlspecialchars($_SESSION['user']['nachname']) ?>">
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label for="email">E-Mail:</label>
                            <div id="email_display">
                                <span><?= preg_replace('/(?<=.).(?=[^@]*?@)/', '*', $_SESSION['user']['email']) ?></span>
                                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="bearbeiten('email')">Bearbeiten</button>
                            </div>
                            <input type="email" name="email" id="email" class="form-control d-none" value="<?= htmlspecialchars($_SESSION['user']['email']) ?>">
                        </div>

                        <div class="form-group mb-3">
                            <label for="adresse">Adresse:</label>
                            <div id="adresse_display">
                                <span><?= maskiere($_SESSION['user']['adresse']) ?></span>
                                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="bearbeiten('adresse')">Bearbeiten</button>
                            </div>
                            <input type="text" name="adresse" id="adresse" class="form-control d-none" value="<?= htmlspecialchars($_SESSION['user']['adresse']) ?>">
                        </div>

                        <div class="row">
                            <div class="form-group col-md-6 mb-3">
                                <label for="plz">PLZ:</label>
                                <div id="plz_display">
                                    <span><?= maskiere($_SESSION['user']['plz']) ?></span>
                                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="bearbeiten('plz')">Bearbeiten</button>
                                </div>
                                <input type="text" name="plz" id="plz" class="form-control d-none" value="<?= htmlspecialchars($_SESSION['user']['plz']) ?>">
                            </div>

                            <div class="form-group col-md-6 mb-3">
                                <label for="ort">Ort:</label>
                                <div id="ort_display">
                                    <span><?= maskiere($_SESSION['user']['ort']) ?></span>
                                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="bearbeiten('ort')">Bearbeiten</button>
                                </div>
                                <input type="text" name="ort" id="ort" class="form-control d-none" value="<?= htmlspecialchars($_SESSION['user']['ort']) ?>">
                            </div>
                        </div>

                        <label for="zahlungsinformationen">Zahlungsmethode:</label>
                        <select name="zahlungsinformationen" id="zahlungsinformationen" class="form-control" required>
                            <option value="">Bitte wählen</option>
                            <option value="Kreditkarte" <?= (($_SESSION['user']['zahlungsinformationen'] ?? '') === 'Kreditkarte') ? 'selected' : '' ?>>Kreditkarte</option>
                            <option value="PayPal" <?= (($_SESSION['user']['zahlungsinformationen'] ?? '') === 'PayPal') ? 'selected' : '' ?>>PayPal</option>
                            <option value="Rechnung" <?= (($_SESSION['user']['zahlungsinformationen'] ?? '') === 'Rechnung') ? 'selected' : '' ?>>Rechnung</option>
                        </select>

                        <div class="form-group mb-3">
                            <label for="benutzername">Benutzername:</label>
                            <div id="benutzername_display">
                                <span><?= maskiere($_SESSION['user']['benutzername']) ?></span>
                                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="bearbeiten('benutzername')">Bearbeiten</button>
                            </div>
                            <input type="text" name="benutzername" id="benutzername" class="form-control d-none" value="<?= htmlspecialchars($_SESSION['user']['benutzername']) ?>">
                        </div>

                        <label for="oldpassword">Aktuelles Passwort (Pflicht):</label>
                        <input type="password" name="oldpassword" class="form-control" placeholder="Aktuelles Passwort" required>

                        <label for="password">Neues Passwort (Optional):</label>
                        <input type="password" id="password" class="form-control" name="passwort" placeholder="Neues Passwort (optional)">

                        <label for="wpassword">Wiederholen Sie das Passwort:</label>
                        <input type="password" id="wpassword" class="form-control" name="wpassword" placeholder="Wiederholen">

                        <input type="submit" class="btn btn-primary" value="Submit" id="submit">
                        <input type="reset" class="btn btn-primary" value="Reset" id="reset">
                    </form>
                </div>
                <div class="col-md-2"></div>
            </div>
        </div>
    </main>

    <div class="footer">
        <p>&copy;2025 SakuraShine</p>
    </div>
</body>
</html>
