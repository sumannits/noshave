<?php
include_once 'db_connect.php';
include_once 'functions.php';
 
sec_session_start(); // Our custom secure way of starting a PHP session.

$error_msg = "";

if (isset($_POST['email_or_username'], $_POST['password'])) {
    $email_or_username = $_POST['email_or_username'];
    $password = $_POST['password']; // passwurdeee -- it's hashed in DB
 
    $login_status = login($email_or_username, $password, $mysqli);  

    if ($login_status == 1) {

        // Take them to the admin console
        header('Location: /dashboard');

    } elseif ($login_status == 0) {

        // They need to register for this year      
        //$error_msg = '<strong>Welcome back!</strong>  Please register for 2017. ' . $login_status;
        $error_msg = '<strong>Oops-A-Daisy!</strong>  Incorrect email or password entered.  Please try again. ';

    } else {

        // Real failure here
        $error_msg = '<strong>Oops-A-Daisy!</strong>  Incorrect email or password entered.  Please try again. ';

    }
} else {
    // The correct POST variables were not sent to this page.
    //echo 'Invalid Request';
}
