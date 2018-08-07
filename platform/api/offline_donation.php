<?php
// This API endpoint will return the update the users information

// includes necessary to make the db call
include_once '../includes/db_connect.php';
include_once '../includes/psl-config.php';
include_once '../includes/functions.php';
sec_session_start();

$m_id = $_SESSION['user_id'];

// get the POST vars
$donation_amount = $_POST['donation_amount'];
$donation_name = $_POST['donation_name'];
$donation_company = $_POST['donation_company'];
$donation_message = $_POST['donation_message'];
$donation_visbile = $_POST['donation_visbile'];
$donation_attribution = $_POST['donation_attribution'];

$error_msg = "";

if (isset($m_id,$_POST['donation_amount'], $_POST['donation_name'], $_POST['donation_company'], $_POST['donation_message'], $_POST['donation_visbile'], $_POST['donation_attribution'])) {

    // Grab existing vars
    if ($stmt = $mysqli->prepare("SELECT m_team_id, m_org_id FROM member WHERE m_id = ? LIMIT 1")) {
        $stmt->bind_param('s', $m_id);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($m_team_id, $m_org_id);
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

    if ($donation_amount == ""){
        $error_msg .= 'Please enter a donation amount. ';
    }

    if ($donation_name == ""){
        $error_msg .= 'Please enter a name for the donation. ';
    }

    if ($donation_visbile == ""){
        $error_msg .= 'Please set the donation visibility. ';
    }

    if ($donation_attribution == ""){
        $error_msg .= 'Please set where the donation should go. ';
    }


    // if (!is_int($donation_amount)) {
    //     $error_msg .= 'Please enter a valid donation amount. ';
    // }

    if ($donation_attribution == 3){
        $d_classifier_id = $m_org_id;
    } else if ($donation_attribution == 2){
        $d_classifier_id = $m_team_id;
    } else {
        $d_classifier_id = $m_id;
    }

    // LEGGO
    if (empty($error_msg)){
        // success, let's update the DB
        $one = "1";

        // donation pin
        $donation_pin = mt_rand(100000, 999999);

        // bogey email
        $do_not_mail = "null";

        // let's grab what we can based on the email
        if ($stmt = $mysqli->prepare("INSERT INTO donation (d_classifier, d_classifier_id, d_pin, d_name, d_display_name, d_company, d_message, d_amount, d_email, d_visible_on_page, d_manual, d_verified_payment) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)")) {
            $stmt->bind_param('iiissssisiii', $donation_attribution, $d_classifier_id, $donation_pin, $donation_name, $donation_name, $donation_company, $donation_message, $donation_amount, $do_not_mail, $donation_visbile, $one, $one);
            
            // execute the insert
            if (!$stmt->execute()) {
                // it failed
                $response = array(
                    'status' => 'failure',
                    'fail_code' => '1',
                    'reason' => 'Database error: Unable to add offline contribution.'
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
                'reason' => 'Database error: Unable to add offline contribution.'
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
