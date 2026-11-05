<?php
    #https://stackoverflow.com/questions/768431/how-do-i-make-a-redirect-in-php
    #https://www.geeksforgeeks.org/php/how-to-make-a-redirect-in-php/

    if (!isset($_SESSION["logged_in"]) || $_SESSION["logged_in"] !== true) {
            header("Location: ../login.php");
            exit;
        }

    if (isset($_SESSION["user_id"])) {
        #echo "FOUND SESSION USER ID";
        $user_id = $_SESSION["user_id"];
    }
    else {
        echo "NO USER ID SESSION FOUND";
    }

    if (isset($_SESSION["user_account_type"])) {
        #echo "FOUND USER ACCOUNT TYPE";
        $user_account_type = trim($_SESSION["user_account_type"]);
    }
    else {
        echo "NO USER ACCOUNT TYPE FOUND";
    }
?>
