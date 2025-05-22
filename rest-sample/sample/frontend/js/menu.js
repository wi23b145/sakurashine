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


