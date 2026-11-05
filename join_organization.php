<?php
//https://www.w3schools.com/php/php_mysql_select.asp
    session_start();
    include "scripts/auth.php";

    if (!isset($_SESSION["logged_in"])) {
        echo "NO LOGIN SESSION DETECTED";
    }

    if ($_SESSION["logged_in"] !== true) {
        echo "NOT LOGGED IN";
    }

    if (isset($_SESSION["user_id"])) {
        $user_id = $_SESSION["user_id"];
    }
    else {
        echo "NO USER ID SESSION FOUND";
    }

    if (isset($_SESSION["user_account_type"])) {
        $user_account_type = $_SESSION["user_account_type"];
        if ($user_account_type === "owner") {
        echo "OWNER ACCOUNTS CANNOT JOIN AN ORGANIZATION";
    }
    }
    else {
        echo "NO USER ACCOUNT TYPE FOUND";
    }

    

    $check_org_stmt = $conn->prepare("SELECT * FROM organization_members WHERE user_id = ?");
    $temp_user_id = 2;
    $check_org_stmt->bind_param("i", $temp_user_id);
    $check_org_stmt->execute();
    $check_org_stmt_result = $check_org_stmt->get_result();
    if ($check_org_stmt_result->num_rows > 0) {
        echo "ALREADY APART OF ORG";
    }

    if (isset($_POST["invitecode-msg"])) {
        $invite_code = $_POST["invitecode-msg"];
        $check_org_inv_code = $conn->prepare("SELECT * FROM organizations WHERE organization_invite_code = ?");
        $check_org_inv_code->bind_param("s", $invite_code);
        $check_org_inv_code->execute();
        $test_result = $check_org_inv_code->get_result();
        if ($test_result->num_rows > 0) {
            $check_org_inv_code_result = $test_result->fetch_assoc();
            $organization_id = $check_org_inv_code_result['organization_id'];
            $stmt = $conn->prepare("INSERT INTO organization_members (organization_id, user_id, member_role, hourly_rate) VALUES (?, ?, ?, ?)");

            $temp_role = "standard";
            $temp_wage = 15;

            $stmt->bind_param("iisi", $organization_id, $temp_user_id, $temp_role, $temp_wage);
            if ($stmt->execute()) {
                $error_msg = "Joined Organization Successfully";
            }
            else {
                $error_msg = "Error Joining Organization";
            }
            $stmt->close();
        }
        else {
            $error_msg = "Error Joining Organization";
        }
        $check_org_inv_code->close();
    }
    else {
        $error_msg = 'POST NOT FOUND';
    }

    $check_org_stmt->close();
    



?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ESM Join Organization</title>
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
                <h2>Join Organization</h2>
            </div>
            <div class="form-container-content">

                <form action="join_organization.php" id="loginForm" method="POST">
                    <label for="invitecode-msg">Invite Code</label><br>
                    <input id="invitecode-msg" name="invitecode-msg" type="text" maxlength="12"><br>
                </form>
                

            </div>
            <div class="form-container-bottom">
                <button type="submit" form="loginForm">Join</button>
            </div>
        </div>
    </div>
    <div class="footer-container"></div>
</body>
</html>