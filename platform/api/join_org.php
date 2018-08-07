<?php
// This API endpoint will return the update the users information

// includes necessary to make the db call
include_once '../includes/db_connect.php';
include_once '../includes/psl-config.php';
include_once '../includes/functions.php';
sec_session_start();

$m_id = $_SESSION['user_id'];

// get the POST vars
$o_id = $_POST['o_id'];

$error_msg = "";

if (isset($m_id,$_POST['o_id'])) {

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

    if ($o_id == ""){
        $error_msg .= 'Please select an organization to join. ';
    }

    // LEGGO
    if (empty($error_msg)){
        // success, let's update the DB
        $one = "1";

        //UPDATE TEAM FIELD - THEY ALREADY ARE A TEAM CAPTAIN
        if ($stmt = $mysqli->prepare("UPDATE team SET t_org_id = ? WHERE t_id = ? LIMIT 1")) {
            $stmt->bind_param('ii', $o_id, $m_team_id);

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

        // GET TEAM OF USER AND UPDATE THAT TEAM AND EVERYONE ON IT
        if ($stmt = $mysqli->prepare("UPDATE member SET m_org_id = ? WHERE m_team_id = ? LIMIT 1")) {
            $stmt->bind_param('ii', $o_id, $m_team_id);

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

        // just to be safe, the user themselves
        if ($stmt = $mysqli->prepare("UPDATE member SET m_org_id = ? WHERE m_id = ?")) {
            $stmt->bind_param('ii', $o_id, $m_id);
            
            // execute the insert
            if (!$stmt->execute()) {
              
                // Unable to grab info, TODO, handle error
                $error_msg .= "Failed to update the database. ";

            } else {
                
                // SUCCESS

            }

        } else {

            // Unable to grab info, TODO, handle error
            $error_msg .= "Failed to update the database. ";

        }

        // LAST CHECK BEFORE UPDATE
        if (empty($error_msg)){
            // it succeeded
            $response = array(
                'status' => 'success'
            );
            echo json_encode($response);

        } else  {
            // it failed
            $response = array(
                'status' => 'failure',
                'fail_code' => '10',
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
