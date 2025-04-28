<?php
require_once ('../config/dbaccess.php'); // Sicherstellen, dass dbaccess.php die DB-Verbindung enthält
session_start();

// Überprüfen, ob die Anfrage eine POST-Anfrage ist
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Eingabewerte sicher machen, um SQL-Injektion zu verhindern
    $anrede = $con->real_escape_string($_POST['anrede']);
    $firstname = $con->real_escape_string($_POST['firstname']);
    $lastname = $con->real_escape_string($_POST['lastname']);
    $email = $con->real_escape_string($_POST['email']);
    $username = $con->real_escape_string($_POST['username']);
    $adresse = $con->real_escape_string($_POST['adresse']);
    $plz = $con->real_escape_string($_POST['plz']);
    $ort = $con->real_escape_string($_POST['ort']);
    $passwort = $con->real_escape_string($_POST['passwort']);
    $wpassword = $con->real_escape_string($_POST['wpassword']);

    // Überprüfen, ob die Passwörter übereinstimmen
    if ($passwort !== $wpassword) {
        $_SESSION['error'] = "Passwörter stimmen nicht überein.";
        header("Location: ../frontend/sites/signup.html");
        exit;
    }

    // Überprüfen, ob der Benutzername oder die E-Mail bereits existiert
    $stmt = $con->prepare("SELECT id FROM users WHERE benutzername = ? OR email = ?");
    $stmt->bind_param("ss", $username, $email); // 
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $_SESSION['error'] = "Benutzername oder E-Mail-Adresse existiert bereits.";
        header("Location: ../frontend/sites/signup.html");
        exit;
    }

    // Passwort hashen
    $hashed_password = password_hash($passwort, PASSWORD_BCRYPT);

    // SQL-Query zum Einfügen des neuen Nutzers
    $sql = "INSERT INTO users (anrede, vorname, nachname, email, benutzername, passwort, adresse, plz, ort) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

    // Ausführen der Abfrage
    if ($stmt = $con->prepare($sql)) {
        // Hier fehlt eine Variable wie 'user' und 'status'. Diese müssen korrekt zugewiesen werden.
        // Setze Standardwerte oder sorge für die korrekte Zuweisung
        $user = 2; // Beispiel: Standard-Rolle für den Benutzer
        $status = 1; // Beispiel: Standard-Status für den Benutzer
        $stmt->bind_param("sssssssss", $anrede, $firstname, $lastname, $email, $username, $hashed_password, $adresse, $plz, $ort);
        $stmt->execute();

        // Erfolgreiche Registrierung
        $_SESSION['success'] = "Das Konto wurde erfolgreich erstellt! Melden Sie sich doch gleich hier an.";
        header("Location: ../../frontend/sites/login.html");
        exit;
    } else {
        echo "Fehler bei der SQL-Abfrage: " . $con->error;
    }

    // Statement und Verbindung schließen
    $stmt->close();
}
$con->close();
?>
