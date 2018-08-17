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
$t_id = $_POST['t_id'];

$error_msg = "";

if (isset($m_id,$_POST['t_id'])) {

  // check if the team belongs to an org
  if ($stmt = $mysqli->prepare("SELECT t_org_id FROM team WHERE t_id = ?")) {
      $stmt->bind_param('i', $t_id);
      $stmt->execute();
      $stmt->store_result();
      $stmt->bind_result($t_org_id);
      $stmt->fetch();
      $stmt->close();

    } else {
      // unable to get data
      $t_org_id = 0;
    }

    if ($t_id == ""){
        $error_msg .= 'Please select a team to join. ';
    }

    // LEGGO
    if (empty($error_msg)){
        // success, let's update the DB
        $one = "1";

        // let's grab what we can based on the email
        if ($stmt = $mysqli->prepare("UPDATE member SET m_team_id = ?, m_org_id = ? WHERE m_id = ?")) {
            $stmt->bind_param('iii', $t_id, $t_org_id, $m_id);
            
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
                    'msg' => 'You have successfully join the team.'
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
