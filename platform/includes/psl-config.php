<?php
// USER THE DB PROXY

// Using the SQL proxy
define("HOST","192.168.1.68");
define("USER","shave");
define("PASSWORD","abc123");
define("PORT","3306");
define("SOCKET","/cloudsql/no-shave-november:us-central1:nsn-dev");
define("DATABASE", "no-shave");    // The database name.
define("CAN_REGISTER", "any");
define("DEFAULT_ROLE", "member");
define("SECURE", FALSE);    // FOR DEVELOPMENT ONLY!!!!
define('base_url','http://localhost/noshave-new');

?>
