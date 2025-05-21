<?php
require_once '../config/dbaccess.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$con = mysqli_connect('localhost', 'root', '', 'sakura_shine');
if ($con->connect_error) {
    die("Verbindung fehlgeschlagen: " . $con->connect_error);
}

// Prüfen, ob Admin existiert
$check = $con->prepare("SELECT id FROM users WHERE benutzername = 'admin'");
$check->execute();
$result = $check->get_result();

if ($result->num_rows === 0) {
    // Admin erstellen
    $hashedPassword = password_hash("admin123", PASSWORD_DEFAULT);

    $stmt = $con->prepare("INSERT INTO users 
        (benutzername, passwort, vorname, nachname, email, anrede, ist_admin, ist_aktiv)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)");

    $stmt->bind_param("ssssssii", 
        $benutzername, $passwort, $vorname, $nachname, $email, $anrede, $ist_admin, $ist_aktiv
    );

    $benutzername = "admin";
    $passwort = $hashedPassword;
    $vorname = "Admin";
    $nachname = "System";
    $email = "admin@sakurashine.at";
    $anrede = "Herr";
    $ist_admin = 1;
    $ist_aktiv = 1;

    if ($stmt->execute()) {
        echo "✅ Admin wurde erfolgreich erstellt!";
    } else {
        echo "❌ Fehler beim Einfügen: " . $stmt->error;
    }

    $stmt->close();
} else {
    echo "ℹ️ Admin existiert bereits.";
}

$con->close();
