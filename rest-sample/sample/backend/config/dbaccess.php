<?php
class Db {
    public static function connect() {
        try {
            $pdo = new PDO('mysql:host=localhost;dbname=sakura_shine;charset=utf8', 'root', '');
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $pdo;
        } catch (PDOException $e) {
            // JSON-Antwort bei Fehler
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Datenbankverbindung fehlgeschlagen: ' . $e->getMessage()]);
            exit;
        }
    }
}
