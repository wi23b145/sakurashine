document.addEventListener("DOMContentLoaded", function () {
    fetch("../backend/logic/adminHandler.php?action=getKunden")
      .then(res => res.json())
      .then(kunden => {
        const tbody = document.querySelector("#kunden-tabelle tbody");
        kunden.forEach(kunde => {
          const row = document.createElement("tr");
          row.innerHTML = `
            <td>${kunde.id}</td>
            <td>${kunde.vorname}</td>
            <td>${kunde.nachname}</td>
            <td>${kunde.email}</td>
            <td>${kunde.aktiv ? "Aktiv" : "Deaktiviert"}</td>
            <td>
              <button class="btn btn-danger btn-sm" onclick="deaktiviereKunde(${kunde.id})">Deaktivieren</button>
              <button class="btn btn-info btn-sm" onclick="ladeBestellungen(${kunde.id})">Bestellungen</button>
            </td>
          `;
          tbody.appendChild(row);
        });
      });
  });
  
  function deaktiviereKunde(kundenId) {
    fetch(`../backend/logic/adminHandler.php?action=deaktivieren&id=${kundenId}`)
      .then(() => location.reload());
  }
  
  function ladeBestellungen(kundenId) {
    fetch(`../backend/logic/adminHandler.php?action=getBestellungen&id=${kundenId}`)
      .then(res => res.json())
      .then(bestellungen => {
        const tbody = document.querySelector("#bestellungen-tabelle tbody");
        tbody.innerHTML = "";
        bestellungen.forEach(b => {
          const row = document.createElement("tr");
          row.innerHTML = `
            <td>${b.id}</td>
            <td>${b.erstellt_am}</td>
            <td>${b.gesamtpreis}</td>
            <td>
              <ul>${b.produkte.map(p => `
                <li>
                  ${p.name} (${p.menge}) 
                  <button class="btn btn-sm btn-outline-danger" onclick="entferneProdukt(${b.id}, ${p.id})">entfernen</button>
                </li>
              `).join('')}</ul>
            </td>
          `;
          tbody.appendChild(row);
        });
      });
  }
  
  function entferneProdukt(bestellungId, produktId) {
    fetch(`../backend/logic/adminHandler.php?action=entferneProdukt&bestellung_id=${bestellungId}&produkt_id=${produktId}`)
      .then(() => alert("Produkt entfernt. Du kannst die Seite neu laden."));
  }
  