<?php
    session_start();
    include "../scripts/auth.php";
    include "../scripts/db_conn.php";

    if (isset($_POST['logout-btn'])) {
        header("Location: ../scripts/logout.php");
        exit;
    }

    if (isset($_POST['organization-btn'])) {
        header("Location: organization.php");
        exit;
    }

    if (isset($_POST['timeoffreq-btn'])) {
        header("Location: timeoffrequest_edit.php");
        exit;
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ESM Dashboard</title>
    <link rel="stylesheet" href="../templates/template1.css">
</head>
<body>
    <div class="nav-bar-container">
        <div class="nav-logo"></div>
        <div class="nav-header">
            <h1>Employee Management and Scheduling</h1>
        </div>
        <div class="nav-buttons">
            <form action="" id=navForm method="POST">
                <button type='submit' name='organization-btn'>Organization</button>
                <button type='submit' name='logout-btn'>Logout</button>
            </form>
        </div>
    </div>
    <div class="dashboard_container">
        <div class="dashboard_left_layout">
            <div class="dashboard_usercontrol_userinfo_layout">
                <div class="dashboard_userinfo_layout">
                    <h2>User Information</h2>
                    <br>
                    <p><strong>Email: </strong>zachlusk00@gmail.com</p>
                    <p><strong>Organization: </strong>En Su Casa</p>
                    <p><strong>Member Role: </strong>Standard</p>
                    <p><strong>Hourly Pay: </strong>$17.00</p>
                </div>
                <div class="dashboard_admincontrol_layout">
                    <h2>Organization Controls</h2>
                    <form action="" id=adminControlForm method="POST">
                        <button>Create Announcement</button>
                        <button type='submit' name='timeoffreq-btn'>Time-Off Requests</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="dashboard_center_layout">
            <div class="dashboard_shift_layout">
                <div class="shift_header_layout">
                <form id=navForm>
                    <button>Previous</button>
                    <h1>Scheduled Shift</h1>
                    <button>Next</button>
                </form>
                </div>
                <div class="shift_content_layout">
                    <br>
                    <p><strong>Date: </strong>11/06/2026</p>
                    <p><strong>Start Time: </strong>07:00AM</p>
                    <p><strong>End Time: </strong>15:00PM</p>
                    <p><strong>Assigned Patient: </strong>Richard F.</p>
                </div>
            </div>
        </div>
        <div class="dashboard_right_layout">
            <div class="dashboard_announcements_layout">
                <div class="dashboard_announcement"></div>
                <div class="dashboard_announcement"></div>
            </div>
        </div>
    </div>
    <div class="footer-container"></div>
</body>
</html>