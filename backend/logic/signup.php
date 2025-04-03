<?php
require_once "dbaccess.php";
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $sal = $con->real_escape_string($_POST['sal']);
    $firstname = $con->real_escape_string($_POST['firstname']);
    $lastname = $con->real_escape_string($_POST['lastname']);
    $email = $con->real_escape_string($_POST['email']);
    $username = $con->real_escape_string($_POST['username']);
    $passwort = $con->real_escape_string($_POST['passwort']);
    $wpassword = $con->real_escape_string($_POST['wpassword']);

    $user = 2; // Standard-Rolle
    $status = 1; // Standard-Status

    $_SESSION['role_id'] = $user;
    $sql_role = "SELECT role_name FROM role WHERE id = ?";
    $stmt_role = $con->prepare($sql_role);
    $stmt_role->bind_param("i", $_SESSION['role_id']);
    $stmt_role->execute();
    $stmt_role->bind_result($role_name);
    $stmt_role->fetch();
    $_SESSION['role'] = $role_name;
    $stmt_role->close();

    $_SESSION['status_id'] = $status;
    $sql_status = "SELECT user_status FROM status WHERE id = ?";
    $stmt_status = $con->prepare($sql_status);
    $stmt_status->bind_param("i", $_SESSION['status_id']);
    $stmt_status->execute();
    $stmt_status->bind_result($user_status);
    $stmt_status->fetch();
    $_SESSION['status'] = $user_status;
    $stmt_status->close();

    if ($passwort !== $wpassword) {
        $_SESSION['error'] = "Passwörter stimmen nicht überein.";
        header("Location: ../register.php");
        exit;
    }

    // Überprüfen, ob der Benutzername oder die E-Mail bereits existiert
    $stmt = $con->prepare("SELECT id FROM user WHERE username = ? OR email = ?");
    $stmt->bind_param("ss", $username, $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $_SESSION['error'] = "Benutzername oder E-Mail-Adresse existiert bereits.";
        header("Location: ../register.php");
        exit;
    }

    $hashed_password = password_hash($passwort, PASSWORD_BCRYPT);

    $sql = "INSERT INTO user (sal, firstname, lastname, email, username, passwort, role_id, status_id) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

    if ($stmt = $con->prepare($sql)) {
        $stmt->bind_param("ssssssii", $sal, $firstname, $lastname, $email, $username, $hashed_password, $user, $status);
        $stmt->execute();

        $_SESSION['success'] = "Das Konto wurde erfolgreich erstellt! Melden Sie sich doch gleich hier an.";
        header("Location: ../login.php");
    } else {
        echo "Error: " . $sql . "<br>" . $con->error;
    }

    $stmt->close();
}
$con->close();
?>