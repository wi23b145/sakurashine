document.addEventListener('DOMContentLoaded', () => {
  ladeProdukte();
});

async function ladeProdukte() {
  try {
    const response = await fetch('../../backend/api/products.php'); // <-- Hier der API-Endpunkt
    const produkte = await response.json();

    const container = document.getElementById('produktListe');
    container.innerHTML = produkte.map(p => `
      <div>
        <h3>${p.name}</h3>
        <p>${p.beschreibung}</p>
        <p>Preis: €${p.preis.toFixed(2)}</p>
      </div>
    `).join('');
  } catch (err) {
    console.error('Fehler beim Laden der Produkte:', err);
  }
}
