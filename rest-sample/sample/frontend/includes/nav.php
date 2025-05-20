<?php session_start();?>
<nav class="navbar">
    <ul class="nav">
      <li class="nav-item">
        <a class="nav-link" href="/sakurashine/rest-sample/sample/frontend/index.php">HOME</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="/sakurashine/rest-sample/sample/frontend/sites/produkte.php">PRODUKTE</a>
      </li>
      <?php 
          if(isset($_SESSION['user'])){
              echo "<li class='nav-item'>
                <a class='nav-link' href='/sakurashine/rest-sample/sample/frontend/sites/myAccount.php'>MEIN KONTO</a>
              </li>";}
        ?>
        <?php 
          if(!isset($_SESSION['user'])){
              echo "<li class='nav-item'>
            <a class='nav-link' href='/sakurashine/rest-sample/sample/frontend/sites/signup.php'>SIGN UP</a>
          </li>";}
        ?>
        <?php 
          if(!isset($_SESSION['user'])){
              echo "<li class='nav-item'>
            <a class='nav-link' href='/sakurashine/rest-sample/sample/frontend/sites/login.php'>SIGN IN</a>
          </li>";}
        ?>
   
      <li class="nav-item ms-auto">
        <div class="dropdown">
          <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" 
            data-bs-toggle="dropdown" aria-expanded="false">
            -
          </button>
          <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton">
            <?php 
                if(isset($_SESSION['user'])) {
                    $vorname = htmlspecialchars($_SESSION['user']['vorname']);
                    echo "<li><a class='dropdown-item'>Willkommen $vorname</a></li>";

                } 
            ?>
            <?php 
              if (isset($_SESSION['user'])) {
                  echo "<li><a class='dropdown-item' href='/sakurashine/rest-sample/sample/frontend/sites/editUser.php'>Profil bearbeiten</a></li>";
              }
            ?>

            <li><a class="dropdown-item" href="../sites/cart.php">CART</a></li>
            <?php 
              if (isset($_SESSION['user'])) {
                  echo "<li><a class='dropdown-item' href='/sakurashine/rest-sample/sample/backend/logic/logout.php'>SIGN OUT</a></li>";
              }
            ?>
            
          </ul>
        </div>
      </li>
    </ul>
</nav>