<?php
// This API endpoint will return the update the users information

// includes necessary to make the db call
include_once '../includes/db_connect.php';
include_once '../includes/psl-config.php';
include_once '../includes/functions.php';
sec_session_start();

$m_id = $_SESSION['user_id'];

// get the POST vars
$m_full_name = $_POST['m_full_name'];
$m_email = $_POST['m_email'];
$m_city = $_POST['m_city'];
$m_state = $_POST['m_state'];
$m_country = $_POST['m_country'];
$m_got_screen = $_POST['m_got_screen'];

$error_msg = "";

if (isset($m_id,$_POST['m_full_name'], $_POST['m_email'], $_POST['m_city'], $_POST['m_state'], $_POST['m_country'], $_POST['m_got_screen'])) {

    // Grab existing vars
    if ($stmt = $mysqli->prepare("SELECT m_email FROM member WHERE m_id = ? LIMIT 1")) {
        $stmt->bind_param('s', $m_id);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($m_email_existing);
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

    if ($m_full_name == ""){
        $error_msg .= 'Please enter your full name. ';
    }

    if ($m_email == ""){
        $error_msg .= 'Please senter your email address. ';
    }

    if ($m_city == ""){
        $error_msg .= 'Please enter your city. ';
    }

    if ($m_country == ""){
        $error_msg .= 'Please enter your country. ';
    }

    if ($m_got_screen == ""){
        $error_msg .= 'Please specify whether or not you got a cancer screening. ';
    }

    // scrub email server side
    if (!filter_var($m_email, FILTER_VALIDATE_EMAIL)) {
        // Not a valid email
        $error_msg .= 'This email entered is invalid.  How do I know?  Because I have a particular set of skills. ';
    }

    // Check username server side
    if ($m_email == $m_email_existing) {
        // do nothing

    } else {

        if ($stmt = $mysqli->prepare("SELECT m_id FROM member WHERE m_email = ? LIMIT 1")) {
            $stmt->bind_param('s', $m_email);
            
            // execute the insert
            if (!$stmt->execute()) {
                // silentyl allow it, I guess? fudge.

            } else {
                // it succeeded
                $stmt->store_result();

                if ($stmt->num_rows == 1) {

                    $error_msg .= "The email, " . $m_email . ", is already in-use by another member. ";

                } else {
                    // no big deal
                }

            }

        } else {

            // fail
            $error_msg .= "Somethings not right. We couldn't check to see if that username has been used before.  Please try again or contact support. ";

        }
    }

    // LEGGO
    if (empty($error_msg)){
        // success, let's update the DB
        $one = "1";

        // let's grab what we can based on the email
        if ($stmt = $mysqli->prepare("UPDATE member SET m_full_name = ?, m_email = ?, m_city = ?, m_state = ?, m_country = ?, m_got_screen = ? WHERE m_id = ?")) {
            $stmt->bind_param('sssssii', $m_full_name, $m_email, $m_city, $m_state, $m_country, $m_got_screen, $m_id);
            
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
