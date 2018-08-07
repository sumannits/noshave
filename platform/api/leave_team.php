<?php
// This API endpoint will return the update the users information

// includes necessary to make the db call
include_once '../includes/db_connect.php';
include_once '../includes/psl-config.php';
include_once '../includes/functions.php';
sec_session_start();

$m_id = $_SESSION['user_id'];

$error_msg = "";

if ($m_id == ""){
    $error_msg .= 'Please sign in to make this request. ';
}

// LEGGO
if (empty($error_msg)){
    // success, let's update the DB
    $zero = "0";

    // let's grab what we can based on the email
    if ($stmt = $mysqli->prepare("UPDATE member SET m_team_id = ?, m_org_id = ? WHERE m_id = ?")) {
        $stmt->bind_param('iii', $zero, $zero, $m_id);
        
        // execute the insert
        if (!$stmt->execute()) {
            // it failed
            $response = array(
                'status' => 'failure',
                'fail_code' => '1',
                'reason' => 'Database error: Unable to leave team.'
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
            'reason' => 'Database error: Unable to leave team.'
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


?>
