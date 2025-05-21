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

document.getElementById('bild').addEventListener('change', function(event) {
  const [file] = event.target.files;
  const preview = document.getElementById('bildVorschau');
  if (file) {
    preview.src = URL.createObjectURL(file);
    preview.classList.remove('d-none');
  } else {
    preview.classList.add('d-none');
  }
});

document.getElementById('btnNeuSpeichern').addEventListener('click', async (e) => {
  e.preventDefault();

  const formData = new FormData();
  formData.append('name', document.getElementById('neuName').value.trim());
  formData.append('beschreibung', document.getElementById('neuBeschreibung').value.trim());
  formData.append('bewertung', document.getElementById('neuBewertung').value);
  formData.append('preis', document.getElementById('neuPreis').value);
  const bildInput = document.getElementById('neuBild');
  if (bildInput.files.length > 0) {
    formData.append('bild', bildInput.files[0]);
  }

  try {
    const resp = await fetch('../../backend/api/products.php', {
      method: 'POST',
      body: formData
    });
    const result = await resp.json();
    if (result.success) {
      alert('Produkt erfolgreich angelegt!');
      // Produkte neu laden
      ladeProdukte();
      // Felder zurücksetzen
      document.getElementById('neuName').value = '';
      document.getElementById('neuBeschreibung').value = '';
      document.getElementById('neuBewertung').value = '';
      document.getElementById('neuPreis').value = '';
      bildInput.value = '';
    } else {
      alert('Fehler: ' + (result.error || 'Unbekannt'));
    }
  } catch (err) {
    alert('Fehler: ' + err.message);
  }
});

