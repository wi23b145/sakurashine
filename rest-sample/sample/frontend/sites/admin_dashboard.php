<!DOCTYPE html>
<html lang="de">
  <head>
    <?php include("../includes/header.php");?>
    <title>Admin Dashboard</title>
  </head> 
<body>
  <?php include("../includes/nav.php");?>

    <div class="container" id="admin-content">
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
      <?php endif; ?>
        <h1 class="mb-4">Willkommen im Adminbereich!</h1>
        <a class="btn btn-primary mb-3" href="admin_customers.php">Kund*innen verwalten</a>
        <a class="btn btn-primary mb-3" href="admin_produkte.php">Produkte verwalten</a>
    </div>

    <div class="footer">
        <p>@2025 SakuraShine</p>
    </div>
</body>
</html>