<?php
include_once 'includes/brain.php';
include_once 'includes/db_connect.php';
include_once 'includes/psl-config.php';

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
    <link rel="icon" type="image/png" href="/favicon.png">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" />
    <meta name="author" content="No-Shave November">
    <meta name="description" content="No-Shave November and its funded programs are putting your donation dollars to work, investing in groundbreaking cancer research and providing free information and services to cancer patients and their caregivers.">
    <title>Donate | No-Shave November</title>
    <script src='https://www.google.com/recaptcha/api.js'></script>
    <link href="/assets/css/bootstrap.css" rel="stylesheet">    
    <link href="/assets/css/main.css" rel="stylesheet">
    <link href='/assets/css/font.css' rel='stylesheet' type='text/css'>
    <script src="/assets/js/jquery.min.js" type="text/javascript"></script>
    <script type="text/javascript">
      $(function(){
        $("#menu").load("/platform/platform_menu.php"); 
      });
      $(function(){
        $("#footer").load("/platform/platform_footer.html"); 
      });
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
          url: '/platform/api/search_members.php', 
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
          url: '/platform/api/search_teams.php', 
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
          url: '/platform/api/search_orgs.php', 
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
          url: '/platform/api/donation.php', 
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

    <!-- MEDU BAR -->
    <div id="menu"></div>
    <!-- MENU BAR-->

    <!-- GIMME A BREAK -->
    <br><br>
    <!-- GIMME A BREAK -->

    <!-- - - - - - -  -->
    <!-- PAGE CONTENT -->
    <!-- - - - - - -  -->
    
    <div class="container">

      <div class="row">
        <div class="col-md-12">
          <div class="page-header">
            <h1>Make a Donation</h1>
          </div>
        </div>
      </div>

      <div class="row">

        <form class="form-horizontal">

          <div class="col-md-8 col-md-offset-2">

            <h4>Donation Amount</h4>
            <hr>
            <br>

            <div class="form-group">
              <label for="donation_amount" class="col-md-3 control-label">Amount (USD) <span class="required-field">*</span></label>
              <div class="col-md-9">
                <label class="radio-inline">
                  <input type="radio" name="donation_amount" id="donation_amount_25" value="25"> $25
                </label>
                <label class="radio-inline">
                  <input type="radio" name="donation_amount" id="donation_amount_50" value="50"> $50
                </label>
                <label class="radio-inline">
                  <input type="radio" name="donation_amount" id="donation_amount_100" value="100" checked> $100
                </label>
                <label class="radio-inline">
                  <input type="radio" name="donation_amount" id="donation_amount_250" value="250"> $250
                </label>
                <label class="radio-inline">
                  <input type="radio" name="donation_amount" id="donation_amount_500" value="500"> $500
                </label>
                <label class="radio-inline">
                  <input type="radio" name="donation_amount" id="donation_amount_other" value="other"> Other
                </label>
              </div>
            </div>

            <div class="form-group hidden" id="other_input" visible="false">
              <label for="donation_amount_other_amount" class="col-md-3 control-label">Other <span class="required-field">*</span></label>

              <div class="col-md-3">
                <input type="number" class="form-control full-input-width" id="donation_amount_other_amount" placeholder="Amount" name="donation_amount" onkeyup="updateAmount()">
              </div>
            </div>


            <br><br>
            <h4>Your Information</h4>
            <hr>
            <br>

            <div class="form-group">
              <label for="donation_name" class="col-md-3 control-label">Full Name <span class="required-field">*</span></label>

              <div class="col-md-7">
                <input type="text" class="form-control full-input-width" id="donation_name" name="donation_name" placeholder="Name">
              </div>
            </div>

            <div class="form-group">
              <label for="donation_company" class="col-md-3 control-label">Company</label>

              <div class="col-md-7">
                <input type="text" class="form-control full-input-width" id="donation_company" name="donation_company" placeholder="Company">
              </div>
            </div>

            <div class="form-group">
              <label for="donation_email" class="col-md-3 control-label">Email <span class="required-field">*</span></label>

              <div class="col-md-7">
                <input type="email" class="form-control full-input-width" id="donation_email" name="donation_email" placeholder="Email">
              </div>
            </div>

            <br><br>
            <h4>Make Donation to Member, Team or Organization (optional)</h4>
            <hr>
            <br>

            <div class="form-group">
              <label for="search_type" class="col-md-3 control-label">Search</label>
              <div class="col-md-9">
                <label class="radio-inline">
                  <input type="radio" name="search_type" id="search_type" value="member" <?php echo $member_checked;?>> Member
                </label>
                <label class="radio-inline">
                  <input type="radio" name="search_type" id="search_type" value="team" <?php echo $team_checked;?>> Team
                </label>
                <label class="radio-inline">
                  <input type="radio" name="search_type" id="search_type" value="org" <?php echo $org_checked;?>> Organization (Company, Fraternity, etc.)
                </label>
              </div>
            </div>

            <div class="form-group">
              <label for="search" class="col-md-3 control-label"></label>

              <div class="col-md-7">
                <input type="text" class="form-control full-input-width" id="search_input" name="search_input" placeholder="Enter Search Terms" onkeyup="search()" autocomplete="off" value="<?php echo $prefill_search; ?>">
              </div>
            </div>

            <div class="form-group">

              <div id="hide-label" <?php echo $hide_search_label; ?>><label for="search" class="col-md-3 control-label">Select</label></div>
              <div class="col-md-7">
                <div id="search_select">
                  <?php echo $search_result_get; ?>
                </div>
              </div>
            </div>

            <div class="form-group">
              <label for="make_anonymous" class="col-md-3 control-label">Make Anonymously</label>
              <div class="col-md-9">
                <label class="radio-inline">
                  <input type="radio" name="make_anonymous" id="make_anonymous" value="1"> Yes
                </label>
                <label class="radio-inline">
                  <input type="radio" name="make_anonymous" id="make_anonymous" value="0"> No
                </label>
              </div>
            </div>

            <div class="form-group">
              <label for="donation_visbile" class="col-md-3 control-label">Visible on Page</label>
              <div class="col-md-9">
                <div class="radio">
                  <label>
                    <input type="radio" name="donation_visbile" id="donation_visbile" value="1" >
                    Yes, display this donation publicly
                  </label>
                </div>
                <div class="radio">
                  <label>
                    <input type="radio" name="donation_visbile" id="donation_visbile" value="0" >
                    No, please hide this donation
                  </label>
                </div>
              </div>
            </div>

            <div class="form-group">
              <label for="donation_comment" class="col-md-3 control-label">Comment</label>

              <div class="col-md-7">
                <textarea type="text" class="form-control full-input-width" id="donation_comment" name="donation_comment" placeholder="Comment" rows="3"></textarea>
              </div>
            </div>

            <br><br>
            <h4>Payment</h4>
            <hr>
            <br>

            <div class="form-group">
              <label for="dropin" class="col-md-3 control-label">Payment Method <span class="required-field">*</span></label>
              <div class="col-md-7">
                <div id="dropin-container"></div>
                <br>
                 <div class="g-recaptcha center-recaptcha" data-sitekey="6LfcNwUTAAAAAOqFjBNkLEi63xKPwvdhqBYoagCK"></div>
              </div>
            </div>

            <br><br>
            <h4>Complete Donation</h4>
            <hr>
            <br>
            <div class="form-group">
              <div class="col-md-7 col-md-offset-3">
                <button id="complete_donation" name="complete_donation" class="btn btn-success btn-lg btn-block pull-right double-font" type="submit" onclick="wait_for_nonce()">Donate $100</button>
                <button id="processing" name="processing" type="button" class="btn btn-success btn-lg btn-block pull-right double-font disabled hidden"><i class="fa fa-cog fa-spin fa-fw"></i>  Processing</button>
              </div>
              <div class="col-md-3"></div>
            </div>
          </div>
        </form>

      </div>

    </div>
    
    <!-- - - - - - -  -->
    <!-- PAGE CONTENT -->
    <!-- - - - - - -  -->

    <!-- GIMME A BREAK -->
    <br><br>
    <!-- GIMME A BREAK -->

    <!-- FOOTER -->
    <div id="footer"></div>
    <!-- FOOTER -->

    <!-- BRAINTREE -->

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

    <!-- BRAINTREE -->

    <!-- MODALS -->

    <div id="failed_donation" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="failed_donation">
      <div class="modal-dialog modal-sm">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">Failed to Make Donation</h4>
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

    <script src="/assets/js/bootstrap.min.js" type="text/javascript"></script>
    <script src="/assets/js/loadingoverlay.js" type="text/javascript"></script>
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.6.0/css/font-awesome.min.css" rel="stylesheet">
    <?php include_once("analyticstracking.php") ?>
  </body>
</html>
