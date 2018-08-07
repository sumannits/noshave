<?php

require_once 'braintree/lib/Braintree.php';

Braintree_Configuration::environment('production');
Braintree_Configuration::merchantId('rxn35zvzhyq2m2yt');
Braintree_Configuration::publicKey('48s5tzh9f4tkymwc');
Braintree_Configuration::privateKey('68d746242773084de5e07bfef00b82b2');

// Braintree_Configuration::environment('sandbox');
// Braintree_Configuration::merchantId('589xb5xzssg346xz');
// Braintree_Configuration::publicKey('hck28kdx44kcsvxg');
// Braintree_Configuration::privateKey('68a36568fbdb0c7604d9b9ab5b3f0d21');

if (isset($_POST["payment_method_nonce"])){

  $nonceFromTheClient = $_POST["payment_method_nonce"];

  $result = Braintree_Transaction::sale([
      'amount' => '100.00',
      'paymentMethodNonce' => $nonceFromTheClient,
      'options' => [ 'submitForSettlement' => true ]
  ]);

  if ($result->success) {
      print_r("success!: " . $result->transaction->id);
  } else if ($result->transaction) {
      print_r("Error processing transaction:");
      print_r("\n  code: " . $result->transaction->processorResponseCode);
      print_r("\n  text: " . $result->transaction->processorResponseText);
  } else {
      print_r("Validation errors: \n");
      print_r($result->errors->deepAll());
  }

}

?>
