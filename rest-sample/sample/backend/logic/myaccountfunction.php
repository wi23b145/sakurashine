<?php

function safe($key) {
    global $user;
    return htmlspecialchars($user[$key] ?? '');
}

function maskiere($text) {
    if (!$text || strlen($text) < 2) return '*';
    return substr($text, 0, 1) . str_repeat('*', max(1, strlen($text) - 1));
}
?>
