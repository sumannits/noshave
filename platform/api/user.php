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
}else if($service_type == 'team_page'){
        
    $user_id = $_POST['user_id'];
    $team_det_arr=array();
    $error_msg = "";
        
    if (isset($user_id) && $user_id!='') {
        if ($stmt = $mysqli->prepare("SELECT m_team_id FROM member WHERE m_id = ? LIMIT 1")) {
            $stmt->bind_param('i', $user_id);
            $stmt->execute();
            $stmt->store_result();
            $stmt->bind_result($m_team_id);
            $stmt->fetch();
            $stmt->close();
          
            if ($m_team_id != "0") {
                // Fetch team information
                if ($stmt1 = $mysqli->prepare("SELECT t_id, t_name, t_username, t_pic_0, t_page_title, t_page_description, t_page_goal FROM team WHERE t_id = ? LIMIT 1")) {
                  $stmt1->bind_param('i', $m_team_id);
                  $stmt1->execute();
                  $result = $stmt1->get_result();
                  $data_det = $result->fetch_assoc();
                  $data_det['t_page_link']=base_url.'/team/'.$data_det['t_username'];
                  $stmt->close();
                  $team_det_arr=$data_det;
                }
            }

            $is_team_owner=is_team_owner($user_id, $mysqli);
            $is_team_member=is_team_member($user_id, $mysqli);
            if($is_team_owner == true){
                $is_team_member = false;
            }
            $response = array(
                'status' => 'success',
                'is_team_owner' => $is_team_owner,
                'is_team_member' => $is_team_member,
                'team_details'=>$team_det_arr
            );
            echo json_encode($response); 
        }else{
            $response = array(
                'status' => 'failure',
                'fail_code' => '0',
                'reason' => 'Invalid user id.'
            );
            // print out response to stdout
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
}else if($service_type == 'organization_page'){
        
    $user_id = $_POST['user_id'];
    $team_det_arr=array();
    $error_msg = "";
        
    if (isset($user_id) && $user_id!='') {
        if ($stmt = $mysqli->prepare("SELECT m_org_id FROM member WHERE m_id = ? LIMIT 1")) {
            $stmt->bind_param('i', $user_id);
            $stmt->execute();
            $stmt->store_result();
            $stmt->bind_result($m_org_id);
            $stmt->fetch();
            $stmt->close();
          
            if ($m_org_id != "0") {
                // Fetch team information
                if ($stmt1 = $mysqli->prepare("SELECT o_id, o_name, o_username, o_pic_0, o_page_title, o_page_description, o_page_goal FROM org WHERE o_id = ? LIMIT 1")) {
                  $stmt1->bind_param('i', $m_org_id);
                  $stmt1->execute();
                  $result = $stmt1->get_result();
                  $data_det = $result->fetch_assoc();
                  $data_det['o_page_link']=base_url.'/org/'.$data_det['o_username'];
                  $stmt->close();
                  $team_det_arr=$data_det;
                }
            }

            $is_team_owner=is_org_owner($user_id, $mysqli);
            $is_team_member=is_org_member($user_id, $mysqli);
            if($is_team_owner == true){
                $is_team_member = false;
            }
            $response = array(
                'status' => 'success',
                'is_org_owner' => $is_team_owner,
                'is_org_member' => $is_team_member,
                'org_details'=>$team_det_arr
            );
            echo json_encode($response); 
        }else{
            $response = array(
                'status' => 'failure',
                'fail_code' => '0',
                'reason' => 'Invalid user id.'
            );
            // print out response to stdout
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
}else if($service_type == 'donation_page'){
        
    $user_id = $_POST['user_id'];
    $per_don_arr=array();
    $team_don_arr=array();
    $org_don_arr=array();
    $error_msg = "";
        
    if (isset($user_id) && $user_id!='') {
        if ($stmt = $mysqli->prepare("SELECT m_team_id, m_org_id,m_team_editor,m_org_editor FROM member WHERE m_id = ? LIMIT 1")) {
            $stmt->bind_param('i', $user_id);
            $stmt->execute();
            $stmt->store_result();
            $stmt->bind_result($m_team_id,$m_org_id, $m_team_editor,$m_org_editor);
            $stmt->fetch();
            $stmt->close();

            // GET PERSONAL DONATIONS
            if ($stmt1 = $mysqli->prepare("SELECT d_id, d_time, d_name, d_email, d_amount, d_message, d_anonymous, d_message_on_page, d_thank_you_sent FROM donation WHERE d_classifier = 1 AND d_classifier_id = ? AND d_verified_payment = 1")) {
                $stmt1->bind_param('s', $user_id);
                $stmt1->execute();
                $stmt1->bind_result($d_id, $d_time, $d_name, $d_email,$d_amount, $d_message, $d_anonymous, $d_message_on_page, $d_thank_you_sent);
              
                // donation table
                $donation_count = 0;
                while ($stmt1->fetch()) {
                    $donation_count = $donation_count + 1;
                    if ($d_anonymous == 1) {
                      $d_name = "Anonymous";
                    }
                    $perbind_arr_donation = array('donation_count'=>$donation_count, 'd_time'=> date('F j, Y', strtotime($d_time . '- 5 hours')), 'd_name'=>$d_name, 'd_amount'=>$d_amount,'d_message'=>$d_message);
                    array_push($per_don_arr,$perbind_arr_donation);
                }
                $stmt1->close();
            }

            if ($m_team_editor == 1) {
                if ($stmt2 = $mysqli->prepare("SELECT d_time, d_name, d_email, d_amount, d_message, d_anonymous, d_message_on_page FROM donation WHERE d_classifier = 2 AND d_classifier_id = ? AND d_verified_payment = 1")) {
                    $stmt2->bind_param('s', $m_team_id);
                    $stmt2->execute();
                    $stmt2->bind_result($d_time, $d_name, $d_email,$d_amount, $d_message, $d_anonymous, $d_message_on_page);

                    // donation table
                    $team_donation_count = 0;
                    while ($stmt2->fetch()) {
                        $team_donation_count = $team_donation_count + 1;
                        if ($d_anonymous == 1) {
                            $d_name = "Anonymous";
                        }
                        $teambind_arr_donation = array('donation_count'=>$team_donation_count, 'd_time'=> date('F j, Y', strtotime($d_time . '- 5 hours')), 'd_name'=>$d_name, 'd_amount'=>$d_amount,'d_message'=>$d_message);
                        array_push($team_don_arr,$teambind_arr_donation);
                    }
                    $stmt2->close();
                }
            }

            if ($m_org_editor == 1) {
                if ($stmt3 = $mysqli->prepare("SELECT d_time, d_name, d_email, d_amount, d_message, d_anonymous, d_message_on_page FROM donation WHERE d_classifier = 3 AND d_classifier_id = ? AND d_verified_payment = 1")) {
                    $stmt3->bind_param('s', $m_org_id);
                    $stmt3->execute();
                    $stmt3->bind_result($d_time, $d_name, $d_email,$d_amount, $d_message, $d_anonymous, $d_message_on_page);

                    // donation table
                    $org_donation_count = 0;
                    while ($stmt3->fetch()) {
                        $org_donation_count = $org_donation_count + 1;
                        if ($d_anonymous == 1) {
                            $d_name = "Anonymous";
                        }
                        $orgbind_arr_donation = array('donation_count'=>$org_donation_count, 'd_time'=> date('F j, Y', strtotime($d_time . '- 5 hours')), 'd_name'=>$d_name, 'd_amount'=>$d_amount,'d_message'=>$d_message);
                        array_push($org_don_arr,$orgbind_arr_donation);
                    }
                    $stmt3->close();
                }
            }

            $response = array(
                'status' => 'success',
                'personal_donation' => $per_don_arr,
                'team_donation' => $team_don_arr,
                'org_donation'=>$org_don_arr
            );
            echo json_encode($response); 
        }else{
            $response = array(
                'status' => 'failure',
                'fail_code' => '0',
                'reason' => 'Invalid user id.'
            );
            // print out response to stdout
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
}else if($service_type == 'signup'){
        
    $full_name = $_POST['full_name'];
    $email_address = $_POST['email_address'];
    $password = $_POST['password'];
    $password_verify = $_POST['password_verify'];
    $username = $_POST['username'];
    $city = $_POST['city'];
    $state = $_POST['state'];
    $country = $_POST['country'];
    $t_id = 0;
    $o_id = 0;
    $error_msg = "";
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
                    'status' => 'success',
                    'user_id' => $stmt->insert_id
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

}else if($service_type == 'braintreeclientToken'){
    include_once '../includes/brain.php';

    $clientToken = Braintree_ClientToken::generate();
    $response = array(
        'status' => 'success',
        'clientToken' => $clientToken
    );
    echo json_encode($response);
}else if($service_type == 'dashboard_page'){
        
    $user_id = $_POST['user_id'];
    $donation_count = 0;
    $total_raised = 0;
    $m_page_goal = 0;
    $error_msg = "";
        
    if (isset($user_id) && $user_id!='') {
        if ($stmt1 = $mysqli->prepare("SELECT m_page_goal FROM member WHERE m_id = ? LIMIT 1")) {
            $stmt1->bind_param('i', $user_id);
            $stmt1->execute();
            $stmt1->store_result();
            $stmt1->bind_result($m_page_goal);
            $stmt1->fetch();
            $stmt1->close();
        }

        if ($stmt = $mysqli->prepare("SELECT d_id, d_time, d_name, d_email, d_amount, d_message, d_anonymous, d_message_on_page, d_thank_you_sent FROM donation WHERE d_classifier = 1 AND d_classifier_id = ? AND d_verified_payment = 1")) {
            $stmt->bind_param('s', $user_id);
            $stmt->execute();
            $stmt->bind_result($d_id, $d_time, $d_name, $d_email,$d_amount, $d_message, $d_anonymous, $d_message_on_page, $d_thank_you_sent);
            while ($stmt->fetch()) {
                $donation_count = $donation_count + 1;
                $total_raised += $d_amount;
            }

            $stmt->close();

            $goal_percentage = ceil(($total_raised / $m_page_goal) * 100) . "%";
            if ($goal_percentage > 100) {
                $goal_percentage = "100%+";
            }
            $response = array(
                'status' => 'success',
                'donations' => $donation_count,
                'raised' => number_format($total_raised),
                'goal_percentage'=>$goal_percentage,
                'goal_amt'=>number_format($m_page_goal)
            );
            echo json_encode($response);             
        }else{
            $response = array(
                'status' => 'failure',
                'fail_code' => '0',
                'reason' => 'Invalid user id.'
            );
            // print out response to stdout
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
}else if($service_type == 'leaderboard_page'){
    $member_count = 0;
    $member_arr=array();
    $team_arr=array();
    $org_arr=array();
    if ($stmt = $mysqli->prepare("SELECT total_raised, total_members, total_teams, total_orgs, top_members, top_teams, top_orgs FROM leaderboard")) {
        $stmt->execute();
        //print_r($stmt);
        $stmt->store_result();
        $stmt->bind_result($total_raised, $total_members, $total_teams, $total_orgs, $member_table, $team_table, $org_table);
        $stmt->fetch();
        $stmt->close();
        //member leaderboard
        if ($stmt = $mysqli->prepare("SELECT m_username, m_full_name, m_profile_pic, sum(d_amount) FROM donation, member WHERE m_id = d_classifier_id GROUP BY d_classifier_id ORDER BY sum(d_amount) DESC LIMIT 10")) {
            $stmt->execute();
            $stmt->store_result();
            $stmt->bind_result($m_username, $m_full_name, $m_profile_pic, $m_total_raised);
            while ($stmt->fetch()) {
                $member_count += 1;
                $bind_arr_member = array('member_count'=>$member_count, 'm_profile_pic'=>$m_profile_pic,'m_username'=>$m_username,'m_full_name'=>$m_full_name,'m_total_raised'=>number_format($m_total_raised));
                array_push($member_arr,$bind_arr_member);
            }
            $stmt->close();
        }
        //Team
        if($team_table!=''){
            $exp_str_team = explode('</tr>', $team_table);
            foreach($exp_str_team as $val){
                $exp_str_price = explode('<h3 class="donation-green">', $val);
                $exp_str_team_name = explode('</a>', $val);
                $exp_str_team_uname = explode('<a href="', $exp_str_team_name[0]);
                $get_name = explode('">', $exp_str_team_uname[1]);
                $get_uname_exp = explode('/', $get_name[0]);
                
                $team_price=strip_tags($exp_str_price[1]);
                $price_string = trim(preg_replace('/\s\s+/', ' ', $team_price));
                $bind_arr_team = array('team_name'=>$get_name[1], 'team_price'=>$price_string,'team_uname'=>end($get_uname_exp));
                if($get_name[1]!=''){
                    array_push($team_arr,$bind_arr_team);
                }
            }
        }
        // Organization
        if($org_table!=''){
            $exp_str_team = explode('</tr>', $org_table);
            foreach($exp_str_team as $val){
                $exp_str_price = explode('<h3 class="donation-green">', $val);
                $exp_str_team_name = explode('</a>', $val);
                $exp_str_team_uname = explode('<a href="', $exp_str_team_name[0]);
                $get_name = explode('">', $exp_str_team_uname[1]);
                $get_uname_exp = explode('/', $get_name[0]);
                
                $team_price=strip_tags($exp_str_price[1]);
                $price_string = trim(preg_replace('/\s\s+/', ' ', $team_price));
                $bind_arr_org = array('org_name'=>$get_name[1], 'org_price'=>$price_string,'org_uname'=>end($get_uname_exp));
                if($get_name[1]!=''){
                    array_push($org_arr,$bind_arr_org);
                }
            }
        }

        $response = array(
            'status' => 'success',
            'member_arr' => $member_arr,
            'team_arr' => $team_arr,
            'org_arr'=>$org_arr
        );
        echo json_encode($response);             
    }else{
        $response = array(
            'status' => 'failure',
            'fail_code' => '0',
            'reason' => 'Invalid user id.'
        );
        // print out response to stdout
        echo json_encode($response);  
    }
    
}
?>
