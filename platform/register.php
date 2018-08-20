<?php
  //include_once 'includes/db_connect.php';
  include_once 'includes/functions.php';
  include_once 'includes/brain.php';
  sec_session_start();
?>

<?php
// are they going to join a team on signup? let's remind them
if (isset($_GET['t'])){
  $team_username = $_GET['t'];

  //TEAM INFO
  if ($m_team_id != "0") {
    // Fetch team information
    if ($stmt = $mysqli->prepare("SELECT t_id, t_name FROM team WHERE t_username = ? LIMIT 1")) {
      $stmt->bind_param('s', $team_username);
      $stmt->execute();
      $stmt->store_result();
      $stmt->bind_result($t_id, $t_name);
      $stmt->fetch();
      $stmt->close();

    } else {
      // Unable to grab info, TODO, handle error
    }
  } else {
  }

  $join_team_alert = '
                  <div class="alert alert-success">
                    <p class="centered">Join Team: ' . $t_name . '</p>
                  </div>
                  <br>
                  ';

} else {
  // Nothing to fetch
  $t_name = "";
  $t_id = 0;
  $join_team_alert = "";
  $team_username = "";
}

?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <link rel="icon" type="image/png" href="<?php echo base_url; ?>/img/favicon.png">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" />
    <meta name="author" content="No-Shave November">
    <meta name="description" content="Grow more than just your facial hair. Grow awareness, help fund research and provide care and services to those battling cancer by signing up to officially participate in No-Shave November 2017.">
    <title>Register | No-Shave November</title>
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
    
    <script src='https://www.google.com/recaptcha/api.js'></script>
    <script src="<?php echo base_url; ?>/assets/js/bootstrap.min.js" type="text/javascript"></script>
    <script src="<?php echo base_url; ?>/assets/js/loadingoverlay.js" type="text/javascript"></script>
    <!-- menu and footer -->
    <script type="text/javascript"> 
//      $(function(){
//        $("#menu").load("platform/platform_menu.php"); 
//      });
//      $(function(){
//        $("#footer").load("platform/platform_footer.html"); 
//      });
    </script>
    <!-- menu and footer -->

    <!-- form adventure -->
<script>
    function new_register() {
        // build creds
        var user_info = {
          full_name: $('#n_full_name').val(),
          email_address: $('#n_email_address').val(),
          password: $('#n_password').val(),
          password_verify: $('#n_password_verify').val(),
          username: $('#n_username').val(),
          city: $('#n_city').val(),
          state: $('#n_state').val(),
          country: $('#n_country').val(),
          recaptcha: $('#g-recaptcha-response').val(),
          t_id: <?php echo $t_id; ?>
        }
        // make call
        $.ajax({ 
          type: 'POST',
          url: './platform/api/registration_new.php', 
          data: user_info, 
          dataType: 'json',
          success: function (data) { 
            if (data['status'] == "success") {

              // dummy loading overlay
              $.LoadingOverlay("show");

              // Hide it after 1.5 seconds
              setTimeout(function(){
                  $.LoadingOverlay("hide");
              }, 1500);

              // show continue and hide sign in
              $("#section_4_continue").show();
              $("#section_4_submit").hide();

              // and advance them
              $("#section_4_continue").click();

            } else if (data['fail_code'] == 1) {

              // database error, failed to submit query
              $('#failed_new_registration_message').text(data['reason']);
              $('#failed_new_registration').modal();
              grecaptcha.reset();

            } else if (data['fail_code'] == 2) {

              // database error, failed to submit query
              $('#failed_new_registration_message').text(data['reason']);
              $('#failed_new_registration').modal();
              grecaptcha.reset();

            } else if (data['fail_code'] == 3) {

              // bad input
              $('#failed_new_registration_message').text(data['reason']);
              $('#failed_new_registration').modal();
              grecaptcha.reset();

            } else if (data['fail_code']) {

              // empty fields / catch all
              $('#failed_new_registration_message').text(data['reason']);
              $('#failed_new_registration').modal();
              grecaptcha.reset();

            }
          }
        });
      }

      function old_register() {
        // build creds
        var user_info = {
          full_name: $('#e_full_name').val(),
          email_address: $('#e_email_address').val(),
          username: $('#e_username').val(),
          city: $('#e_city').val(),
          state: $('#e_state').val(),
          country: $('#e_country').val(),
          m_id: $('#e_m_id').val(),
          t_id: <?php echo $t_id; ?>
        }
        // make call
        $.ajax({ 
          type: 'POST',
          url: './platform/api/registration_old.php', 
          data: user_info, 
          dataType: 'json',
          success: function (data) { 
            if (data['status'] == "success") {

              // dummy loading overlay
              $.LoadingOverlay("show");

              // Hide it after 1.5 seconds
              setTimeout(function(){
                  $.LoadingOverlay("hide");
              }, 1500);

              // show continue and hide sign in
              $("#section_3_continue").show();
              $("#section_3_submit").hide();

              // and advance them
              $("#section_3_continue").click();

            } else if (data['fail_code'] == 1) {

              // database error, failed to submit query
              $('#failed_existing_registration_message').text(data['reason']);
              $('#failed_existing_registration').modal();

            } else if (data['fail_code'] == 2) {

              // database error, failed to submit query
              $('#failed_existing_registration_message').text(data['reason']);
              $('#failed_existing_registration').modal();

            } else if (data['fail_code'] == 3) {

              // bad input
              $('#failed_existing_registration_message').text(data['reason']);
              $('#failed_existing_registration').modal();

            } else if (data['fail_code']) {

              // empty fields / catch all
              $('#failed_existing_registration_message').text(data['reason']);
              $('#failed_existing_registration').modal();

            }
          }
        });
      }
      
    function check_n_username() {
        // grab field
        var args = {
          username: $('#n_username').val()
        }

        if ($('#n_username').val() == "") {
          // do nothing

        } else {

          if (typeof current_username === 'undefined') {

            // make call
            $.ajax({ 
              type: 'POST',
              url: './platform/api/lookup_username.php', 
              data: args, 
              dataType: 'json',
              success: function (data) { 
                if (data['user_exists'] == "true") {

                  // warn - username taken
                  $("#n_username_exists_warning").show();

                } else {

                  // user does not exist
                  $("#n_username_exists_warning").hide();

                }
              }
            });

          } else {

            if ($('#n_username').val() == current_username){
              // do nothing
            } else {

              // make call
              $.ajax({ 
                type: 'POST',
                url: './platform/api/lookup_username.php', 
                data: args, 
                dataType: 'json',
                success: function (data) { 
                  if (data['user_exists'] == "true") {

                    // warn - username taken
                    $("#n_username_exists_warning").show();

                  } else {

                    // user does not exist
                    $("#n_username_exists_warning").hide();

                  }
                }
              });
            }
          }
        }
    }
    
    // warn user before losing progress
    $(window).bind('beforeunload', function(){
        return 'Are you sure you want to leave? All progress will be lost.';
    });

    // allow the complete registration to work though
    function unbindUnload() {
        $(window).unbind();
    }
    function existing_member() {
        $('.otherSection').hide();
        if ($('input[name="existing_member"]:checked').val() == "yes") {
            $("#section_2").show();
          //$("#section_1_continue").attr('href', "#section_2");
          //$("#section_2_back").attr('href', "#section_1");
        } else {
            $("#section_4").show();
          //$("#section_1_continue").attr('href', "#section_4");
          //$("#section_4_back").attr('href', "#section_1");
        }

        // hide the continue button
//        $("#section_2_continue").hide();
//        $("#section_3_continue").hide();
//        $("#section_4_continue").hide();

        // show just in case they go back and forth
        //$("#sign_in").show();
    }
    
    // change back for section 5
      function old_member() {
        $("#section_17_back").attr('href', "#section_3");
      };

      function goback(section_name){
          $('.otherSection').hide();
          $("#"+section_name).show();
      }
      // change back for section 5
      function new_member() {
          $('.otherSection').hide();
          $("#section_17").show();
        //$("#section_17_back").attr('href', "#section_4");
      };
    
    function toggleCompletion() {
        $("#section_18_continue").removeClass("disabled");
        $('#terms_checkbox').prop('disabled', true);
    }
      
    function toggleProcessing() {
        $("#make_donation").addClass("hidden");
        $("#section_17_skip").addClass("hidden");
        $("#section_17_continue").removeClass("hidden");
    }
      
    function updateAmount() {
        $("#make_donation").html('Donate $' + commaSeparateNumber($('#donation_amount_other_amount').val()));
    }  
    
    function commaSeparateNumber(val){
        while (/(\d+)(\d{3})/.test(val.toString())){
          val = val.toString().replace(/(\d+)(\d{3})/, '$1'+','+'$2');
        }
        return val;
    }
      
    function waitForNonce(){
        if(typeof client_nonce !== "undefined"){
          // finally, submit
          submit_donation();
        }
        else{
          setTimeout(function(){
              waitForNonce();
          },250);
        }
    }
      
    // get users information with their login
    function sign_in1() {
        // build creds
        var credentials = {
          email: $('#email_login').val(),
          password: $('#password_login').val()
        }
        // make call
        $.ajax({ 
          type: 'POST',
          url: './platform/api/registration_login.php', 
          data: credentials, 
          dataType: 'json',
          success: function (data) { 
            if (data['status'] == "success") {
              $('input[name="e_full_name"]').val(data['m_full_name']);
              $('input[name="e_email_address"]').val(data['m_email']);
              $('input[name="e_username"]').val(data['m_username']);
              $('input[name="e_city"]').val(data['m_city']);
              $('input[name="e_state"]').val(data['m_state']);
              $('input[name="e_country"]').val(data['m_country']);
              $('input[name="e_m_id"]').val(data['m_id']);

              // add username to variable to allow it in the form when checking usernames
              window.current_username = data['m_username'];

              // Yahoo, we found them
              //$('#successful_login').modal();

              // dummy loading overlay
              $.LoadingOverlay("show");

              // Hide it after 3 seconds
              setTimeout(function(){
                  $.LoadingOverlay("hide");
              }, 1500);

              // show continue and hide sign in
              $("#section_2_continue").show();
              $("#sign_in").hide();

              // press the continue button for them
              $("#section_2_continue").click();

            } else if (data['fail_code'] == 0) {

              // missing either
              $('#failed_login_message').text(data['reason']);
              $('#failed_login').modal();

            } else if (data['fail_code']) {

              // bad email
              $('#failed_login_message').text(data['reason']);
              $('#failed_login').modal();

            } else if (data['fail_code'] == 2) {

              // bad password
              $('#failed_login_message').text(data['reason']);
              $('#failed_login').modal();

            }
          }
        });
    }
    
    function submit_donation() {
        // dummy loading overlay
        $.LoadingOverlay("show");

        // Hide it after 1.5 seconds
        setTimeout(function(){
            $.LoadingOverlay("hide");
        }, 3500);

        // lets get the right amount
        if ($('#donation_amount_25').is(":checked")) {
          var final_donation_amount = '25';
        } else if ($('#donation_amount_50').is(":checked")) {
          var final_donation_amount = '50';
        } else if ($('#donation_amount_100').is(":checked")) {
          var final_donation_amount = '100';
        } else if ($('#donation_amount_250').is(":checked")) {
          var final_donation_amount = '250';
        } else if ($('#donation_amount_500').is(":checked")) {
          var final_donation_amount = '500';
        } else {
          var final_donation_amount = $('#donation_amount_other_amount').val();
        }


        // determine whether to pull new or existing fields
        if ($('#n_full_name').val() != "") {

          // gather information to complete the donation
          var donation_info = {
            donation_name: $('#donation_name').val(),
            donation_company: $('#donation_company').val(),
            donation_email: $('#n_email_address').val(),
            donation_username: $('#n_username').val(),
            donation_amount: final_donation_amount,
            donation_comment: $('#donation_comment').val(),
            donation_nonce: client_nonce
          }

        } else {

          // gather information to complete the donation
          var donation_info = {
            donation_name: $('#donation_name').val(),
            donation_company: $('#donation_company').val(),
            donation_email: $('#e_email_address').val(),
            donation_username: $('#e_username').val(),
            donation_amount: final_donation_amount,
            donation_comment: $('#donation_comment').val(),
            donation_nonce: client_nonce
          }
        }

        // make call
        $.ajax({ 
          type: 'POST',
          url: './platform/api/registration_donation.php', 
          data: donation_info, 
          dataType: 'json',
          success: function (data) { 
            if (data['status'] == "success") {

              // toggle those buttons
              toggleProcessing();

              // advance the page
              $("#section_17_continue").click();


            } else if (data['fail_code'] == 0) {

              // do something
              $('#failed_donation_message').text(data['reason']);
              $('#failed_donation').modal();

            } else if (data['fail_code'] == 1) {

              // do something
              $('#failed_donation_message').text(data['reason']);
              $('#failed_donation').modal();

            } else if (data['fail_code']) {

              // we don't know what happened
              $('#failed_donation_message').text(data['reason']);
              $('#failed_donation').modal();

            }
          }
        });

    }
      
    function toggleAmount() {
        if ($("input[name=donation_amount]:checked").val() === "other") {
          $("#other_input").removeClass("hidden");
          $("#make_donation").html('Donate $' + commaSeparateNumber($('#donation_amount_other_amount').val()));
        } else {
          $("#other_input").addClass("hidden");
          $("#make_donation").html('Donate $' + $("input[name=donation_amount]:checked").val());
          $("#donation_amount_value").val($("input[name=donation_amount]:checked").val());
        }
    }
      
     
    $(document).ready(function() {
        $(".selectType").click(function() {
            var selectUserType = $(this).attr('selVal');
            $('.selUser').each(function() {
                $(this).removeAttr('checked');
            });
            $('#'+selectUserType).attr('checked', 'checked');
            if(selectUserType!=''){
                existing_member();
            }
        });
        
        $("input[name=donation_amount]").change(function () {
            toggleAmount();
        });  
    });
    

</script>
    <!-- form adventure -->

    <!-- custom css -->
    <style>
      .fa-arrow-right {
        color: white;
      }
      .fa-arrow-left {
        color: black;
      }
      .required-field {
        color: red;
      }
      .fa-check {
        color: white;
      }
      .fa-undo {
        color: black;
      }
      .fa-sign-in {
        color: white;
      }
      .big-button {
        height: 100%;
        width: 100%;
      }
      .hide-on-start {
        display: none;
      }
      .form-control.full-input-width {
        width: 100%;
      }
      .btn-checkbox {
        width: 200px;
        height: 200px;
        vertical-align: middle;
        display: table-cell;
      }
      .btn-table {
        display: table;
        margin: auto;
      }
      .fa-child {
        color: #555;
      }
      .fa-repeat {
        color: #555;
      }
      .center-recaptcha {
        display: inline-block;
      }
      .fa-paper-plane-o {
        color: white;
      }
      .otherSection{display: none;}
      .modal-body h4{
        color: #333;
        font-weight: 400;
        font-size: 20px;
      }
      .hidden{display: none;}
    </style>
    <!-- custom css -->
  </head>
  <body>
    <header>           
    <?php include_once('menu.php')?>
    </header>
    <?php if($join_team_alert!=''){?>
      <div class="row">
          <div class="col-md-12">
              <?php echo $join_team_alert; ?>
          </div>
      </div>
    <?php }?>
      <section class="register-step-1 register-div otherSection" style="display:block;" id="section_1">
          <div class="container">
              <div class="r-container text-center">
                  <h2>Registration</h2>
                  <h3>Did you participate in No-Shave November last year?</h3>
                  <p>If you signed up to participate last year we can retrieve your account.</p>
                  <div class="d-flex justify-content-center">
                      <a href="Javascript:void(0);" class="btn-box selectType" selVal="no">
                          <input type="radio" name="existing_member" id="no" class="selUser" value="no" checked>
                          <figure><img src="./img/icon1.png" class="img-fluid" alt=""></figure>
                          <span>New</span>
                      </a>
                      <a href="Javascript:void(0);" class="btn-box selectType" selVal="yes">
                          <input type="radio" name="existing_member" id="yes" class="selUser" value="yes">
                          <figure><img src="./img/icon2.png" class="img-fluid" alt=""></figure>
                          <span>Returning</span>
                      </a>
                  </div>
                  <div class="col-12 col-md-12">&nbsp;</div>
                  <div class="col-12 col-md-8">
                      <!--<button name="section_1_continue" id="section_1_continue" type="button" class="btn btn-success pull-right" data-toggle="tab" href="#section_2" onclick="existing_member()">Continue <i class="fa fa-arrow-right" aria-hidden="true"></i></button>-->
                  </div>
              
              </div>
          </div>
      </section>
      
        <section class="register-step-2 register-div otherSection" id="section_2">
            <div class="container">
                <div class="text-center">
                    <h2 class="mb-0">Please enter your login information.</h2>                 
                    <p>Welcome back! Please sign in so that we can pull up your account information.</p>

                    <div class="reg-form shadow-sm">
                        <form class="form">
                            <div class="row">
                                <div class="col-md-12 col-12">
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <input type="email" name="email_login" id="email_login" class="form-control" placeholder="Email Address *" required="required" autocomplete="off" data-error="Please enter a valid email address.">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12 col-12">
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <input type="password" class="form-control" name="password_login" id="password_login" placeholder="Password *" required="required" autocomplete="off">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group text-right">
                                        <button name="section_2_back" id="section_2_back" type="button" class="btn btn-outline-primary" data-toggle="tab" onclick="goback('section_1')"><i class="fa fa-arrow-left" aria-hidden="true"></i> Go Back</button>
                                        <button name="sign_in" id="sign_in"  type="button" class="btn btn-primary pull-right" onclick="sign_in1()">Sign In <i class="fa fa-sign-in" aria-hidden="true"></i> </button> &nbsp;
              <button name="section_2_continue" id="section_2_continue" style="display:none;" type="button" class="btn btn-success pull-right" onclick="goback('section_3')">Continue <i class="fa fa-arrow-right" aria-hidden="true"></i></button>
                                        
                                        <a href="<?php echo base_url; ?>/password" class="forgot-password">
                                            <p class="text-center">Forgot your Password?</p>
                                        </a>
                                        
                                        <!--<input type="submit" class="btn btn-outline-primary" value="Back">
                                        <input type="reset" class="btn btn-primary" value="Submit">-->
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </section>
      
        <section class="register-step-2 register-div otherSection" id="section_3">
            <div class="container">
                <div class="text-center">
                    <h2 class="mb-0">Please review and update your information.</h2>                 
                    <p>Make sure to fill in all the fields as clearly and accurately as possible.</p>

                    <div class="reg-form shadow-sm">
                        <form class="form">
                            <div class="row">
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <input type="text" class="form-control" placeholder="Your Name *" required="required" autocomplete="off" autofocus="" maxlength="128" name="e_full_name" id="e_full_name">
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <input type="email" name="e_email_address" id="e_email_address" class="form-control" placeholder="Email Address *" required="required" autocomplete="off" data-error="Please enter a valid email address." maxlength="128">
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <input type="text" class="form-control" placeholder="Username *" required="required" autocomplete="off" name="e_username" id="e_username" onkeyup="check_e_username()" maxlength="128" >
                                    </div>
                                    <div id="e_username_exists_warning" class="alert alert-danger hide-on-start" role="alert">Uh oh! Looks like that username is already taken.</div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <input type="text" class="form-control" placeholder="City *" required="required" autocomplete="off" name="e_city" id="e_city" maxlength="64">
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <input type="text" class="form-control" placeholder="State/ Region" name="e_state" id="e_state" maxlength="64">
                                    </div>
                                </div>
                                 <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <input type="text" class="form-control" placeholder="Country *" required="required" autocomplete="off" name="e_country" id="e_country" maxlength="64">
                                    </div>
                                </div>
                                <input id="e_m_id" name="e_m_id" type="hidden">
                                <div class="col-12">
                                    <div class="form-group text-right">
                                        <button name="section_3_back" id="section_3_back" type="button" class="btn btn-outline-primary" data-toggle="tab" onclick="goback('section_2')"><i class="fa fa-arrow-left" aria-hidden="true"></i> Go Back</button>
                                        <button href="#" name="section_3_submit" id="section_3_submit"  type="button" class="btn btn-primary pull-right" onclick="old_register()">Submit <i class="fa fa-paper-plane-o" aria-hidden="true"></i></button> &nbsp;
              <button name="section_3_continue" id="section_3_continue" style="display:none;" type="button" class="btn btn-success pull-right" data-toggle="tab" onclick="goback('section_17')">Continue <i class="fa fa-arrow-right" aria-hidden="true"></i></button>
                                        
                                        
                                        
                                        <!--<input type="submit" class="btn btn-outline-primary" value="Back">
                                        <input type="reset" class="btn btn-primary" value="Submit">-->
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </section>
      
        <section class="register-step-2 register-div otherSection" id="section_4">
            <div class="container">
                <div class="text-center">
                    <h2 class="mb-0">Please Enter Your Information</h2>                 
                    <p>Make sure to fill in all the fields as clearly and accurately as possible.</p>

                    <div class="reg-form shadow-sm">
                        <form class="form">
                            <div class="row">
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <input type="text" class="form-control" placeholder="Your Name *" required="required" autocomplete="off" autofocus="" maxlength="128" name="n_full_name" id="n_full_name">
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <input type="email" name="n_email_address" id="n_email_address" class="form-control" placeholder="Email Address *" required="required" autocomplete="off" maxlength="128">
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <input type="password" class="form-control" name="n_password" id="n_password" placeholder="Password *" minlength="6" required="required" autocomplete="off">
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <input type="password" class="form-control" placeholder="Verify Password *" required="required" name="n_password_verify" id="n_password_verify" autocomplete="off" data-match="#n_password" data-match-error="Uh oh, this doesn't match the password above.">
                                    </div>
                                </div>                             
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <input type="text" class="form-control" placeholder="Username *" required="required" autocomplete="off" name="n_username" id="n_username" onkeyup="check_n_username()" maxlength="128" >
                                    </div>
                                    <div id="n_username_exists_warning" class="alert alert-danger hide-on-start" role="alert">Uh oh! Looks like that username is already taken.</div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <input type="text" class="form-control" placeholder="City *" required="required" autocomplete="off" name="n_city" id="n_city" maxlength="64">
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <input type="text" class="form-control" placeholder="State/ Region" name="n_state" id="n_state" maxlength="64">
                                    </div>
                                </div>
                                 <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <input type="text" class="form-control" placeholder="Country *" required="required" autocomplete="off" name="n_country" id="n_country" maxlength="64">
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="centered">
                                        <div class="g-recaptcha center-recaptcha" data-sitekey="6LfQi2gUAAAAADJr1olB_ilCPMFjfhLHYkyRydXs"></div>
                                    </div>
                                </div>
                                
                                <div class="col-12">
                                    <div class="form-group text-right">
                                        <button name="section_4_back" id="section_4_back" type="button" class="btn btn-outline-primary" data-toggle="tab" onclick="goback('section_1')"><i class="fa fa-arrow-left" aria-hidden="true"></i> Go Back</button>
                                        <button href="#" name="section_4_submit" id="section_4_submit"  type="button" class="btn btn-primary pull-right" onclick="new_register()">Submit <i class="fa fa-paper-plane-o" aria-hidden="true"></i></button> &nbsp;
              <button name="section_4_continue" id="section_4_continue" style="display:none;" type="button" class="btn btn-success pull-right" data-toggle="tab" href="#section_17" onclick="new_member()">Continue <i class="fa fa-arrow-right" aria-hidden="true"></i></button>
                                        
                                        
                                        
                                        <!--<input type="submit" class="btn btn-outline-primary" value="Back">
                                        <input type="reset" class="btn btn-primary" value="Submit">-->
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </section>
      
        <section class="register-step-3 register-div otherSection" id="section_17">
            <div class="container">
                <div class="text-left">
                    <h2 class="mb-0">Make a Donation</h2>                 
                    <p class="mb-5">No-Shave November and its funded programs are putting your donaftion dollars to work, investing in groundbreaking cancer research and providing free information and services to cancer patients and their caregivers.</p>

                    <h5>Amount</h5>
                    <ul class="list-inline">
                        <li>
                            <div class="clearfix">
                                <input type="radio" name="donation_amount" id="donation_amount_25" value="25">
                                <label for="donation_amount_25">$25</label>
                            </div>
                        </li>
                        <li>
                            <div class="clearfix">
                                <input type="radio" name="donation_amount" id="donation_amount_50" value="50" checked>
                                <label for="donation_amount_50">$50</label>
                            </div>
                        </li>
                        <li>
                            <div class="clearfix">
                                <input type="radio" name="donation_amount" id="donation_amount_100" value="100">
                                <label for="donation_amount_100">$100</label>
                            </div>
                        </li>
                        <li>
                            <div class="clearfix">
                                <input type="radio" name="donation_amount" id="donation_amount_250" value="250">
                                <label for="donation_amount_250">$250</label>
                            </div>
                        </li>
                        <li>
                            <div class="clearfix">
                                <input type="radio" name="donation_amount" id="donation_amount_500" value="500">
                                <label for="donation_amount_500">$500</label>
                            </div>
                        </li>
                        <li>
                            <div class="clearfix">
                                <input type="radio" name="donation_amount" id="donation_amount_other" value="other">
                                <label for="donation_amount_other">Other</label>
                            </div>
                        </li>
                    </ul>
                    <div class="form-group hidden" id="other_input" visible="false">
                        <label for="donation_amount_other_amount" class="col-md-3 control-label">Other <span class="required-field">*</span></label>

                        <div class="col-md-3">
                          <input type="number" class="form-control full-input-width" id="donation_amount_other_amount" placeholder="Amount" name="donation_amount_other" onkeyup="updateAmount()">
                        </div>
                    </div>
                    <!--<h6>Most people are giving <span>$100</span> right now. Please give what you can.</h6>-->

                    <div class="pay-form">
                        <form class="form" id="donation_form">
                            <input type="hidden" name="donation_amount" id="donation_amount_value" value="50">
                            <div class="row">
                                <div class="col-md-5 col-12">
                                    <h5 class="pt-5 mb-4">Personal Information</h5>
                                    <div class="form-group">
                                        <input type="text" required="required" class="form-control" placeholder="Name" id="donation_name" autofocus="">
                                    </div>                             
                                    <div class="form-group">
                                        <input type="text" class="form-control" id="donation_company" placeholder="Company">
                                    </div>                             
                                    <div class="form-group">
                                        <input type="text" class="form-control" id="donation_comment" placeholder="Comment">
                                    </div>
                                </div>
                                <div class="col-md-7 col-12">
                                   <div class="border-pay">
                                       <h5 class="mb-3">Payment Method</h5>
                                       <div class="col-md-7">
                                            <div id="dropin-container"></div>
                                        </div>
                                       <!--<ul class="list-inline">
                                           <li>
                                               <div class="clearfix">
                                                   <input type="radio" id="pay1" name="pay" checked="">
                                                   <label for="pay1"><img src="images/paypal.png" class="img-fluid" alt="paypal"></label>
                                               </div>
                                           </li>
                                           <li>
                                               <div class="clearfix">
                                                   <input type="radio" id="pay2" name="pay">
                                                   <label for="pay2"><img src="images/card.png" class="img-fluid" alt="payment"></label>
                                               </div>
                                           </li>
                                       </ul>-->
                                       <!--<div class="row">
                                           <div class="col-6">
                                               <div class="form-group">
                                                  <input type="text" class="form-control" placeholder="Name On Your Card">
                                              </div> 
                                           </div>
                                           <div class="col-6">
                                               <div class="form-group">
                                                  <input type="text" class="form-control" placeholder="Card Number">
                                              </div> 
                                           </div>
                                           <div class="col-4">
                                               <div class="form-group">
                                                  <input type="text" class="form-control" placeholder="Expiry Date MM/YYYY">
                                              </div> 
                                           </div>
                                           <div class="col-4">
                                               <div class="form-group">
                                                  <input type="text" class="form-control" placeholder="CVV">
                                              </div> 
                                           </div>
                                           <div class="col-4">
                                               <div class="form-group">
                                                  <input type="text" class="form-control" placeholder="Postal">
                                              </div> 
                                           </div>
                                       </div>-->
                                   </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group text-right">
                                        <button name="section_17_back" id="section_17_back" type="button" class="btn btn-outline-primary" data-toggle="tab" onclick="goback('section_4')"><i class="fa fa-arrow-left" aria-hidden="true"></i> Go Back</button> &nbsp;
                                        <button name="section_17_continue" id="section_17_continue"  type="button" class="btn btn-primary pull-right hidden" onclick="goback('section_18')" >Continue <i class="fa fa-arrow-right" aria-hidden="true"></i></button>&nbsp;
                <button href="#" id="make_donation" name="make_donation" type="submit" class="btn btn-primary pull-right" onclick="waitForNonce()">Donate $50</button>
                                        
                                    </div>
                                    <a id="section_17_skip" class="pull-right" onclick="goback('section_18')"><i>skip</i></a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </section>
      
        <section class="register-step-3 register-div otherSection" id="section_18">
            <div class="container">
                <div class="text-left">
                    <h2 class="mb-0">Complete Registration</h2>                 
                    <p class="mb-5">Good luck and Hairy November!</p>
                    <h5>Terms of Service</h5>
                    <div class="row">
                        <div class="col-md-12 col-12 text-center">
                            <div style="border: 1px solid #e5e5e5; height: 200px; overflow: auto; padding: 10px;">
                        <h4>No-Shave November Terms of Service</h4>
                        <hr>
                        <p>Please read these Terms of Service ("Terms", "Terms of Service") carefully before using the https://www.no-shave.org website (the "Service") operated by Matthew Hill Foundation, Inc. ("us", "we", or "our").</p>
                        <p>Your access to and use of the Service is conditioned upon your acceptance of and compliance with these Terms. These Terms apply to all visitors, users and others who wish to access or use the Service.<p>
                        <p>By accessing or using the Service you agree to be bound by these Terms. If you disagree with any part of the terms then you do not have permission to access the Service.</p>
                        <h4>Communications</h4>
                        <hr>
                        <p>By creating an Account on our service, you agree to subscribe to newsletters, marketing or promotional materials and other information we may send. However, you may opt out of receiving any, or all, of these communications from us by following the unsubscribe link or instructions provided in any email we send.</p>
                        <h4>Purchases</h4>
                        <hr>
                        <p>If you wish to purchase any product or service made available through the Service ("Purchase"), you may be asked to supply certain information relevant to your Purchase including, without limitation, your credit card number, the expiration date of your credit card, your billing address, and your shipping information.</p>
                        <p>You represent and warrant that: (i) you have the legal right to use any credit card(s) or other payment method(s) in connection with any Purchase; and that (ii) the information you supply to us is true, correct and complete.</p>
                        <p>The service may employ the use of third party services for the purpose of facilitating payment and the completion of Purchases. By submitting your information, you grant us the right to provide the information to these third parties subject to our Privacy Policy.</p>
                        <h4>Content</h4>
                        <hr>
                        <p>Our Service allows you to post, link, store, share and otherwise make available certain information, text, graphics, videos, or other material ("Content"). You are responsible for the Content that you post on or through the Service, including its legality, reliability, and appropriateness.</p> 
                        <p>By posting Content on or through the Service, You represent and warrant that: (i) the Content is yours (you own it) and/or you have the right to use it and the right to grant us the rights and license as provided in these Terms, and (ii) that the posting of your Content on or through the Service does not violate the privacy rights, publicity rights, copyrights, contract rights or any other rights of any person or entity. We reserve the right to terminate the account of anyone found to be infringing on a copyright.</p> 
                        <p>You retain any and all of your rights to any Content you submit, post or display on or through the Service and you are responsible for protecting those rights. We take no responsibility and assume no liability for Content you or any third party posts on or through the Service. However, by posting Content using the Service you grant us the right and license to use, modify, publicly perform, publicly display, reproduce, and distribute such Content on and through the Service. You agree that this license includes the right for us to make your Content available to other users of the Service, who may also use your Content subject to these Terms.</p> 
                        <p>Matthew Hill Foundation, Inc. has the right but not the obligation to monitor and edit all Content provided by users.</p> 
                        <p>In addition, Content found on or through this Service are the property of Matthew Hill Foundation, Inc. or used with permission. You may not distribute, modify, transmit, reuse, download, repost, copy, or use said Content, whether in whole or in part, for commercial purposes or for personal gain, without express advance written permission from us.</p> 
                        <h4>Accounts</h4>
                        <hr>
                        <p>When you create an account with us, you guarantee that you are above the age of 18, and that the information you provide us is accurate, complete, and current at all times. Inaccurate, incomplete, or obsolete information may result in the immediate termination of your account on the Service.</p> 
                        <p>You are responsible for maintaining the confidentiality of your account and password, including but not limited to the restriction of access to your computer and/or account. You agree to accept responsibility for any and all activities or actions that occur under your account and/or password, whether your password is with our Service or a third-party service. You must notify us immediately upon becoming aware of any breach of security or unauthorized use of your account.</p> 
                        <p>You may not use as a username the name of another person or entity or that is not lawfully available for use, a name or trademark that is subject to any rights of another person or entity other than you, without appropriate authorization. You may not use as a username any name that is offensive, vulgar or obscene.</p> 
                        <p>We reserve the right to refuse service, terminate accounts, remove or edit content, or cancel orders in our sole discretion.</p> 
                        <h4>Copyright Policy</h4>
                        <hr>
                        <p>We respect the intellectual property rights of others. It is our policy to respond to any claim that Content posted on the Service infringes on the copyright or other intellectual property rights ("Infringement") of any person or entity.</p> 
                        <p>If you are a copyright owner, or authorized on behalf of one, and you believe that the copyrighted work has been copied in a way that constitutes copyright infringement, please submit your claim via email to support@no-shave.org, with the subject line: "Copyright Infringement" and include in your claim a detailed description of the alleged Infringement as detailed below, under "DMCA Notice and Procedure for Copyright Infringement Claims"</p> 
                        <p>You may be held accountable for damages (including costs and attorneys' fees) for misrepresentation or bad-faith claims on the infringement of any Content found on and/or through the Service on your copyright.</p> 
                        <p>DMCA Notice and Procedure for Copyright Infringement Claims</p> 
                        <p>You may submit a notification pursuant to the Digital Millennium Copyright Act (DMCA) by providing our Copyright Agent with the following information in writing (see 17 U.S.C 512(c)(3) for further detail):
                        <br>• an electronic or physical signature of the person authorized to act on behalf of the owner of the copyright's interest;
                        <br>• a description of the copyrighted work that you claim has been infringed, including the URL (i.e., web page address) of the location where the copyrighted work exists or a copy of the copyrighted work;
                        <br>• identification of the URL or other specific location on the Service where the material that you claim is infringing is located;
                        <br>• your address, telephone number, and email address;
                        <br>• a statement by you that you have a good faith belief that the disputed use is not authorized by the copyright owner, its agent, or the law;
                        <br>• a statement by you, made under penalty of perjury, that the above information in your notice is accurate and that you are the copyright owner or authorized to act on the copyright owner's behalf.</p> 
                        <p>You can contact our Copyright Agent via email at support@no-shave.org
                        <h4>Intellectual Property</h4>
                        <hr>
                        <p>The Service and its original content (excluding Content provided by users), features and functionality are and will remain the exclusive property of Matthew Hill Foundation, Inc. and its licensors. The Service is protected by copyright, trademark, and other laws of both the United States and foreign countries. Our trademarks and trade dress may not be used in connection with any product or service without the prior written consent of Matthew Hill Foundation, Inc..</p>
                        <h4>Links To Other Web Sites</h4>
                        <hr>
                        <p>Our Service may contain links to third party web sites or services that are not owned or controlled by Matthew Hill Foundation, Inc..</p> 
                        <p>Matthew Hill Foundation, Inc. has no control over, and assumes no responsibility for the content, privacy policies, or practices of any third party web sites or services. We do not warrant the offerings of any of these entities/individuals or their websites.</p> 
                        <p>You acknowledge and agree that Matthew Hill Foundation, Inc. shall not be responsible or liable, directly or indirectly, for any damage or loss caused or alleged to be caused by or in connection with use of or reliance on any such content, goods or services available on or through any such third party web sites or services.</p> 
                        <p>We strongly advise you to read the terms and conditions and privacy policies of any third party web sites or services that you visit.</p> 
                        <h4>Termination</h4>
                        <hr>
                        <p>We may terminate or suspend your account and bar access to the Service immediately, without prior notice or liability, under our sole discretion, for any reason whatsoever and without limitation, including but not limited to a breach of the Terms.</p> 
                        <p>If you wish to terminate your account, you may simply discontinue using the Service.</p> 
                        <p>All provisions of the Terms which by their nature should survive termination shall survive termination, including, without limitation, ownership provisions, warranty disclaimers, indemnity and limitations of liability.</p> 
                        <h4>Indemnification</h4>
                        <hr>
                        <p>You agree to defend, indemnify and hold harmless Matthew Hill Foundation, Inc. and its licensee and licensors, and their employees, contractors, agents, officers and directors, from and against any and all claims, damages, obligations, losses, liabilities, costs or debt, and expenses (including but not limited to attorney's fees), resulting from or arising out of a) your use and access of the Service, by you or any person using your account and password; b) a breach of these Terms, or c) Content posted on the Service.</p>     
                        <h4>Limitation Of Liability</h4>
                        <hr>
                        <p>In no event shall Matthew Hill Foundation, Inc., nor its directors, employees, partners, agents, suppliers, or affiliates, be liable for any indirect, incidental, special, consequential or punitive damages, including without limitation, loss of profits, data, use, goodwill, or other intangible losses, resulting from (i) your access to or use of or inability to access or use the Service; (ii) any conduct or content of any third party on the Service; (iii) any content obtained from the Service; and (iv) unauthorized access, use or alteration of your transmissions or content, whether based on warranty, contract, tort (including negligence) or any other legal theory, whether or not we have been informed of the possibility of such damage, and even if a remedy set forth herein is found to have failed of its essential purpose.</p> 
                        <h4>Disclaimer</h4>
                        <hr>
                        <p>Your use of the Service is at your sole risk. The Service is provided on an "AS IS" and "AS AVAILABLE" basis. The Service is provided without warranties of any kind, whether express or implied, including, but not limited to, implied warranties of merchantability, fitness for a particular purpose, non-infringement or course of performance.</p> 
                        <p>Matthew Hill Foundation, Inc. its subsidiaries, affiliates, and its licensors do not warrant that a) the Service will function uninterrupted, secure or available at any particular time or location; b) any errors or defects will be corrected; c) the Service is free of viruses or other harmful components; or d) the results of using the Service will meet your requirements.</p> 
                        <h4>Exclusions</h4>
                        <hr>
                        <p>Some jurisdictions do not allow the exclusion of certain warranties or the exclusion or limitation of liability for consequential or incidental damages, so the limitations above may not apply to you.</p>
                        <h4>Governing Law</h4>
                        <hr>
                        <p>These Terms shall be governed and construed in accordance with the laws of California, United States, without regard to its conflict of law provisions.</p> 
                        <p>Our failure to enforce any right or provision of these Terms will not be considered a waiver of those rights. If any provision of these Terms is held to be invalid or unenforceable by a court, the remaining provisions of these Terms will remain in effect. These Terms constitute the entire agreement between us regarding our Service, and supersede and replace any prior agreements we might have had between us regarding the Service.</p> 
                        <h4>Changes</h4>
                        <hr>
                        <p>We reserve the right, at our sole discretion, to modify or replace these Terms at any time. If a revision is material we will provide at least 30 days notice prior to any new terms taking effect. What constitutes a material change will be determined at our sole discretion.</p> 
                        <p>By continuing to access or use our Service after any revisions become effective, you agree to be bound by the revised terms. If you do not agree to the new terms, you are no longer authorized to use the Service.</p> 
                        <h4>Contact Us</h4>
                        <hr>
                        <p>If you have any questions about these Terms, please <a href="#" data-toggle="modal" data-target="#contactUS">contact us</a>.</p>
                      </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-xs-9 col-xs-offset-3">
                            <div class="checkbox">
                                <label>
                                    <input id="terms_checkbox" type="checkbox" name="agree" value="agree" onchange="toggleCompletion()"/> I agree to the terms and conditions<br>
                                </label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-12">
                        <div class="form-group text-right">
                            <button name="section_18_back" id="section_18_back" type="button" class="btn btn-outline-primary" onclick="goback('section_17')"><i class="fa fa-arrow-left" aria-hidden="true"></i> Go Back</button> &nbsp;
              <a name="section_18_continue" id="section_18_continue" type="button" class="btn btn-block btn-primary disabled" href="<?php echo base_url; ?>/login?new=true" onclick="unbindUnload()" style="width: 314px;display:inline-block;">Complete Registration &nbsp;<i class="fa fa-check" aria-hidden="true"></i></a>   
                        </div>
                    </div>
                </div>
            </div>
        </section>
      <!-- MODALS -->

    <div id="failed_login" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="failed_login">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
              <h4 class="modal-title">Failed to Login</h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            
          </div>
          <div class="modal-body">
            <h4 id="failed_login_message" name="failed_login_message"></h4>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Try Again</button>
          </div>
        </div><!-- /.modal-content -->
      </div><!-- /.modal-dialog -->
    </div>

    <div id="successful_login" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="successful_login">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
              <h4 class="modal-title">Success</h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            
          </div>
          <div class="modal-body">
            <h4>Great news! We found your account!</h4>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-success" data-dismiss="modal" data-toggle="tab" href="#section_3">Sweet! Continue <i class="fa fa-arrow-right" aria-hidden="true"></i></button>
          </div>
        </div><!-- /.modal-content -->
      </div><!-- /.modal-dialog -->
    </div>

    <div id="failed_new_registration" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="failed_new_registration">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
              <h4 class="modal-title">Failed to Create Account</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            
          </div>
          <div class="modal-body">
            <h4 id="failed_new_registration_message" name="failed_new_registration_message"></h4>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Try Again</button>
          </div>
        </div><!-- /.modal-content -->
      </div><!-- /.modal-dialog -->
    </div>

    <div id="failed_existing_registration" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="failed_existing_registration">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">Failed to Update Information and Register for 2017</h4>
          </div>
          <div class="modal-body">
            <h4 id="failed_existing_registration_message" name="failed_existing_registration_message"></h4>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Try Again</button>
          </div>
        </div><!-- /.modal-content -->
      </div><!-- /.modal-dialog -->
    </div>

    <div id="failed_donation" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="failed_donation">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
              <h4 class="modal-title">Failed to Make Donation</h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            
          </div>
          <div class="modal-body">
            <h4 id="failed_donation_message" name="failed_donation_message"></h4>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Try Again</button>
          </div>
        </div><!-- /.modal-content -->
      </div><!-- /.modal-dialog -->
    </div>
 
    <!-- MODALS -->
    
    <!-- BRAIN -->
    <script src="https://js.braintreegateway.com/js/braintree-2.26.0.min.js"></script>
    <script>
      braintree.setup('<?php echo($clientToken = Braintree_ClientToken::generate()); ?>', 'dropin', {
        container: 'dropin-container',
        onPaymentMethodReceived: function (obj) {
          // Do some logic in here.
          // When you're ready to submit the form:
          //myForm.submit();
          window.client_nonce = obj.nonce;
        }
      });
    </script>


    <!--<script src="./assets/js/bootstrap.min.js" type="text/javascript"></script>
    <script src="./assets/js/loadingoverlay.js" type="text/javascript"></script>
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.6.0/css/font-awesome.min.css" rel="stylesheet">-->
    <?php include_once('footer.php')?>
    <?php include_once("analyticstracking.php") ?>
  </body>
</html>
