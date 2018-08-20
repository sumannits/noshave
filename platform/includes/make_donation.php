<?php

require_once 'braintree/lib/Braintree.php';

Braintree_Configuration::environment(environment);
Braintree_Configuration::merchantId(merchantId);
Braintree_Configuration::publicKey(publicKey);
Braintree_Configuration::privateKey(privateKey);
?>
