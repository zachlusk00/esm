<?php
#https://stackoverflow.com/questions/11929471/how-do-i-use-two-submit-buttons-and-differentiate-between-which-one-was-used-to
#https://stackoverflow.com/questions/1853516/how-to-get-delay-of-30-sec-in-current-time-in-php
#https://stackoverflow.com/questions/51566143/retrieve-datetime-object-from-mysql-database-in-php
    session_start();
    include "scripts/auth.php";


    if (isset($_POST['code_btn'])) {
        echo "code btn pressed";
        if (isset($_POST['email'])) {
            $email = trim($_POST['email']);
            $stmt = $conn->prepare("SELECT * FROM users WHERE user_email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $stmt_result = $stmt->get_result();
            if ($stmt_result->num_rows > 0) {
                echo "Email found";
                $result = $stmt_result->fetch_assoc();
                $user_id = $result['user_id'];

                function getInviteCode($n) {
                    return bin2hex(random_bytes($n/2));
                }

                $verification_code_check = true;
                while ($verification_code_check) {
                    $email_verification_code = getInviteCode(6);
                    $retrieve_email_verification_codes_stmt = $conn->prepare("SELECT * FROM email_verification_codes WHERE email_verification_code = ?");
                    $retrieve_email_verification_codes_stmt->bind_param("s", $email_verification_code);
                    $retrieve_email_verification_codes_stmt->execute();
                    $email_stmt_result = $retrieve_email_verification_codes_stmt->get_result();
                    if ($email_stmt_result->num_rows === 0) {
                        $verification_code_check = false;
                        echo "generated code";
                    }
                }
                echo "<br>broke out of while loop successful";

                $create_email_verification_code_stmt = $conn->prepare("INSERT INTO email_verification_codes (user_id, email_verification_code, expire_date) VALUES (?, ?, ?)");
                $expire_date = date("Y-m-d H:i:s", time() + 300);
                $create_email_verification_code_stmt->bind_param("iss", $user_id, $email_verification_code, $expire_date);
                if ($create_email_verification_code_stmt->execute()) {
                    echo "assigned code with expiration to user email";
                }
                else {
                    echo "could not create new code with expiration";
                }
            }
            else {
                echo "An Account With That Email Does Not Exist";
            }
        }
        else {
            echo "email post not found";
        }
    }

    if (isset($_POST['submit_btn'])) {
        if (isset($_POST['email']) && isset($_POST['token-code'])) {
            $email = trim($_POST['email']);
            $token_code = trim($_POST['token-code']);

            $stmt = $conn->prepare("SELECT * FROM users WHERE user_email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $stmt_result = $stmt->get_result();
            if ($stmt_result->num_rows > 0) {
                $result = $stmt_result->fetch_assoc();
                $user_id = $result['user_id'];
                #CHECK IF VERIFICATION CODE EXISTS
                $verify_code_stmt = $conn->prepare("SELECT * FROM email_verification_codes WHERE user_id = ? AND email_verification_code = ?");
                $verify_code_stmt->bind_param("is", $user_id, $token_code);
                $verify_code_stmt->execute();
                $verify_code_stmt_result = $verify_code_stmt->get_result();
                if ($verify_code_stmt_result->num_rows > 0) {
                    $verify_result = $verify_code_stmt_result->fetch_assoc();
                    $expiration_date = $verify_result['expire_date'];
                    $expiration_date_datetime = new DateTime($expiration_date);
                    $current_datetime = new DateTime();
                    if ($current_datetime < $expiration_date_datetime) {
                        $_SESSION['pw_reset_user_id'] = $user_id;
                        $session_expire = date("Y-m-d H:i:s", time() + 300);
                        $_SESSION['pw_reset_expiration_date'] = new DateTime($session_expire);
                        echo "Verification Code Successful Redirecting Now";
                        header("Location: reset_password.php");
                        exit();
                    }  
                    else {
                        echo "Verification Code has expired";
                    }

                }
                else {
                    echo "No verification code found";
                }
            }
            else {
                echo "An Account With That Email Does Not Exist";
            }
        }
        else {
            echo "email and code post not found";
        }
    }       

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ESM Forgot Password</title>
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
                <h2>Forgot your password?</h2>
            </div>
            <div class="form-container-content">

                <form action="forgot_password.php" id="loginForm" method="POST">
                    <label for="email">Email</label><br>
                    <input id="email" name="email" type="text" maxlength="100" required><br>
                    <label for="token-code">Code</label><br>
                    <input id="token-code" name="token-code" type="text" maxlength="6"><br>
                    <button type="submit" name="code_btn" value="code_btn">Send Code</button>
                </form>
                

            </div>
            <div class="form-container-bottom">
                <button type="submit" name="submit_btn" value="submit_btn" form="loginForm">Submit</button>
            </div>
        </div>
    </div>
    <div class="footer-container"></div>
</body>
</html>