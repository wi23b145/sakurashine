<?php
function maskiere($text) {
    if (!$text || strlen($text) < 2) return '*';
    return substr($text, 0, 1) . str_repeat('*', strlen($text) - 1);
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <?php include("../includes/header.php");?>
    <title>Daten bearbeiten</title>
  </head>
<body>
    <?php include("../includes/nav.php");?>
<main>
    <div class="container">
        <div class="row registrationrow">
            <div class="col-md-2"></div>
            <div class="col-8 form">
                <form action="../../backend/logic/changedata.php" method="post">
                    <div class="row">
                         <?php if (isset($_SESSION['error'])): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <?= htmlspecialchars($_SESSION['error']) ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                            <?php unset($_SESSION['error']); ?>
                            <?php endif; ?>

                            <?php if (isset($_SESSION['success'])): ?>
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <?= htmlspecialchars($_SESSION['success']) ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                            <?php unset($_SESSION['success']); ?>
                            <script src="../js/message.js"></script>
                        <?php endif; ?>
                    
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
                        <div class="col-md-6">       
                            <label for="firstname">Vorname:</label>
                            <input  type="text" id="firstname" class="form-control" name="firstname" style="padding-right: 0;" value="<?php echo maskiere($_SESSION['user']['vorname']); ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label for="lastname">Nachname:</label>
                            <input  type="text" id="lastname" class="form-control" name="lastname" style="padding-right: 0;" value="<?php echo maskiere($_SESSION['user']['nachname']); ?>" required>
                        </div>
                    </div>
                    
                    <label for="email">Email:</label>
                    <input  type="email" id="email" class="form-control" name="email" value="<?php
                        $email = $_SESSION['user']['email'];
                        $masked_email = preg_replace('/(?<=.).(?=[^@]*?@)/', '*', $email); 
                        echo $masked_email; // z. B. "m****.meier@example.com"?>" >

                    <label for="adresse">Adresse:</label>
                    <input  type="text" id="adresse" class="form-control" name="adresse" value="<?php echo maskiere($_SESSION['user']['adresse']); ?>" >

                    <div class="row">
                        <div class="col-md-6">       
                            <label for="plz">PLZ:</label>
                            <input  type="text" id="plz" class="form-control" name="plz" style="padding-right: 0;" value="<?php echo maskiere($_SESSION['user']['plz']); ?>">
                        </div>
                        <div class="col-md-6">
                            <label for="ort">Ort:</label>
                            <input  type="text" id="ort" class="form-control" name="ort" style="padding-right: 0;" value="<?php echo maskiere($_SESSION['user']['ort']); ?>">
                        </div>
                    </div>
                            
                    <label for="username">Username:</label>
                    <input  type="text" id="username" class="form-control" name="username" value="<?php echo maskiere($_SESSION['user']['benutzername']); ?>">

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