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
