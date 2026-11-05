<?php
    include "scripts/auth.php";

    $error_msg = '';

    if (isset($_POST['email']) && isset($_POST['password']) && isset($_POST['fname']) && isset($_POST['account_type']) && isset($_POST['lname'])) {
        $email = $_POST['email'];
        $fname = $_POST['fname'];
        $lname = $_POST['lname'];
        $account_type = $_POST['account_type'];
        $password = $_POST['password'];

        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("SELECT user_email FROM users WHERE user_email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $email_result = $stmt->get_result();

        if ($email_result->num_rows > 0) {
            $error_msg = "Email already exists";
        }
        else {
            $stmt = $conn->prepare("INSERT INTO users (user_email, user_firstname, user_lastname, user_account_type, user_pass) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", $email, $fname, $lname, $account_type, $hashed_password);
            if ($stmt->execute()) {
                $error_msg = "Created Account Successfully";
                $stmt->close();
                $conn->close();
            }
            else {
                $error_msg = 'Error Creating Account';
                $stmt->close();
            }
        }
    }
    else {
        echo "POST NOT FOUND";
        $error_msg = 'POST NOT FOUND';
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ESM Create Account</title>
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
                <h2>Create an account</h2>
            </div>
            <div class="form-container-content">

                <form action="create_account.php" id="createAccountForm" method="POST">
                    <label for="email">Email</label><br>
                    <input id="email" name="email" type="email" maxlength="100" required><br>
                    <label for="fname">First Name</label><br>
                    <input id="fname" name="fname" type="text" maxlength="100" required><br>
                    <label for="lname">Last Name</label><br>
                    <input id="lname" name="lname" type="text" maxlength="100" required><br>
                    <label for="account_type">Account Type</label><br>
                    <select name="account_type" id="account_type">
                        <option value="standard">Standard</option>
                        <option value="owner">Owner</option>
                    </select><br>
                    <label for="password">Password</label><br>
                    <input id="password" name="password" type="password" maxlength="255" required>
                </form>
                

            </div>
            <div class="form-container-bottom">
                <button type="submit" form="createAccountForm">Create</button>
            </div>
        </div>
        <?php echo "<h4 style='color:red;''>$error_msg</h4>"; ?>
    </div>
    <div class="footer-container"></div>
</body>
</html>