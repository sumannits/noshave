<?php
  include_once 'includes/db_connect.php';
  include_once 'includes/functions.php';
  include_once 'includes/reset.php';
  include_once 'includes/process_login.php';
  sec_session_start();
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <link rel="icon" type="image/png" href="/favicon.png">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" />
    <meta name="author" content="No-Shave November">
    <meta name="description" content="Change or reset you No-Shave November account password.">
    <title>Password Reset | No-Shave November</title>
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
    <script src="<?php echo base_url; ?>/assets/js/loadingoverlay.js" type="text/javascript"></script>
    <script type="text/javascript">
//      $(function(){
//        $("#menu").load("/platform/platform_menu.php"); 
//      });
//      $(function(){
//        $("#footer").load("/platform/platform_footer.html"); 
//      });
    </script>
    <style>
        .form-control.extra-wide {
          width: 100%;
        }
    </style>
      <script>
      $(document).ready(function () {
        toggleFields();

        $("#change").change(function () {
          toggleFields();
        });
      });

      function toggleFields() {
        if ($("#change").val() == "change") {
          // show fields to enter old and new
          $("#old_label").show();
          $("#new_1_label").show();
          $("#new_2_label").show();

          $("#old_password").show();
          $("#new_password").show();
          $("#new_password_confirm").show();
          $("#change_password").show();

          $("#send_password").hide();

        } else if ($("#change").val() == "forgot") {
          // allow them to send email
          $("#send_password").show();

          $("#old_password").hide();
          $("#new_password").hide();
          $("#new_password_confirm").hide();
          $("#change_password").hide();

          $("#old_label").hide();
          $("#new_1_label").hide();
          $("#new_2_label").hide();

        } else {
          // hide all?
          $("#send_password").hide();

          $("#old_password").hide();
          $("#new_password").hide();
          $("#new_password_confirm").hide();
          $("#change_password").hide();

          $("#old_label").hide();
          $("#new_1_label").hide();
          $("#new_2_label").hide();
        }
      }         
      </script>
  </head>
  <body>
    <header>           
    <?php include_once('menu.php')?>
    </header>
    <section class="register-step-2 register-div">
        <div class="container">              
            <div class="text-center">
                <h2 class="mb-0">Password Reset</h2>                 
                <!--<p>Make sure to fill in all the fields as clearly and accurately.</p>-->
                <?php
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
      
      
      
      
      
      
      
    <div class="container">
      <div class="row">

        
        
          <div class="card card-container col-md-4 col-md-offset-4 ">
            <div class="panel panel-default">
            <div class="panel-heading"> <strong class="">Password Reset</strong></div>

            <div class="panel-body">
              <img id="profile-img" class="profile-img-card img-responsive" src="/img/nsn-full-logo.png" />
              <br>
              <p id="profile-name" class="profile-name-card"></p>
              <form class="form-signin" action="<?php  echo esc_url($_SERVER['PHP_SELF']); ?>" method="post" name="login_form">
                  <span id="reauth-email" class="reauth-email"></span>
                  <label for="email">Email Address</label>
                  <input type="email" class="form-control extra-wide" id="email" name="email" placeholder="Enter Email Address" required>
                  <br>
                   <select  class="form-control extra-wide" id="change" name="change">
                      <option value="default">Please select an option</option>
                      <option value="forgot">Forgot Password (Reset Password)</option>
                      <option value="change">Change Password (Set New Password)</option>
                  </select>
                  <div id="old_password" style="display:none;">
                  <br>
                  <label id="old_label" for="old_password" style="display:none;">Old Password</label>
                  <input type="password" class="form-control extra-wide" id="old_password" name="old_password" placeholder="Enter Current Password">
                  <br></div>
                  <label id="new_1_label" for="new_password" style="display:none;">New Password</label>
                  <div id="new_password" style="display:none;"><input type="password" class="form-control extra-wide" id="new_password" name="new_password" placeholder="Enter New Password">
                  <br></div>
                  <label id="new_2_label" for="new_password_confirm" style="display:none;">New Password Confirm</label>
                  <div id="new_password_confirm" style="display:none;"><input type="password" class="form-control extra-wide" id="new_password_confirm" name="new_password_confirm" placeholder="Confirm New Password">
                  </div>

                  <div id="change_password" style="display:none;"><br>
                  <button class="btn btn-lg btn-primary btn-block btn-signin" type="submit">Change Password</button></div>
                  <div id="send_password" style="display:none;"><br>
                  <button class="btn btn-lg btn-primary btn-block btn-signin" type="submit">Send Email</button></div>
              </form><!-- /form -->
              <br>
            </div>

              <div class="panel-footer"> Need Help?<a href="" data-toggle="modal" data-target="#contactUS"> Contact Support</a>
              </div>
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

    <!--<link href="/assets/css/bootstrap.css" rel="stylesheet">    
    <link href="/assets/css/main.css" rel="stylesheet">
    <link href='/assets/css/font.css' rel='stylesheet' type='text/css'>
    <script src="/assets/js/bootstrap.min.js" type="text/javascript"></script>
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.6.0/css/font-awesome.min.css" rel="stylesheet">-->
    <?php include_once('footer.php')?>
    <?php include_once("analyticstracking.php") ?>
  </body>
</html>
