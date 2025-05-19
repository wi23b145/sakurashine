<?php 
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['ist_admin'] != 1) {
    echo json_encode(['error' => 'Kein Zugriff']);
    exit;
}

$pdo = Db::connect();
$action = $_GET['action'] ?? '';

switch ($action) {
    case 'getKunden':
        $stmt = $pdo->query("SELECT id, vorname, nachname, email, ist_aktiv AS aktiv FROM users WHERE ist_admin = 0");
        echo json_encode($stmt->fetchAll());
        break;
    
    case 'checkAdmin':
        if (isset($_SESSION['user']) && $_SESSION['user']['ist_admin'] == 1) {
            echo json_encode(['ist_admin' => 1]);
        } else {
            echo json_encode(['ist_admin' => 0]);
        }
        break;

    case 'deaktivieren':
        $id = $_GET['id'];
        $stmt = $pdo->prepare("UPDATE users SET ist_aktiv = 0 WHERE id = ?");
        $stmt->execute([$id]);
        echo json_encode(['success' => true]);
        break;

    case 'getBestellungen':
        $id = $_GET['id'];
        $stmt = $pdo->prepare("SELECT * FROM Bestellungen WHERE user_id = ?");
        $stmt->execute([$id]);
        $bestellungen = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($bestellungen as &$b) {
            $stmt2 = $pdo->prepare("SELECT bp.produkt_id AS id, p.name, bp.menge 
                                    FROM Bestellpositionen bp 
                                    JOIN produkte p ON bp.produkt_id = p.id 
                                    WHERE bp.bestellung_id = ?");
            $stmt2->execute([$b['id']]);
            $b['produkte'] = $stmt2->fetchAll(PDO::FETCH_ASSOC);
        }

        echo json_encode($bestellungen);
        break;

    case 'entferneProdukt':
        $bid = $_GET['bestellung_id'];
        $pid = $_GET['produkt_id'];
        $stmt = $pdo->prepare("DELETE FROM Bestellpositionen WHERE bestellung_id = ? AND produkt_id = ?");
        $stmt->execute([$bid, $pid]);
        echo json_encode(['success' => true]);
        break;

    default:
        echo json_encode(['error' => 'Ungültige Aktion']);
}
?>