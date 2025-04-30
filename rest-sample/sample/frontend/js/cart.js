document.addEventListener('DOMContentLoaded', function() {
  const warenkorb = JSON.parse(localStorage.getItem('warenkorb') || '[]');
  const tbody = document.querySelector('#cart-table tbody');
  const cartTotal = document.getElementById('cart-total');


  function aktualisiereWarenkorb() {
    tbody.innerHTML = '';
    let summe = 0;

    warenkorb.forEach((produkt, index) => {
      const tr = document.createElement('tr');

      const gesamt = produkt.preis * produkt.menge;
      summe += gesamt;

      tr.innerHTML = `
      <td>${produkt.name}</td>
      <td>€${produkt.preis.toFixed(2)}</td>
      <td class="d-flex align-items-center gap-2">
        <button class="btn btn-sm btn-outline-secondary decrease-qty" data-index="${index}">-</button>
        <span>${produkt.menge}</span>
        <button class="btn btn-sm btn-outline-secondary increase-qty" data-index="${index}">+</button>
      </td>
      <td>€${gesamt.toFixed(2)}</td>
      <td><button class="btn btn-sm btn-outline-danger remove-item" data-index="${index}">Entfernen</button></td>
    `;


      tbody.appendChild(tr);
    });
    // Entfernen-Buttons
    document.querySelectorAll('.remove-item').forEach(button => {
      button.addEventListener('click', function() {
        const index = this.dataset.index;
        warenkorb.splice(index, 1);
        localStorage.setItem('warenkorb', JSON.stringify(warenkorb));
        aktualisiereWarenkorb();
      });
    });

    // Plus-Buttons
    document.querySelectorAll('.increase-qty').forEach(button => {
      button.addEventListener('click', function() {
        const index = this.dataset.index;
        warenkorb[index].menge += 1;
        localStorage.setItem('warenkorb', JSON.stringify(warenkorb));
        aktualisiereWarenkorb();
      });
    });

    // Minus-Buttons
    document.querySelectorAll('.decrease-qty').forEach(button => {
      button.addEventListener('click', function() {
        const index = this.dataset.index;
        warenkorb[index].menge -= 1;
        if (warenkorb[index].menge <= 0) {
          warenkorb.splice(index, 1); // Produkt entfernen
        }
        localStorage.setItem('warenkorb', JSON.stringify(warenkorb));
        aktualisiereWarenkorb();
      });
    });


    cartTotal.textContent = `Gesamtsumme: €${summe.toFixed(2)}`;

    // Entfernen-Buttons neu verbinden
    document.querySelectorAll('.remove-item').forEach(button => {
      button.addEventListener('click', function() {
        const index = this.dataset.index;
        warenkorb.splice(index, 1); // Produkt löschen
        localStorage.setItem('warenkorb', JSON.stringify(warenkorb));
        aktualisiereWarenkorb(); // Warenkorb neu anzeigen
      });
    });
  }
  
  aktualisiereWarenkorb();

  const checkoutButton = document.getElementById('checkout-btn');

  if (checkoutButton) {
    checkoutButton.addEventListener('click', function() {
      console.log("Checkout-Button wurde geklickt"); // Test
      window.location.href = '../sites/checkout.html'; // Weiterleitung
    });
  } else {
    console.error("Checkout-Button nicht gefunden!"); // Falls Button fehlt
  }
});




/*
// frontend/js/cart.js
$(document).ready(function () {
    loadCart();
  
    $('#checkout-btn').click(function () {
      $.post('../../backend/logic/cartHandler.php', { action: 'checkout' }, function (response) {
        alert(response.message);
        if (response.success) location.reload();
      }, 'json');
    });
  });
  
  function loadCart() {
    $.get('../../backend/logic/cartHandler.php', { action: 'get' }, function (response) {
      let tbody = $('#cart-table tbody');
      tbody.empty();
      let total = 0;
  
      response.cart.forEach(item => {
        const subtotal = item.price * item.quantity;
        total += subtotal;
  
        tbody.append(`
          <tr>
            <td>${item.name}</td>
            <td>€${item.price.toFixed(2)}</td>
            <td>
              <input type="number" value="${item.quantity}" min="1"
                     onchange="updateQuantity(${item.id}, this.value)">
            </td>
            <td>€${subtotal.toFixed(2)}</td>
            <td>
              <button onclick="removeItem(${item.id})">Entfernen</button>
            </td>
          </tr>
        `);
      });
  
      $('#cart-total').text('Gesamtsumme: €' + total.toFixed(2));
    }, 'json');
  }
  
  function updateQuantity(productId, quantity) {
    $.post('../../backend/logic/cartHandler.php', { action: 'update', id: productId, quantity }, function () {
      loadCart();
    });
  }
  
  function removeItem(productId) {
    $.post('../../backend/logic/cartHandler.php', { action: 'remove', id: productId }, function () {
      loadCart();
    });
  }

    // cart.js oder direkt auf der Warenkorb-Seite einfügen
  document.getElementById('checkout-btn').addEventListener('click', function() {
    window.location.href = '../sites/checkout.html';
  });*/

  