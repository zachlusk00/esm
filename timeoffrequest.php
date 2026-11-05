<?php
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

    if (isset($_POST['date']) && isset($_POST['reason'])) {
        $request_date = trim($_POST['date']);
        $request_reason = trim($_POST['reason']);
        //CHECK SHIFT EXISTS
        $check_shift_stmt = $conn->prepare("SELECT * FROM shifts WHERE user_id = ? AND shift_date = ?");
        $check_shift_stmt->bind_param("is", $user_id, $request_date);
        $check_shift_stmt->execute();
        $check_shift_stmt_result = $check_shift_stmt->get_result();
        if ($check_shift_stmt_result->num_rows > 0) {
            $shift_result = $check_shift_stmt_result->fetch_assoc();
            $shift_date = $shift_result['shift_date'];
            echo "Shift found";
            $stmt = $conn->prepare("INSERT INTO time_off_requests ('user_id, shift_date, request_reason, request_action') VALUES (?, ?, ?, ?)");
            $stmt->bind_param("isss", $user_id, $shift_date, $request_reason, "await");
            if ($stmt->execute()) {
                echo "Created time off request successfully";
            }
            else {
                echo "Failed to create time off request";
            }
        }
        else {
            echo "No shift found";
        }
    }
    else {
        echo "POST NOT SUCCESSFUL"
    }

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ESM Time-Off Request</title>
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
                <h2>Time-Off Request</h2>
            </div>
            <div class="form-container-content">

                <form action="timeoffrequest.php" id="loginForm" method="POST">
                    <label for="date">Date</label><br>
                    <input id="date" name="date" type="text" maxlength="100" required><br>
                    <label for="reason">Reason</label><br>
                    <input id="reason" name="reason" type="text" maxlength="255" required>
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