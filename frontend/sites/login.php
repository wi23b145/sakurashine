<?php
session_start();
$error = isset($_GET['error']) ? htmlspecialchars($_GET['error']) : '';
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sakura Shine</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/stylesheet.css">
</head>
<header>
<?php include '/htdocs/sakurashine/frontend/sites/navbar.php'; ?>
</header>
<body>
<div class="main-container d-flex flex-column min-vh-100">
<header>
    <?php include '../site/navbar.php'; ?>
</header>

<div class="container mt-5 flex-grow-1">
    <div class="card p-4 shadow-lg w-100" style="max-width: 500px; margin: 0 auto;">
        <h2 class="text-center mb-4">Login</h2>

        <!-- Fehlermeldung anzeigen -->
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <!-- Login-Formular -->
        <form action="../config/login_handler.php" method="POST">
            <div class="mb-3">
                <label for="benutzername" class="form-label">Benutzername</label>
                <input type="text" class="form-control" id="benutzername" name="benutzername" placeholder="Benutzername eingeben" required>
            </div>
            <div class="mb-3">
                <label for="passwort" class="form-label">Passwort</label>
                <input type="passwort" class="form-control" id="passwort" name="passwort" placeholder="Passwort eingeben" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Anmelden</button>
        </form>

        <a href="../site/passwordreset.php" class="d-block mt-3 text-center">Passwort vergessen?</a>
    </div>
</div>

<footer class="text-center mt-4">
    <?php include '../site/footerbar.php'; ?>
</footer>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
