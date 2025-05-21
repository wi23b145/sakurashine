<<<<<<< HEAD
// frontend/js/checkout.js
document.addEventListener('DOMContentLoaded', () => {
  const cart = JSON.parse(localStorage.getItem('cart') || '[]');
  const tbody = document.querySelector('#checkout-tabelle tbody');
  let total = 0;

  cart.forEach(item => {
    const row = document.createElement('tr');
    const lineTotal = item.preis * item.menge;
    total += lineTotal;

    row.innerHTML = `
      <td>${item.name}</td>
      <td>${item.menge}</td>
      <td>€ ${item.preis.toFixed(2)}</td>
      <td>€ ${lineTotal.toFixed(2)}</td>
    `;
    tbody.appendChild(row);
  });

  document.getElementById('checkout-summe').textContent =
    `Gesamtsumme: €${total.toFixed(2)}`;

  // Warenkorb JSON ins Hidden-Feld packen
  document.getElementById('warenkorb-input').value = JSON.stringify(cart);

  // Formular-Submit per AJAX
  document.getElementById('checkout-form')
    .addEventListener('submit', async e => {
      e.preventDefault();
      const form = e.target;
      const data = new FormData(form);

      try {
        const res = await fetch('../backend/logic/submit_order.php', {
          method: 'POST',
          body: data
        });
        const text = await res.text();
        if (!res.ok) throw new Error(text);
        // Erfolgreiche Bestellung: Anzeigen oder weiterleiten
        document.body.innerHTML = `<div class="container">${text}</div>`;
        // Optional: clear cart
        localStorage.removeItem('cart');
      } catch (err) {
        alert('Fehler bei der Bestellung: ' + err.message);
      }
    });
});
=======
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


>>>>>>> 342c54d66036d2092c6f831a5ead80ecbc768cdc
