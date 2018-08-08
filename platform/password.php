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
    <link rel="icon" type="image/png" href="<?php echo base_url; ?>/img/favicon.png">
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
          $("#forgot_password").hide();
          $("#change_password").show();
          $("#change_password_div").show();
        } else if ($("#change").val() == "forgot") {
          // allow them to send email
          $("#forgot_password").show();
          $("#change_password").hide();
          $("#change_password_div").hide();

        } else {
          // hide all?
          $("#forgot_password").hide();
          $("#change_password").hide();
          $("#change_password_div").hide();
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
                <form class="form" action="<?php  echo esc_url($_SERVER['PHP_SELF']); ?>" method="post" name="login_form">
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <input type="email" id="email" name="email" placeholder="Enter Email Address" required class="form-control"/>
                                </div>
                            </div>                             
                            <div class="col-12">
                                <div class="form-group">
                                <select  class="form-control" id="change" name="change">
                                    <option value="default">Please select an option</option>
                                    <option value="forgot">Forgot Password (Reset Password)</option>
                                    <option value="change">Change Password (Set New Password)</option>
                                </select>
                                </div>
                            </div> 
                            <div id="change_password_div" style="display:none;">
                              <div class="col-12">
                                  <div class="form-group">                                     
                                    <input type="password" class="form-control" id="old_password" name="old_password" placeholder="Enter Current Password"/>
                                  </div>
                              </div> 
                              <div class="col-12">
                                  <div class="form-group">  
                                     <input type="password" class="form-control" id="new_password" name="new_password" placeholder="Enter New Password"> 
                                  </div>
                              </div> 
                              <div class="col-12">
                                  <div class="form-group">  
                                    <input type="password" class="form-control" id="new_password_confirm" name="new_password_confirm" placeholder="Confirm New Password"> 
                                  </div>
                              </div> 
                            </div>

                            <div class="col-12" id="change_password" style="display:none;">
                                <div class="form-group text-center">                                <input type="submit" class="btn btn-block btn-primary" value="Change Password"/>
                                </div>
                            </div>

                            <div class="col-12" id="forgot_password" style="display:none;">
                                <div class="form-group text-center">                                     
                                    <input type="submit" class="btn btn-block btn-primary" value="Send Email"/>
                                </div>
                            </div>
                            <div class="col-12">
                                  <div class="form-group text-center">  
                                  <div class="panel-footer"> Need Help?<a href="" data-toggle="modal" data-target="#contactUS"> Contact Support</a>
              </div>
                                  </div>
                            </div> 
                            
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
    
    <?php include_once('footer.php')?>
    <?php include_once("analyticstracking.php") ?>
  </body>
</html>
