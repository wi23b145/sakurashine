<?php session_start();


$con = mysqli_connect('localhost', 'root', '', 'sakura_shine');

if ($con -> connect_error){
    $_SESSION['error'] = "Fehler bei der Verbindung zur Datenbank";
}

if (!isset($_SESSION['user']) && isset($_COOKIE['login_token'])) {
    $token = $_COOKIE['login_token'];

    $stmt = $con->prepare("SELECT * FROM users WHERE login_token = ?");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user) {
        $_SESSION['user'] = $user;
    }
}
?>

