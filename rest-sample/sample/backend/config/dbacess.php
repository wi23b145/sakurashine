<?php

class Database {
    private $host = 'localhost';
    private $user = 'root';
    private $pass = '';
    private $dbname = 'csv_db7';
    private $charset = 'utf8mb4';

    private $pdo;
    private $error;

    public function __construct() {
        $dsn = "mysql:host=" . $this->host . ";dbname=" . $this->dbname . ";charset=" . $this->charset;

        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];

        try {
            $this->pdo = new PDO($dsn, $this->user, $this->pass, $options);
            echo "DB Verbindung erfolgreich!"; // Erfolgreiche Verbindungsmeldung
        } catch (PDOException $e) {
            $this->error = $e->getMessage();
            echo "Datenbankverbindung fehlgeschlagen: " . $this->error;
        }
    }

    public function query($sql, $params = []) {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    public function lastInsertId() {
        return $this->pdo->lastInsertId();
    }

    public function closeConnection() {
        $this->pdo = null;
    }
}

// Datenbankverbindung aufrufen
new Database();

?>