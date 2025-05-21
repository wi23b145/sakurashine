document.addEventListener('DOMContentLoaded', function () {
  const warenkorb = JSON.parse(localStorage.getItem('warenkorb') || '[]');

  document.querySelectorAll('.add-to-cart').forEach(button => {
    button.addEventListener('click', function () {
      const id = this.dataset.id;
      const name = this.dataset.name;
      const preis = parseFloat(this.dataset.preis);

      // Prüfen, ob Produkt schon im Warenkorb vorhanden
      const bestehendesProdukt = warenkorb.find(item => item.id === id);

      if (bestehendesProdukt) {
        bestehendesProdukt.menge += 1; // Menge erhöhen
      } else {
        warenkorb.push({ id, name, preis, menge: 1 }); // Neues Produkt
      }

      localStorage.setItem('warenkorb', JSON.stringify(warenkorb));
      zeigeNachricht('Produkt wurde zum Warenkorb hinzugefügt!', 'success');

      function zeigeNachricht(text, typ = 'success') {
  const container = document.getElementById('feedback-area');
  const alertBox = document.createElement('div');
  alertBox.className = `alert alert-${typ} alert-dismissible fade show`;
  alertBox.setAttribute('role', 'alert');
  alertBox.innerHTML = `
    ${text}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  `;
  container.appendChild(alertBox);

  // Automatisch nach 3 Sekunden entfernen
  setTimeout(() => {
    alertBox.classList.remove('show');
    alertBox.classList.add('hide');
    setTimeout(() => alertBox.remove(), 300);
  }, 3000);
}

    });
  });
});