<?php
// This API endpoint will return the users existing information.

// includes necessary to make the db call
include_once '../includes/db_connect.php';
include_once '../includes/psl-config.php';
include_once '../includes/email.php';

// get the POST vars
$full_name = $_POST['full_name'];
$email_address = $_POST['email_address'];
$password = $_POST['password'];
$password_verify = $_POST['password_verify'];
$username = $_POST['username'];
$city = $_POST['city'];
$state = $_POST['state'];
$country = $_POST['country'];
$recaptcha = $_POST["recaptcha"];
$t_id = $_POST['t_id'];

$error_msg = "";

if (isset($_POST['full_name'], $_POST['email_address'], $_POST['password'], $_POST['password_verify'], $_POST['username'], $_POST['city'], $_POST['state'], $_POST['country'], $_POST["recaptcha"], $_POST['t_id'])){

    if ($full_name == ""){
        $error_msg .= 'Please enter your full name. ';
    }

    if ($email_address == ""){
        $error_msg .= 'Please enter your email address. ';
    }

    if ($password == ""){
        $error_msg .= 'Please enter a password. ';
    }

    if ($password_verify == ""){
        $error_msg .= 'Please verify your password. ';
    }

    if ($username == ""){
        $error_msg .= 'Please enter a username. ';
    }

    if ($city == ""){
        $error_msg .= 'Please enter your city. ';
    }

    if ($country == ""){
        $error_msg .= 'Please enter your country. ';
    }

    // check username pattern
    if(preg_match("/^([_A-z0-9]){1,}$/",$username)){
        // matched - no issue

    } else {
        // didn't match
        $error_msg .= 'Usernames should only contain letters, numbers, and underscores. ';

    }

    // PASSWORD length TODO THIS DOESN'T WORK
    if (strlen($password) <= 5){
        // password needs to be longer
        $error_msg .= 'Please enter a password that is six or more characters in length. ';
    }

    // PASSWORD
    $hash = password_hash($password, PASSWORD_BCRYPT);

    if ($password != $password_verify){
        $error_msg .= 'The passwords your entered do not match. Please try again. ';
    }

    // RECAPCHTA
    if(!$recaptcha){
        $error_msg .= 'You did not verify that you are a human, please try again using the reCAPTCHA. ';
    }
    else{
        $response=file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=6LfQi2gUAAAAANH-6ujMKSqhioxWB8EJi7nwrIkB&response=" . $recaptcha . "&remoteip=" . $_SERVER['REMOTE_ADDR']);
        if (strpos($response,'true') === false) {
            $error_msg .= 'Robot? Nah? The reCapchta failed, please try again. ';
        }
    }

    // EMAIL
    if (!filter_var($email_address, FILTER_VALIDATE_EMAIL)) {
        // Not a valid email
        $error_msg .= 'This email entered is invalid.  How do I know?  Because I have a particular set of skills. ';
    }

    // Check email server side
    if ($stmt = $mysqli->prepare("SELECT m_id FROM member WHERE m_email = ? LIMIT 1")) {
        $stmt->bind_param('s', $email_address);
        
        // execute the insert
        if (!$stmt->execute()) {
            // silentyl allow it, I guess? fudge.

        } else {
            // it succeeded
            $stmt->store_result();

            if ($stmt->num_rows == 1) {

                $error_msg .= "The email address " . $email_address . " is already registered. ";

            } else {
                // no big deal
            }

        }

    } else {

        // fail
        $error_msg .= "Somethings not right. We couldn't check to see if that email has been used before.  Please try again or contact support. ";

    }

    // Check username server side
    if ($stmt = $mysqli->prepare("SELECT m_id FROM member WHERE m_username = ? LIMIT 1")) {
        $stmt->bind_param('s', $username);
        
        // execute the insert
        if (!$stmt->execute()) {
            // silentyl allow it, I guess? fudge.

        } else {
            // it succeeded
            $stmt->store_result();

            if ($stmt->num_rows == 1) {

                $error_msg .= "The username " . $username . " is already registered. ";

            } else {
                // no big deal
            }

        }

    } else {

        // fail
        $error_msg .= "Somethings not right. We couldn't check to see if that username has been used before.  Please try again or contact support. ";

    }

    // Grab teams ORG if need be
    if ($t_id != 0){
        
        // Grab org it we can
        if ($stmt_org = $mysqli->prepare("SELECT t_org_id FROM team WHERE t_id = ? LIMIT 1")) {
            $stmt_org->bind_param('i', $t_id);
            $stmt_org->execute();
            $stmt_org->store_result();
            $stmt_org->bind_result($o_id);
            $stmt_org->fetch();

            if ($stmt_org->num_rows == 1) {

                // user exists, good - no error, we have the values

            } else {

                // no dice
                $o_id = 0;
  
            }

        } else {
            
            // no dice
            $o_id = 0;

        }
    } else {
        $o_id = 0;
    }

    // LEGGO
    if (empty($error_msg)){
        // success, let's update the DB
        $one = "1";

        // let's grab what we can based on the email
        if ($stmt = $mysqli->prepare("INSERT INTO member (m_full_name, m_email, m_username, m_password, m_city, m_state, m_country, m_2017, m_team_id, m_org_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)")) {
            $stmt->bind_param('sssssssiii', $full_name, $email_address, $username, $hash, $city, $state, $country, $one, $t_id, $o_id);
            
            // execute the insert
            if (!$stmt->execute()) {
                // it failed
                $response = array(
                    'status' => 'failure',
                    'fail_code' => '1',
                    'reason' => 'Database error: Unable to create account.'
                );

                echo json_encode($response);  
            } else {

                // perpare email
                $message_subject = "Welcome to No-Shave November!";
                $message_body = 'Greetings ' . $full_name . ',

                                You have officially registered for No-Shave November 2017! Thank you for joining our efforts in raising cancer awareness and for starting your hairy journey with us. We can’t wait to see what this November brings!

                                You can view your personal fundraising page here: '.base_url.'/member/' . $username . '
                                ';

                // send the email
                send_email($message_subject, $message_body, $email_address, 0);

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
                'reason' => 'Database error: Unable to create account.'
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
