<?php
    session_start();
    include "scripts/auth.php";
    
    $error_msg = '';

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

    $role_stmt = $conn->prepare("SELECT * FROM organizations_members WHERE user_id = ?");
    $role_stmt->bind_param("i", $user_id);
    $role_stmt->execute();
    if ($role_stmt->num_rows > 0) {
        $role_stmt_result = $role_stmt->fetch_assoc();
        $role = $role_stmt_result['member_role'];
        if ($role !== "owner" || $role !== "manager") {
            if (isset($_POST['email']) && isset($_POST['date']) && isset($_POST['start_time']) && isset($_POST['end_time'])) {
                $email = $_POST['email'];
                $date = $_POST['date'];
                $start_time = $_POST['start_time'];
                $end_time = $_POST['end_time'];

                $org_id = $role_stmt_result['organiztion_id']

                $user_id_target_stmt = $conn->prepare("SELECT * FROM users WHERE user_email = ?");
                $user_id_target_stmt->bind_param("s", $email);
                $user_id_target_stmt->execute();
                if ($user_id_target_stmt->num_rows > 0) {
                    $user_id_target_result = $user_id_target_stmt->fetch_assoc();
                    $user_id_target = $user_id_target_result["user_id"]; 
                    $org_check_stmt = $conn->prepare("SELECT * FROM organization_members WHERE user_id = ?");
                    $org_check_stmt->bind_param("i", $user_id_target);
                    $org_check_stmt->execute();
                    if ($org_check_stmt->num_rows > 0) {
                        $org_check_stmt_result = $org_check_stmt->fetch_assoc();
                        $org_check = $org_check_stmt_result['organization_id'];
                        $target_user_member_id = $org_check_stmt_result['member_id'];
                        if ($org_check === $org_id) {
                            #$stmt = "INSERT INTO shifts (user_id, member_id, shift_date, start_time, end_time) VALUES ('$user_id', '$member_id', '$date', '$start_time', '$end_time')"; 
                            $stmt = $conn->prepare("INSERT INTO shifts (user_id, member_id, shift_date, start_time, end_time) VALUES (?, ? ,? ,? ,?)");
                            $stmt->bind_param("iisss", $user_id_target, $target_user_member_id, $date, $start_time, $end_time);
                            if ($stmt->execute()) {
                                $error_msg = "Created Shift Successfully."
                            }
                            else {
                                echo "FAILED TO CREATE SHIFT"
                            }
                        }
                        else {
                            echo "TARGET USER IS A PART OF ANOTHER ORGANIZATION"
                        }
                    }
                    else {
                        echo "TARGET USER NOT A PART OF AN ORG"
                    }
                }
                else {
                    echo "NO TARGET USER FOUND"
                }
            }
            else {
                echo "POST NOT FOUND"
            }
        }
        else {
            echo "INSUFFICIENT ROLE"
        }
    }
    else {
        echo "YOU ARE NOT A PART OF AN ORGANIZATION"
    }
    $conn->close();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ESM Add Schedule</title>
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
                <h2>Create Shift</h2>
            </div>
            <div class="form-container-content">

                <form action="addschedule.php" id="loginForm" method="POST">
                    <label for="email">Email</label><br>
                    <input id="email" name="email" type="text" maxlength="100"><br>
                    <label for="date">Date</label><br>
                    <input id="date" name="date" type="date" maxlength="100"><br>
                    <label for="start_time">Start Time</label><br>
                    <input id="start_time" name="start_time" type="time" maxlength="100"><br>
                    <label for="end_time">End Time</label><br>
                    <input id="end_time" name="end_time" type="time" maxlength="100"><br>
                </form>
                

            </div>
            <div class="form-container-bottom">
                <button type="submit" form="loginForm">Create</button>
            </div>
        </div>
    </div>
    <div class="footer-container"></div>
</body>
</html>