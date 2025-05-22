<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


$con = new mysqli('localhost', 'root', '', 'sakura_shine');

if ($con->connect_error) {
    // Stoppe die Ausführung und zeige die Fehlermeldung an
    die("Fehler bei der Verbindung zur Datenbank: " . $con->connect_error);
}

if (!isset($_SESSION['user']) && isset($_COOKIE['login_token'])) {
    $token = $_COOKIE['login_token'];

    $stmt = $con->prepare("SELECT * FROM users WHERE login_token = ?");
    if (!$stmt) {
        die("Prepare fehlgeschlagen: " . $con->error);
    }

    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user) {
        $_SESSION['user'] = $user;
    }
}
?>
