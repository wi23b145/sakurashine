document.addEventListener("DOMContentLoaded", function() {
    fetch("../../backend/logic/userHandler.php?action=info")
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                window.location.href = "login.html"; // Weiterleitung falls nicht eingeloggt
                return;
            }
            document.getElementById("anrede").innerText = `Anrede: ${data.anrede}`;
            document.getElementById("name").innerText = `Name: ${data.vorname} ${data.nachname}`;
            document.getElementById("adresse").innerText = `Adresse: ${data.adresse}, ${data.plz} ${data.ort}`;
            document.getElementById("email").innerText = `E-Mail: ${data.email}`;
            document.getElementById("benutzername").innerText = `Benutzername: ${data.benutzername}`;
            document.getElementById("zahlung").innerText = `Zahlungsinformationen: ${data.zahlungsinformationen}`;
        })
        .catch(error => {
            console.error('Fehler beim Laden der Kontodaten:', error);
        });
});
