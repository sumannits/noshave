<?php
// This API endpoint will return the users existing information.

// includes necessary to make the db call
include_once '../includes/db_connect.php';
include_once '../includes/psl-config.php';

// get the POST vars
$support_name = $_POST['support_name'];
$support_email = $_POST['support_email'];
$support_subject = $_POST['support_subject'];
$support_message = $_POST['support_message'];

if (isset($_POST['support_name'], $_POST['support_email'], $_POST['support_subject'], $_POST['support_message'])){

    if ($support_name == ""){
        $error_msg .= "<strong>Somethings Missing!</strong>&nbsp; Please enter your full name. ";
    }   
    if ($support_email == ""){
        $error_msg .= "<strong>Somethings Missing!</strong>&nbsp; Please enter your email address. ";
    }
    if ($support_subject == ""){
        $error_msg .= "<strong>Somethings Missing!</strong>&nbsp; Please enter an email subject. ";
    }
    if ($support_message == ""){
        $error_msg .= "<strong>Somethings Missing!</strong>&nbsp; Please enter an email message. ";
    }

    if (empty($error_msg)) {
        $data = array(
                    "ticket" => array(
                        "comment" => array(
                            "body" => $support_message),
                            "subject" => $support_subject,
                            "requester" => array("email" => $support_email, "name" => $support_name)
                    )
                );


        $data_string = json_encode($data);

        $ch = curl_init('https://no-shave.zendesk.com/api/v2/tickets.json');
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_USERPWD, 'andrew.hill@no-shave.org/token:YZyYHTfFMEUFNVNHDmnPSIilftonObCm5e8L1uUf');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($data_string))
        );

        $result = curl_exec($ch);

        $response = array(
            'status' => 'success',
            'user_exists' => 'true'
        );
        echo json_encode($response); 
    } else {

        $response = array(
            'status' => 'failure'
        );
        echo json_encode($response);  

    }       

} else {

    $response = array(
        'status' => 'failure'
    );
    echo json_encode($response);  
    
}
?>
