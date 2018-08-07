<?php
// This API endpoint will return the update the users information

// includes necessary to make the db call
include_once '../includes/db_connect.php';
include_once '../includes/psl-config.php';
include_once '../includes/functions.php';
include_once '../includes/email.php';
sec_session_start();

$m_id = $_SESSION['user_id'];

// get the POST vars
$message_subject = $_POST['message_subject'];
$message_body = $_POST['message_body'];

$error_msg = "";

if (isset($m_id, $_POST['message_subject'], $_POST['message_body'])) {

  // get the users team and confirm that they're a team captain
  if ($stmt = $mysqli->prepare("SELECT m_team_editor, m_team_id FROM member WHERE m_id = ?")) {
      $stmt->bind_param('i', $m_id);
      $stmt->execute();
      $stmt->store_result();
      $stmt->bind_result($m_team_editor, $m_team_id);
      $stmt->fetch();
      $stmt->close();

    } else {
      // unable to get data
      $m_team_editor = 0;
      $m_team_id = 0;
    }

    if ($m_team_id == 0){
        $error_msg .= "You don't currently belong to any team. ";
    }

    if ($m_team_editor == 0){
      $error_msg .= "Only the team captain can message the entire team. ";
    }

    // LEGGO
    if (empty($error_msg)){
        // success, let's update the DB
        $one = "1";

        // user array
        $team_member_emails = array();

        // let's grab what we can based on the email
        if ($stmt = $mysqli->prepare("SELECT m_email FROM member WHERE m_team_id = ?")) {
            $stmt->bind_param('i', $m_team_id);   
            $stmt->execute();
            $stmt->store_result();
            $stmt->bind_result($m_email);

            while ($stmt->fetch()) {

              $team_member_emails[] = $m_email;

            }

        } else {
          // unable to get data
          $error_msg .= "Unable to fetch team email addresses. ";

        }

        // massage those
        $message_subject = 'Message From Team Captain: ' . $message_subject;
        $message_body = '\n' . $message_body;

        // NOW SEND THE MESSAGE TO EACH
        for ($i = 0; $i < count($team_member_emails); ++$i) {
          //print $team_member_emails[$i];
          if (send_email($message_subject, $message_body, $team_member_emails[$i], 4)){
            // success

          } else {
            $error_msg .= "Failed to send message to " . $team_member_emails[$i] . ". ";
          }

        }


        if (empty($error_msg)){
          // success!
          $response = array(
              'status' => 'success'
          );

          echo json_encode($response);

        } else {
          // failure :(
          $response = array(
              'status' => 'failure',
              'fail_code' => '2',
              'reason' => $error_msg
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
