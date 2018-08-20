<?php
// USER THE DB PROXY

// Using the SQL proxy
define("HOST","localhost");
define("USER","root");
define("PASSWORD","abc123");
define("PORT","3306");
define("SOCKET","/cloudsql/no-shave-november:us-central1:nsn-dev");
define("DATABASE", "no-shave");    // The database name.
define("CAN_REGISTER", "any");
define("DEFAULT_ROLE", "member");
define("SECURE", FALSE);    // FOR DEVELOPMENT ONLY!!!!
define('base_url','http://localhost/noshave-new');
define('ROOT_PATH','/var/www/html/noshave-new/platform/uploads/');
// define('environment','production');
// define('merchantId','rxn35zvzhyq2m2yt');
// define('publicKey','48s5tzh9f4tkymwc');
// define('privateKey','68d746242773084de5e07bfef00b82b2');
define('environment','sandbox');
define('merchantId','3593rkgbs3gz2mmd');
define('publicKey','ydjn3bwbjnkgpgqs');
define('privateKey','b38316021c5a81541fea68b00d21f29f');
?>
