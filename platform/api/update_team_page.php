<?php
// This API endpoint will return the update the users information

// includes necessary to make the db call
include_once '../includes/db_connect.php';
include_once '../includes/psl-config.php';
include_once '../includes/functions.php';
sec_session_start();
$api_user_id=isset($_POST['user_id'])?$_POST['user_id']:'';
if($api_user_id!=''){
    $m_id = $api_user_id;
}else{
    $m_id = $_SESSION['user_id'];
}

// get the POST vars
$t_name = $_POST['t_name'];
$t_username = $_POST['t_username'];
$t_page_title = $_POST['t_page_title'];
$t_page_goal = $_POST['t_page_goal'];
$t_page_description = $_POST['t_page_description'];

$error_msg = "";

if (isset($m_id, $_POST['t_name'], $_POST['t_username'], $_POST['t_page_title'], $_POST['t_page_goal'], $_POST['t_page_description'])) {

    // Grab existing vars
    if ($stmt = $mysqli->prepare("SELECT m_team_id FROM member WHERE m_id = ? LIMIT 1")) {
        $stmt->bind_param('i', $m_id);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($t_id);
        $stmt->fetch();

        if ($stmt->num_rows == 1) {
            // user exists, good - no error, we have the values

        } else {
            // FAIL!
            $error_msg .= "It looks like we couldn't pull up your information.  Please try again or contact support. ";
                       
        }

    } else {
        // silent fail
        $error_msg .= "It looks like we couldn't pull up your information.  Please try again or contact support.";

    }

    // Grab existing vars
    if ($stmt = $mysqli->prepare("SELECT t_username FROM team WHERE t_id = ? LIMIT 1")) {
        $stmt->bind_param('i', $t_id);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($t_username_existing);
        $stmt->fetch();

        if ($stmt->num_rows == 1) {
            // user exists, good - no error, we have the values

        } else {
            // FAIL!
            $error_msg .= "It looks like we couldn't pull up your information.  Please try again or contact support. ";
                       
        }

    } else {
        // silent fail
        $error_msg .= "It looks like we couldn't pull up your information.  Please try again or contact support.";

    }

    if ($t_name == ""){
        $error_msg .= 'Please enter a team name. ';
    }

    if ($t_username == ""){
        $error_msg .= 'Please enter a team username. ';
    }

    if ($t_page_title == ""){
        $error_msg .= 'Please enter a team page title. ';
    }

    if ($t_page_goal == ""){
        $error_msg .= 'Please enter a team fundraising goal. ';
    }

    if ($t_page_description == ""){
        $error_msg .= 'Please enter a team page description. ';
    }

    // check username pattern
    if(preg_match("/^([_A-z0-9]){1,}$/",$t_username)){
        // matched - no issue

    } else {
        // didn't match
        $error_msg .= 'Team usernames should only contain letters, numbers, and underscores. ';

    }

    // Check username server side
    if ($t_username == $t_username_existing) {
        // do nothing

    } else {

        if ($stmt = $mysqli->prepare("SELECT t_id FROM team WHERE t_username = ? LIMIT 1")) {
            $stmt->bind_param('s', $t_username);
            
            // execute the insert
            if (!$stmt->execute()) {
                // silentyl allow it, I guess? fudge.

            } else {
                // it succeeded
                $stmt->store_result();

                if ($stmt->num_rows == 1) {

                    $error_msg .= "The team username " . $t_username . " is already in use by another team. ";

                } else {
                    // no big deal
                }

            }

        } else {

            // fail
            $error_msg .= "Somethings not right. We couldn't check to see if that team username has been used before.  Please try again or contact support. ";

        }
    }

    // LEGGO
    if (empty($error_msg)){
        // success, let's update the DB
        $one = "1";

        // let's grab what we can based on the email
        if ($stmt = $mysqli->prepare("UPDATE team SET t_name = ?, t_username = ?, t_page_title = ?, t_page_goal = ?, t_page_description = ? WHERE t_id = ?")) {
            $stmt->bind_param('sssisi', $t_name, $t_username, $t_page_title, $t_page_goal, $t_page_description, $t_id);
            
            // execute the insert
            if (!$stmt->execute()) {
                // it failed
                $response = array(
                    'status' => 'failure',
                    'fail_code' => '1',
                    'reason' => 'Database error: Unable to update team.'
                );

                echo json_encode($response);  
            } else {
                // it succeeded
                $response = array(
                    'status' => 'success',
                    'msg' => 'You have successfully edit your team.'
                );

                echo json_encode($response);
            }

        } else {
            // it failed
            $response = array(
                'status' => 'failure',
                'fail_code' => '2',
                'reason' => 'Database error: Unable to update team.'
            );

            echo json_encode($response);
        }
    } else {
        // there was an error with the input
        $response = array(
            'status' => 'failure',
            'fail_code' => '3',
            'reason' => $error_msg
        );

        echo json_encode($response);
    }

} else {
    // POST vars not provided
    $response = array(
        'status' => 'failure',
        'fail_code' => '0',
        'reason' => 'One or more fields was not provided.'
    );

    // print out response to stdout
    echo json_encode($response);  
}
?>
