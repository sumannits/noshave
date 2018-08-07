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

    <!-- Bootstrap core CSS -->
    <link href="<?php echo base_url; ?>/assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?php echo base_url; ?>/assets/css/slick-theme.css" rel="stylesheet">
    <link href="<?php echo base_url; ?>/assets/css/slick.css" rel="stylesheet">   
    <link href="<?php echo base_url; ?>/assets/font-awesome/css/font-awesome.min.css" rel="stylesheet">
    <link href="<?php echo base_url; ?>/assets/fonts/themify-icons.css" rel="stylesheet">   
    <!-- Custom styles for this template -->
    <link href="<?php echo base_url; ?>/assets/css/animations.css" rel="stylesheet">
    <link href="<?php echo base_url; ?>/assets/css/theme.css" rel="stylesheet">
    <link href="<?php echo base_url; ?>/assets/css/responsive.css" rel="stylesheet">
    <script src="<?php echo base_url; ?>/assets/js/jquery.min.js" type="text/javascript"></script>
  </head>
  <body>
    <header>           
    <?php include_once('menu.php')?>
    </header>
      <section class="register-step-2 register-div">
          <div class="container">              
              <div class="text-center">
                  <h2 class="mb-0">Login</h2>                 
                  <p>Make sure to fill in all the fields as clearly and accurately.</p>
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
                  
                  <div class="login-form shadow-sm px-4 pt-5 mx-auto" style="max-width:420px;">
                  <form class="form" action="<?php echo base_url; ?>/login" method="post" name="login_form">
                          <div class="row">
                              <div class="col-12">
                                  <div class="form-group">
                                      <input type="text" id="email_or_username" name="email_or_username" class="form-control" placeholder="Email or Username" required autofocus/>
                                  </div>
                              </div>                             
                              <div class="col-12">
                                  <div class="form-group">
                                      <input type="password" id="password" name="password" class="form-control" placeholder="Password" required/>
                                  </div>
                              </div> 
                              <div class="col-12">
                                  <div class="form-group text-center">                                     
                                      <input type="submit" class="btn btn-block btn-primary" value="Login"/>
                                  </div>
                              </div>
                              <div class="col-12">
                                  <div class="form-group text-center">                                     
                                    <a href="<?php echo base_url; ?>/password" class="forgot-password">
                                        <p class="text-center">Forgot your Password?</p>
                                    </a>
                                  </div>
                              </div>                              
                              <div class="col-12">
                                  <div class="form-group text-center">  
                                    <a href="<?php echo base_url; ?>/register" class="btn btn-block btn-signin btn-success " type="submit">Sign Up</a>
                                    
                                  </div>
                              </div>
                          </div>
                      </form>
                  </div>
              </div>
          </div>
      </section>
      <!--end of register-div-->
        <!--<section class="app-sec" style="background-image: url(images/fbg.png);">
            <div class="container">
                <div class="row animatedParent">
                    <div class="col-12 col-md-5">
                        <figure class="animated bounceInUp animate-2">
                            <img src="images/half-mobile.png" class="img-fluid" alt="">                           
                        </figure>
                    </div>
                    <div class="col-12 col-md-7">
                        <div class="app-btn">
                            <h2>Download App</h2>
                            <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry.</p>
                            <div class="btn-inline animated growIn animate-3">
                                <a href="" class="btn btn-light-outline"><i class="fa fa-apple"></i></a>
                                <a href="" class="btn btn-light-outline"><i class="fa fa-android"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>-->
       <!--end of app-sec-->    
       <?php include_once('footer.php')?>
       <?php include_once("analyticstracking.php") ?>

  </body>
</html>

<?php endif; ?>
