<?php
// USER THE DB PROXY

// Using the SQL proxy
define("HOST","localhost");
define("USER","root");
define("PASSWORD","abc123");
define("SOCKET","/cloudsql/no-shave-november:us-central1:nsn-dev");
define("DATABASE", "no-shave");    // The database name.
define("CAN_REGISTER", "any");
define("DEFAULT_ROLE", "member");
define("SECURE", FALSE);    // FOR DEVELOPMENT ONLY!!!!
define('base_url','http://localhost/noshave-new');

?>
