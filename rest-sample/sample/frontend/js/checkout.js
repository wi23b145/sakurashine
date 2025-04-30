document.addEventListener('DOMContentLoaded', function () {
  const warenkorb = JSON.parse(localStorage.getItem('warenkorb') || '[]');
  const tabelle = document.querySelector('#checkout-tabelle tbody');
  const summeAnzeige = document.getElementById('checkout-summe');

  if (!warenkorb.length) {
    tabelle.innerHTML = '<tr><td colspan="4">Ihr Warenkorb ist leer.</td></tr>';
    summeAnzeige.textContent = '';
    return;
  }

  let gesamtsumme = 0;
  tabelle.innerHTML = ''; // Tabelle leeren

  warenkorb.forEach(produkt => {
    const gesamt = produkt.preis * produkt.menge;
    gesamtsumme += gesamt;

    const tr = document.createElement('tr');
    tr.innerHTML = `
      <td>${produkt.name}</td>
      <td>${produkt.menge}</td>
      <td>€${produkt.preis.toFixed(2)}</td>
      <td>€${gesamt.toFixed(2)}</td>
    `;
    tabelle.appendChild(tr);
  });

  summeAnzeige.textContent = `Gesamtsumme: €${gesamtsumme.toFixed(2)}`;
});
