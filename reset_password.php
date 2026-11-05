<?php
    session_start();
    include "scripts/db_conn.php";

    $current_datetime = new DateTime();
    if (isset($_SESSION['pw_reset_user_id']) && $_SESSION['pw_reset_expiration_date'] && $current_datetime < $_SESSION['pw_reset_expiration_date']) {
        if (isset($_POST['password']) && isset($_SESSION['retyped-password'])) {
            $password = trim($_POST['password']);
            $retyped_password = trim($_POST['retyped-password']);
            if ($password === $retyped_password) {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $user_id = trim($_POST['pw_reset_user_id']);
                $stmt = $conn->prepare("UPDATE users SET user_password = ? WHERE user_id = ?");
                $stmt->bind_param("si", $hashed_password, $user_id);
                if ($stmt->execute()) {
                    session_unset();
                    session_destroy();
                    echo("PASSWORD RESET SUCCESSFUL!");
                    #REDIRECT
                }
                else {
                    echo "Failed to reset password";
                }
            }
            else {
                echo "PASSWORDS DO NOT MATCH";
            }
            
        }
        else {
            echo "POST NOT FOUND";
        }
    }
    else {
        echo "Session Expired or Not Found";
        #REDIRECT
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ESM Password Reset</title>
    <link rel="stylesheet" href="templates/template1.css">
</head>
<body>
    <div class="nav-bar-container">
        <h1>Employee Management and Scheduling</h1>
        <!--<div class="nav-logo"></div>
        <div class="nav-header">
            <h1>Template1</h1>
        </div>
        <div class="nav-buttons"></div>-->
    </div>
    <div class="content-container">
        <div class="form-container">
            <div class="form-container-top">
                <h2>Reset your password</h2>
            </div>
            <div class="form-container-content">

                <form id="loginForm" method="POST">
                    <label for="password">New Password</label><br>
                    <input id="password" name="password" type="text" maxlength="255" required>
                    <label for="retyped-password">Re-type New Password</label><br>
                    <input id="retyped-password" name="retyped-password" type="text" maxlength="255" required>
                </form>
                

            </div>
            <div class="form-container-bottom">
                <button type="submit" form="loginForm">Submit</button>
            </div>
        </div>
    </div>
    <div class="footer-container"></div>
</body>
</html>