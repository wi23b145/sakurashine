function bearbeiten(feld) {
  const displayDiv = document.getElementById(feld + "_display");
  const inputField = document.getElementById(feld);
  if (displayDiv && inputField) {
    displayDiv.style.display = "none";
    inputField.classList.remove("d-none");
    inputField.disabled = false; // Wichtig
  }
}

// Wenn Benutzer in ein Eingabefeld tippt, markiere es als "bearbeitet"
document.querySelectorAll("input").forEach(input => {
  input.addEventListener("input", () => {
    input.classList.add("was-edited");
  });
});

// Sicherheit: Eingabefelder, die d-none sind und nicht bearbeitet wurden, beim Absenden deaktivieren
document.querySelector("form").addEventListener("submit", function () {
  document.querySelectorAll("input.d-none").forEach(i => {
    if (!i.classList.contains("was-edited")) {
      i.disabled = true;
    }
  });
});
