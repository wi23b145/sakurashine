// frontend/js/checkout.js
document.addEventListener('DOMContentLoaded', () => {
  const form       = document.getElementById('checkout-form');
  const tbody      = document.querySelector('#checkout-tabelle tbody');
  const sumElem    = document.getElementById('checkout-summe');
  const warenkorb  = JSON.parse(localStorage.getItem('warenkorb') || '[]');

  // 1) Warenkorb-Tabelle befüllen & Ursprungs-Summe berechnen
  const sumOriginal = warenkorb.reduce((acc, p) => acc + p.preis * p.menge, 0);
  tbody.innerHTML = '';
  warenkorb.forEach(p => {
    const gesamt = p.preis * p.menge;
    const tr = document.createElement('tr');
    tr.innerHTML = `
      <td>${p.name}</td>
      <td>${p.menge}</td>
      <td>€${p.preis.toFixed(2)}</td>
      <td>€${gesamt.toFixed(2)}</td>
    `;
    tbody.appendChild(tr);
  });
  sumElem.textContent = `Gesamtsumme: €${sumOriginal.toFixed(2)}`;

  // 2) Formular abschicken abfangen
  form.addEventListener('submit', async e => {
  e.preventDefault();
  let sumFinal = sumOriginal;
  const code = form.gutschein.value.trim().toUpperCase();

  if (code !== '') {
    try {
      const resp = await fetch(`../../backend/logic/voucherHandler.php?code=${encodeURIComponent(code)}`);
      const voucher = await resp.json();
      if (!resp.ok) throw new Error(voucher.error || `HTTP ${resp.status}`);

      if (voucher.typ === 'percent') {
        sumFinal = sumOriginal * (1 - voucher.rabatt_prozent / 100);
      } else {
        sumFinal = sumOriginal - voucher.geldwert;
      }
      if (sumFinal < 0) sumFinal = 0;
    } catch (err) {
      alert('Fehler beim Einlösen des Gutscheins:\n' + err.message);
      return;
    }
  }

  // Bestellung an Backend senden
  const bestellung = {
    name: form.name.value.trim(),
    address: form.adress.value.trim(),
    plz: form.plz.value.trim(),
    ort: form.ort.value.trim(),
    zahlungsmethode: form.zahlungsmethode.value,
    gutschein: code,
    warenkorb: warenkorb
  };

  try {
    const response = await fetch('../../backend/logic/submit_order.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(bestellung),
    });

    const result = await response.json();

    if (!response.ok) {
      throw new Error(result.message || 'Bestellung fehlgeschlagen');
    }

    alert(`Bestellung erfolgreich! Bestell-ID: ${result.bestellung_id}`);

    localStorage.removeItem('warenkorb');
    window.location.href = '../index.php';

  } catch (error) {
    alert('Fehler bei der Bestellung:\n' + error.message);
  }
});
});
