<?php
  // Load database connection and php functions
  include_once 'includes/db_connect.php';
  include_once 'includes/functions.php';
  // Start secure session
  sec_session_start();
?>

<?php

$new_org_html = '

<!DOCTYPE html>
<html lang="en">
  <head>
    <link rel="icon" type="image/png" href="/img/favicon.png">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="author" content="No Shave November">
    <meta name="description" content="This page doesn\'t exist just yet!">
    <title>This page doesn\'t exist just yet! | No-Shave November</title>
    <link href="/assets/css/bootstrap.css" rel="stylesheet">    
    <link href="/assets/css/main.css" rel="stylesheet">
    <link href=\'https://fonts.googleapis.com/css?family=Lato:300,400,900\' rel=\'stylesheet\' type=\'text/css\'>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.6.0/css/font-awesome.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.8/jquery.min.js" type="text/javascript"></script> 
    <script src="/assets/js/jquery.localScroll.js" type="text/javascript"></script> 
    <script src="/assets/js/jquery.scrollTo.js" type="text/javascript"></script>
    <script src="/assets/js/bootstrap.min.js" type="text/javascript"></script>

    <script type="text/javascript">
      $(document).ready(function() {
        $(\'#nav\').localScroll({duration:800});
      });
      $(function(){
        $("#menu").load("/menu.html"); 
      });
      $(function(){
        $("#footer").load("/footer.html"); 
      });
    </script>
  </head>
  <body>

    <!-- MEDU BAR -->
    <div id="menu"></div>
    <!-- MENU BAR-->
      <!-- PAGE HEADER --> 
      <br>
      <br>
      <div class="container">
        <div class="row">
          <div class="col-md-12">
            <div class="page-header">
              <h2 class="hidden-xs"><i class="fa fa-exclamation-triangle"></i> This page doesn\'t exist just yet!</h2>
              <h4 class="hidden-lg hidden-md hidden-sm"><i class="fa fa-exclamation-triangle"></i> This page doesn\'t exist just yet!</h4>
            </div>
          </div>
        </div>          
      </div>
    <!-- Page Content -->
    <div class="container">
        <div class="row">
          <div class="col-md-12">
              <p>
                <br>
                    It can take as long as five minutes for new organization pages to appear. If you continue to receive an error, please <a href="" data-toggle="modal" data-target="#contactUS">contact us</a>.
                <br>
              </p>
          </div>
        </div>
    </div>


    <!-- FOOTER -->
    <div id="footer"></div>
    <!-- FOOTER -->

    <script>
      (function(i,s,o,g,r,a,m){i[\'GoogleAnalyticsObject\']=r;i[r]=i[r]||function(){
      (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
      m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
      })(window,document,\'script\',\'//www.google-analytics.com/analytics.js\',\'ga\');

      ga(\'create\', \'UA-53118539-1\', \'auto\');
      ga(\'send\', \'pageview\');

    </script>
  </body>
</html>

';

// team id in the get
$o_username = $_GET['p'];

if ($stmt = $mysqli->prepare("SELECT oc_raw_html FROM org_cache WHERE oc_name = ? LIMIT 1")) {
    $stmt->bind_param('s', $o_username);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($raw_html);
    $stmt->fetch();
    $stmt->close();

    if ($raw_html) {
        echo $raw_html;
    } else {
        echo $new_org_html;
    }
    

} else {
    // unable to grab total
}

?>