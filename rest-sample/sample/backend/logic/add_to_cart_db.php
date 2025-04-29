<?php
session_start();
require_once('../config/dbaccess.php');

if (!isset($_SESSION['id'])) {
    header('Location: ../sites/login.html');
    exit;
}

$user_id = $_SESSION['id'];
$produkt_id = $_POST['produkt_id'];
$menge = $_POST['menge'];

// Prüfen, ob Produkt schon im Warenkorb ist
$sql = "SELECT * FROM Warenkorb WHERE user_id = ? AND produkt_id = ?";
$stmt = $con->prepare($sql);
$stmt->bind_param("ii", $user_id, $produkt_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Menge erhöhen
    $sql = "UPDATE Warenkorb SET menge = menge + ? WHERE user_id = ? AND produkt_id = ?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("iii", $menge, $user_id, $produkt_id);
} else {
    // Neu einfügen
    $sql = "INSERT INTO Warenkorb (user_id, produkt_id, menge, erstellt_am) VALUES (?, ?, ?, NOW())";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("iii", $user_id, $produkt_id, $menge);
}

$stmt->execute();
header('Location: ../sites/warenkorb.php');
exit;
?>
