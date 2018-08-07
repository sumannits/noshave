<?php
// This API endpoint will complete a donation

// includes necessary to make the db call
include_once '../includes/db_connect.php';
include_once '../includes/psl-config.php';
include_once '../includes/email.php';
require_once '../includes/braintree/lib/Braintree.php';

Braintree_Configuration::environment('production');
Braintree_Configuration::merchantId('rxn35zvzhyq2m2yt');
Braintree_Configuration::publicKey('48s5tzh9f4tkymwc');
Braintree_Configuration::privateKey('68d746242773084de5e07bfef00b82b2');

// Braintree_Configuration::environment('sandbox');
// Braintree_Configuration::merchantId('589xb5xzssg346xz');
// Braintree_Configuration::publicKey('hck28kdx44kcsvxg');
// Braintree_Configuration::privateKey('68a36568fbdb0c7604d9b9ab5b3f0d21');

$donation_amount = $_POST['donation_amount'];
$donation_name = $_POST['donation_name'];
$donation_company = $_POST['donation_company'];
$donation_email = $_POST['donation_email'];
$donation_classifier = $_POST['donation_classifier'];
$donation_to_id = $_POST['donation_to_id'];
$donation_anonymous = $_POST['donation_anonymous'];
$donation_visbile = $_POST['donation_visbile'];
$donation_comment = $_POST['donation_comment'];
$donation_nonce = $_POST['donation_nonce'];

$error_msg = "";

// Check the vars and make the call
if (isset($_POST['donation_amount'], $_POST['donation_name'], $_POST['donation_company'], $_POST['donation_email'], $_POST['donation_classifier'], $_POST['donation_to_id'], $_POST['donation_anonymous'], $_POST['donation_visbile'], $_POST['donation_comment'], $_POST['donation_nonce'])){

  if ($donation_amount == ""){
      $error_msg .= 'Please enter a donation amount. ';
  }

  if ($donation_name == ""){
      $error_msg .= 'Please enter your name. ';
  }  

  // donation visbile
  if ($donation_visbile == ""){
    $donation_visbile = 1;
  }

  // donation visbile
  if ($donation_anonymous == ""){
    $donation_anonymous = 0;
  }

  if ($donation_to_id >= 10000000 && $donation_to_id <= 19999999) {
    $donation_classifier = 3;
  } elseif ($donation_to_id >= 20000000) {
    $donation_classifier = 2;
  } elseif ($donation_to_id < 10000000) {
    $donation_classifier = 1;
  }

  // EMAIL
  if (!filter_var($donation_email, FILTER_VALIDATE_EMAIL)) {
      // Not a valid email
      $error_msg .= 'Please enter a valid email address. ';
  }

  // Check if the value is an integer
  // TODO - does this work?
  if (is_numeric($donation_amount) && floatval($donation_amount) == intval(floatval($donation_amount))){
      // true == good
      if (floatval($donation_amount) > 0){
          // great than zero == good
      } else {
          $error_msg .= "Please enter a positive donation amount. ";      
      }
  } else {
      $error_msg .= "Please enter a positive integer for the donation amount. ";
  }

  if ($donation_name == "") {
    $error_msg .= "Please enter the name the donation should be made under.";
  }

  if (empty($error_msg)){

    $result = Braintree_Transaction::sale([
        'amount' => $donation_amount,
        'paymentMethodNonce' => $donation_nonce,
        'options' => [ 'submitForSettlement' => true ]
    ]);

    if ($result->success) {

      if (empty($error_msg)){
        // success, let's update the DB
        $one = "1";

        // donation pin
        $donation_pin = mt_rand(100000, 999999);

        // let's grab what we can based on the email
        if ($stmt = $mysqli->prepare("INSERT INTO donation (d_classifier, d_classifier_id, d_pin, d_name, d_display_name, d_company, d_message, d_amount, d_email, d_message_on_page, d_verified_payment, d_visible_on_page, d_anonymous) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)")) {
          $stmt->bind_param('iiissssisiiii', $donation_classifier, $donation_to_id, $donation_pin, $donation_name, $donation_name, $donation_company, $donation_comment, $donation_amount, $donation_email, $one, $one, $donation_visbile, $donation_anonymous);

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

              // company
              if ($donation_company == ""){
                $donation_company = "n/a";
              }

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

              // Now tell the receiver
              if ($donation_classifier == 0) {
                // general - no email

              } elseif ($donation_classifier == 1) {
                // member
                if ($stmt_donation = $mysqli->prepare("SELECT m_full_name, m_email FROM member WHERE m_id = ?")) {
                  $stmt_donation->bind_param('i', $donation_to_id);
                  $stmt_donation->execute();
                  $stmt_donation->store_result();
                  $stmt_donation->bind_result($m_full_name, $m_email);
                  $stmt_donation->fetch();
                  $stmt_donation->close();

                  if ($donation_anonymous == 1){
                    $donor_name = "an anonymous donor";
                  } else {
                    $donor_name = $donation_name;
                  }

                  // perpare email
                  $message_subject = "You Received a Donation!";
                  $message_body = 'Greetings ' . $m_full_name . ',

                                  You have received a donation from ' . $donor_name . ' in the amount of $' . number_format($donation_amount) . '.
                                  ';

                  // send the email
                  send_email($message_subject, $message_body, $m_email, 3);

                } else {
                  // no email I guess
                }   

              } elseif ($donation_classifier == 2) {
                // team
                if ($stmt_donation = $mysqli->prepare("SELECT m_full_name, m_email FROM member WHERE m_team_id = ? AND m_team_editor = 1")) {
                  $stmt_donation->bind_param('i', $donation_to_id);
                  $stmt_donation->execute();
                  $stmt_donation->store_result();
                  $stmt_donation->bind_result($m_full_name, $m_email);
                  $stmt_donation->fetch();
                  $stmt_donation->close();

                  if ($donation_anonymous == 1){
                    $donor_name = "an anonymous donor";
                  } else {
                    $donor_name = $donation_name;
                  }

                  // perpare email
                  $message_subject = "Your Team Received a Donation!";
                  $message_body = 'Greetings ' . $m_full_name . ',

                                  Your team received a donation from ' . $donor_name . ' in the amount of $' . number_format($donation_amount) . '.
                                  ';

                  // send the email
                  send_email($message_subject, $message_body, $m_email, 3);

                } else {
                  // no email I guess
                }   

              } elseif ($donation_classifier == 3) {
                // company
                if ($stmt_donation = $mysqli->prepare("SELECT m_full_name, m_email FROM member WHERE m_org_id = ? AND m_org_editor = 1")) {
                  $stmt_donation->bind_param('i', $donation_to_id);
                  $stmt_donation->execute();
                  $stmt_donation->store_result();
                  $stmt_donation->bind_result($m_full_name, $m_email);
                  $stmt_donation->fetch();
                  $stmt_donation->close();

                  if ($donation_anonymous == 1){
                    $donor_name = "an anonymous donor";
                  } else {
                    $donor_name = $donation_name;
                  }

                  // perpare email
                  $message_subject = "Your Organization Received a Donation!";
                  $message_body = 'Greetings ' . $m_full_name . ',

                                  Your organization received a donation from ' . $donor_name . ' in the amount of $' . number_format($donation_amount) . '.
                                  ';

                  // send the email
                  send_email($message_subject, $message_body, $m_email, 3);

                } else {
                  // no email I guess
                }  

              } else {
                // no email, fail
                
              }

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

    // payment failed
    $response = array(
        'status' => 'failure',
        'fail_code' => '1',
        'reason' => $error_msg
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
        'reason' => 'Uh oh. Something\'s missing. Please double-check that you filled in every field.'
    );

    // print out response to stdout
    echo json_encode($response);  

}

?>
