<!DOCTYPE html>
<html lang="en">
  <head>
    <?php include("../includes/header.php");?>
    <title>Login</title>
  <head>

<body>
    <?php include("../includes/nav.php");?>
    <main>
    
        <div class="container">
            <div class="row loginform">
                <div class="col-md-4"></div>
                <div class="col-4 form">
                    <form action="../../backend/logic/signin.php" method="post">
                        <label for="username">Username:</label>
                        <input  type="text" class="form-control" id="username" name="username" value="<?php echo $_COOKIE['username'] ?? ''; ?>" required>

                        <label for="passwort">Passwort:</label>
                        <input  type="password" class="form-control" id="passwort" name="passwort" placeholder="Password" required>
                        <br>

                        <label>
                            <input type="checkbox" name="remember" <?php if (isset($_COOKIE['username'])) echo 'checked'; ?>>
                            Login merken
                         </label>

                        <input type="submit" class="btn btn-primary" value="Login" id="submit">
                        <input type="reset" class="btn btn-primary" value="Reset" id="reset">
                        
                        <!--<p>Passwort vergessen? <a href="passwordreset.php" id="resetpwd">Passwort zurücksetzen</a>-->
                    </form>
                    <p style="color:red;">
                      <?php
                        if (isset($_SESSION['error'])) {
                          echo $_SESSION['error'];
                          unset($_SESSION['error']);
                        }
                      ?>
                    </p>
                </div>
                <div class="col-md-4"></div>
            </div>
        </div>
    </main>
    
    <div class="footer">
        <p>@2025 SakuraShine</p>
      </div>
      
</body>


</html>