
document.querySelector("form").addEventListener("submit", function(e) {
  const pw = document.getElementById("password").value;
  const pw2 = document.getElementById("wpassword").value;

  if (pw !== pw2) {
    alert("Die Passwörter stimmen nicht überein.");
    e.preventDefault();
  }
});
