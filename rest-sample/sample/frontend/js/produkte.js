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
      alert('Produkt wurde zum Warenkorb hinzugefügt!');
    });
  });
});