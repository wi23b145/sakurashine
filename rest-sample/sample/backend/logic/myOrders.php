<?php
session_start();
require_once "../../backend/config/dbaccess.php";

if (!isset($_SESSION['user'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Nicht eingeloggt']);
    exit;
}

$userId = $_SESSION['user']['id'];

// Bestellungen des Users abfragen (aufsteigend nach Datum)
$stmt = $con->prepare("
  SELECT id, erstellt_am, bestellstatus, gesamtpreis, name, adresse, plz, ort 
  FROM bestellungen 
  WHERE user_id = ? 
  ORDER BY erstellt_am ASC
");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$orders = $result->fetch_all(MYSQLI_ASSOC);
$_SESSION['orders'] = $orders;

$stmt->close();

header('Location: ../../frontend/sites/myAccount.php');
echo json_encode($orders);
