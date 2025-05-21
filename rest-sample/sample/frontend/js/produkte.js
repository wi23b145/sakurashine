document.addEventListener('DOMContentLoaded', function () {
  // Debug: prüfen, ob das Script geladen wird
  console.log('produkte.js geladen');

  // Elemente référencen
  const suchfeld        = document.getElementById('suchfeld');
  const kategorieFilter = document.getElementById('kategorieFilter');
  const karten          = document.querySelectorAll('.product-card');

  // Debug: prüfen, ob wir die richtigen Targets haben
  console.log('Suchfeld gefunden?     ', !!suchfeld);
  console.log('Dropdown gefunden?     ', !!kategorieFilter);
  console.log('Anzahl Produkt-Karten:', karten.length);

  // ======= 1) Warenkorb-Logik =======
  const warenkorb = JSON.parse(localStorage.getItem('warenkorb') || '[]');
  document.querySelectorAll('.add-to-cart').forEach(button => {
    button.addEventListener('click', function () {
      const id    = this.dataset.id;
      const name  = this.dataset.name;
      const preis = parseFloat(this.dataset.preis);

      const bestehendes = warenkorb.find(item => item.id === id);
      if (bestehendes) {
        bestehendes.menge += 1;
      } else {
        warenkorb.push({ id, name, preis, menge: 1 });
      }
      localStorage.setItem('warenkorb', JSON.stringify(warenkorb));

      const badge = document.getElementById('cart-count');
      if (badge) {
        const gesamt = warenkorb.reduce((sum, p) => sum + p.menge, 0);
        badge.textContent = gesamt;
      }
      alert('Produkt wurde zum Warenkorb hinzugefügt!');
    });
  });

  // ======= 2) Continuous Search & Kategorie-Filter =======
  function filterProdukte() {
    const filter = (suchfeld?.value || '').trim().toLowerCase();
    const kat    = kategorieFilter?.value || 'alle';

    console.log('Filter ausgelöst – Text:', filter, 'Kat:', kat);

    karten.forEach(card => {
      const titel   = card.querySelector('.card-title').textContent.toLowerCase();
      const desc    = card.querySelector('.card-text').textContent.toLowerCase();
      const cardKat = card.dataset.kategorie;
      const passtText = titel.includes(filter) || desc.includes(filter);
      const passtKat  = (kat === 'alle' || cardKat === kat);

      console.log(`  Karte "${titel}" [${cardKat}] → Text?${passtText} Kat?${passtKat}`);
      card.style.display = (passtText && passtKat) ? '' : 'none';
    });
  }

  if (suchfeld)        suchfeld.addEventListener('input', filterProdukte);
  if (kategorieFilter) kategorieFilter.addEventListener('change', filterProdukte);

  // initial ausführen, damit beim Laden schon gefiltert wird
  filterProdukte();
});
