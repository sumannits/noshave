<?php
// This API endpoint will return the update the users information

// includes necessary to make the db call
include_once '../includes/db_connect.php';
include_once '../includes/psl-config.php';
include_once '../includes/functions.php';
sec_session_start();

$m_id = $_SESSION['user_id'];

// get the POST vars
$m_username_new = $_POST['m_username_new'];
$m_location_visibility = $_POST['m_location_visibility'];
$m_location_format = $_POST['m_location_format'];
$m_page_title = $_POST['m_page_title'];
$m_page_goal = $_POST['m_page_goal'];
$m_page_description = $_POST['m_page_description'];

$error_msg = "";

if (isset($m_id,$_POST['m_username_new'], $_POST['m_location_visibility'], $_POST['m_location_format'], $_POST['m_page_title'], $_POST['m_page_goal'], $_POST['m_page_description'])) {

    // Grab existing vars
    if ($stmt = $mysqli->prepare("SELECT m_username FROM member WHERE m_id = ? LIMIT 1")) {
        $stmt->bind_param('s', $m_id);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($m_username);
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

    if ($m_username_new == ""){
        $error_msg .= 'Please enter a username. ';
    }

    if ($m_location_visibility == ""){
        $error_msg .= 'Please set your location visibility. ';
    }

    if ($m_location_format == ""){
        $error_msg .= 'Please set your location format. ';
    }

    if ($m_page_title == ""){
        $error_msg .= 'Please enter a page title. ';
    }

    if ($m_page_goal == ""){
        $error_msg .= 'Please enter a fundraising goal. ';
    }

    if ($m_page_description == ""){
        $error_msg .= 'Please enter a page description. ';
    }

    // check username pattern
    if(preg_match("/^([_A-z0-9]){1,}$/",$m_username_new)){
        // matched - no issue

    } else {
        // didn't match
        $error_msg .= 'Usernames should only contain letters, numbers, and underscores. ';

    }

    // Check username server side
    if ($m_username_new == $m_username) {
        // do nothing

    } else {

        if ($stmt = $mysqli->prepare("SELECT m_id FROM member WHERE m_username = ? LIMIT 1")) {
            $stmt->bind_param('s', $m_username_new);
            
            // execute the insert
            if (!$stmt->execute()) {
                // silentyl allow it, I guess? fudge.

            } else {
                // it succeeded
                $stmt->store_result();

                if ($stmt->num_rows == 1) {

                    $error_msg .= "The username " . $m_username_new . " is already registered. ";

                } else {
                    // no big deal
                }

            }

        } else {

            // fail
            $error_msg .= "Somethings not right. We couldn't check to see if that username has been used before.  Please try again or contact support. ";

        }
    }

    // maintain newlines of description
    //$description = nl2br($m_page_description);

    // LEGGO
    if (empty($error_msg)){
        // success, let's update the DB
        $one = "1";

        // let's grab what we can based on the email
        if ($stmt = $mysqli->prepare("UPDATE member SET m_username = ?, m_display_location = ?, m_location_format = ?, m_page_title = ?, m_page_description = ?, m_page_goal = ? WHERE m_id = ?")) {
            $stmt->bind_param('siissii', $m_username_new, $m_location_visibility, $m_location_format, $m_page_title, $m_page_description, $m_page_goal, $m_id);
            
            // execute the insert
            if (!$stmt->execute()) {
                // it failed
                $response = array(
                    'status' => 'failure',
                    'fail_code' => '1',
                    'reason' => 'Database error: Unable to update account information.'
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
                'reason' => 'Database error: Unable to update information.'
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
