// frontend/js/checkout.js
document.addEventListener('DOMContentLoaded', () => {
  const form = document.getElementById('checkout-form');

  form.addEventListener('submit', async e => {
    e.preventDefault();

    // 1) Form-Felder
    const name    = form.name.value.trim();
    const address = form.adress.value.trim();
    const plz     = form.plz.value.trim();
    const ort     = form.ort.value.trim();
    const zahlung = form.zahlungsmethode.value;
    const code    = form.gutschein.value.trim().toUpperCase(); // Gutscheincode

    // 2) Warenkorb aus localStorage
    const warenkorb = JSON.parse(localStorage.getItem('warenkorb') || '[]');
    if (warenkorb.length === 0) {
      alert('Ihr Warenkorb ist leer.');
      return;
    }

    // 3) Ursprungs-Summe berechnen
    const sumOriginal = warenkorb.reduce(
      (acc, p) => acc + p.preis * p.menge,
      0
    );
    // 4) Endbetrag initial auf Ursprungs-Summe setzen
    let sumFinal = sumOriginal;

   // … oben unverändert …

// 5) Gutschein einlösen (falls eingegeben)
if (code !== '') {
  try {
    // hier den Pfad korrigiert:
    const resp    = await fetch(
      `../../backend/logic/voucherHandler.php?code=${encodeURIComponent(code)}`
    );
    const voucher = await resp.json();

    if (!resp.ok) {
      throw new Error(voucher.error || `HTTP ${resp.status}`);
    }

    if (voucher.typ === 'percent') {
      sumFinal = sumOriginal * (1 - voucher.rabatt_prozent / 100);
    } else { // 'fixed'
      sumFinal = sumOriginal - voucher.geldwert;
    }
    if (sumFinal < 0) sumFinal = 0;

  } catch (err) {
    alert('Fehler beim Einlösen des Gutscheins:\n' + err.message);
    return;
  }
}


    // 6) Nur zum Test: Alert mit beiden Beträgen
    alert(
      `Bestellung bestätigt!\n` +
      `Ursprungs-Summe: €${sumOriginal.toFixed(2)}\n` +
      `Endbetrag:       €${sumFinal.toFixed(2)}`
    );

    // 7) Aufräumen / Redirect
    localStorage.removeItem('warenkorb');
    window.location.href = 'index.php';
  });
});
