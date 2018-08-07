<?php
// This API endpoint will return the update the users information

// includes necessary to make the db call
include_once '../includes/db_connect.php';
include_once '../includes/psl-config.php';
include_once '../includes/functions.php';
sec_session_start();

$m_id = $_SESSION['user_id'];

// get the POST vars
$o_name = $_POST['o_name'];
$o_username = $_POST['o_username'];

$error_msg = "";

if (isset($m_id, $_POST['o_name'], $_POST['o_username'])) {

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

    if ($o_name == ""){
        $error_msg .= 'Please enter an organization name. ';
    }

    if ($o_username == ""){
        $error_msg .= 'Please enter an organization username. ';
    }

    // check username pattern
    if(preg_match("/^([_A-z0-9]){1,}$/",$o_username)){
        // matched - no issue

    } else {
        // didn't match
        $error_msg .= 'organization usernames should only contain letters, numbers, and underscores. ';

    }

    // Check if the username is in use
    if ($stmt = $mysqli->prepare("SELECT o_id FROM org WHERE o_username = ? LIMIT 1")) {
        $stmt->bind_param('s', $o_username);
        
        // execute the insert
        if (!$stmt->execute()) {
            // silentyl allow it, I guess? fudge.
            $error_msg .= "Unable to determine if " . $o_username . " is already in use by another organization. ";

        } else {
            // it succeeded
            $stmt->store_result();

            if ($stmt->num_rows == 1) {

                $error_msg .= "The organization username " . $o_username . " is already in use by another organization. ";

            } else {
                // no big deal
            }

        }

    } else {

        // fail
        $error_msg .= "Somethings not right. We couldn't check to see if the organization username is already in use.  Please try again or contact support. ";

    }

    // LEGGO
    if (empty($error_msg)){
        // success, let's update the DB
        $one = "1";

        // let's grab what we can based on the email
        if ($stmt = $mysqli->prepare("INSERT INTO org (o_name, o_username) VALUES (?, ?)")) {
            $stmt->bind_param('ss', $o_name, $o_username);
            
            // execute the insert
            if (!$stmt->execute()) {
                // it failed
                $response = array(
                    'status' => 'failure',
                    'fail_code' => '1',
                    'reason' => 'Database error: Unable to create organization.'
                );

                echo json_encode($response);  

            } else {

                $o_id = $stmt->insert_id;

                // UPDATE THE TEAM, THEN ALL MEMEBRS
                if ($m_team_id != 0){

                  //UPDATE TEAM FIELD
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


                // UPDATE THE OWNER AND MAKE SURE THEY'RE AN EDITOR
                if ($stmt = $mysqli->prepare("UPDATE member SET m_org_id = ?, m_org_editor = ? WHERE m_id = ? LIMIT 1")) {
                    $stmt->bind_param('iii', $o_id, $one, $m_id);

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
