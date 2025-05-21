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
    const code   = form.gutschein.value.trim().toUpperCase();

    if (code !== '') {
      try {
        const resp    = await fetch(
          `../../backend/logic/voucherHandler.php?code=${encodeURIComponent(code)}`
        );
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

    // 3) Nur zum Test: Alert mit Beträgen
    alert(
      `Bestellung bestätigt!\n` +
      `Ursprungs-Summe: €${sumOriginal.toFixed(2)}\n` +
      `Endbetrag:       €${sumFinal.toFixed(2)}` +
      (code ? `\nGutschein: ${code}` : '')
    );

    // 4) Aufräumen / Weiterleitung
    localStorage.removeItem('warenkorb');
    window.location.href = '../index.php';
  });
});
