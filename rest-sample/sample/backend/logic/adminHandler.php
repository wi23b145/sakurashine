<?php 
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['ist_admin'] != 1) {
    echo json_encode(['error' => 'Kein Zugriff']);
    exit;
}
?>