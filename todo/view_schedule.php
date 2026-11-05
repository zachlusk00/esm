<?php
    session_start();
    include "../scripts/auth.php";

    if (!isset($_SESSION["logged_in"]) && $_SESSION["logged_in"] !== true) {
        echo "NO LOGIN SESSION DETECTED";
        echo date('Y-m-d');
    }

    if (isset($_SESSION["user_id"])) {
        $user_id = $_SESSION["user_id"];
    }
    else {
        echo "NO USER ID SESSION FOUND";
    }

    if (isset($_SESSION["user_account_type"])) {
        $user_account_type = trim($_SESSION["user_account_type"]);
    }
    else {
        echo "NO USER ACCOUNT TYPE FOUND";
    }

    $user_id = 2;
    #CHECK MEMBER_ID
    $role_stmt = $conn->prepare("SELECT * FROM organization_members WHERE user_id = ?");
    $role_stmt->bind_param("i", $user_id);
    $role_stmt->execute();
    $role_stmt_result= $role_stmt->get_result();
    #IF MEMBER_ID FOUND
    if ($role_stmt_result->num_rows > 0) {
        $role_result = $role_stmt_result->fetch_assoc();
        $member_id = $role_result['member_id'];
        $role = $role_result['member_role'];
        #$role = 'manager';
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

    #EDIT BUTTON
    if (isset($_POST['edit']) && $_POST['edit'] !== null) {
        $selected_user_id = $_POST['edit'];
    }
    else {
        $selected_user_id = null;
    }

    #SAVE BUTTON
    if (isset($_POST['save'])) {
        $save_target_id = (int)$_POST['save']; #HAVE TO USE TYPE CAST AS IT WILL SAVE AS STRING
        
        $save_new_start_time = $_POST['start_time'][$save_target_id];
        $save_new_end_time = $_POST['end_time'][$save_target_id];
        $save_new_shift_date = $_POST['shift_date'][$save_target_id];

        $save_stmt = $conn->prepare("UPDATE shifts SET start_time = ?, end_time = ? WHERE user_id = ? AND organization_id = ? AND shift_date = ?");
        $save_stmt->bind_param("ssiis", $save_new_start_time, $save_new_end_time, $save_target_id, $organization_id, $save_new_shift_date);
        $save_stmt->execute();
        $save_stmt->close();
    }

    #DELETE BUTTON
    if (isset($_POST['delete'])) {
        $save_target_id = (int)$_POST['delete']; #HAVE TO USE TYPE CAST AS IT WILL SAVE AS STRING
        $save_new_shift_date = $_POST['shift_date'][$save_target_id];

        $delete_stmt = $conn->prepare("DELETE FROM shifts WHERE user_id = ? AND organization_id = ? AND shift_date = ?");
        $delete_stmt->bind_param("iis", $save_target_id, $organization_id, $save_new_shift_date);
        $delete_stmt->execute();
        $delete_stmt->close();

    }

    $role_stmt->close();
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
        
        <div class="nav-logo"><button>Back</button></div>
        <div class="nav-header">
            <h1>Employee Management and Scheduling</h1>
        </div>
        <div class="nav-buttons"></div>
    </div>
    <div class="content-container">
        <div class="form-container" id="container-alt-style">
            <div class="form-container-top" id="top-alt-style">
                <h2>Schedule<br>10/30/2026</h2>
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
                                <?php 
                                    if ($role !== 'standard') {
                                        echo "<th>Action</th>";
                                    }
                                ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php

                                $current_datetime = date("Y-m-d");
                                $stmt = $conn->prepare("SELECT * FROM shifts WHERE organization_id = ? AND shift_date = ?");
                                $stmt->bind_param("is", $organization_id, $current_datetime);
                                $stmt->execute();
                                $stmt_result = $stmt->get_result();
                                if ($stmt_result->num_rows > 0) {
                                   while ($row = $stmt_result->fetch_assoc()) {
                                        $target_user_id = $row['user_id'];
                                        $target_shift_date = $row['shift_date'];
                                        $target_start_time = $row['start_time'];
                                        $target_end_time = $row['end_time'];

                                        $user_id_stmt = $conn->prepare("SELECT * FROM users WHERE user_id = ?");
                                        $user_id_stmt->bind_param("i", $target_user_id);
                                        $user_id_stmt->execute();
                                        $user_id_stmt_result = $user_id_stmt->get_result();
                                        if ($user_id_stmt_result->num_rows > 0) {
                                            $target_user_fname_result = $user_id_stmt_result->fetch_assoc();
                                            $target_user_fname = $target_user_fname_result['user_firstname'];
                                            $target_user_lname = $target_user_fname_result['user_lastname'];
                                        }
                                        #$selected_user_id = "";
                                        $is_editing = ($selected_user_id == $target_user_id);
                                    
                                        $display_name = strtoupper($target_user_fname) . " " . strtoupper($target_user_lname);

                                        echo "<tr>";
                                        #NAME
                                        echo "<td>" . $display_name . "</td>";

                                        #DATE
                                        #https://stackoverflow.com/questions/3676127/how-do-i-make-a-text-input-non-editable

                                        echo "<td>";
                                            if ($is_editing) {
                                                echo "<input type='date' name='shift_date[$target_user_id]' value='$target_shift_date' readonly>";
                                                
                                            }
                                            else {
                                                #HIDDEN INPUT FOR DELETE
                                                echo "<input type='hidden' name='shift_date[$target_user_id]' value='$target_shift_date' readonly>";

                                                echo "$target_shift_date";
                                            }
                                        echo "</td>";

                                        #echo "<td>" . $current_datetime . "</td>";

                                        #START TIME
                                        echo "<td>";
                                            if ($is_editing) {
                                                echo "<input type='time' name='start_time[$target_user_id]' value='$target_start_time'>";
                                                
                                            }
                                            else {
                                                echo "$target_start_time";
                                            }
                                        echo "</td>";

                                        #END TIME
                                        echo "<td>";
                                            if ($is_editing) {
                                                echo "<input type='time' name='end_time[$target_user_id]' value='$target_end_time'>";
                                                
                                            }
                                            else {
                                                echo "$target_end_time";
                                            }
                                        echo "</td>";

                                        #ACTION BUTTONS
                                        if ($role !== 'standard') {
                                            echo "<td>";
                                            if ($is_editing) {
                                                echo "<button type='submit' name='save' value='$target_user_id'>Save</button>";
                                                echo "<button id='delete-btn' type='submit' name='cancel'>Cancel</button>";
                                            }
                                            else {
                                                echo "<button type='submit' name='edit' value='$target_user_id'>Edit</button>";
                                                echo "<button id='delete-btn' type='submit' name='delete' value='$target_user_id'>Delete</button>";
                                            }
                                            echo "</td>";
                                        }
                                        
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