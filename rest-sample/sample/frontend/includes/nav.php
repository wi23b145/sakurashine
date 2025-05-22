<?php if (session_status() === PHP_SESSION_NONE) {
    session_start();
} ?>
<nav class="navbar">
  <ul class="nav">
    <li class="nav-item">
      <a class="nav-link" href="/sakurashine/rest-sample/sample/frontend/index.php">HOME</a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="/sakurashine/rest-sample/sample/frontend/sites/produkte.php">PRODUKTE</a>
    </li>

    <?php if (!isset($_SESSION['user'])): ?>
      <!-- Gast -->
      <li class="nav-item">
        <a class="nav-link" href="/sakurashine/rest-sample/sample/frontend/sites/signup.php">SIGN UP</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="/sakurashine/rest-sample/sample/frontend/sites/login.php">SIGN IN</a>
      </li>

    <?php elseif ($_SESSION['user']['ist_admin'] == 1): ?>
      <!-- Admin -->
      <li class="nav-item">
        <a class="nav-link" href="/sakurashine/rest-sample/sample/frontend/sites/admin_dashboard.php">
          ADMIN DASHBOARD
        </a>
      </li>
      

    <?php else: ?>
      <!-- Eingeloggter normaler User -->
      
      <li class="nav-item ms-auto">
        <div class="dropdown">
          <li class="nav-item">
            <a class="nav-link" href="/sakurashine/rest-sample/sample/frontend/sites/cart.php" aria-label="Warenkorb">
              <img src="/sakurashine/rest-sample/sample/frontend/res/img/einkaufswagen.png" alt="Warenkorb" id="cart">
            </a>
          </li>


          <button class="btn btn-secondary dropdown-toggle" type="button"
                  id="dropdownMenuButton" data-bs-toggle="dropdown"
                  aria-expanded="false">
            <?= htmlspecialchars($_SESSION['user']['vorname']) ?>
          </button>
          <ul class="dropdown-menu dropdown-menu-end" 
              aria-labelledby="dropdownMenuButton">
            <li>
              <a class="dropdown-item" 
                 href="/sakurashine/rest-sample/sample/backend/logic/logout.php">
                SIGN OUT
              </a>
            </li>
          </ul>
        </div>
      </li>
    <?php endif; ?>
  </ul>
</nav>
