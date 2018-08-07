<?php
// This API endpoint will return the users existing information.

// includes necessary to make the db call
include_once '../includes/db_connect.php';
include_once '../includes/psl-config.php';

// get the POST vars
$email = $_POST['email'];
$password = $_POST['password'];

if (isset($_POST['email'], $_POST['password'])){
    // let's grab what we can based on the email
    if ($stmt = $mysqli->prepare("SELECT m_id, m_full_name, m_email, m_username, m_password, m_pic_0 FROM member WHERE m_email = ? LIMIT 1")) {
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($m_id, $m_full_name, $m_email, $m_username, $m_password, $m_pic_0);
        $stmt->fetch();
        if ($stmt->num_rows == 1) {
            if (password_verify($password, $m_password)) {
                // yes, it's a match
                $response = array(
                    'status' => 'success',
                    'm_id' => $m_id,
                    'm_full_name' => $m_full_name,
                    'm_email' => $m_email,
                    'm_username' => $m_username,
                    'm_username' => $m_username,
                    'm_pic_0' => $m_pic_0 
                );

                // print out response to stdout
                echo json_encode($response);

            } else {
                // no, bad password
                $response = array(
                    'status' => 'failure',
                    'fail_code' => '2',
                    'reason' => 'The password you’ve entered is incorrect. Please try again.'
                );

                // print out response to stdout
                echo json_encode($response);
            }
        } else {

            // bad email
            $response = array(
                'status' => 'failure',
                'fail_code' => '1',
                'reason' => 'The email you’ve entered doesn’t match any account. Please try again.'
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
        'reason' => 'Please enter both your email and password. Please try again.'
    );

    // print out response to stdout
    echo json_encode($response);  
}
?>
