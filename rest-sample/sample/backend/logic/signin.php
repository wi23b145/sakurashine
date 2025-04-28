<?php
    session_start();
    require_once ('../config/dbaccess.php'); 

    if($_SERVER["REQUEST_METHOD"] == "POST"){
        $username = $_POST['username'];
        $passwort = $_POST['passwort'];
        

        $sql = "SELECT * FROM users WHERE benutzername = ?";
        $stmt = $con->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if($result->num_rows == 1){
            $user = $result->fetch_assoc();
            $hashed_password = $user['passwort'];

            /*if($user['status_id'] == 2){
                $_SESSION['error'] = "Ihr Konto ist inaktiv. Bitte kontaktieren Sie den Administrator.";
                header("Location:../login.php");
                exit;
            }*/

            if(password_verify($passwort, $hashed_password)){
                $_SESSION['benutzername'] = $user["benutzername"];
                $_SESSION['id'] = $user["id"];
                $_SESSION['vorname'] = $user["vorname"];
                $_SESSION['nachname'] = $user["nachname"];
                $_SESSION['email'] = $user["email"];
                $_SESSION['anrede'] = $user["anrede"];
                
                $_SESSION['success'] = "WILLKOMMEN, " . htmlspecialchars($username) ."!";
                header("Location:../../frontend/index.html");
               
            exit;
                
            }else{
                $_SESSION['error'] = "Das Passwort ist falsch. Bitte versuchen Sie es erneut.";
                //header("Location:../login.php");
            }
        }else{
            $_SESSION['error'] = "Kein Benutzer mit diesem Usernamen gefunden!";
            //header("Location:../login.php");
        }

        $con->close();
    }
?>