<?php
  include_once 'includes/db_connect.php';
  include_once 'includes/functions.php';
  include_once 'includes/process_login.php';
  sec_session_start();
?>

<?php if (login_check($mysqli) == true) : ?>

<meta http-equiv="refresh" content="0;url=/dashboard" />

<?php else : ?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <link rel="icon" type="image/png" href="favicon.png">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" />
    <meta name="author" content="No-Shave November">
    <meta name="description" content="Login to No-Shave November. Update your personal, team and organization fundraising pages, track your fundraising progress, and update your account information.">
    <title>Login | No-Shave November</title>
    <script src="assets/js/jquery.min.js" type="text/javascript"></script>
    <script type="text/javascript">
      $(function(){
        $("#menu").load("platform/platform_menu.php"); 
      });
      $(function(){
        $("#footer").load("/platform/platform_footer.html"); 
      });
    </script>
    <style>
        .form-control.extra-wide {
          width: 100%;
        }
    </style>
  </head>
  <body>

    <!-- MEDU BAR -->
    <div id="menu"></div>
    <!-- MENU BAR-->

    <!-- GIMME A BREAK -->
    <br><br><br><br>
    <!-- GIMME A BREAK -->

    <!-- - - - - - -  -->
    <!-- PAGE CONTENT -->
    <!-- - - - - - -  -->

    <div class="container">
      <div class="row">

        <?php

          if (isset($_GET['new'])) {
            echo '
              <div class="alert alert-success">
                <p class="centered">You have successfully registered for No-Shave November 2017! Please sign in below.</p>
              </div>
            ';
          }

          if (isset($_GET['reset'])) {
            echo '
              <div class="alert alert-success">
                 <p class="centered">Password Successfully Reset.  Please log back in.</p>
              </div>
            ';
          }

          if (!empty($error_msg)) {
            echo '
              <div class="alert alert-danger">
                ' . $error_msg . '
              </div>
            ';            
          }

        ?>
        
        <div class="card card-container col-md-4 col-md-offset-4 col-sm-6 col-sm-offset-3">
          <div class="panel panel-default">
          <div class="panel-heading"> <strong class="">Please Login</strong></div>

          <div class="panel-body">
            <center><img id="profile-img" class="profile-img-card img-responsive" src="img/no-shave-november-logo-400.png"/></center>
            <br>
            <p id="profile-name" class="profile-name-card"></p>
            <form class="form-signin" action="<?php  echo esc_url($_SERVER['PHP_SELF']); ?>" method="post" name="login_form">
                <label for="email_or_username">Email or Username</label>
                <input type="text" id="email_or_username" name="email_or_username" class="form-control extra-wide" placeholder="Email or Username" required autofocus>
                <br>
                <label for="password">Password</label>
                <input type="password" id="password" name="password" class="form-control extra-wide" placeholder="Password" required>
                <br>
                <button class="btn btn-lg btn-success btn-block btn-signin" type="submit">Log in</button>
            </form><!-- /form -->
            <br>
            <a href="/password" class="forgot-password">
                <p class="text-center">Forgot your Password?</p>
            </a>
            <hr>
            <a href="/register" class="btn btn-lg btn-primary btn-block btn-signin" type="submit">Sign Up</a>
          </div>

          <div class="panel-footer"> Need Help?<a href="" data-toggle="modal" data-target="#contactUS"> Contact Support</a>
          </div>
          </div>
      </div><!-- /card-container -->
      </div>
    </div><!-- /container -->

    <!-- - - - - - -  -->
    <!-- PAGE CONTENT -->
    <!-- - - - - - -  -->

    <!-- GIMME A BREAK -->
    <br><br>
    <!-- GIMME A BREAK -->

    <!-- FOOTER -->
    <div id="footer"></div>
    <!-- FOOTER -->

    <link href="assets/css/bootstrap.css" rel="stylesheet">    
    <link href="assets/css/main.css" rel="stylesheet">
    <link href='assets/css/font.css' rel='stylesheet' type='text/css'>
    <script src="assets/js/bootstrap.min.js" type="text/javascript"></script>
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.6.0/css/font-awesome.min.css" rel="stylesheet">
    <?php include_once("analyticstracking.php") ?>
  </body>
</html>

<?php endif; ?>
