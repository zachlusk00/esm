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
            if (isset($_POST['announcement-msg'])) {
                $announcement_msg = $_POST['announcement-msg'];

                #$stmt = "INSERT INTO announcements (announcement_msg, user_id) VALUES ('$announcement_msg', '$user_id')"; 
                $stmt = $conn->prepare("INSERT INTO announcements (announcement_msg, user_id) VALUES (?, ?)");
                $stmt->bind_param("si", $announcement_msg, $user_id);

                if ($stmt->execute()) {
                    $error_msg = "Created Announcement Successfully";
                }
                else {
                    $error_msg = "Error Creating Announcement";
                }
            }
            else {
                echo "POST NOT FOUND";
                $error_msg = 'POST NOT FOUND';
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
    <title>ESM Create Announcement</title>
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
                <h2>Create Announcement</h2>
            </div>
            <div class="form-container-content">

                <form action="create_announcement.php" id="loginForm" method="POST">
                    <label for="announcement-msg">Message</label><br>
                    <textarea id="announcement-msg" name="announcement-msg" rows=8 cols=50 max_length="255"></textarea>
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