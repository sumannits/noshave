<?php
// This API endpoint will return the update the users information

// includes necessary to make the db call
include_once '../includes/db_connect.php';
include_once '../includes/psl-config.php';
include_once '../includes/functions.php';
sec_session_start();

$m_id = $_SESSION['user_id'];

// get the POST vars
$team_photo = $_POST['team_photo'];

$error_msg = "";

if (isset($m_id, $_POST['team_photo'])) {

    if ($team_photo == ""){
        $error_msg .= 'Please select a new photo to upload. ';
    }

    $imageContent = file_get_contents($team_photo);

    if(imagecreatefromstring($imageContent)) {
        #ITS AN IMAGE YAY
    } else {
        #error, the base64 string is not a valid string.
        $error_msg .= "The file you selected does not appear to be an image. Please try again. ";
    }

    // md5 of image
    $imageMD5 = md5($imageContent);

    // save locally to ship to bucket
    file_put_contents('/var/www/noshave/platform/uploads/' . $imageMD5, $imageContent);

    // move to GCS
    $cmd = 'sudo -u root gsutil mv /var/www/noshave/platform/uploads/' . $imageMD5 . ' gs://nsn-img/2017/' . $imageMD5;
    // lets get an error here TODO
    shell_exec($cmd);

    // now save to DB - so have var
    $image_url = 'https://storage.googleapis.com/nsn-img/2017/' . $imageMD5;

    // get team_id from user_id - any team member could abuse this, meh?
    if ($stmt = $mysqli->prepare("SELECT m_team_id FROM member WHERE m_id = ? LIMIT 1")) {
        $stmt->bind_param('i', $m_id);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($t_id);
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

    if (empty($error_msg)){
        // success, let's update the DB
        $one = "1";

        // let's grab what we can based on the email
        if ($stmt = $mysqli->prepare("UPDATE team SET t_pic_0 = ? WHERE t_id = ?")) {
            $stmt->bind_param('si', $image_url, $t_id);
            
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
                    'status' => 'success'
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
