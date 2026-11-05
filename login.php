<?php
    //REFERENCES: https://www.geeksforgeeks.org/php/creating-a-registration-and-login-system-with-php-and-mysql/
    //https://stackoverflow.com/questions/35086115/retrieving-data-from-database-using-prepared-statement
    //https://www.w3schools.com/php/php_sessions.asp
    //https://www.w3schools.com/php/php_mysql_prepared_statements.asp
    // https://www.w3schools.com/php/php_mysql_insert.asp
    //https://www.w3schools.com/php/php_mysql_insert_multiple.asp
    //https://www.w3schools.com/php/php_mysql_select_where.asp

    include "scripts/db_conn.php";
    session_start();
    $error_msg = '';

    if (isset($_POST['email']) && isset($_POST['password'])) {
        $email = $_POST['email'];
        $password = $_POST['password'];

        //SQL Injection Vulnerability [DO NOT USE]
        //$stmt = mysqli_query($conn, "SELECT * FROM users WHERE user_email = '$email' and user_pass = '$password'");
        //Need to get password after email, hash values dont match, php generates new hash
        //$stmt = $conn->prepare("SELECT * FROM users WHERE user_email = ? and user_pass = ?");

        $stmt = $conn->prepare("SELECT * FROM users WHERE user_email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $db_result = $result->fetch_assoc();
            $db_password = $db_result['user_pass'];
            if (password_verify($password, $db_password)) {
                #echo "Login Succesful!";
                session_regenerate_id(true);
                $_SESSION["email"] = $email;
                $_SESSION["user_account_type"] = $db_result['user_account_type'];
                $_SESSION["user_id"] = $db_result["user_id"];
                $_SESSION["logged_in"] = true;
                #echo "Session Email =" . $_SESSION['email'];
                #$error_msg = 'Correct Password';

                #REDIRECT LOGIN SUCCESS
                header("Location: todo/dashboard.php");
                exit;
            }
            else {
                $error_msg = 'Password Incorrect';
            }
        }
        else {
            $error_msg = 'Email Not Found';
        }
    }
    else {
        #echo "POST NOT FOUND";
        
    }
    $conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ESM Login</title>
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
                <h2>Log in to your account</h2>
            </div>
            <div class="form-container-content">

                <form action="login.php" id="loginForm" method="POST">
                    <label for="email">Email</label><br>
                    <input id="email" name="email" type="text" maxlength="100"><br>
                    <label for="password">Password</label><br>
                    <input id="password" name="password" type="password" maxlength="255">
                    <a href="templates/template1.php">Forgot your password?</a>
                    <?php echo "<h2 style='color:red;''>$error_msg</h2>"; ?>
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