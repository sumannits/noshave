<?php
  //include_once 'includes/db_connect.php';
  include_once 'includes/functions.php';
  //include_once 'includes/process_login.php';
  sec_session_start();
  //print_r($mysqli);
?>



<!DOCTYPE html>
<html lang="en">
  <head>
    <link rel="icon" type="image/png" href="<?php echo base_url; ?>/img/favicon.png">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" />
    <meta name="author" content="No-Shave November">
    <meta name="description" content="Login to No-Shave November. Update your personal, team and organization fundraising pages, track your fundraising progress, and update your account information.">
    <title>Contact | No-Shave November</title>

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
    <script src="<?php echo base_url; ?>/assets/js/loadingoverlay.js" type="text/javascript"></script>
  </head>

    <script>
      function submit_support_ticket_new() {

        // grab field
        var args = {
          support_name: $('#contact_support_full_name').val(),
          support_email: $('#contact_support_email_address').val(),
          support_subject: $('#contact_support_subject').val(),
          support_message: $('#contact_support_message').val()
        }

        // make call
        $.ajax({
          type: 'POST',
          url: './platform/api/submit_support_ticket.php',
          data: args,
          dataType: 'json',
          success: function (data) {
            if (data['status'] == "success") {
              // Close all
              $('#contact_support_full_name').val('');
              $('#contact_support_email_address').val('');
              $('#contact_support_subject').val('');
              $('#contact_support_message').val('');
              // success modal
              $('#success_submit_support_ticket').modal();
            } else {
              $('#failed_submit_support_ticket').modal(); // this should show the modal
            }
          }
        });
      }
    </script>
  <body>
    <header>           
    <?php include_once('menu.php')?>
    </header>

        <section class="contact-us register-div">
          <div class="container">
              <div class="r-container">
                  <h2 class="text-center">Contact Us</h2>
                  
                  <div class="d-md-flex p-border justify-content-center">
                      <div class="c-form-main pr-4">
                          <form class="d-block pt-4 pl-3">
                                <div class="form-group">
                                    <input type="text" class="form-control" placeholder="Full Name" autofocus="" id="contact_support_full_name" name="contact_support_full_name">
                                </div>
                                <div class="form-group">
                                    <input type="email" class="form-control" placeholder="Email Address" id="contact_support_email_address" name="contact_support_email_address">
                                </div>
                                <div class="form-group">
                                    <input type="text" class="form-control" placeholder="Subject" id="contact_support_subject" name="contact_support_subject">
                                </div>
                                <div class="form-group">
                                    <input type="text" id="contact_support_message" name="contact_support_message" class="form-control" placeholder="Message" >
                                </div>
                                <div class="form-group text-right">
                                    <input type="button" onclick="submit_support_ticket_new()" class="btn btn-primary" value="Submit">
                                </div>
                          </form>
                      </div>
                      <div class="addr-main">
                          <ul class="list-unstyled">
                              <li>
                                  <div class="text-center">
                                      <i class="fa fa-map-marker"></i>
                                      <span>Address</span>
                                      <small>2340 Powell Street #293 Emeryville, CA 94608</small>
                                  </div>
                              </li>
                              <li>
                                  <div class="text-center">
                                      <i class="fa fa-phone"></i>
                                      <span>Phone No</span>
                                      <a  href="Javascript:void(0);" data-toggle="modal" data-target="#supportTicket" class="text-warning">Request a Call</a>
                                      <small>Fax: (510) 255-6073</small>
                                  </div>
                              </li>
                              <li>
                                  <div class="text-center">
                                      <i class="fa fa-envelope"></i>
                                      <span>Email Address</span>
                                      <small>support@no-shave.org</small>
                                      <small>media@no-shave.org</small>
                                  </div>
                              </li>
                          </ul>
                      </div>
                  </div>
              </div>
          </div>
        </section>
       <?php include_once('footer.php')?>
       <?php include_once("analyticstracking.php") ?>

  </body>
</html>

