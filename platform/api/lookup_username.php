<?php
// This API endpoint will return the users existing information.

// includes necessary to make the db call
include_once '../includes/db_connect.php';
include_once '../includes/psl-config.php';

// get the POST vars
$username = $_POST['username'];

if (isset($_POST['username'])){
    // let's check it
    if ($stmt = $mysqli->prepare("SELECT m_username FROM member WHERE m_username = ? LIMIT 1")) {
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($m_username);
        $stmt->fetch();
        if ($stmt->num_rows == 1) {

            // user exists
            $response = array(
                'status' => 'success',
                'user_exists' => 'true'
            );

            // print out response to stdout
            echo json_encode($response);

        } else {

            // user does not exist
            $response = array(
                'status' => 'success',
                'user_exists' => 'false'
            );

            // print out response to stdout
            echo json_encode($response);            
        }
    }
} else {
    // POST vars not provided
    $response = array(
        'status' => 'failure',
        'fail_code' => '0',
        'reason' => 'Unable to lookup if username exists.'
    );

    // print out response to stdout
    echo json_encode($response);  
}
?>
