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

$error_msg = "";

if ($m_id == ""){
    $error_msg .= 'Please sign in to make this request. ';
}

// check if the user has a team
if ($stmt = $mysqli->prepare("SELECT m_team_id FROM member WHERE m_id = ?")) {
  $stmt->bind_param('i', $m_id);
  $stmt->execute();
  $stmt->store_result();
  $stmt->bind_result($m_team_id);
  $stmt->fetch();
  $stmt->close();

} else {
  // unable to get data
  $m_team_id = 0;
}

// LEGGO
if (empty($error_msg)){
    // success, let's update the DB
    $zero = "0";

    // remove from team and all team members
    if ($stmt = $mysqli->prepare("UPDATE team SET t_org_id = ? WHERE t_id = ? LIMIT 1")) {
        $stmt->bind_param('ii', $zero, $m_team_id);

        if (!$stmt->execute()) {

            // Unable to make this call
            $error_msg .= "Failed to update the database. ";

        } else {
            
            // SUCCESS

        }

    } else {

        // Unable to grab info, TODO, handle error
        $error_msg .= "Failed to update the database. ";

    }

    // remove from team and all team members
    if ($stmt = $mysqli->prepare("UPDATE member SET m_org_id = ? WHERE m_team_id = ? LIMIT 1")) {
        $stmt->bind_param('ii', $zero, $m_team_id);

        if (!$stmt->execute()) {

            // Unable to make this call
            $error_msg .= "Failed to update the database. ";

        } else {
            
            // SUCCESS

        }

    } else {

        // Unable to grab info, TODO, handle error
        $error_msg .= "Failed to update the database. ";

    }

    // zero out org membership
    if ($stmt = $mysqli->prepare("UPDATE member SET m_org_id = ? WHERE m_id = ?")) {
        $stmt->bind_param('ii', $zero, $m_id);
        
        // execute the insert
        if (!$stmt->execute()) {

            // Unable to make this call
            $error_msg .= "Failed to update the database. ";
 
        } else {

          // success

        }

    } else {

      // Unable to make this call
      $error_msg .= "Failed to update the database. ";

    }

    if (empty($error_msg)){

        // it succeeded
        $response = array(
            'status' => 'success'
        );

        echo json_encode($response);

    } else {

        // it failed
        $response = array(
            'status' => 'failure',
            'fail_code' => '2',
            'reason' => 'Database error: Unable to leave organization.'
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
