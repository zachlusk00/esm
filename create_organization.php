<?php
    #https://www.geeksforgeeks.org/php/generating-random-string-using-php/
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
        if ($user_account_type !== owner) {
        echo "NOT OWNER ACCOUNT";
    }
    }
    else {
        echo "NO USER ACCOUNT TYPE FOUND";
    }

    

    $error_msg = '';

    if (isset($_POST['org_name'])) {
        $org_name = $_POST['org_name'];

        function getInviteCode($n) {
            return bin2hex(random_bytes($n/2));
        }

        $invite_code_check = true;
        while ($invite_code_check) {
            $invite_code = getInviteCode(9);
            $invite_code_check_db = $conn->prepare("SELECT * FROM organizations WHERE organization_invite_code = ?");
            $invite_code_check_db->bind_param("s", $invite_code);
            $invite_code_check_db->execute();
            $invite_code_check_result = $invite_code_check_db->get_result();

            if ($invite_code_check_result->num_rows === 0) {
                $invite_code_check = false;
            }
        }

        $stmt = $conn->prepare("INSERT INTO organizations (organization_name, user_id, organization_invite_code) VALUES (?, ?, ?)");
        $temp_user_id = 1;
        $stmt->bind_param("sis", $org_name, $temp_user_id, $invite_code);

        if ($stmt->execute()) {
            $get_org_id_stmt = $conn->prepare("SELECT * FROM organizations WHERE user_id = ?");
            $get_org_id_stmt->bind_param("i", $temp_user_id);
            $get_org_id_stmt->execute();
            $get_org_id_stmt_result = $get_org_id_stmt->get_result();
            if ($get_org_id_stmt_result->num_rows > 0) {
                $get_result = $get_org_id_stmt_result->fetch_assoc();
                $org_id = $get_result['organization_id'];
                $org_role = "owner";
                $org_hourly_rate = 15;

                $add_to_org_stmt = $conn->prepare("INSERT INTO organization_members (organization_id, user_id, member_role, hourly_rate) VALUES (?, ?, ?, ?)");
                $add_to_org_stmt->bind_param("iisi", $org_id, $temp_user_id, $org_role, $org_hourly_rate);
                if ($add_to_org_stmt->execute()) {
                    $error_msg = "Created Organization Successfully";
                }
                else {
                    $error_msg = "Error Creating Organization";
                }
            }
        }
        else {
            $error_msg = "Error Creating Organization";
        }
    }
    else {
        echo "POST NOT FOUND";
        $error_msg = 'POST NOT FOUND';
    }

    $conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ESM Create Organization</title>
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
                <h2>Create an organization</h2>
            </div>
            <div class="form-container-content">

                <form action="create_organization.php" id="createAccountForm" method="POST">
                    <label for="org_name">Organization Name</label><br>
                    <input id="org_name" name="org_name" type="text" max_length="72"><br>
                </form>
                

            </div>
            <div class="form-container-bottom">
                <button type="submit" form="createAccountForm">Create</button>
            </div>
        </div>
    </div>
    <div class="footer-container"></div>
</body>
</html>