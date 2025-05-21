<!DOCTYPE html>
<html lang="en">
  <head>
    <?php include("includes/header.php");?>
    
    <title>Home</title>
  </head>
<body>
  <div class="container-fluid">
    <main>
      <div class="intromessage">
        <div class="row intromessage">
            <div class="col-md-4"></div>
            <div class="col-md-4">
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
              <script src="js/message.js"></script>
            </div>  
            </div> 
      </div> 
      <div class="intro">                
          <div id="text">
              <p id="intro">SakuraShine</p>
          </div>
      </div>
  </main>
  </div>
  <?php include("includes/nav.php");?>
  <div class="footer">
    <p>@2025 SakuraShine</p>
  </div>
</body>
</html>
