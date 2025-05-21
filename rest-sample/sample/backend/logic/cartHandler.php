<?php
header('Content-Type: application/json');

// Wenn Session-Warenkorb nicht gesetzt, versuchen aus DB zu laden (falls eingeloggt)
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];

    if (isset($_SESSION['user'])) {
        require_once '../config/dbaccess.php';
        $pdo = Db::connect();

        $stmt = $pdo->prepare("SELECT produkt_id, menge, p.name, p.preis 
                               FROM Warenkorb w 
                               JOIN Produkte p ON w.produkt_id = p.id 
                               WHERE w.user_id = ?");
        $stmt->execute([$_SESSION['user']['id']]);

        while ($row = $stmt->fetch()) {
            $_SESSION['cart'][] = [
                'id' => $row['produkt_id'],
                'name' => $row['name'],
                'price' => $row['preis'],
                'quantity' => $row['menge']
            ];
        }
    }
}

$action = $_REQUEST['action'] ?? '';

switch ($action) {
    case 'get':
        echo json_encode(['cart' => $_SESSION['cart']]);
        break;

    case 'add':
        $id = (int)$_POST['id'];
        $name = $_POST['name'];
        $price = (float)$_POST['price'];
        $quantity = (int)$_POST['quantity'];

        $found = false;
        foreach ($_SESSION['cart'] as &$item) {
            if ($item['id'] === $id) {
                $item['quantity'] += $quantity;
                $found = true;
                break;
            }
        }
        if (!$found) {
            $_SESSION['cart'][] = [
                'id' => $id,
                'name' => $name,
                'price' => $price,
                'quantity' => $quantity
            ];
        }

        if (isset($_SESSION['user'])) {
            require_once '../config/dbaccess.php';
            $pdo = Db::connect();
            $stmt = $pdo->prepare("INSERT INTO Warenkorb (user_id, produkt_id, menge)
                                   VALUES (?, ?, ?) 
                                   ON DUPLICATE KEY UPDATE menge = menge + VALUES(menge)");
            $stmt->execute([$_SESSION['user']['id'], $id, $quantity]);
        }

        echo json_encode(['success' => true]);
        break;

    case 'update':
        $id = (int)$_POST['id'];
        $quantity = (int)$_POST['quantity'];

        foreach ($_SESSION['cart'] as &$item) {
            if ($item['id'] === $id) {
                $item['quantity'] = $quantity;
                break;
            }
        }

        if (isset($_SESSION['user'])) {
            require_once '../config/dbaccess.php';
            $pdo = Db::connect();
            $stmt = $pdo->prepare("UPDATE Warenkorb SET menge = ? WHERE user_id = ? AND produkt_id = ?");
            $stmt->execute([$quantity, $_SESSION['user']['id'], $id]);
        }

        echo json_encode(['success' => true]);
        break;

    case 'remove':
        $id = (int)$_POST['id'];
        $_SESSION['cart'] = array_filter($_SESSION['cart'], fn($item) => $item['id'] !== $id);

        if (isset($_SESSION['user'])) {
            require_once '../config/dbaccess.php';
            $pdo = Db::connect();
            $stmt = $pdo->prepare("DELETE FROM Warenkorb WHERE user_id = ? AND produkt_id = ?");
            $stmt->execute([$_SESSION['user']['id'], $id]);
        }

        echo json_encode(['success' => true]);
        break;

    case 'checkout':
        if (!isset($_SESSION['user'])) {
            echo json_encode(['success' => false, 'message' => 'Bitte zuerst einloggen!']);
            exit;
        }

        require_once '../config/dbaccess.php';
        $pdo = Db::connect();

        try {
            $pdo->beginTransaction();

            $userId = $_SESSION['user']['id'];
            $total = 0;
            foreach ($_SESSION['cart'] as $item) {
                $total += $item['price'] * $item['quantity'];
            }

            // Bestellung speichern
            $stmt = $pdo->prepare("INSERT INTO Bestellungen (user_id, bestellstatus, gesamtpreis, erstellt_am)
                                   VALUES (?, 'offen', ?, NOW())");
            $stmt->execute([$userId, $total]);
            $bestellungId = $pdo->lastInsertId();

            // Positionen speichern
            $stmt = $pdo->prepare("INSERT INTO Bestellpositionen (bestellung_id, produkt_id, menge, einzelpreis)
                                   VALUES (?, ?, ?, ?)");
            foreach ($_SESSION['cart'] as $item) {
                $stmt->execute([$bestellungId, $item['id'], $item['quantity'], $item['price']]);
            }

            // Warenkorb leeren
            $stmt = $pdo->prepare("DELETE FROM Warenkorb WHERE user_id = ?");
            $stmt->execute([$userId]);

            $pdo->commit();
            $_SESSION['cart'] = [];

            echo json_encode(['success' => true, 'message' => 'Bestellung erfolgreich gespeichert!']);
        } catch (Exception $e) {
            $pdo->rollBack();
            echo json_encode(['success' => false, 'message' => 'Fehler: ' . $e->getMessage()]);
        }
        break;

    default:
        echo json_encode(['error' => 'Ungültige Aktion']);
}
