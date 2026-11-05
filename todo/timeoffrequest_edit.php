<?php
    session_start();
    include "../scripts/auth.php";
    include "../scripts/db_conn.php";

    #$user_id = 2;
    #CHECK MEMBER_ID
    $role_stmt = $conn->prepare("SELECT * FROM organization_members WHERE user_id = ?");
    $role_stmt->bind_param("i", $user_id);
    $role_stmt->execute();
    $role_stmt_result= $role_stmt->get_result();
    #IF MEMBER_ID FOUND
    if ($role_stmt_result->num_rows > 0) {
        $role_result = $role_stmt_result->fetch_assoc();
        #$role = $role_result['member_role'];
        $role = 'manager';
        $organization_id = $role_result['organization_id'];
        

        $org_name_stmt = $conn->prepare("SELECT * FROM organizations WHERE organization_id = ?");
        $org_name_stmt->bind_param("i", $organization_id);
        $org_name_stmt->execute();
        $org_name_stmt_result = $org_name_stmt->get_result();
        if ($org_name_stmt_result->num_rows > 0) {
            $org_name_result = $org_name_stmt_result->fetch_assoc();
            $org_name = trim($org_name_result['organization_name']);
        }
        #IF ORG NAME NOT FOUND
        else {
            $org_name = "ORG NAME NOT FOUND";
        }
        $org_name_stmt->close();
    }
    #IF MEMBER_ID NOT FOUND
    else {
        echo "Not Part of An Organization";
        if ($user_account_type === "owner") {
            #REDIRECT TO CREATE AN ORGANIZATION PAGE
        }
        else {
            #REDIRECT TO JOIN AN ORGANIZATION PAGE
        }
    }

    #REMOVE FROM SHIFTS AND SET STATUS TO APPROVED
    if (isset($_POST['approve'])) {

    }

    #SET STATUS TO DENIED
    if (isset($_POST['deny'])) {

    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ESM Login</title>
    <link rel="stylesheet" href="../templates/template1.css">
</head>
<body>
    <div class="nav-bar-container">
        
        <div class="nav-logo"><form action="dashboard.php" id=navForm method="POST"><button>Back</button></form></div>
        <div class="nav-header">
            <h1>Employee Management and Scheduling</h1>
        </div>
        <div class="nav-buttons"></div>
    </div>
    <div class="content-container">
        <div class="form-container" id="container-alt-style">
            <div class="form-container-top" id="top-alt-style">
                <h2>Time-Off Requests</h2>
            </div>
            <div class="table-form">
                <form method="POST" action="">
                    <table>
                        <thead>
                            <tr>
                                <th>Member</th>
                                <th>Date</th>
                                <th>Start Time</th>
                                <th>End Time</th>
                                <th>Reason</th>
                                <?php
                                    if ($role !== 'standard') {
                                        echo "<th>Action</th>";
                                    }
                                ?>
                                
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                #TODO: REWORK AND FIX - IF MORE THAN 1 SHIFT ON 1 DAY, WILL BREAK
                                $stmt_tor = $conn->prepare("SELECT * FROM time_off_requests WHERE organization_id = ?");
                                $stmt_tor->bind_param("i", $organization_id);
                                $stmt_tor->execute();
                                $stmt_tor_result = $stmt_tor->get_result();
                                if ($stmt_tor_result->num_rows > 0) {
                                    while ($row = $stmt_tor_result->fetch_assoc()) {
                                        $target_user_id = $row['user_id'];
                                        $target_shift_date = $row['shift_date'];
                                        $target_request_reason = $row['request_reason'];

                                        $user_id_stmt = $conn->prepare("SELECT * FROM users WHERE user_id = ?");
                                        $user_id_stmt->bind_param("i", $target_user_id);
                                        $user_id_stmt->execute();
                                        $user_id_stmt_result = $user_id_stmt->get_result();
                                        if ($user_id_stmt_result->num_rows > 0) {
                                            $target_user_fname_result = $user_id_stmt_result->fetch_assoc();
                                            $target_user_fname = $target_user_fname_result['user_firstname'];
                                            $target_user_lname = $target_user_fname_result['user_lastname'];
                                        }

                                        $display_name = strtoupper($target_user_fname) . " " . strtoupper($target_user_lname);

                                        $user_time_stmt = $conn->prepare("SELECT * FROM shifts WHERE user_id =? AND shift_date = ? AND organization_id = ?");
                                        $user_time_stmt->bind_param("isi", $target_user_id, $target_shift_date, $organization_id);
                                        $user_time_stmt->execute();
                                        $user_time_stmt_result = $user_time_stmt->get_result();
                                        if ($user_time_stmt_result->num_rows > 0 ) {
                                            $user_time_result = $user_time_stmt_result->fetch_assoc();
                                            $target_shift_start = $user_time_result['start_time'];
                                            $target_shift_end = $user_time_result['end_time'];
                                        }

                                        echo "<tr>";
                                        echo "<td>" . $display_name . "</td>";
                                        echo "<td>" . $target_shift_date . "</td>";
                                        echo "<td>" . $target_shift_start . "</td>";
                                        echo "<td>" . $target_shift_end . "</td>";
                                        echo "<td>" . $target_request_reason . "</td>";

                                        if ($role !== 'standard') {
                                            echo "<td>";
                                            echo "<button type='submit' name='approve' value='$target_user_id'>Approve</button>";
                                            echo "<button id='delete-btn' type='submit' name='deny' value='$target_user_id'>Deny</button>";
                                            echo "</td>";
                                        }

                                        echo "</tr>";

                                    }
                                }

                            ?>
                        </tbody>
                    </table>
                </form>
            </div>
            <!--<div class="form-container-content" id="content-alt-style">
                <div class="content-member">
                    <h1>Naomi M.</h1>
                    <h1>Karen B.</h1>
                </div>
                <div class="date">
                    <h1>10/25/2026</h1>
                    <h1>10/28/2026</h1>
                </div>
                <div class="time">
                    <h1>06:30-13:00</h1>
                    <h1>13:00-20:00</h1>
                </div>
                <div class="approve-deny">
                    <h1>Approve/Deny</h1>
                    <h1>Approve/Deny</h1>
                </div>
            </div>-->
            
        </div>
    </div>
    <div class="footer-container"></div>
</body>
</html>