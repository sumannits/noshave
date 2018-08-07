<?php
include_once 'includes/db_connect.php';
include_once 'includes/psl-config.php';
include_once 'includes/functions.php';
include_once 'includes/email.php';

$error_msg = "";
 
              
if (isset($_POST['email'], $_POST['old_password'], $_POST['new_password'])) {


    $email = $_POST['email'];
    $password = $_POST['old_password'];
    $new_password = $_POST['new_password'];
    $old_password_hash = password_hash($password, PASSWORD_BCRYPT);
    $new_password_hash =  password_hash($new_password, PASSWORD_BCRYPT);

    // check against current password
    if ($stmt_donation = $mysqli->prepare("SELECT m_password FROM member WHERE m_email = ?")) {
      $stmt_donation->bind_param('s', $email);
      $stmt_donation->execute();
      $stmt_donation->store_result();
      $stmt_donation->bind_result($m_password);
      $stmt_donation->fetch();
      $stmt_donation->close();

    } else {
      $error_msg .= "Failed to check existing account password. ";
    }

    if (password_verify($password, $m_password)) {
        // worked
    } else {
        $error_msg .= "The old password you entered does not match your account password. ";
    }
    
    if (empty($error_msg)) {
 
        if ($insert_stmt = $mysqli->prepare("UPDATE member SET m_password = ? WHERE m_email = ?")) {
            $insert_stmt->bind_param('ss', $new_password_hash, $email);
            // Execute the prepared query.
            if (! $insert_stmt->execute()) {
                header('Location: /error.php');
            }
        }

        // Now log back in
        header('Location: /login?reset=t');

    }

}

if (isset($_POST['email']) && ($_POST['change'] === "forgot")){

        function randStrGen($len){
            $result = "";
            $chars = "aAbBcCdDeEfFgGhHJkKmMnNpPqQrRsStTuUvVwWxXyYZz23456789";
            $charArray = str_split($chars);
            for($i = 0; $i < $len; $i++){
                $randItem = array_rand($charArray);
                $result .= "".$charArray[$randItem];
            }
            return $result;
        }

        // Usage example
        $random_password = randStrGen(8);
        $random_password_hash = password_hash($random_password, PASSWORD_BCRYPT);

        // See if the email exists in our DB
        $resultSet = $mysqli->query("SELECT m_id FROM member WHERE m_email = '" . $email . "' LIMIT 1");
        if($resultSet->num_rows != 0){
            $email_exists = 1;
        } else {
            $email_exists = 0;
        }


        if ($email_exists == 1) {

            if ($insert_stmt = $mysqli->prepare("UPDATE member SET m_password = ? WHERE m_email = ?")) {
                $insert_stmt->bind_param('ss', $random_password_hash, $email);
                // Execute the prepared query.
                if (! $insert_stmt->execute()) {
                    header('Location: /error');
                }
            }

            // SEND THE NEW PASSWORD
            $message_subject = "Your No-Shave November Password Has Been Reset!";
            $message_body = 'Please login to your account using the new password below: 

            <strong>' . $random_password . '</strong>

            To change your password, go to settings, select account, then select change password.<br><br>You can log back into your account by going to https://no-shave.org/login.
            ';

            send_email($message_subject, $message_body, $email, 5);


        } else {
            // Umm no email exists
            //header('Location: /error');
        }

        // Now go to the admin page 
        header('Location: /login?reset=true');

}
