<?php
// This API endpoint will return the update the users information

// includes necessary to make the db call
//include_once '../includes/db_connect.php';
//include_once '../includes/psl-config.php';
include_once '../includes/functions.php';
sec_session_start();

$m_id = isset($_SESSION['user_id'])?$_SESSION['user_id']:'';

// get the POST vars
//$o_name = $_POST['o_name'];
$service_type = isset($_POST['service_type'])?$_POST['service_type']:'';
if($service_type == 'login'){
    if (isset($_POST['email_or_username'], $_POST['password'])) {
        $email_or_username = $_POST['email_or_username'];
        $password = $_POST['password']; // passwurdeee -- it's hashed in DB
    
        $login_status = login($email_or_username, $password, $mysqli);  

        if ($login_status == 1) {
            $response = array(
                'status' => 'success',
                'user_id' => $_SESSION['user_id']
            );

        } elseif ($login_status == 0) {
            $response = array(
                'status' => 'failure',
                'fail_code' => '1',
                'reason' => 'Oops-A-Daisy! Incorrect email or password entered.  Please try again.'
            );
        } else {
            $response = array(
                'status' => 'failure',
                'fail_code' => '1',
                'reason' => 'Oops-A-Daisy! Incorrect email or password entered.  Please try again.'
            );
        }
        echo json_encode($response);
    } else {
        // it failed
        $response = array(
            'status' => 'failure',
            'fail_code' => '1',
            'reason' => 'Please provide email and password!'
        );
        echo json_encode($response);
    }
    
}else if($service_type == 'user_details'){
    if (isset($_POST['user_id'])) {
        $user_id = $_POST['user_id'];
        if ($stmt = $mysqli->prepare("SELECT m_full_name, m_id, m_email, m_username, m_team_id, m_org_id, m_team_editor, m_org_editor, m_city, m_state, m_country, m_display_location, m_location_format, m_profile_pic, m_pic_0, m_pic_1, m_pic_2, m_pic_3, m_pic_4, m_pic_5, m_pic_6, m_pic_7, m_pic_8, m_pic_9, m_page_title, m_page_description, m_page_goal, m_got_screen FROM member WHERE m_id = ? LIMIT 1")) {
            $stmt->bind_param('i', $user_id);
            $stmt->execute();
            
            //$stmt->store_result();
            //$stmt->bind_result( $m_city, $m_state, $m_country, $m_display_location, $m_location_format);
            $result = $stmt->get_result();
            $user_det = $result->fetch_assoc();

            // set these so it doesn't complain
            $city_state = "";
            $city_country = "";
            $city_only = "";
            $country_only = "";

            if ($user_det['m_location_format'] == 0) {

            $personal_location = $user_det['m_city'] . ", " . $user_det['m_state'];
            $city_state = "checked";

            } elseif ($user_det['m_location_format'] == 1) {

            $personal_location = $user_det['m_city'] . ", " . $user_det['m_country'];
            $city_country = "checked";

            } elseif ($user_det['m_location_format'] == 2) {

            $personal_location = $user_det['m_city'];
            $city_only = "checked";

            } elseif ($user_det['m_location_format'] == 3) {

            $personal_location = $user_det['m_country'];
            $country_only = "checked";

            } else {

            $personal_location = $user_det['m_country'];
            $country_only = "checked";

            }

            $user_det['personal_location']=$personal_location;
            //$stmt->fetch();
            $stmt->close();
            //print_r($myrow);
            $response = array(
                'status' => 'success',
                'user_details' => $user_det
            );
        } else {
            $response = array(
                'status' => 'failure',
                'fail_code' => '1',
                'reason' => 'Oops-A-Daisy! Incorrect user id.  Please try again.'
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
    
}
?>
