<?php
    session_start();
    require_once ('../config/dbaccess.php');


    unset($_SESSION);
    session_destroy();
    header("Location: ../../frontend/index.html");

    exit();
?>