<?php

function safe($key) {
    global $user;
    return htmlspecialchars($user[$key] ?? '');
}

function maskiere($text) {
  $len = strlen($text);
  if ($len <= 2) return str_repeat('*', $len);
  return $text[0] . str_repeat('*', $len - 2) . $text[$len - 1];
}

function isMasked($wert) {
    return str_contains($wert, '*');
}

function holeLetzteBestellungId(mysqli $con, int $user_id): ?int {
    $sql = "SELECT id FROM bestellungen WHERE user_id = ? ORDER BY erstellt_am DESC LIMIT 1";
    $stmt = $con->prepare($sql);
    if (!$stmt) {
        return null; // Fehler bei Vorbereitung
    }
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();

    return $row ? (int)$row['id'] : null;
}

?>
