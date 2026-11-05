<?php
    #gave inspiration to use array for table rows for identifying user id
    #https://stackoverflow.com/questions/46619727/how-can-edit-records-in-a-php-table-by-clicking-an-edit-button
    #https://stackoverflow.com/questions/13506616/how-to-edit-and-save-rows-in-a-table-using-php
    #https://stackoverflow.com/questions/6230131/how-to-update-database-from-dynamically-created-webpage-php-mysql
    #https://www.askingbox.com/question/php-get-dynamic-post-variables-from-form
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
        $role = $role_result['member_role'];
        $organization_id = $role_result['organization_id'];
        $hourly_rate = $role_result['hourly_rate'];

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

    # EDIT BUTTON
    if (isset($_POST['edit']) && $_POST['edit'] !== null) {
        $selected_user_id = $_POST['edit'];
    }
    else {
        $selected_user_id = null;
    }

    # SAVE BUTTON
    if (isset($_POST['save'])) {
        $save_target_id = (int)$_POST['save']; #HAVE TO USE TYPE CAST AS IT WILL SAVE AS STRING

        $save_new_role = $_POST['member_role'][$save_target_id];
        $save_hourly_rate = $_POST['hourly_rate'][$save_target_id];

        echo "[SAVED INFO] TARGET_ID = '$save_target_id', NEW_ROLE = '$save_new_role', HOURLY_RATE = '$save_hourly_rate'";

        $save_stmt = $conn->prepare("UPDATE organization_members SET member_role = ?, hourly_rate = ? WHERE user_id = ?");
        $save_stmt->bind_param("sii", $save_new_role, $save_hourly_rate, $save_target_id);
        $save_stmt->execute();
        $save_stmt->close();
    }
    
    if (isset($_POST['back-btn'])) {
        header("Location: dashboard.php");
        exit;
    }


    $role_stmt->close();


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ESM Organization</title>
    <link rel="stylesheet" href="../templates/template1.css">
</head>
<body>
    <div class="nav-bar-container">
        
        <div class="nav-logo"><form action="dashboard.php" id=navForm method="POST"><button>Back</button></form></div>
        <div class="nav-header">
            <h1>Employee Management and Scheduling</h1>
        </div>
        <div class="nav-buttons">Invite Code: GSJKLJGKSDLF</div>
    </div>
    <div class="content-container">
        <div class="form-container" id="container-alt-style">
            <div class="form-container-top" id="top-alt-style">
                <h2><?php echo"$org_name"; ?></h2>
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
            <div class="table-form">
                <form method="POST" action="">
                <table>
                    <thead>
                        <tr>
                            <th>Member</th>
                            <th>Role</th>
                            
                            <?php
                             if($role !== 'standard') {
                                echo "<th>Hourly Rate</th>";
                                echo "<th>Action</th>";
                             }
                             ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            #$get_data = true;

                            $stmt = $conn->prepare("SELECT * FROM organization_members WHERE organization_id = ?");
                            $stmt->bind_param("i", $organization_id);
                            $stmt->execute();
                            $stmt_result = $stmt->get_result();
                            if ($stmt_result->num_rows > 0) {
                                while ($row = $stmt_result->fetch_assoc()) {
                                    $target_user_id = $row['user_id'];
                                    #$target_user_fname = $row['user_firstname'];
                                    $target_user_member_role = $row['member_role'];
                                    $target_user_hourly_rate = $row['hourly_rate'];

                                    $user_id_stmt = $conn->prepare("SELECT * FROM users WHERE user_id = ?");
                                    $user_id_stmt->bind_param("i", $target_user_id);
                                    $user_id_stmt->execute();
                                    $user_id_stmt_result = $user_id_stmt->get_result();
                                    if ($user_id_stmt_result->num_rows > 0) {
                                        $target_user_fname_result = $user_id_stmt_result->fetch_assoc();
                                        $target_user_fname = $target_user_fname_result['user_firstname'];
                                        $target_user_lname = $target_user_fname_result['user_lastname'];
                                    }

                                    $is_editing = ($selected_user_id == $target_user_id);
                                    
                                    $display_name = strtoupper($target_user_fname) . " " . strtoupper($target_user_lname);

                                    echo "<tr>";
                                    echo "<td>" . $display_name . "</td>";

                                    #MEMBER ROLE
                                    echo "<td>";
                                    if ($is_editing) {
                                        #PHP will make an array for member_role with target user ids
                                        echo "<input type='text' name='member_role[$target_user_id]' value='$target_user_member_role'>";
                                    }
                                    else {
                                        $display_role = strtoupper($target_user_member_role);
                                        echo "$display_role";
                                    }
                                    echo "</td>";

                                    if ($role !== 'standard') {
                                    #MEMBER HOURLY RATE
                                        echo "<td>";
                                            if ($is_editing) {
                                                #PHP will make an array for hourly_rate with target user ids
                                                echo "<input type='number' name='hourly_rate[$target_user_id]' min='0' value='$target_user_hourly_rate'>" ;
                                            }
                                            else {
                                                echo "$target_user_hourly_rate";
                                            }
                                        echo "</td>";

                                    //action button
                                    
                                        echo "<td>";
                                            if ($is_editing) {
                                                echo "<button type='submit' name='save' value='$target_user_id'>Save</button>";
                                            }
                                            else {
                                                echo "<button type='submit' name='edit' value='$target_user_id'>Edit</button>";
                                            }
                                        echo "</td>";
                                    }
                                    
                                    
                                    #echo "<td>" . $row['hourly_rate'] . "</td>";
                                    #echo "<td>EDIT|SAVE</td>";
                                    echo "</tr>";
                                }
                            }
                            $stmt->close();
                        ?>
                    </tbody>
                </table>
                <form>
            </div>
            
        </div>
    </div>
    <div class="footer-container"></div>
</body>
</html>