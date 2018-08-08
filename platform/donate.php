<?php
//include_once 'includes/psl-config.php';
//include_once 'includes/db_connect.php';
include_once 'includes/functions.php';
include_once 'includes/brain.php';
sec_session_start();

// set defaults to empty
$prefill_search = "";
$hide_search_label = "";
$search_result_get = "";
$member_checked = "";
$team_checked = "";
$org_checked = "";

// Get the GET vars from any page
if (isset($_GET['c'], $_GET['id'])) {
  $donation_classifier = $_GET['c'];
  $donation_to_id = $_GET['id'];

  if ($donation_classifier == 1) {
    // check box
    $member_checked = "checked";

    // PREFIL THE SELECTION
    if ($stmt = $mysqli->prepare("SELECT m_full_name, m_username FROM member WHERE m_id = ? LIMIT 1")) {
        $stmt->bind_param('i', $donation_to_id);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($m_full_name, $m_username);
        $stmt->fetch();

        $search_result_get = '<select id="select_result" name="select_result" class="form-control full-input-width">
                                <option value="' . $donation_to_id . '">' . $m_full_name . ' (' . $m_username . ')</option>
                              </select>';

        $prefill_search = $m_full_name;

    } else {
      //failed to fetch
    }

  } elseif ($donation_classifier == 2) {
    // check box
    $team_checked = "checked";

    // PREFIL THE SELECTION
    if ($stmt = $mysqli->prepare("SELECT t_name, t_username FROM team WHERE t_id = ? LIMIT 1")) {
        $stmt->bind_param('i', $donation_to_id);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($t_name, $t_username);
        $stmt->fetch();

        $search_result_get = '<select id="select_result" name="select_result" class="form-control full-input-width">
                                <option value="' . $donation_to_id . '">' . $t_name . ' (' . $t_username . ')</option>
                              </select>';

        $prefill_search = $t_name;

    } else {
      //failed to fetch
    }

  } elseif ($donation_classifier == 3) {
    // check box
    $org_checked = "checked";

    // PREFIL THE SELECTION
    if ($stmt = $mysqli->prepare("SELECT o_name, o_username FROM org WHERE o_id = ? LIMIT 1")) {
        $stmt->bind_param('i', $donation_to_id);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($o_name, $o_username);
        $stmt->fetch();

        $search_result_get = '<select id="select_result" name="select_result" class="form-control full-input-width">
                                <option value="' . $donation_to_id . '">' . $o_name . ' (' . $o_username . ')</option>
                              </select>';

        $prefill_search = $o_name;

    } else {
      //failed to fetch
    }

  } else {
    // SOL

  }

} else {
  // hide the select lable
  $hide_search_label = 'style="display:none;"';
}

// }

?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <link rel="icon" type="image/png" href="<?php echo base_url; ?>/img/favicon.png">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" />
    <meta name="author" content="No-Shave November">
    <meta name="description" content="No-Shave November and its funded programs are putting your donation dollars to work, investing in groundbreaking cancer research and providing free information and services to cancer patients and their caregivers.">
    <title>Donate | No-Shave November</title>
    <script src='https://www.google.com/recaptcha/api.js'></script>   
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
      // $(function(){
      //   $("#menu").load("/platform/platform_menu.php"); 
      // });
      // $(function(){
      //   $("#footer").load("/platform/platform_footer.html"); 
      // });
    </script>
    <style>
    .required-field {
      color: red;
    }
    .form-control.full-input-width {
      width: 100%;
    }
    .btn.double-font {
      font-size: 28px;
    }
    .fa-cog {
      color: #FFF;
    }
    .modal-body h4{
      color: #333;
      font-weight: 400;
      font-size: 20px;
    }
    .hidden{display: none;}
    </style>
    <script>

      function search(){

        if ($('input[name="search_type"]:checked').val() == "team") {
          // search teams
          search_teams();

        } else if ($('input[name="search_type"]:checked').val() == "org") {
          // search orgs
          search_orgs();

        } else {
          // assume member of search member if no selection
          search_members();

        }

      }

      function search_members() {
        var vars = {
                      search_term: $('#search_input').val()
                    }

        $.ajax({ 
          type: 'POST',
          url: './platform/api/search_members.php', 
          data: vars, 
          dataType: 'json',
          success: function (data) { 
            if (data['status'] == "success") {

              // show the select label
              $("#hide-label").show();

              // show the results
              document.getElementById("search_select").innerHTML = data['html'];

            } else {
              // handle no response... responsibly
            }
          }
        });
      }

      function search_teams() {
        var vars = {
                      search_term: $('#search_input').val()
                    }

        $.ajax({ 
          type: 'POST',
          url: './platform/api/search_teams.php', 
          data: vars, 
          dataType: 'json',
          success: function (data) { 
            if (data['status'] == "success") {

              // show the select label
              $("#hide-label").show();

              // show the results
              document.getElementById("search_select").innerHTML = data['html'];

            } else {
              // handle no response... responsibly
            }
          }
        });
      }

      function search_orgs() {
        var vars = {
                      search_term: $('#search_input').val()
                    }

        $.ajax({ 
          type: 'POST',
          url: './platform/api/search_orgs.php', 
          data: vars, 
          dataType: 'json',
          success: function (data) { 
            if (data['status'] == "success") {

              // show the select label
              $("#hide-label").show();

              // show the results
              document.getElementById("search_select").innerHTML = data['html'];

            } else {
              // handle no response... responsibly
            }
          }
        });
      }

      function commaSeparateNumber(val){
        while (/(\d+)(\d{3})/.test(val.toString())){
          val = val.toString().replace(/(\d+)(\d{3})/, '$1'+','+'$2');
        }
        return val;
      }

      $(document).ready(function () {
        $("input[name=donation_amount]").change(function () {
          toggleAmount();
        });            
      });

      function toggleAmount() {
        if ($("input[name=donation_amount]:checked").val() === "other") {
          $("#other_input").removeClass("hidden");
          $("#complete_donation").html('Donate $' + commaSeparateNumber($('#donation_amount_other_amount').val()));
        } else {
          $("#other_input").addClass("hidden");
          $("#complete_donation").html('Donate $' + $("input[name=donation_amount]:checked").val());
          $("#donation_amount_value").val($("input[name=donation_amount]:checked").val());
        }
      }

      function updateAmount() {
        $("#complete_donation").html('Donate $' + commaSeparateNumber($('#donation_amount_other_amount').val()));
      }

      function toggleProcessing() {
        $("#complete_donation").addClass("hidden");
        $("#processing").removeClass("hidden");
      }

      function wait_for_nonce() {
        //alert('Got here');

        if(typeof client_nonce !== "undefined"){
          // finally, submit
          submit_donation();
        }
        else{
          setTimeout(function(){
              wait_for_nonce();
          },250);
        }
      }

      function submit_donation() {
        // dummy loading overlay
        $.LoadingOverlay("show");

        // Hide it after 1.5 seconds
        setTimeout(function(){
            $.LoadingOverlay("hide");
        }, 1500);

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

        if ($('input[name="search_type"]:checked').val() == "team") {
          // donat to team
          donation_classifier = 2;
          donation_classifier_id = $('#select_result').val();

        } else if ($('input[name="search_type"]:checked').val() == "org") {
          // donate to org
          donation_classifier = 3;
          donation_classifier_id = $('#select_result').val();

        } else if ($('input[name="search_type"]:checked').val() == "member") {
          // donate to member
          donation_classifier = 1;
          donation_classifier_id = $('#select_result').val();

        } else if ($('#select_result').val() != 0 && $('input[name="search_type"]:checked').val() == "") {
          // general donation
          donation_classifier = 1;
          donation_classifier_id = $('#select_result').val();

        } else {
          // general donation
          donation_classifier = 0;
          donation_classifier_id = 0;
         
        }

        // gather information to complete the donation
        var donation_info = {
          donation_amount: final_donation_amount,
          donation_name: $('#donation_name').val(),
          donation_company: $('#donation_company').val(),
          donation_email: $('#donation_email').val(),
          donation_classifier: donation_classifier,
          donation_to_id: donation_classifier_id,
          donation_anonymous: $('input[name="make_anonymous"]:checked').val(),
          donation_visbile: $('input[name="donation_visbile"]:checked').val(),
          donation_comment: $('#donation_comment').val(),
          donation_nonce: client_nonce,
          recaptcha: $('#g-recaptcha-response').val()
        }

        // make call
        $.ajax({ 
          type: 'POST',
          url: './platform/api/donation.php', 
          data: donation_info, 
          dataType: 'json',
          success: function (data) { 
            if (data['status'] == "success") {

              // toggle those buttons
              toggleProcessing();

              // thank them elsewhere
              window.location.replace("/leaderboard?d=1");

            } else if (data['fail_code']) {

              // do something
              $('#failed_donation_message').text(data['reason']);
              $('#failed_donation').modal();
              grecaptcha.reset();

            } else {
              // no response from the endpoint?
              // TODO handle this better
            }
          }
        });

      }

    </script>
  </head>
  <body>
    <header>           
      <?php include_once('menu.php');?>
    </header>

    <section class="register-step-3 register-div">
        <div class="container">
        <form class="form" id="donation_form" method="post">
            <div class="text-left">
                <h2 class="mb-0">Make a Donation</h2>                 
                <!--<p class="mb-5">No-Shave November and its funded programs are putting your donaftion dollars to work, investing in groundbreaking cancer research and providing free information and services to cancer patients and their caregivers.</p>-->

                <h5>Donation Amount *</h5>
                <ul class="list-inline">
                    <li>
                        <div class="clearfix">
                            <input type="radio" name="donation_amount" id="donation_amount_25" value="25">
                            <label for="donation_amount_25">$25</label>
                        </div>
                    </li>
                    <li>
                        <div class="clearfix">
                            <input type="radio" name="donation_amount" id="donation_amount_50" value="50">
                            <label for="donation_amount_50">$50</label>
                        </div>
                    </li>
                    <li>
                        <div class="clearfix">
                            <input type="radio" name="donation_amount" id="donation_amount_100" value="100" checked>
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
                <h6>Most people are giving <span>$100</span> right now. Please give what you can.</h6>

                <div class="pay-form">
                    <!--<form class="form" id="donation_form" method="post">-->
                        <input type="hidden" name="donation_amount" id="donation_amount_value" value="100">
                        <div class="row">
                            <div class="col-md-5 col-12">
                                <h5 class="pt-5 mb-4">Your Information</h5>
                                <div class="form-group">
                                    <input type="text" required="required" class="form-control" placeholder="Name *" id="donation_name" name="donation_name" autofocus="">
                                </div>                             
                                <div class="form-group">
                                    <input type="text" class="form-control" id="donation_company" name="donation_company" placeholder="Company" >
                                </div>                             
                                <div class="form-group">
                                    <input type="email" required="required" class="form-control" id="donation_email" name="donation_email" placeholder="Email *">
                                </div>
                            </div>
                            <div class="col-md-7 col-12">
                                <div class="border-pay">
                                    <h5 class="mb-3">Make Donation to Member, Team or Organization (optional)</h5>
                                    <div class="col-md-12">
                                    <div class="form-group" style="margin-bottom: 8px;">
                                        <label for="search_type" class="col-md-3">Search</label>
                                        <div class="col-md-12">
                                        <ul class="list-inline">
                                              <li>
                                                  <div class="clearfix">
                                                      <input type="radio" name="search_type" id="search_type_member" value="member" <?php echo $member_checked;?>>
                                                      <label for="search_type_member" style="font-size:12px;">Member</label>
                                                  </div>
                                              </li>
                                              <li>
                                                  <div class="clearfix">
                                                      <input type="radio" name="search_type" id="search_type_team" value="team" <?php echo $team_checked;?>>
                                                      <label for="search_type_team" style="font-size:12px;">Team</label>
                                                  </div>
                                              </li>
                                              <li>
                                                  <div class="clearfix">
                                                      <input type="radio" name="search_type" id="search_type_org" value="org" <?php echo $org_checked;?>>
                                                      <label for="search_type_org" style="font-size:12px;">Organization (Company, Fraternity, etc.)</label>
                                                  </div>
                                              </li>
                                        </ul>
                                        </div>
                                      </div>
                                    </div>
                                    <div class="form-group" style="margin-bottom: 28px;">
                                      <label for="search" class="col-md-3"></label>

                                      <div class="col-md-12">
                                        <input type="text" class="form-control" id="search_input" name="search_input" placeholder="Enter Search Terms" onkeyup="search()" autocomplete="off" value="<?php echo $prefill_search; ?>">
                                      </div>
                                    </div>
                                    <div class="form-group" style="margin-bottom: 28px;">

              <div id="hide-label" <?php echo $hide_search_label; ?>><label for="search" class="col-md-3">Select</label></div>
              <div class="col-md-7">
                <div id="search_select">
                  <?php echo $search_result_get; ?>
                </div>
              </div>
            </div>

            <div class="form-group" style="margin-bottom: 28px;">
              <label for="make_anonymous" class="col-md-6">Make Anonymously</label>
              <div class="col-md-12">
              <ul class="list-inline">
                  <li>
                      <div class="clearfix">
                        <input type="radio" name="make_anonymous" id="make_anonymous_yes" value="1">
                          <label for="make_anonymous_yes">Yes</label>
                      </div>
                  </li>
                  <li>
                      <div class="clearfix">
                        <input type="radio" name="make_anonymous" id="make_anonymous_no" value="0">
                          <label for="make_anonymous_no">No</label>
                      </div>
                  </li>
              </ul>
              </div>
            </div>

            <div class="form-group" style="margin-bottom: 28px;">
              <label for="donation_visbile" class="col-md-3">Visible on Page</label>
              <div class="col-md-12">
                <ul class="list-inline">
                    <li>
                        <div class="clearfix">
                        <input type="radio" name="donation_visbile" id="donation_visbile_yes" value="1" >
                            <label for="donation_visbile_yes">Yes, display this donation publicly</label>
                        </div>
                    </li>
                    <li>
                        <div class="clearfix">
                        <input type="radio" name="donation_visbile" id="donation_visbile_no" value="0" >
                            <label for="donation_visbile_no">No, please hide this donation</label>
                        </div>
                    </li>
                </ul>
              </div>
            </div>

            <div class="form-group" style="margin-bottom: 28px;">
              <div class="col-md-12">
                <textarea type="text" class="form-control" id="donation_comment" name="donation_comment" placeholder="Comment" rows="3"></textarea>
              </div>
            </div>
                                </div>
                            </div>
                            <div class="col-md-12 col-12">
                                <div class="border-pay">
                                    <h5 class="mb-3">Payment Method</h5>
                                    <div class="col-md-7">
                                        <div id="dropin-container"></div>
                                    </div>
                                    <div class="col-md-12">
                                    <div class="g-recaptcha center-recaptcha" data-sitekey="6LfQi2gUAAAAADJr1olB_ilCPMFjfhLHYkyRydXs"></div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group text-right">
                                <button id="complete_donation" name="complete_donation" class="btn btn-primary pull-right" type="submit" onclick="wait_for_nonce()">Donate $100</button>

                <button id="processing" name="processing" type="button" class="btn btn-success pull-right hidden"><i class="fa fa-cog fa-spin fa-fw"></i>  Processing</button>

                                    
                                </div>
                               
                            </div>
                        </div>

                </div>
                </form>
            </div>
        </div>
    </section>
    
    <section class="app-sec" style="background-image: url(./img/fbg.png);">
        <div class="container">
            <div class="row animatedParent">
                <div class="col-12 col-md-5">
                    <figure class="animated bounceInUp animate-2">
                        <img src="./img/half-mobile.png" class="img-fluid" alt="">                           
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
    </section>

    <script src="https://js.braintreegateway.com/js/braintree-2.26.0.min.js"></script>
    <script>
      braintree.setup('<?php echo($clientToken = Braintree_ClientToken::generate()); ?>', 'dropin', {
        container: 'dropin-container',
        onPaymentMethodReceived: function (obj) {
          window.client_nonce = obj.nonce;
        }
      });
    </script>

    <!-- BRAINTREE -->

    <!-- MODALS -->

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

    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.6.0/css/font-awesome.min.css" rel="stylesheet">
    <?php include_once('footer.php')?>
    <?php include_once("analyticstracking.php") ?>
  </body>
</html>
