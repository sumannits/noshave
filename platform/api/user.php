<?php
include_once '../includes/functions.php';
include_once '../includes/email.php';
sec_session_start();
// get the POST vars
//$o_name = $_POST['o_name'];
$service_type = isset($_POST['service_type'])?$_POST['service_type']:'';
if($service_type == 'email_exist'){
    $email = $_POST['email'];
    if (isset($email)){
        // let's check it
        if ($stmt = $mysqli->prepare("SELECT m_email FROM member WHERE m_email = ? LIMIT 1")) {
            $stmt->bind_param('s', $email);
            $stmt->execute();
            $stmt->store_result();
            $stmt->bind_result($m_email);
            $stmt->fetch();
            if ($stmt->num_rows == 1) {
                // email exists
                $response = array(
                    'status' => 'success',
                    'email_exists' => 'true'
                );
                echo json_encode($response);
            } else {
                $response = array(
                    'status' => 'success',
                    'email_exists' => 'false'
                );
                echo json_encode($response);            
            }
        }
    } else {
        // POST vars not provided
        $response = array(
            'status' => 'failure',
            'fail_code' => '0',
            'reason' => 'Unable to lookup if email exists.'
        );

        // print out response to stdout
        echo json_encode($response);  
    }
}else if($service_type == 'donations'){
    // GET PERSONAL DONATIONS
    $personal_donation =array();
    if (isset($_POST['user_id'])) {
        $total_raised = 0;
        $donation_count = 0;
        $user_id = $_POST['user_id'];
        $m_page_goal = $_POST['m_page_goal'];
        $goal_percentage = '0 %';
        if ($stmt = $mysqli->prepare("SELECT d_id, d_time, d_name, d_email, d_amount, d_message, d_anonymous, d_message_on_page, d_thank_you_sent FROM donation WHERE d_classifier = 1 AND d_classifier_id = ? AND d_verified_payment = 1")) {
            $stmt->bind_param('s', $user_id);
            $stmt->execute();
            $stmt->bind_result($d_id, $d_time, $d_name, $d_email,$d_amount, $d_message, $d_anonymous, $d_message_on_page, $d_thank_you_sent);
            while ($stmt->fetch()) {
                $donation_count = $donation_count + 1;
                if ($d_anonymous == 1) {
                    $d_name = "Anonymous";
                }
                // add to total
                $total_raised += $d_amount;
                $bind_arr_donation = array('sl_no'=>$donation_count, 'name'=>$d_name,'donation_price'=>$d_amount,'date'=>date('F j, Y', strtotime($d_time . '- 5 hours')),'comment'=>$d_message);
                array_push($personal_donation,$bind_arr_donation);
            }
            //print_r($user_det);
            $stmt->close();
                    
            // Personal goal status
            // calculate goal
            $goal_percentage = ceil(($total_raised / $m_page_goal) * 100) . "%";

            if ($goal_percentage > 100) {
            $goal_percentage = "100%+";
            }

            $response = array(
                'status' => 'success',
                'personal_donation' => $personal_donation,
                'total_raised' => number_format($total_raised),
                'donation_count' => $donation_count,
                'goal_percentage' => $goal_percentage,
                'm_page_goal' => number_format($m_page_goal)
            );
        } else {
            $response = array(
                'status' => 'success',
                'personal_donation' => $personal_donation,
                'total_raised' => number_format($total_raised),
                'donation_count' => $donation_count,
                'goal_percentage' => $goal_percentage,
                'm_page_goal' => number_format($m_page_goal)
            );
        }
        echo json_encode($response);
    } else {
        // it failed
        $response = array(
            'status' => 'failure',
            'fail_code' => '1',
            'reason' => 'Please provide user id!'
        );
        echo json_encode($response);
    }
    
}else if($service_type == 'password_change'){
    //echo base_url;
    $change_type = $_POST['change'];
    if (isset($change_type) && $change_type == 'change'){
        $error_msg = "";
        $email = $_POST['email'];
        $password = $_POST['old_password'];
        $new_password = $_POST['new_password'];
        $old_password_hash = password_hash($password, PASSWORD_BCRYPT);
        $new_password_hash =  password_hash($new_password, PASSWORD_BCRYPT);

        // check against current password
        if ($stmt_donation = $mysqli->prepare("SELECT m_password FROM member WHERE m_email = ?")) {
        $stmt_donation->bind_param('s', $email);
        $stmt_donation->execute();
        $stmt_donation->store_result();
        $stmt_donation->bind_result($m_password);
        $stmt_donation->fetch();
        $stmt_donation->close();

        } else {
            $error_msg .= "Failed to check existing account password. ";
        }

        if (password_verify($password, $m_password)) {
            // worked
        } else {
            $error_msg .= "The old password you entered does not match your account password. ";
        }
        
        if (empty($error_msg)) {
            if ($insert_stmt = $mysqli->prepare("UPDATE member SET m_password = ? WHERE m_email = ?")) {
                $insert_stmt->bind_param('ss', $new_password_hash, $email);
                // Execute the prepared query.
                if (! $insert_stmt->execute()) {
                    $response = array(
                        'status' => 'failure',
                        'fail_code' => '0',
                        'reason' => 'Something wrong! Please try again.'
                    );
                    // print out response to stdout
                    echo json_encode($response); 
                }else{
                    $response = array(
                        'status' => 'success',
                        'msg' => 'You have successfully change your password.'
                    );
                    echo json_encode($response);
                }
            }
        }else{
            $response = array(
                'status' => 'failure',
                'fail_code' => '0',
                'reason' => $error_msg
            );
            // print out response to stdout
            echo json_encode($response);  
        }


    }else if (isset($change_type) && $change_type == 'forgot'){
        $error_msg = "";
        $email = $_POST['email'];
        function randStrGen($len){
            $result = "";
            $chars = "aAbBcCdDeEfFgGhHJkKmMnNpPqQrRsStTuUvVwWxXyYZz23456789";
            $charArray = str_split($chars);
            for($i = 0; $i < $len; $i++){
                $randItem = array_rand($charArray);
                $result .= "".$charArray[$randItem];
            }
            return $result;
        }

        // Usage example
        $random_password = randStrGen(8);
        $random_password_hash = password_hash($random_password, PASSWORD_BCRYPT);

        // See if the email exists in our DB
        $resultSet = $mysqli->query("SELECT m_id FROM member WHERE m_email = '" . $email . "' LIMIT 1");
        if($resultSet->num_rows != 0){
            $email_exists = 1;
        } else {
            $email_exists = 0;
        }


        if ($email_exists == 1) {

            if ($insert_stmt = $mysqli->prepare("UPDATE member SET m_password = ? WHERE m_email = ?")) {
                $insert_stmt->bind_param('ss', $random_password_hash, $email);
                // Execute the prepared query.
                if (! $insert_stmt->execute()) {
                    $response = array(
                        'status' => 'failure',
                        'fail_code' => '0',
                        'reason' => 'Something wrong! Please try again.'
                    );
                    // print out response to stdout
                    echo json_encode($response); 
                }
            }

            // SEND THE NEW PASSWORD
            $message_subject = "Your No-Shave November Password Has Been Reset!";
            $message_body = 'Please login to your account using the new password below: 

            <strong>' . $random_password . '</strong>

            To change your password, go to settings, select account, then select change password.<br><br>You can log back into your account by going to '.base_url.'/login.
            ';

            send_email($message_subject, $message_body, $email, 5);
            $response = array(
                'status' => 'success',
                'msg' => 'The new password successfully sent to your email. Please check your email.'
            );
            echo json_encode($response);
        } else {
            $response = array(
                'status' => 'failure',
                'fail_code' => '0',
                'reason' => 'Email does not exist.'
            );
            // print out response to stdout
            echo json_encode($response); 
        }        
    } else {
        // POST vars not provided
        $response = array(
            'status' => 'failure',
            'fail_code' => '0',
            'reason' => 'Something wrong! Please try again.'
        );
        // print out response to stdout
        echo json_encode($response);  
    }
}else if($service_type == 'update_account'){
        
    $m_id = $_POST['user_id'];
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
                        'status' => 'success',
                        'msg'=>'You have successfully update your account.'
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
}
?>
