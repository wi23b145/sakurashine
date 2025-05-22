// File: frontend/js/cart.js
// Dynamische Anzeige und Verwaltung des Warenkorbs auf der Warenkorbseite

document.addEventListener('DOMContentLoaded', () => {
  // 1) Elemente referenzieren
  const tbody          = document.querySelector('#cart-table tbody');
  const cartTotalElem  = document.getElementById('cart-total');
  const checkoutBtn    = document.getElementById('checkout-btn');

  // 2) Warenkorb aus localStorage laden
  const warenkorb = JSON.parse(localStorage.getItem('warenkorb') || '[]');

  // 3) Warenkorb rendern
  function renderCart() {
    tbody.innerHTML = '';
    let summe = 0;

    warenkorb.forEach((produkt, index) => {
      const gesamt = produkt.preis * produkt.menge;
      summe += gesamt;

      const tr = document.createElement('tr');
      tr.innerHTML = `
        <td>${produkt.name}</td>
        <td>${produkt.menge}</td>
        <td>€${produkt.preis.toFixed(2)}</td>
        <td>€${gesamt.toFixed(2)}</td>
        <td>
          <button class="btn btn-sm btn-outline-secondary decrease-qty" data-index="${index}">-</button>
          <button class="btn btn-sm btn-outline-secondary increase-qty" data-index="${index}">+</button>
          <button class="btn btn-sm btn-outline-danger remove-item" data-index="${index}">Entfernen</button>
        </td>
      `;
      tbody.appendChild(tr);
    });

    cartTotalElem.textContent = `Gesamtsumme: €${summe.toFixed(2)}`;

    bindCartButtons();
  }

  // 4) Event-Handler binden (Nach jedem Render)
  function bindCartButtons() {
    document.querySelectorAll('.increase-qty').forEach(btn => {
      btn.addEventListener('click', () => {
        const i = +btn.dataset.index;
        warenkorb[i].menge += 1;
        updateStorageAndRender();
      });
    });

    document.querySelectorAll('.decrease-qty').forEach(btn => {
      btn.addEventListener('click', () => {
        const i = +btn.dataset.index;
        warenkorb[i].menge -= 1;
        if (warenkorb[i].menge <= 0) warenkorb.splice(i, 1);
        updateStorageAndRender();
      });
    });

    document.querySelectorAll('.remove-item').forEach(btn => {
      btn.addEventListener('click', () => {
        const i = +btn.dataset.index;
        warenkorb.splice(i, 1);
        updateStorageAndRender();
      });
    });
  }

  // 5) Speicher aktualisieren und neu rendern
  function updateStorageAndRender() {
    localStorage.setItem('warenkorb', JSON.stringify(warenkorb));
    renderCart();
  }

  // 6) Checkout-Button Weiterleitung
  if (checkoutBtn) {
    checkoutBtn.addEventListener('click', () => {
      window.location.href = 'checkout.php';
    });
  } else {
    console.error('Checkout-Button nicht gefunden!');
  }

  // Erstrederung
  renderCart();
});
