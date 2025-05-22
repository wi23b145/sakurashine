async function ladeBestellungen() {
  try {
    const resp = await fetch('../../backend/api/myOrders.php');
    if (!resp.ok) throw new Error('Nicht eingeloggt oder Fehler: ' + resp.status);
    const orders = await resp.json();
    
    const container = document.getElementById('bestellungenContainer');
    container.innerHTML = '';
    
    orders.forEach(order => {
      container.innerHTML += `
        <div class="order">
          <p>Bestellung #${order.id} - Datum: ${order.erstellt_am}</p>
          <p>Status: ${order.bestellstatus} - Gesamtpreis: €${Number(order.gesamtpreis).toFixed(2)}</p>
          <p>Lieferadresse: ${order.name}, ${order.adresse}, ${order.plz} ${order.ort}</p>
          <button onclick="zeigeDetails(${order.id})">Details anzeigen</button>
          <button onclick="druckeRechnung(${order.id})">Rechnung drucken</button>
        </div>
      `;
    });
  } catch (err) {
    console.error('Fehler beim Laden der Bestellungen:', err);
  }
}

function zeigeDetails(orderId) {
  // Hier implementierst du das Laden und Anzeigen der Bestelldetails z.B. in einem Modal
  alert('Details für Bestellung ' + orderId + ' anzeigen');
}

function druckeRechnung(orderId) {
  // Öffnet ein neues Fenster oder Tab mit der Rechnung
  window.open(`rechnung.php?order_id=${orderId}`, '_blank');
}

document.addEventListener('DOMContentLoaded', ladeBestellungen);
