<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
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
        <a class="nav-link" href="/sakurashine/rest-sample/sample/frontend/sites/admin_dashboard.php">ADMIN DASHBOARD</a>
      </li>
      <li class="nav-item ms-auto">
        <a class="nav-link" href="/sakurashine/rest-sample/sample/backend/logic/logout.php">SIGN OUT</a>
      </li>
 
    <?php else: ?>
      <!-- Eingeloggter normaler User -->
      <li class="nav-item">
        <a class="nav-link" href="/sakurashine/rest-sample/sample/frontend/sites/myAccount.php">MEIN KONTO</a>
      </li>
      <li class="nav-item ms-auto">
        <div class="dropdown">
          <button class="btn btn-secondary dropdown-toggle" type="button"
                  id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
            <?= htmlspecialchars($_SESSION['user']['vorname']) ?>
          </button>
          <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton">
            <li><a class="dropdown-item" href="/sakurashine/rest-sample/sample/frontend/sites/cart.php">CART</a></li>
            <li><a class="dropdown-item" href="/sakurashine/rest-sample/sample/backend/logic/logout.php">SIGN OUT</a></li>
          </ul>
        </div>
      </li>
    <?php endif; ?>
  </ul>
</nav>
 
 
document.addEventListener("DOMContentLoaded", function () {
  fetch("../../backend/logic/userHandler.php?action=session")
    .then(response => response.json())
    .then(user => {
        console.log(user);  // Debugging-Ausgabe in der Konsole
 
        const menu = document.getElementById("dropdown-menu");
        menu.innerHTML = "";
 
        if (user.error) {
          // Nicht eingeloggt: Login/Register anzeigen
          menu.innerHTML = `
            <li><a class="dropdown-item" href="sites/signup.html">Sign Up</a></li>
            <li><a class="dropdown-item" href="sites/login.html">Sign In</a></li>
          `;
        } else {
          // Eingeloggt: Normales Benutzer-Menü
          menu.innerHTML = `
            <li><a class="dropdown-item" href="sites/konto.html">Kontoinformation</a></li>
            <li><a class="dropdown-item" href="sites/bestellungen.html">Bestellungen</a></li>
            <li><a class="dropdown-item" href="sites/cart.html">Warenkorb</a></li>
            <li><a class="dropdown-item" href="sites/passwordreset.html">Passwort ändern</a></li>
          `;
  
          if (user.ist_admin == 1) {
            menu.innerHTML += `
              <li><hr class="dropdown-divider"></li>
              <li><a class="dropdown-item text-danger" href="sites/admin_dashboard.html">Adminbereich</a></li>
              <li><a class="dropdown-item" href="sites/admin_kunden.html">Kund*innen verwalten</a></li>
              <li><a class="dropdown-item" href="sites/alle_bestellungen.html">Alle Bestellungen</a></li>
            `;
          }
  
          // Logout-Link am Ende immer
          menu.innerHTML += `
            <li><a class="dropdown-item" href="../backend/logic/logout.php">Sign Out</a></li>
          `;
        }
    })
    .catch(error => {
      console.error("Fehler beim Laden des Benutzerstatus:", error);
    });
  });
 
 
 
 
