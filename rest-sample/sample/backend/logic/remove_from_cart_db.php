<?php
session_start();
require_once('../config/dbaccess.php');

if (!isset($_SESSION['id'])) {
    header('Location: ../../../../frontend/sites/login.php');
    exit;
}

$user_id = $_SESSION['id'];
$produkt_id = $_POST['produkt_id'];

$sql = "DELETE FROM Warenkorb WHERE user_id = ? AND produkt_id = ?";
$stmt = $con->prepare($sql);
$stmt->bind_param("ii", $user_id, $produkt_id);
$stmt->execute();

header('Location: ../../../../frontend/sites/cart.php');
exit;
?>
