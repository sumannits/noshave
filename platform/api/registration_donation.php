<?php
// This API endpoint will complete a donation

// includes necessary to make the db call
include_once '../includes/db_connect.php';
include_once '../includes/psl-config.php';
include_once '../includes/email.php';
require_once '../includes/braintree/lib/Braintree.php';

Braintree_Configuration::environment(environment);
Braintree_Configuration::merchantId(merchantId);
Braintree_Configuration::publicKey(publicKey);
Braintree_Configuration::privateKey(privateKey);

$donation_name = $_POST['donation_name'];
$donation_company = $_POST['donation_company'];
$donation_email = $_POST['donation_email'];
$donation_username = $_POST['donation_username'];
$donation_amount = $_POST['donation_amount'];
$donation_comment = $_POST['donation_comment'];
$donation_nonce = $_POST['donation_nonce'];

$error_msg = "";

// Check the vars and make the call
if (isset($_POST['donation_name'], $_POST['donation_email'], $_POST['donation_username'], $_POST['donation_amount'], $_POST['donation_nonce'])){

  // Check if the value is an integer
  if (is_numeric($donation_amount) && floatval($donation_amount) == intval(floatval($donation_amount))){
      // true == good
      if (floatval($donation_amount) > 0){
          // great than zero == good
      } else {
          $error_msg .= "Please enter a <i>positive</i> donation amount. ";      
      }
  } else {
      $error_msg .= "Please enter a positive integer for the donation amount. ";
  }

  if ($donation_name == "") {
    $error_msg .= "Please enter the name the donation should be made under.";
  }

  // get m_id from username - it exists already
  $resultSet = $mysqli->query("SELECT m_id FROM member WHERE m_username = '$donation_username' LIMIT 1");
    if($resultSet->num_rows != 0){
        while($rows = $resultSet->fetch_assoc()){
            $m_id = $rows['m_id'];
        }
    } else {
      $error_msg .= "Unable to get user ID from username.";
    }


  $nonceFromTheClient = $donation_nonce;

  $result = Braintree_Transaction::sale([
      'amount' => $donation_amount,
      'paymentMethodNonce' => $nonceFromTheClient,
      'options' => [ 'submitForSettlement' => true ]
  ]);

  if ($result->success) {

    if (empty($error_msg)){
      // success, let's update the DB
      $one = "1";

      // donation pin
      $donation_pin = mt_rand(100000, 999999);

      // let's grab what we can based on the email
      if ($stmt = $mysqli->prepare("INSERT INTO donation (d_classifier, d_classifier_id, d_pin, d_name, d_display_name, d_company, d_message, d_amount, d_email, d_message_on_page, d_verified_payment) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)")) {
        $stmt->bind_param('iiissssisii', $one, $m_id, $donation_pin, $donation_name, $donation_name, $donation_company, $donation_comment, $donation_amount, $donation_email, $one, $one);

          // execute the insert
          if (!$stmt->execute()) {

            // it failed to enter DB
            $response = array(
                'status' => 'failure',
                'fail_code' => '3',
                'reason' => 'Database error: Unable to enter donation in database.'
            );

            echo json_encode($response);

          } else {

            // say thanks!
            date_default_timezone_set('America/New_York');
            $date= date('m-d-Y') ;

            // perpare email
            $message_subject = "Thank you for donating to No-Shave November!";
            $message_body = 'Hello ' . $donation_name . ',

                            Thank you for your donation to No-Shave November!  Your generous gift helps support programs at St. Jude Children\'s Research Hospital, Prevent Cancer Foundation, and Fight Colorectal Cancer. All four of these foundations are making great strides to fight, research, and find a cure for cancer, each in their own unique way.

                            For tax purposes, please keep this email as your receipt.

                            Donor: '. $donation_name . '
                            Company: ' . $donation_company . '
                            Organization: No Shave November (Tax ID #473673254)
                            Date: ' . $date . '
                            Amount: $' . number_format($donation_amount) . '  
                            ';

            // send the email
            send_email($message_subject, $message_body, $donation_email, 2);

            // payment accepted and entered in DB
            $response = array(
                'status' => 'success'
            );

            // print out response to stdout
            echo json_encode($response);
          }

      } else {

        // it failed to enter DB
        $response = array(
            'status' => 'failure',
            'fail_code' => '3',
            'reason' => 'Database error: Unable to enter donation in database.'
        );

        echo json_encode($response);
      }

    } else {
      // there was an error with the input
      $response = array(
          'status' => 'failure',
          'fail_code' => '2',
          'reason' => $error_msg
      );

      echo json_encode($response);
    }

  } else if ($result->transaction) {

    // payment failed
    $response = array(
        'status' => 'failure',
        'fail_code' => $result->transaction->processorResponseCode,
        'reason' => 'The payment failed.  Please try again or contact support.'
        //'reason' => $result->transaction->processorResponseText
    );

    // print out response to stdout
    echo json_encode($response);

  } else {

    // payment failed
    $response = array(
        'status' => 'failure',
        'fail_code' => '1',
        'reason' => 'The payment failed.  Please try again or contact support.'
        //'reason' => $result->errors->deepAll()
    );

    // print out response to stdout
    echo json_encode($response);

  }

} else {

    // POST vars not provided
    $response = array(
        'status' => 'failure',
        'fail_code' => '0',
        'reason' => 'Missing variables in POST.'
    );

    // print out response to stdout
    echo json_encode($response);  

}

?>
