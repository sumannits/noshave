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
$t_name = $_POST['t_name'];
$t_username = $_POST['t_username'];

$error_msg = "";

if (isset($m_id, $_POST['t_name'], $_POST['t_username'])) {

    // check if user owns an org
    if ($stmt = $mysqli->prepare("SELECT m_org_id, m_org_editor FROM member WHERE m_id = ?")) {
      $stmt->bind_param('i', $m_id);
      $stmt->execute();
      $stmt->store_result();
      $stmt->bind_result($m_org_id, $m_org_editor);
      $stmt->fetch();
      $stmt->close();

      if ($m_org_editor == 0){
        $m_org_id = 0;
      }

    } else {
      // unable to get data
      $m_org_id = 0;
    }

    if ($t_name == ""){
        $error_msg .= 'Please enter a team name. ';
    }

    if ($t_username == ""){
        $error_msg .= 'Please enter a team username. ';
    }

    // check username pattern
    if(preg_match("/^([_A-z0-9]){1,}$/",$t_username)){
        // matched - no issue

    } else {
        // didn't match
        $error_msg .= 'Team usernames should only contain letters, numbers, and underscores. ';

    }

    // Check if the username is in use
    if ($stmt = $mysqli->prepare("SELECT t_id FROM team WHERE t_username = ? LIMIT 1")) {
        $stmt->bind_param('s', $t_username);
        
        // execute the insert
        if (!$stmt->execute()) {
            // silentyl allow it, I guess? fudge.

        } else {
            // it succeeded
            $stmt->store_result();

            if ($stmt->num_rows == 1) {

                $error_msg .= "The team username " . $t_username . " is already in use by another team. ";

            } else {
                // no big deal
            }

        }

    } else {

        // fail
        $error_msg .= "Somethings not right. We couldn't check to see if the team username is already in use.  Please try again or contact support. ";

    }

    // LEGGO
    if (empty($error_msg)){
        // success, let's update the DB
        $one = "1";

        // let's grab what we can based on the email
        if ($stmt = $mysqli->prepare("INSERT INTO team (t_name, t_username, t_org_id) VALUES (?, ?, ?)")) {
            $stmt->bind_param('ssi', $t_name, $t_username, $m_org_id);
            
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

                $t_id = $stmt->insert_id;

                //UPDATE USER FIELDS
                if ($stmt_member = $mysqli->prepare("UPDATE member SET m_team_id = ?, m_team_editor = ? WHERE m_id = ? LIMIT 1")) {
                    $stmt_member->bind_param('iii', $t_id, $one, $m_id);

                    if (!$stmt_member->execute()) {
                        // failed
                        $response = array(
                            'status' => 'failure',
                            'fail_code' => '4',
                            'reason' => 'Database error: Unable to update information.'
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
                    // Unable to grab info, TODO, handle error
                    $response = array(
                        'status' => 'failure',
                        'fail_code' => '5',
                        'reason' => 'Database error: Unable to update information.'
                    );

                    echo json_encode($response);
                }

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
