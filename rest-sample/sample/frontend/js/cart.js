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
  