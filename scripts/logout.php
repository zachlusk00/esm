<?php
#https://www.w3schools.com/php/php_sessions.asp
    session_start();
    session_unset();
    session_destroy();
    header("Location: ../login.php");
    exit;
?>