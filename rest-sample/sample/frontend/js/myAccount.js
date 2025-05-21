function bearbeiten(feld) {
  document.getElementById(feld + '_display').style.display = 'none';
  document.getElementById(feld).classList.remove('d-none');
}