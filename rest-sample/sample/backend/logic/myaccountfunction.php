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


?>
