// File: frontend/js/admin_customer.js

document.addEventListener('DOMContentLoaded', () => {
  // Passe den Dateinamen exakt an dein PHP-Skript an:
  const API_BASE = '../../backend/logic/adminHandler.php';

  // 1) Alle Kunden laden
  function loadCustomers() {
    fetch(`${API_BASE}?action=getKunden`)
      .then(response => {
        if (!response.ok) throw new Error(`HTTP ${response.status}`);
        return response.json();
      })
      .then(kunden => {
        const tbody = document.querySelector('#kunden-tabelle tbody');
        tbody.innerHTML = '';
        kunden.forEach(k => {
          const tr = document.createElement('tr');
          tr.dataset.id = k.id;
          tr.innerHTML = `
            <td>${k.id}</td>
            <td>${k.vorname}</td>
            <td>${k.nachname}</td>
            <td>${k.email}</td>
            <td>${k.aktiv ? 'Aktiv' : 'Gesperrt'}</td>
            <td>
              <button class="btn btn-sm btn-info show-orders">Bestellungen</button>
              <button class="btn btn-sm btn-warning toggle-active">
                ${k.aktiv ? 'Sperren' : 'Entsperren'}
              </button>
            </td>
          `;
          tbody.appendChild(tr);
        });
        attachEventHandlers();
      })
      .catch(err => console.error('Fehler beim Laden der Kunden:', err));
  }

  // 2) Event-Handler für Buttons
  function attachEventHandlers() {
    document.querySelectorAll('.show-orders').forEach(btn => {
      btn.addEventListener('click', () => {
        const userId = btn.closest('tr').dataset.id;
        loadOrders(userId);
      });
    });

    document.querySelectorAll('.toggle-active').forEach(btn => {
      btn.addEventListener('click', () => {
        const userId = btn.closest('tr').dataset.id;
        fetch(`${API_BASE}?action=deaktivieren&id=${userId}`)
          .then(response => {
            if (!response.ok) throw new Error(`HTTP ${response.status}`);
            return response.json();
          })
          .then(() => {
            loadCustomers();
            const ordersTbody = document.querySelector('#bestellungen-tabelle tbody');
            ordersTbody.innerHTML = '<tr><td colspan="4">Wähle oben einen Kunden aus</td></tr>';
          })
          .catch(err => console.error('Fehler beim (De-)Aktivieren:', err));
      });
    });
  }

  // 3) Bestellungen eines Kunden laden
  function loadOrders(userId) {
    fetch(`${API_BASE}?action=getBestellungen&id=${userId}`)
      .then(response => {
        if (!response.ok) throw new Error(`HTTP ${response.status}`);
        return response.json();
      })
      .then(orders => {
        const tbody = document.querySelector('#bestellungen-tabelle tbody');
        tbody.innerHTML = '';
        if (!orders.length) {
          tbody.innerHTML = '<tr><td colspan="4">Keine Bestellungen</td></tr>';
          return;
        }
        orders.forEach(o => {
          const products = o.produkte.map(p => `${p.name}×${p.menge}`).join(', ');
          const tr = document.createElement('tr');
          tr.innerHTML = `
            <td>${o.id}</td>
            <td>${o.erstellt_am}</td>
            <td>€${parseFloat(o.gesamtpreis).toFixed(2)}</td>
            <td>${products}</td>
          `;
          tbody.appendChild(tr);
        });
      })
      .catch(err => console.error('Fehler beim Laden der Bestellungen:', err));
  }

  // Starte alles
  loadCustomers();
});
