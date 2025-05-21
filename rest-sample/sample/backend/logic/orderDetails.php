<?php
session_start();
require_once "../../backend/config/dbaccess.php";

if (!isset($_SESSION['user'])) {
    http_response_code(401);
    exit;
}

$orderId = intval($_GET['id']);
$userId = $_SESSION['user']['id'];

// Prüfen, ob Bestellung zum User gehört
$stmt = $con->prepare("SELECT * FROM bestellungen WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $orderId, $userId);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$order) {
    http_response_code(403);
    exit;
}

// Bestellpositionen laden
$stmt = $con->prepare("SELECT * FROM bestellpositionen WHERE bestellung_id = ?");
$stmt->bind_param("i", $orderId);
$stmt->execute();
$positions = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

header('Content-Type: application/json');
echo json_encode([
  'order' => $order,
  'positions' => $positions
]);
?>