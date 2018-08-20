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
$o_name = $_POST['o_name'];
$o_username = $_POST['o_username'];
$o_page_title = $_POST['o_page_title'];
$o_page_goal = $_POST['o_page_goal'];
$o_page_description = $_POST['o_page_description'];

$error_msg = "";

if (isset($m_id, $_POST['o_name'], $_POST['o_username'], $_POST['o_page_title'], $_POST['o_page_goal'], $_POST['o_page_description'])) {

    // Grab existing vars
    if ($stmt = $mysqli->prepare("SELECT m_org_id FROM member WHERE m_id = ? LIMIT 1")) {
        $stmt->bind_param('i', $m_id);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($o_id);
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
    if ($stmt = $mysqli->prepare("SELECT o_username FROM org WHERE o_id = ? LIMIT 1")) {
        $stmt->bind_param('i', $o_id);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($o_username_existing);
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

    if ($o_name == ""){
        $error_msg .= 'Please enter an organization name. ';
    }

    if ($o_username == ""){
        $error_msg .= 'Please enter an organization username. ';
    }

    if ($o_page_title == ""){
        $error_msg .= 'Please enter an organization page title. ';
    }

    if ($o_page_goal == ""){
        $error_msg .= 'Please enter an organization fundraising goal. ';
    }

    if ($o_page_description == ""){
        $error_msg .= 'Please enter an organization page description. ';
    }

    // check username pattern
    if(preg_match("/^([_A-z0-9]){1,}$/",$o_username)){
        // matched - no issue

    } else {
        // didn't match
        $error_msg .= 'organization usernames should only contain letters, numbers, and underscores. ';

    }

    // Check username server side
    if ($o_username == $o_username_existing) {
        // do nothing

    } else {

        if ($stmt = $mysqli->prepare("SELECT o_id FROM org WHERE o_username = ? LIMIT 1")) {
            $stmt->bind_param('s', $o_username);
            
            // execute the insert
            if (!$stmt->execute()) {
                // silentyl allow it, I guess? fudge.

            } else {
                // it succeeded
                $stmt->store_result();

                if ($stmt->num_rows == 1) {

                    $error_msg .= "The organization username " . $o_username . " is already in use by another organization. ";

                } else {
                    // no big deal
                }

            }

        } else {

            // fail
            $error_msg .= "Somethings not right. We couldn't check to see if that organization username has been used before.  Please try again or contact support. ";

        }
    }

    // LEGGO
    if (empty($error_msg)){
        // success, let's update the DB
        $one = "1";

        // let's grab what we can based on the email
        if ($stmt = $mysqli->prepare("UPDATE org SET o_name = ?, o_username = ?, o_page_title = ?, o_page_goal = ?, o_page_description = ? WHERE o_id = ?")) {
            $stmt->bind_param('sssisi', $o_name, $o_username, $o_page_title, $o_page_goal, $o_page_description, $o_id);
            
            // execute the insert
            if (!$stmt->execute()) {
                // it failed
                $response = array(
                    'status' => 'failure',
                    'fail_code' => '1',
                    'reason' => 'Database error: Unable to update organization.'
                );

                echo json_encode($response);  
            } else {
                // it succeeded
                $response = array(
                    'status' => 'success'
                );

                echo json_encode($response);
            }

        } else {
            // it failed
            $response = array(
                'status' => 'failure',
                'fail_code' => '2',
                'reason' => 'Database error: Unable to update organization.'
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
