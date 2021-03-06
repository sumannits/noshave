<?php
  include_once 'includes/db_connect.php';
  include_once 'includes/functions.php';
  sec_session_start();
?>

<?php  if (login_check($mysqli) == true) : ?>

<?php
// user id is in the session
$user_id = $_SESSION['user_id'];

// grab data
// get user data
if ($stmt = $mysqli->prepare("SELECT m_full_name, m_email, m_username, m_team_id, m_org_id, m_team_editor, m_org_editor, m_city, m_state, m_country, m_display_location, m_location_format, m_profile_pic, m_pic_0, m_pic_1, m_pic_2, m_pic_3, m_pic_4, m_pic_5, m_pic_6, m_pic_7, m_pic_8, m_pic_9, m_page_title, m_page_description, m_page_goal, m_got_screen FROM member WHERE m_id = ? LIMIT 1")) {
  $stmt->bind_param('i', $user_id);
  $stmt->execute();
  $stmt->store_result();
  $stmt->bind_result($m_full_name, $m_email, $m_username, $m_team_id, $m_org_id, $m_team_editor, $m_org_editor, $m_city, $m_state, $m_country, $m_display_location, $m_location_format, $m_profile_pic, $m_pic_0, $m_pic_1, $m_pic_2, $m_pic_3, $m_pic_4, $m_pic_5, $m_pic_6, $m_pic_7, $m_pic_8, $m_pic_9, $m_page_title, $m_page_description, $m_page_goal, $m_got_screen);
  $stmt->fetch();
  $stmt->close();

} else {
  // give error 5590 - unable to get data from DB using user ID from session
  //header('Location: /error?id=1');
}

// FETCH TEAM INFO
if ($m_team_id != "0") {
  // Fetch team information
  if ($stmt = $mysqli->prepare("SELECT t_id, t_name, t_username, t_pic_0, t_page_title, t_page_description, t_page_goal FROM team WHERE t_id = ? LIMIT 1")) {
    $stmt->bind_param('i', $m_team_id);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($t_id, $t_name, $t_username, $t_pic_0, $t_page_title, $t_page_description, $t_page_goal);
    $stmt->fetch();
    $stmt->close();

  } else {
    // Unable to grab info, TODO, handle error

  }

} else {
  // Nothing to fetch

}

// FETCH ORG INFO
if ($m_org_id != "0") {
  // Fetch team information
  if ($stmt = $mysqli->prepare("SELECT o_id, o_name, o_username, o_pic_0, o_page_title, o_page_description, o_page_goal FROM org WHERE o_id = ? LIMIT 1")) {
    $stmt->bind_param('i', $m_org_id);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($o_id, $o_name, $o_username, $o_pic_0, $o_page_title, $o_page_description, $o_page_goal);
    $stmt->fetch();
    $stmt->close();

  } else {
    // Unable to grab info, TODO, handle error

  }

} else {
  // Nothing to fetch

}

// GET PERSONAL DONATIONS
if ($stmt = $mysqli->prepare("SELECT d_id, d_time, d_name, d_email, d_amount, d_message, d_anonymous, d_message_on_page, d_thank_you_sent FROM donation WHERE d_classifier = 1 AND d_classifier_id = ? AND d_verified_payment = 1")) {
  $stmt->bind_param('s', $user_id);
  $stmt->execute();
  $stmt->bind_result($d_id, $d_time, $d_name, $d_email,$d_amount, $d_message, $d_anonymous, $d_message_on_page, $d_thank_you_sent);

  // donation table
  $total_raised = 0;
  $donation_count = 0;

  $personal_donation_table = '
                            <h4>Personal Donations</h4>
                            <hr>
                            <table class="table table-hover">
                              <thead>
                                <tr>
                                  <th>#</th>
                                  <th class="col-md-2">Date</th>
                                  <th class="col-md-2">Donor</th>
                                  <th>Amount</th>
                                  <th>Comment</th>
                                </tr>
                              </thead>
                              <tbody>
                              ';

  while ($stmt->fetch()) {

    $donation_count = $donation_count + 1;

    if ($d_anonymous == 1) {
      $d_name = "Anonymous";
    }

    // add to total
    $total_raised += $d_amount;


    $personal_donation_table .= '
                              <tr>
                                <td>' . $donation_count . '</td>
                                <td>' . date('F j, Y', strtotime($d_time . '- 5 hours')) . '</td>
                                <td>' . $d_name . '</td>
                                <td>$' . $d_amount . '</td>
                                <td>' . $d_message . '</td>
                              </tr>
                        ';
    }

    $personal_donation_table .= '
                                  </tbody>
                                </table>
                                <br>
                                  ';

  // close that 
  $stmt->close();

  if ($donation_count == "0") {

    $personal_donation_table = '
                            <h4>Personal Donations</h4>
                            <hr>
                            <table class="table table-hover">
                              <thead>
                                <tr>
                                  <th>#</th>
                                  <th class="col-md-2">Date</th>
                                  <th class="col-md-2">Donor</th>
                                  <th>Amount</th>
                                  <th>Comment</th>
                                </tr>
                              </thead>
                              <tbody>
                                <tr>
                                  <td colspan="6" class="vert-align centered">
                                    <br>
                                    <h4>There are no donations to display.</h4>
                                  </td>
                                </tr>
                              </tbody>
                          </table>
                          <br>
                        ';

  }

} else {
  // give error 5590 - unable to get data from DB using user ID from session
  header('Location: /error?id=2');
}

// set these as empty for now
$team_donation_table = "";
$org_donation_table = "";

// TEAM DONATIONS
if ($m_team_editor == 1) {
  // PULL UP TEAM DONATIONS
  if ($stmt = $mysqli->prepare("SELECT d_time, d_name, d_email, d_amount, d_message, d_anonymous, d_message_on_page FROM donation WHERE d_classifier = 2 AND d_classifier_id = ? AND d_verified_payment = 1")) {
    $stmt->bind_param('s', $m_team_id);
    $stmt->execute();
    $stmt->bind_result($d_time, $d_name, $d_email,$d_amount, $d_message, $d_anonymous, $d_message_on_page);

    // donation table
    $team_donation_count = 0;

    $team_donation_table = '
                              <h4>Team Donations</h4>
                              <hr>
                              <table class="table table-hover">
                                <thead>
                                  <tr>
                                    <th>#</th>
                                    <th class="col-md-2">Date</th>
                                    <th class="col-md-2">Donor</th>
                                    <th>Amount</th>
                                    <th>Comment</th>
                                  </tr>
                                </thead>
                                <tbody>
                                ';

    while ($stmt->fetch()) {

      $team_donation_count = $team_donation_count + 1;

      if ($d_anonymous == 1) {
        $d_name = "Anonymous";
      }

      $team_donation_table .= '
                                <tr>
                                  <td>' . $team_donation_count . '</td>
                                  <td>' . date('F j, Y', strtotime($d_time . '- 5 hours')) . '</td>
                                  <td>' . $d_name . '</td>
                                  <td>$' . $d_amount . '</td>
                                  <td>' . $d_message . '</td>
                                </tr>
                          ';
      }

    $team_donation_table .= '
                                  </tbody>
                                </table>
                                <br>
                                  ';

    // close that 
    $stmt->close();

    if ($team_donation_count == "0") {

      $team_donation_table = '
                              <h4>Team Donations</h4>
                              <hr>
                              <table class="table table-hover">
                                <thead>
                                  <tr>
                                    <th>#</th>
                                    <th class="col-md-2">Date</th>
                                    <th class="col-md-2">Donor</th>
                                    <th>Amount</th>
                                    <th>Comment</th>
                                  </tr>
                                </thead>
                                <tbody>
                                  <tr>
                                    <td colspan="6" class="vert-align centered">
                                      <br>
                                      <h4>There are no donations to display.</h4>
                                    </td>
                                  </tr>
                                </tbody>
                            </table>
                            <br>
                          ';

    }

  } else {
    // give error 5590 - unable to get data from DB using user ID from session
    header('Location: error?id=3');
  }

} else {
  // do nothing
}

// ORG DONATIONS
if ($m_org_editor == 1) {
  // PULL UP ORG DONATIONS
  if ($stmt = $mysqli->prepare("SELECT d_time, d_name, d_email, d_amount, d_message, d_anonymous, d_message_on_page FROM donation WHERE d_classifier = 3 AND d_classifier_id = ? AND d_verified_payment = 1")) {
    $stmt->bind_param('s', $m_org_id);
    $stmt->execute();
    $stmt->bind_result($d_time, $d_name, $d_email,$d_amount, $d_message, $d_anonymous, $d_message_on_page);

    // donation table
    $org_donation_count = 0;

    $org_donation_table = '
                              <h4>Organization Donations</h4>
                              <hr>
                              <table class="table table-hover">
                                <thead>
                                  <tr>
                                    <th>#</th>
                                    <th class="col-md-2">Date</th>
                                    <th class="col-md-2">Donor</th>
                                    <th>Amount</th>
                                    <th>Comment</th>
                                  </tr>
                                </thead>
                                <tbody>
                                ';

    while ($stmt->fetch()) {

      $org_donation_count = $org_donation_count + 1;

      if ($d_anonymous == 1) {
        $d_name = "Anonymous";
      }

      $org_donation_table .= '
                                <tr>
                                  <td>' . $team_donation_count . '</td>
                                  <td>' . date('F j, Y', strtotime($d_time . '- 5 hours')) . '</td>
                                  <td>' . $d_name . '</td>
                                  <td>$' . $d_amount . '</td>
                                  <td>' . $d_message . '</td>
                                </tr>
                          ';
      }

    $org_donation_table .= '
                                  </tbody>
                                </table>
                                <br>
                                  ';

    // close that 
    $stmt->close();

    if ($org_donation_count == "0") {

      $org_donation_table = '
                              <h4>Organization Donations</h4>
                              <hr>
                              <table class="table table-hover">
                                <thead>
                                  <tr>
                                    <th>#</th>
                                    <th class="col-md-2">Date</th>
                                    <th class="col-md-2">Donor</th>
                                    <th>Amount</th>
                                    <th>Comment</th>
                                  </tr>
                                </thead>
                                <tbody>
                                  <tr>
                                    <td colspan="6" class="vert-align centered">
                                      <br>
                                      <h4>There are no donations to display.</h4>
                                    </td>
                                  </tr>
                                </tbody>
                            </table>
                            <br>
                          ';

    }

  } else {
    // give error 5590 - unable to get data from DB using user ID from session
    header('Location: /error?id=3');
  }
} else {
  // do nothing
}

// Personal goal status
// calculate goal
$goal_percentage = ceil(($total_raised / $m_page_goal) * 100) . "%";

if ($goal_percentage > 100) {
  $goal_percentage = "100%+";
}

// donations or donation
if ($donation_count == 1) {
  $donation_or_donations = "Donation";
} else {
  $donation_or_donations = "Donations";
}

// screen check box
if ($m_got_screen == 1) {
  $yes_screened = "checked";
  $no_screened = "";
} else {
  $yes_screened = "";
  $no_screened = "checked";
}

// set this
$show_location = "";
$hide_location = "";

if ($m_display_location == 1){
  $show_location = "checked";
} else {
  $hide_location = "checked";
}

// set these so it doesn't complain
$city_state = "";
$city_country = "";
$city_only = "";
$country_only = "";

if ($m_location_format == 0) {

  $personal_location = $m_city . ", " . $m_state;
  $city_state = "checked";

} elseif ($m_location_format == 1) {

  $personal_location = $m_city . ", " . $m_country;
  $city_country = "checked";

} elseif ($m_location_format == 2) {

  $personal_location = $m_city;
  $city_only = "checked";

} elseif ($m_location_format == 3) {

  $personal_location = $m_country;
  $country_only = "checked";

} else {

  $personal_location = $m_country;
  $country_only = "checked";

}

// got screened
if ($m_got_screen == 0) {
  $member_got_screened = "No";
} else {
  $member_got_screened = "Yes";
}

?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <link rel="icon" type="image/png" href="/favicon.png">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" />
    <meta name="author" content="No-Shave November">
    <meta name="description" content="The No-Shave November dashboard is a one-stop shop to help you update your personal, team and organization fundraising pages, track your fundraising progress, and update your account information.">
    <title>Dashboard | No-Shave November</title>
    <link href="/assets/css/bootstrap.css" rel="stylesheet">    
    <link href="/assets/css/main.css" rel="stylesheet">
    <link href='/assets/css/font.css' rel='stylesheet' type='text/css'>
    <script src="/assets/js/jquery.min.js" type="text/javascript"></script>
    <script type="text/javascript">
      // $(function(){
      //   $("#menu").load("/platform/platform_menu.php"); 
      // });
      // $(function(){
      //   $("#footer").load("/platform/platform_footer.html"); 
      // });
    </script>

    <script>

      function update_personal_page() {  

        // grab field
        var args = {
          m_username_new: $('#personal_username').val(),
          m_location_visibility: $('input[name="location_visibility"]:checked').val(),
          m_location_format: $('input[name="location_format"]:checked').val(),
          m_page_title: $('#personal_page_title').val(),
          m_page_goal: $('#personal_goal').val(),
          m_page_description: $('#personal_page_description').val()
        }

        // make call
        $.ajax({ 
          type: 'POST',
          url: '/platform/api/update_personal_page.php', 
          data: args, 
          dataType: 'json',
          success: function (data) { 
            if (data['status'] == "success") {

              // update the field values TODO
              $('#view_personal_page').attr("href", "/member/" + $('#personal_username').val());
              $('#view_personal_page_2').text('no-shave.org/member/' + $('#personal_username').val());
              $('#view_personal_page_2').attr("href", "/member/" + $('#personal_username').val());
              $('#page_title').text($('#personal_page_title').val());
              $('#page_goal').text('$' + $('#personal_goal').val());
              $('#page_description').text($('#personal_page_description').val());

              if ($('input[name="location_format"]:checked').val() == 0) {
                // City, State
                $('#personal_location').text('<?php echo $m_city; ?>, <?php echo $m_state; ?>');

              } else if ($('input[name="location_format"]:checked').val() == 1) {
                // City, Country
                $('#personal_location').text('<?php echo $m_city; ?>, <?php echo $m_country; ?>');

              } else if ($('input[name="location_format"]:checked').val() == 2) {
                // City
                $('#personal_location').text('<?php echo $m_city; ?>');

              } else {
                // Country
                $('#personal_location').text('<?php echo $m_country; ?>');

              }

              // update the fields on the page
              $('#editPersonal').modal('toggle'); // this should hide the modal

              // dummy loading overlay
              $.LoadingOverlay("show");

              // Hide it after 1.5 seconds
              setTimeout(function(){
                $.LoadingOverlay("hide");
              }, 1500);

            } else {

              // we don't know what happened
              $('#failed_update_personal_message').text(data['reason']);
              $('#failed_update_personal').modal(); // this should show the modal

            }
          }
        });
      }

      function update_team_page() {  

        // grab field
        var args = {
          t_name: $('#team_name_edit').val(),
          t_username: $('#team_username_edit').val(),
          t_page_title: $('#team_title_edit').val(),
          t_page_goal: $('#team_goal_edit').val(),
          t_page_description: $('#team_description_edit').val()
        }

        // make call
        $.ajax({ 
          type: 'POST',
          url: '/platform/api/update_team_page.php', 
          data: args, 
          dataType: 'json',
          success: function (data) { 
            if (data['status'] == "success") {

              // UPDATE TEAM VARS IN REAL TIME
              $('#view_team_page').attr("href", "/team/" + $('#team_username_edit').val());
              $('#view_team_page_2').text('no-shave.org/team/' + $('#team_username_edit').val());
              $('#view_team_page_2').attr("href", "/team/" + $('#team_username_edit').val());
              $('#team_title').text($('#team_title_edit').val());
              $('#team_goal').text('$' + $('#team_goal_edit').val());
              $('#team_description').text($('#team_description_edit').val());

              // update the fields on the page
              $('#editTeam').modal('toggle'); // this should hide the modal

              // dummy loading overlay
              $.LoadingOverlay("show");

              // Hide it after 1.5 seconds
              setTimeout(function(){
                $.LoadingOverlay("hide");
              }, 1500);

            } else {

              // we don't know what happened
              $('#failed_update_team_message').text(data['reason']);
              $('#failed_update_team').modal(); // this should show the modal

            }
          }
        });
      }

      function update_org_page() {  

        // grab field
        var args = {
          o_name: $('#org_name_edit').val(),
          o_username: $('#org_username_edit').val(),
          o_page_title: $('#org_title_edit').val(),
          o_page_goal: $('#org_goal_edit').val(),
          o_page_description: $('#org_description_edit').val()
        }

        // make call
        $.ajax({ 
          type: 'POST',
          url: '/platform/api/update_org_page.php', 
          data: args, 
          dataType: 'json',
          success: function (data) { 
            if (data['status'] == "success") {

              // UPDATE TEAM VARS IN REAL TIME
              $('#view_org_page').attr("href", "/org/" + $('#org_username_edit').val());
              $('#view_org_page_1').text('no-shave.org/org/' + $('#org_username_edit').val());
              $('#view_org_page_1').attr("href", "/org/" + $('#org_username_edit').val());
              $('#org_title').text($('#org_title_edit').val());
              $('#org_goal').text('$' + $('#org_goal_edit').val());
              $('#org_description').text($('#org_description_edit').val());

              // update the fields on the page
              $('#editOrg').modal('toggle'); // this should hide the modal

              // dummy loading overlay
              $.LoadingOverlay("show");

              // Hide it after 1.5 seconds
              setTimeout(function(){
                $.LoadingOverlay("hide");
              }, 1500);

            } else {

              // we don't know what happened
              $('#failed_update_org_message').text(data['reason']);
              $('#failed_update_org').modal(); // this should show the modal

            }
          }
        });
      }

      function update_account() {
        // check radio buttons
        // 
        if ($('input[name="got_screened"]:checked').val() == "yes") {
          var screened = 1;
        } else {
          var screened = 0;
        }

        // grab field
        var args = {
          m_full_name: $('#full_name').val(),
          m_email: $('#email_address').val(),
          m_city: $('#city').val(),
          m_state: $('#state').val(),
          m_country: $('#country').val(),
          m_got_screen: screened
        }

        // make call
        $.ajax({ 
          type: 'POST',
          url: '/platform/api/update_account.php', 
          data: args, 
          dataType: 'json',
          success: function (data) { 
            if (data['status'] == "success") {

              // update the field values TODO
              $('#show_full_name').text($('#full_name').val());
              $('#show_email_address').text($('#email_address').val());
              $('#show_city').text($('#city').val());
              $('#show_state').text($('#state').val());
              $('#show_country').text($('#country').val());

              if (screened == 1) {
                $('#show_got_screen').text("Yes");
              } else {
                $('#show_got_screen').text("No");
              }

              // update the fields on the page
              $('#editAccount').modal('toggle'); // this should hide the modal

              // dummy loading overlay
              $.LoadingOverlay("show");

              // Hide it after 1.5 seconds
              setTimeout(function(){
                $.LoadingOverlay("hide");
              }, 1500);

            } else {

              // we don't know what happened
              $('#failed_update_account_message').text(data['reason']);
              $('#failed_update_account').modal(); // this should show the modal

            }
          }
        });
      }

      function offline_donation() {

        // grab field
        var args = {
          donation_amount: $('#donation_amount').val(),
          donation_name: $('#donation_name').val(),
          donation_company: $('#donation_company').val(),
          donation_message: $('#donation_message').val(),
          donation_visbile: $('input[name="donation_visbile"]:checked').val(),
          donation_attribution: $('input[name="donation_attribution"]:checked').val()
        }

        // make call
        $.ajax({ 
          type: 'POST',
          url: '/platform/api/offline_donation.php', 
          data: args, 
          dataType: 'json',
          success: function (data) { 
            if (data['status'] == "success") {

              // close the modal
              $('#offlineDonation').modal('toggle'); // this should hide the modal

              // dummy loading overlay
              $.LoadingOverlay("show");

              // Hide it after 1.5 seconds
              setTimeout(function(){
                $.LoadingOverlay("hide");
              }, 1500);

            } else {

              // we don't know what happened
              $('#failed_offline_donation_message').text(data['reason']);
              $('#failed_offline_donation').modal(); // this should show the modal

            }
          }
        });
      }

      // create or join a team
      function create_or_join_team_modal() {
        if ($('input[name="create_or_join_team_select"]:checked').val() == "create"){
          // they want to create
          $('#createTeam').modal();

        } else {
          // they want to join
          $('#joinTeam').modal();

        }
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

      function join_team() {

        var vars = {
                      t_id: $('#select_result').val()
                    }

        $.ajax({ 
          type: 'POST',
          url: '/platform/api/join_team.php', 
          data: vars, 
          dataType: 'json',
          success: function (data) { 
            if (data['status'] == "success") {

              // close the modal
              $('#joinTeam').modal('toggle');

              // dummy loading overlay
              $.LoadingOverlay("show");

              // Hide it after 1.5 seconds
              setTimeout(function(){
                $.LoadingOverlay("hide");
              }, 1500);

              // refresh the page
              location.reload();

            } else {
              // uh oh
              $('#failed_join_team_message').text(data['reason']);
              $('#failed_join_team').modal(); // this should show the modal

            }
          }
        });
      }

      function join_org() {

        var vars = {
                      o_id: $('#select_org_result').val()
                    }

        $.ajax({ 
          type: 'POST',
          url: '/platform/api/join_org.php', 
          data: vars, 
          dataType: 'json',
          success: function (data) { 
            if (data['status'] == "success") {

              // close the modal
              $('#joinOrg').modal('toggle');

              // dummy loading overlay
              $.LoadingOverlay("show");

              // Hide it after 1.5 seconds
              setTimeout(function(){
                $.LoadingOverlay("hide");
              }, 1500);

              // refresh the page
              location.reload();

            } else {
              // uh oh
              $('#failed_join_org_message').text(data['reason']);
              $('#failed_join_org').modal(); // this should show the modal

            }
          }
        });
      }

      function create_team() {

        var vars = {
                      t_name: $('#team_name').val(),
                      t_username: $('#team_username').val()
                    }

        $.ajax({ 
          type: 'POST',
          url: '/platform/api/create_team.php', 
          data: vars, 
          dataType: 'json',
          success: function (data) { 
            if (data['status'] == "success") {

              // close the modal
              $('#createTeam').modal('toggle');

              // dummy loading overlay
              $.LoadingOverlay("show");

              // Hide it after 1.5 seconds
              setTimeout(function(){
                $.LoadingOverlay("hide");
              }, 1500);

              // refresh the page
              window.location.replace("/dashboard#team_page");
              location.reload();

            } else {
              // uh oh
              $('#failed_create_team_message').text(data['reason']);
              $('#failed_create_team').modal(); // this should show the modal

            }
          }
        });
      }

      function create_org() {

        var vars = {
                      o_name: $('#org_name').val(),
                      o_username: $('#org_username').val()
                    }

        $.ajax({ 
          type: 'POST',
          url: '/platform/api/create_org.php', 
          data: vars, 
          dataType: 'json',
          success: function (data) { 
            if (data['status'] == "success") {

              // close the modal
              $('#createOrg').modal('toggle');

              // dummy loading overlay
              $.LoadingOverlay("show");

              // Hide it after 1.5 seconds
              setTimeout(function(){
                $.LoadingOverlay("hide");
              }, 1500);

              // refresh the page
              window.location.replace("/dashboard#org_page");
              location.reload();

            } else {
              // uh oh
              $('#failed_create_org_message').text(data['reason']);
              $('#failed_create_org').modal(); // this should show the modal

            }
          }
        });
      }

      // create or join an org
      function create_or_join_org_modal() {
        if ($('input[name="create_or_join_org_select"]:checked').val() == "create"){
          // they want to create
          $('#createOrg').modal();

        } else {
          // they want to join
          $('#joinOrg').modal();

        }
      }

      function search_orgs() {
        var vars = {
                      search_term: $('#search_org_input').val()
                    }

        $.ajax({ 
          type: 'POST',
          url: '/platform/api/search_orgs.php', 
          data: vars, 
          dataType: 'json',
          success: function (data) { 
            if (data['status'] == "success") {

              // show the select label
              $("#hide-label-org").show();

              // show the results
              document.getElementById("search_org_select").innerHTML = data['html'];

            } else {
              // handle no response... responsibly
            }
          }
        });
      }

      function leave_team() {

        $.ajax({ 
          type: 'POST',
          url: '/platform/api/leave_team.php', 
          dataType: 'json',
          success: function (data) { 
            if (data['status'] == "success") {

              // dummy loading overlay
              $.LoadingOverlay("show");

              // Hide it after 1.5 seconds
              setTimeout(function(){
                $.LoadingOverlay("hide");
              }, 1500);

              // close the modal
              $('#leaveTeam').modal('toggle');

              // refresh the page
              location.reload();

            } else {

                // TODO ADD ERROR?

            }
          }
        });
      }

      function leave_org() {

        $.ajax({ 
          type: 'POST',
          url: '/platform/api/leave_org.php', 
          dataType: 'json',
          success: function (data) { 
            if (data['status'] == "success") {

              // dummy loading overlay
              $.LoadingOverlay("show");

              // Hide it after 1.5 seconds
              setTimeout(function(){
                $.LoadingOverlay("hide");
              }, 1500);

              // close the modal
              $('#leaveOrg').modal('toggle');

              // refresh the page
              location.reload();

            } else {
              // handle no response... responsibly
            }
          }
        });
      }

      // preview personal image
      function readURL(input) {
          if (input.files && input.files[0]) {
              var reader = new FileReader();
              
              reader.onload = function (e) {
                  $('#personal_image_preview').attr('src', e.target.result);
                  window.new_personal_image = e.target.result;
              }
              
              reader.readAsDataURL(input.files[0]);
          }
      }
 
      // preview team image
      function readURL_team(input) {
          if (input.files && input.files[0]) {
              var reader = new FileReader();
              
              reader.onload = function (e) {
                  $('#team_image_preview').attr('src', e.target.result);
                  window.new_team_image = e.target.result;
              }
              
              reader.readAsDataURL(input.files[0]);
          }
      }

      // preview org image
      function readURL_org(input) {
          if (input.files && input.files[0]) {
              var reader = new FileReader();
              
              reader.onload = function (e) {
                  $('#org_image_preview').attr('src', e.target.result);
                  window.new_org_image = e.target.result;
              }
              
              reader.readAsDataURL(input.files[0]);
          }
      }

      // preview profile picture image
      function readURL_profile_picture(input) {
          if (input.files && input.files[0]) {
              var reader = new FileReader();
              
              reader.onload = function (e) {
                  $('#profile_picture_image_preview').attr('src', e.target.result);
                  window.new_profile_picture_image = e.target.result;
              }
              
              reader.readAsDataURL(input.files[0]);
          }
      }

      function update_personal_photo() {

        // LETS SHOW/HIDE BUTTON
        $("#upload_personal").hide();
        $("#upload_personal_load").show();

        var vars = {
                      personal_photo: $('#personal_image_preview').attr('src')
                    }

        $.ajax({ 
          type: 'POST',
          url: '/platform/api/update_personal_photo.php', 
          data: vars, 
          dataType: 'json',
          success: function (data) { 
            if (data['status'] == "success") {

              // dummy loading overlay
              $.LoadingOverlay("show");

              // Hide it after 1.5 seconds
              setTimeout(function(){
                $.LoadingOverlay("hide");
              }, 1500);

              // close the modal
              $('#editPersonalPhotos').modal('toggle');

              // update photo shown
              $('#current_personal_page_photo').attr('src', window.new_personal_image);

              // revert these
              $("#upload_personal").show();
              $("#upload_personal_load").hide();

            } else {
              // handle no response... responsibly
              $('#failed_update_personal_photo_message').text(data['reason']);
              $('#failed_update_personal_photo').modal(); // this should show the modal

              // revert these
              $("#upload_personal").show();
              $("#upload_personal_load").hide();

            }
          }
        });
      }

      function update_team_photo() {

        // LETS SHOW/HIDE BUTTON
        $("#upload_team").hide();
        $("#upload_team_load").show();

        var vars = {
                      team_photo: $('#team_image_preview').attr('src')
                    }

        $.ajax({ 
          type: 'POST',
          url: '/platform/api/update_team_photo.php', 
          data: vars, 
          dataType: 'json',
          success: function (data) { 
            if (data['status'] == "success") {

              // dummy loading overlay
              $.LoadingOverlay("show");

              // Hide it after 1.5 seconds
              setTimeout(function(){
                $.LoadingOverlay("hide");
              }, 1500);

              // close the modal
              $('#editTeamPhotos').modal('toggle');

              // update photo shown
              $('#current_team_page_photo').attr('src', window.new_team_image);

              // revert these
              $("#upload_team").show();
              $("#upload_team_load").hide();

            } else {
              // handle no response... responsibly
              $('#failed_update_team_photo_message').text(data['reason']);
              $('#failed_update_team_photo').modal(); // this should show the modal

              // revert these
              $("#upload_team").show();
              $("#upload_team_load").hide();

            }
          }
        });
      }

      function update_org_photo() {

        // LETS SHOW/HIDE BUTTON
        $("#upload_org").hide();
        $("#upload_org_load").show();

        var vars = {
                      org_photo: $('#org_image_preview').attr('src')
                    }

        $.ajax({ 
          type: 'POST',
          url: '/platform/api/update_org_photo.php', 
          data: vars, 
          dataType: 'json',
          success: function (data) { 
            if (data['status'] == "success") {

              // dummy loading overlay
              $.LoadingOverlay("show");

              // Hide it after 1.5 seconds
              setTimeout(function(){
                $.LoadingOverlay("hide");
              }, 1500);

              // close the modal
              $('#editOrgPhotos').modal('toggle');

              // update photo shown
              $('#current_org_page_photo').attr('src', window.new_org_image);

              // revert these
              $("#upload_org").show();
              $("#upload_org_load").hide();

            } else {
              // handle no response... responsibly
              $('#failed_update_org_photo_message').text(data['reason']);
              $('#failed_update_org_photo').modal(); // this should show the modal

              // revert these
              $("#upload_org").show();
              $("#upload_org_load").hide();

            }
          }
        });
      }

      function update_profile_picture_photo() {

        // LETS SHOW/HIDE BUTTON
        $("#upload_profile").hide();
        $("#upload_profile_load").show();

        var vars = {
                      profile_picture_photo: $('#profile_picture_image_preview').attr('src')
                    }

        $.ajax({ 
          type: 'POST',
          url: '/platform/api/update_personal_profile_picture.php', 
          data: vars, 
          dataType: 'json',
          success: function (data) { 
            if (data['status'] == "success") {

              // dummy loading overlay
              $.LoadingOverlay("show");

              // Hide it after 1.5 seconds
              setTimeout(function(){
                $.LoadingOverlay("hide");
              }, 1500);

              // close the modal
              $('#changeProfilePicture').modal('toggle');

              // update photo shown
              $('#preview_profile_picture_image').attr('src', window.new_profile_picture_image);

              // revert these
              $("#upload_profile").show();
              $("#upload_profile_load").hide();

            } else {
              // handle no response... responsibly
              $('#failed_update_profile_picture_message').text(data['reason']);
              $('#failed_update_profile_picture').modal(); // this should show the modal

              // revert these
              $("#upload_profile").show();
              $("#upload_profile_load").hide();

            }
          }
        });
      }

      // email team
      function email_team() {

        var vars = {
                      message_subject: $('#message_subject').val(),
                      message_body: $('#message_body').val()
                    }

        $.ajax({ 
          type: 'POST',
          url: '/platform/api/email_team.php', 
          data: vars, 
          dataType: 'json',
          success: function (data) { 
            if (data['status'] == "success") {

              // dummy loading overlay
              $.LoadingOverlay("show");

              // Hide it after 1.5 seconds
              setTimeout(function(){
                $.LoadingOverlay("hide");
              }, 1500);

              // close the modal
              $('#emailTeam').modal('toggle');

            } else {
              // handle no response... responsibly
              $('#failed_email_team_message').text(data['reason']);
              $('#failed_email_team').modal(); // this should show the modal

            }
          }
        });
      }

      // TODO
      // extend to menu buttons at the top
      $(document).ready(function () {
        $('a[href="' + document.location.hash + '"]').trigger('click');
      });

      $(function () {
        $('[data-toggle="tooltip"]').tooltip()
      })

      function home_active_tab() {
        // active tabs
        $("#tab_home").attr('class', 'list-group-item active');
        $("#tab_personal_page").attr('class', 'list-group-item');
        $("#tab_team_page").attr('class', 'list-group-item');
        $("#tab_org_page").attr('class', 'list-group-item');
        $("#tab_donations").attr('class', 'list-group-item');
        $("#tab_account").attr('class', 'list-group-item');

        // icon colors
        $("#icon_home").addClass('white-icon');
        $("#icon_personal_page").removeClass('white-icon');
        $("#icon_team_page").removeClass('white-icon');
        $("#icon_org_page").removeClass('white-icon');
        $("#icon_donations").removeClass('white-icon');
        $("#icon_account").removeClass('white-icon');
      }
      function personal_active_tab() {
        $("#tab_home").attr('class', 'list-group-item');
        $("#tab_personal_page").attr('class', 'list-group-item active');
        $("#tab_team_page").attr('class', 'list-group-item');
        $("#tab_org_page").attr('class', 'list-group-item');
        $("#tab_donations").attr('class', 'list-group-item');
        $("#tab_account").attr('class', 'list-group-item');

        // icon colors
        $("#icon_home").removeClass('white-icon');
        $("#icon_personal_page").addClass('white-icon');
        $("#icon_team_page").removeClass('white-icon');
        $("#icon_org_page").removeClass('white-icon');
        $("#icon_donations").removeClass('white-icon');
        $("#icon_account").removeClass('white-icon');
      }
      function team_active_tab() {
        $("#tab_home").attr('class', 'list-group-item');
        $("#tab_personal_page").attr('class', 'list-group-item');
        $("#tab_team_page").attr('class', 'list-group-item active');
        $("#tab_org_page").attr('class', 'list-group-item');
        $("#tab_donations").attr('class', 'list-group-item');
        $("#tab_account").attr('class', 'list-group-item');

        // icon colors
        $("#icon_home").removeClass('white-icon');
        $("#icon_personal_page").removeClass('white-icon');
        $("#icon_team_page").addClass('white-icon');
        $("#icon_org_page").removeClass('white-icon');
        $("#icon_donations").removeClass('white-icon');
        $("#icon_account").removeClass('white-icon');
      }
      function org_active_tab() {
        $("#tab_home").attr('class', 'list-group-item');
        $("#tab_personal_page").attr('class', 'list-group-item');
        $("#tab_team_page").attr('class', 'list-group-item');
        $("#tab_org_page").attr('class', 'list-group-item active');
        $("#tab_donations").attr('class', 'list-group-item');
        $("#tab_account").attr('class', 'list-group-item');

        // icon colors
        $("#icon_home").removeClass('white-icon');
        $("#icon_personal_page").removeClass('white-icon');
        $("#icon_team_page").removeClass('white-icon');
        $("#icon_org_page").addClass('white-icon');
        $("#icon_donations").removeClass('white-icon');
        $("#icon_account").removeClass('white-icon');
      }
      function donations_active_tab() {
        $("#tab_home").attr('class', 'list-group-item');
        $("#tab_personal_page").attr('class', 'list-group-item');
        $("#tab_team_page").attr('class', 'list-group-item');
        $("#tab_org_page").attr('class', 'list-group-item');
        $("#tab_donations").attr('class', 'list-group-item active');
        $("#tab_account").attr('class', 'list-group-item');

        // icon colors
        $("#icon_home").removeClass('white-icon');
        $("#icon_personal_page").removeClass('white-icon');
        $("#icon_team_page").removeClass('white-icon');
        $("#icon_org_page").removeClass('white-icon');
        $("#icon_donations").addClass('white-icon');
        $("#icon_account").removeClass('white-icon');
      }
      function account_active_tab() {
        $("#tab_home").attr('class', 'list-group-item');
        $("#tab_personal_page").attr('class', 'list-group-item');
        $("#tab_team_page").attr('class', 'list-group-item');
        $("#tab_org_page").attr('class', 'list-group-item');
        $("#tab_donations").attr('class', 'list-group-item');
        $("#tab_account").attr('class', 'list-group-item active');

        // icon colors
        $("#icon_home").removeClass('white-icon');
        $("#icon_personal_page").removeClass('white-icon');
        $("#icon_team_page").removeClass('white-icon');
        $("#icon_org_page").removeClass('white-icon');
        $("#icon_donations").removeClass('white-icon');
        $("#icon_account").addClass('white-icon');
      }
    </script>
    <style>
      .fa-home {
        color: #555;
      }
      .fa-newspaper-o {
        color: #555;
      }
      .fa-usd {
        color: #555;
      }
      .fa-sitemap {
        color: #555;
      }
      .fa-users {
        color: #555;
      }
      .fa-clock-o {
        color: #555;
      }
      .fa-user {
        color: #555;
      }
      .fa-bell-o {
        color: #555;
      }
      .white-icon {
        color: #fff;
      }
      .fa-pencil {
        color: #555;
      }
      .fa-picture-o {
        color: #555;
      }
      .fa-eye {
        color: #555;
      }
      .fa-share {
        color: #555;
      }
      .carousel {
      border-radius: 10px 10px 10px 10px;
        overflow: hidden;
      }
      .full-width {
        width: 100%;
        height: 175px;
      }
      .glyph-size {
        font-size: 4vw;
      }
      .carousel-control.left, .carousel-control.right {
         background-image:none !important;
         filter:none !important;
      }
      .full-width-video {
        width: 100%;
        height: 350px;
      }
      .fa-user-plus {
        color: #555;
      }
      .page-header.no-top-margin {
        margin-top: 0px;
      }
      .fa-gift {
        color: #fff;
      }
      .fa-dollar {
        color: #fff;
      }
      .fa-flag-checkered {
        color: #fff;
      }
      .huge {
        font-size: 35px;
      }
      .black-link {
        color: #000;
      }
      .fa-plus {
        color: #555;
      }
      .carousel-inner img {
      margin: auto;
      }
      .form-control {
        width: 100%;
      }
      .hide-on-start {
        display: none;
      }
      .fa-twitter.share {
        color: #FFF;
      }
      .fa-facebook.share {
        color: #FFF;
      }
      .fa-envelope-o {
        color: #555;
      }
      .fa-trash-o {
        color: #FFF;
      }
      .fa-random {
        color: #555;
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
      .description-text {
        white-space: pre-line;
      }
      .fa-unlock-alt {
        color: #555;
      }
      .fa-user-times {
        color: #555;
      }
      .fa-trash-o {
        color: #555;
      }
      .fa-paper-plane-o {
        color: #fff;
      }
      .fa-spinner {
        color: #fff;
      }
    </style>
  </head>
  <body>

    <!-- MEDU BAR -->
    <div id="menu"></div>
    <!-- MENU BAR-->

    <!-- GIMME A BREAK -->
    <br>
    <!-- GIMME A BREAK -->

    <!-- - - - - - -  -->
    <!-- PAGE CONTENT -->
    <!-- - - - - - -  -->

    <div class="container">
      <div class="row">
        <div class="col-md-12 page-header">
          <h1>Welcome, <?php echo $m_full_name;?></h1>
        </div>
      </div>
      <div class="row">

        <div class="col-md-3">
          <br>
          <div class="list-group nav" role="tablist" id="tabs" name="tabs">
            <a id="tab_home" href="#home" aria-controls="home" role="tab" data-toggle="tab" class="list-group-item active" onclick="home_active_tab()"><i id="icon_home" class="fa fa-home fa-fw white-icon" aria-hidden="true"></i>&nbsp; Home</a>
            <a id="tab_personal_page" href="#personal_page" aria-controls="personal_page" role="tab" data-toggle="tab" class="list-group-item" onclick="personal_active_tab()"><i id="icon_personal_page" class="fa fa-newspaper-o fa-fw" aria-hidden="true"></i>&nbsp; Personal Page</a>
            <a id="tab_team_page" href="#team_page" aria-controls="team_page" role="tab" data-toggle="tab" class="list-group-item" onclick="team_active_tab()"><i id="icon_team_page" class="fa fa-users fa-fw" aria-hidden="true"></i>&nbsp; Team Page</a>
            <a id="tab_org_page" href="#org_page" aria-controls="org_page" role="tab" data-toggle="tab" class="list-group-item" onclick="org_active_tab()"><i id="icon_org_page" class="fa fa-sitemap fa-fw" aria-hidden="true"></i>&nbsp; Organization Page</a>
            <a id="tab_donations" href="#donations" aria-controls="donations" role="tab" data-toggle="tab" class="list-group-item" onclick="donations_active_tab()"><i id="icon_donations" class="fa fa-usd fa-fw" aria-hidden="true"></i>&nbsp; Donations</a>
            <a id="tab_account" href="#account" aria-controls="account" role="tab" data-toggle="tab" class="list-group-item" onclick="account_active_tab()"><i id="icon_account" class="fa fa-user fa-fw" aria-hidden="true"></i>&nbsp; Account</a>
            <a id="tab_previous" href="#previous" aria-controls="previous" role="tab" data-toggle="modal" class="list-group-item" data-target="#previous_coming_soon"><i id="icon_preview" class="fa fa-clock-o fa-fw" aria-hidden="true"></i>&nbsp; Previous Contributors</a>
          </div>
        </div>

        <div class="col-md-9">
          <!-- Tab panes -->
          <div class="tab-content">
            <div role="tabpanel" class="tab-pane fade in active" id="home">

            <!-- Tab panes -->
            <br>

            <div class="tab-content no-top-margin">
              <!-- PERSONAL PROGRESS -->
                <div role="tabpanel" class="tab-pane fade in active" id="personal_progress">
                  <div class="row">
                      <div class="col-md-4">
                          <div class="panel panel-primary">
                              <div class="panel-heading">
                                  <div class="row">
                                      <div class="col-xs-3">
                                          <i class="fa fa-gift fa-4x"></i>
                                      </div>
                                      <div class="col-xs-9 text-right">
                                          <div class="huge"><?php echo $donation_count; ?></div>
                                          <div><?php echo $donation_or_donations; ?></div>
                                      </div>
                                  </div>
                              </div>
                              <a href="#">
                                  <div class="panel-footer">
                                      <span class="pull-left"><a href="#donations" aria-controls="donations" role="tab" data-toggle="tab" onclick="donations_active_tab()">View Donations</a></span>
                                      <span class="pull-right"><a href="#donations" aria-controls="donations" role="tab" data-toggle="tab" onclick="donations_active_tab()"><i class="fa fa-arrow-circle-right"></i></a></span>
                                      <div class="clearfix"></div>
                                  </div>
                              </a>
                          </div>
                      </div>
                      <div class="col-md-4">
                          <div class="panel panel-primary">
                              <div class="panel-heading">
                                  <div class="row">
                                      <div class="col-xs-3">
                                          <i class="fa fa-dollar fa-4x"></i>
                                      </div>
                                      <div class="col-xs-9 text-right">
                                          <div class="huge">$<?php echo number_format($total_raised); ?></div>
                                          <div>Raised</div>
                                      </div>
                                  </div>
                              </div>
                              <a href="#">
                                  <div class="panel-footer">
                                      <span class="pull-left"><a href="#" data-toggle="modal" data-target="#sharePersonal">Share Your Page</a></span>
                                      <span class="pull-right"><a href="#" data-toggle="modal" data-target="#sharePersonal"><i class="fa fa-arrow-circle-right"></i></a></span>
                                      <div class="clearfix"></div>
                                  </div>
                              </a>
                          </div>
                      </div>
                      <div class="col-md-4">
                          <div class="panel panel-primary">
                              <div class="panel-heading">
                                  <div class="row">
                                      <div class="col-xs-3">
                                          <i class="fa fa-flag-checkered fa-4x"></i>
                                      </div>
                                      <div class="col-xs-9 text-right">
                                          <div class="huge"><?php echo $goal_percentage; ?></div>
                                          <div>of $<?php echo number_format($m_page_goal)?></div>
                                      </div>
                                  </div>
                              </div>
                              <a href="#">
                                  <div class="panel-footer">
                                      <span class="pull-left"><a target="_blank" href="/member/<?php echo $m_username; ?>">View Your Page</a></span>
                                      <span class="pull-right"><a target="_blank" href="/member/<?php echo $m_username; ?>"><i class="fa fa-arrow-circle-right"></i></a></span>
                                      <div class="clearfix"></div>
                                  </div>
                              </a>
                          </div>
                      </div>
                  </div>
                  <!-- /.row -->
                </div>
              </div>
              <!-- PERSONAL PROGRESS -->

              <h4>No-Shave November 2017</h4>
              <hr>

              <p>Thank you for joining No-Shave November and helping the fight against cancer! With you and your fundraising efforts, it brings us one step closer to finding a cure for the disease.<br><br>This year's campaign will support programs at Prevent Cancer Foundation, Fight Colorectal Cancer, and St. Jude Children's Research Hospital.  Each of the foundations listed are making great strides in fighting, researching, and preventing cancer.  Put down the razor, skip the hair appointment, and let's make the 2017 No-Shave November campaign the best year yet!<br><br>Hairy November,<br><br>The No-Shave November Team</p>
              <br>

            </div>

            <div role="tabpanel" class="tab-pane fade" id="personal_page">
              <!-- title -->
              <div class="page-header no-top-margin">
                <h2 class="visible-lg visible-md visible-sm hidden-xs">Personal Page
                  <div class="pull-right">
                    <button type="button" class="btn btn-default" data-toggle="modal" data-target="#editPersonal"><i class="fa fa-pencil" aria-hidden="true"></i>&nbsp; Edit</button>
                    <button type="button" class="btn btn-default" data-toggle="modal" data-target="#editPersonalPhotos"><i class="fa fa-picture-o" aria-hidden="true"></i>&nbsp; Change Photo</button>
                    <button type="button" class="btn btn-default" data-toggle="modal" data-target="#sharePersonal"><i class="fa fa-share" aria-hidden="true"></i>&nbsp; Share</button>
                    <a id="view_personal_page" type="button" class="btn btn-default" target="_blank" href="/member/<?php echo $m_username; ?>"><i class="fa fa-eye" aria-hidden="true"></i>&nbsp; View</a>
                  </div>
                </h2>
                <h2 class="hidden-lg hidden-md hidden-sm visible-xs">Personal Page
                  <div class="btn-group pull-right">
                    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                      Menu <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu">
                      <li><a href="#" data-toggle="modal" data-target="#editPersonal"><i class="fa fa-fw fa-pencil" aria-hidden="true"></i>&nbsp; Edit</a></li>
                      <li><a href="#" data-toggle="modal" data-target="#editPersonalPhotos"><i class="fa fa-fw fa-picture-o" aria-hidden="true"></i>&nbsp; Change Photo</a></li>
                      <li><a href="#" data-toggle="modal" data-target="#sharePersonal"><i class="fa fa-fw fa-share" aria-hidden="true"></i>&nbsp; Share</a></li>
                      <li><a id="view_personal_page" target="_blank" href="/member/<?php echo $m_username; ?>"><i class="fa fa-fw fa-eye" aria-hidden="true"></i>&nbsp; View</a></li>
                    </ul>
                  </div>
                </h2>
              </div>
              <!-- title -->

              <!-- raw content -->
              <div class="row">
                <div class="col-md-12">

                  <form class="form-horizontal">

                    <div class="form-group">
                      <label class="col-sm-3 control-label">Page Link</label>
                      <div class="col-sm-9">
                        <p id="personal_url" class="form-control-static"><a id="view_personal_page_2" target="_blank" href="/member/<?php echo $m_username; ?>">no-shave.org/member/<?php echo $m_username; ?></a></p>
                      </div>
                    </div>

                    <div class="form-group">
                      <label class="col-sm-3 control-label">Location</label>
                      <div class="col-sm-9">
                        <p id="personal_location" class="form-control-static"><?php echo $personal_location; ?></p>
                      </div>
                    </div>

                    <div class="form-group">
                      <label class="col-sm-3 control-label">Page Title</label>
                      <div class="col-sm-9">
                        <p id="page_title" class="form-control-static"><?php echo $m_page_title; ?></p>
                      </div>
                    </div>

                    <div class="form-group">
                      <label class="col-sm-3 control-label">Page Photo</label>
                      <div class="col-sm-9">
                        <img id="current_personal_page_photo" style="width: 100%; height: 100%;" src="<?php echo $m_pic_0;?>" alt="<?php echo $m_full_name; ?> No Shave November 2017" class="img-rounded">
                      </div>
                    </div>

                    <div class="form-group">
                      <label class="col-sm-3 control-label">Fundraising Goal</label>
                      <div class="col-sm-9">
                        <p id="page_goal" class="form-control-static">$<?php echo number_format($m_page_goal); ?></p>
                      </div>
                    </div>

                    <div class="form-group">
                      <label class="col-sm-3 control-label">Page Description</label>
                      <div class="col-sm-9">
                        <p id="page_description" class="description-text form-control-static"><?php echo $m_page_description; ?></p>
                      </div>
                    </div>

                  </form>

                </div>
              </div>
              <!-- raw content -->

            </div>

            <div role="tabpanel" class="tab-pane fade" id="team_page">


              <?php if (team_owner($user_id, $mysqli) == true) : ?>

              <!-- TEAM OWNER -->
              <div class="page-header no-top-margin">
                <h2 class="visible-lg visible-md hidden-sm hidden-xs">Team Page
                  <div class="pull-right">
                    <button type="button" class="btn btn-default" data-toggle="modal" data-target="#editTeam"><i class="fa fa-pencil" aria-hidden="true"></i>&nbsp; Edit</button>
                    <button type="button" class="btn btn-default" data-toggle="modal" data-target="#emailTeam"><i class="fa fa-envelope-o" aria-hidden="true"></i>&nbsp; Email Team</button>
                    <button type="button" class="btn btn-default" data-toggle="modal" data-target="#editTeamPhotos"><i class="fa fa-picture-o" aria-hidden="true"></i>&nbsp; Change Photo</button>
                    <button type="button" class="btn btn-default" data-toggle="modal" data-target="#inviteTeam"><i class="fa fa-user-plus" aria-hidden="true"></i>&nbsp; Invite</button>
                    <button type="button" class="btn btn-default" data-toggle="modal" data-target="#shareTeam"><i class="fa fa-share" aria-hidden="true"></i>&nbsp; Share</button>
                    <a id="view_team_page" type="button" class="btn btn-default" target="_blank" href="/team/<?php echo $t_username; ?>"><i class="fa fa-eye" aria-hidden="true"></i>&nbsp; View</a>
                  </div>
                </h2>

                <h2 class="hidden-lg hidden-md visible-sm visible-xs">Team Page
                  <div class="btn-group pull-right">
                    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                      Menu <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu">
                      <li><a href="#" data-toggle="modal" data-target="#editTeam"><i class="fa fa-fw fa-pencil" aria-hidden="true"></i>&nbsp; Edit</a></li>
                      <li><a href="#" data-toggle="modal" data-target="#emailTeam"><i class="fa fa-fw fa-envelope-o" aria-hidden="true"></i>&nbsp; Email Team</a></li>
                      <li><a href="#" data-toggle="modal" data-target="#editTeamPhotos"><i class="fa fa-fw fa-picture-o" aria-hidden="true"></i>&nbsp; Change Photo</a></li>
                      <li><a href="#" data-toggle="modal" data-target="#inviteTeam"><i class="fa fa-fw fa-user-plus" aria-hidden="true"></i>&nbsp; Invite</a></li>
                      <li><a href="#" data-toggle="modal" data-target="#shareTeam"><i class="fa fa-fw fa-share" aria-hidden="true"></i>&nbsp; Share</a></li>
                      <li><a id="view_team_page" target="_blank" href="/team/<?php echo $t_username; ?>"><i class="fa fa-fw fa-eye" aria-hidden="true"></i>&nbsp; View</a></li>
                    </ul>
                  </div>
                </h2>
              </div>

              <div class="row">
                <div class="col-md-12">

                  <form class="form-horizontal">

                    <div class="form-group">
                      <label class="col-sm-3 control-label">Page Link</label>
                      <div class="col-sm-9">
                        <p class="form-control-static"><a id="view_team_page_2" href="https://no-shave.org/team/<?php echo $t_username; ?>">no-shave.org/team/<?php echo $t_username; ?></a></p>
                      </div>
                    </div>

                    <div class="form-group">
                      <label class="col-sm-3 control-label">Page Title</label>
                      <div class="col-sm-9">
                        <p id="team_title" class="form-control-static"><?php echo $t_page_title; ?></p>
                      </div>
                    </div>

                    <div class="form-group">
                      <label class="col-sm-3 control-label">Page Photos</label>
                      <div class="col-sm-9">
                        <img id="current_team_page_photo" style="width: 100%; height: 100%;" src="<?php echo $t_pic_0; ?>" alt="<?php echo $t_name; ?> No Shave November 2017" class="img-rounded">
                      </div>
                    </div>

                    <div class="form-group">
                      <label class="col-sm-3 control-label">Fundraising Goal</label>
                      <div class="col-sm-9">
                        <p id="team_goal" class="form-control-static">$<?php echo number_format($t_page_goal); ?></p>
                      </div>
                    </div>

                    <div class="form-group">
                      <label class="col-sm-3 control-label">Page Description</label>
                      <div class="col-sm-9">
                        <p id="team_description" class="description-text form-control-static"><?php echo $t_page_description;?><br></p>
                      </div>
                    </div>

                  </form>

                </div>
              </div>
              <!-- TEAM OWNER -->

            <?php  elseif (team_member($user_id, $mysqli) == true) : ?>

              <!-- TEAM MEMBER -->
              <div class="page-header no-top-margin">
                <h2 class="visible-lg visible-md visible-sm hidden-xs">Team Page
                  <div class="pull-right">
                    <button type="button" class="btn btn-default" data-toggle="modal" data-target="#leaveTeam"><i class="fa fa-user-times" aria-hidden="true"></i>&nbsp; Leave Team</button>
                    <button type="button" class="btn btn-default" data-toggle="modal" data-target="#shareTeam"><i class="fa fa-share" aria-hidden="true"></i>&nbsp; Share</button>
                    <a id="view_team_page" type="button" class="btn btn-default" target="_blank" href="/team/<?php echo $t_username; ?>"><i class="fa fa-eye" aria-hidden="true"></i>&nbsp; View</a>
                  </div>
                </h2>

                <h2 class="hidden-lg hidden-md hidden-sm visible-xs">Team Page
                  <div class="btn-group pull-right">
                    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                      Menu <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu">
                      <li><a href="#" data-toggle="modal" data-target="#leaveTeam"><i class="fa fa-fw fa-user-times" aria-hidden="true"></i>&nbsp; Leave Team</a></li>
                      <li><a href="#" data-toggle="modal" data-target="#shareTeam"><i class="fa fa-fw fa-share" aria-hidden="true"></i>&nbsp; Share</a></li>
                      <li><a id="view_team_page" target="_blank" href="/team/<?php echo $t_username; ?>"><i class="fa fa-fw fa-eye" aria-hidden="true"></i>&nbsp; View</a></li>
                    </ul>
                  </div>
                </h2>
              </div>

              <div class="row">
                <div class="col-md-12">

                  <form class="form-horizontal">

                    <div class="form-group">
                      <label class="col-sm-3 control-label">Page Link</label>
                      <div class="col-sm-9">
                        <p class="form-control-static"><a href="https://no-shave.org/team/<?php echo $t_username; ?>">no-shave.org/team/<?php echo $t_username; ?></a></p>
                      </div>
                    </div>

                    <div class="form-group">
                      <label class="col-sm-3 control-label">Page Title</label>
                      <div class="col-sm-9">
                        <p class="form-control-static"><?php echo $t_page_title; ?></p>
                      </div>
                    </div>

                    <div class="form-group">
                      <label class="col-sm-3 control-label">Page Photos</label>
                      <div class="col-sm-9">
                        <img style="width: 100%; height: 100%;" src="<?php echo $t_pic_0; ?>" alt="<?php echo $t_name; ?> No Shave November 2017" class="img-rounded">
                      </div>
                    </div>

                    <div class="form-group">
                      <label class="col-sm-3 control-label">Fundraising Goal</label>
                      <div class="col-sm-9">
                        <p class="form-control-static">$<?php echo number_format($t_page_goal); ?></p>
                      </div>
                    </div>

                    <div class="form-group">
                      <label class="col-sm-3 control-label">Page Description</label>
                      <div class="col-sm-9">
                        <p class="description-text form-control-static"><?php echo $t_page_description;?><br></p>
                      </div>
                    </div>

                  </form>

                </div>
              </div>
              <!-- TEAM MEMBER -->

            <?php  else : ?>

              <!-- NO TEAM, YET -->
              <div class="page-header no-top-margin">
                <h2>Create or Join a Team?
                </h2>
              </div>

              <div class="row">
                <h2 class="centered">Select an Option Below to Get Started</h2>
                <p class="centered">If you want to join or create a team, select an option below.</p>
                <br>

                <div class="centered">
                  <div class="centered btn-table" data-toggle="buttons">
                    <label class="btn btn-default btn-checkbox">
                      <input type="radio" name="create_or_join_team_select" id="create_or_join_team_select" value="create" checked> <i class="fa fa-users fa-5x" aria-hidden="true"></i><br><br>Create Team
                    </label>
                    <!-- Need some room, geeze -->
                    &nbsp;&nbsp;&nbsp;&nbsp;
                    <label class="btn btn-default btn-checkbox">
                      <input type="radio" name="create_or_join_team_select" id="create_or_join_team_select" value="join"> <i class="fa fa-user-plus fa-5x" aria-hidden="true"></i><br><br>Join Team
                    </label>
                  </div>
                </div>

              <br><br>
              <button name="create_or_join_team" id="create_or_join_team" type="button" class="btn btn-success center-block" onclick="create_or_join_team_modal()">Get Started</button>
              
              </div>
              <!-- NO TEAM, YET -->

            <?php  endif; ?>

            </div>

            <div role="tabpanel" class="tab-pane fade" id="org_page">

              <?php if (org_owner($user_id, $mysqli) == true) : ?>

              <!-- ORG OWNER -->
              <div class="page-header no-top-margin">
                <h2 class="visible-lg visible-md visible-sm hidden-xs">Organization Page
                  <div class="pull-right">
                    <button type="button" class="btn btn-default" data-toggle="modal" data-target="#editOrg"><i class="fa fa-pencil" aria-hidden="true"></i>&nbsp; Edit</button>
                    <button type="button" class="btn btn-default" data-toggle="modal" data-target="#editOrgPhotos"><i class="fa fa-picture-o" aria-hidden="true"></i>&nbsp; Change Photo</button>
                    <button type="button" class="btn btn-default" data-toggle="modal" data-target="#shareOrg"><i class="fa fa-share" aria-hidden="true"></i>&nbsp; Share</button>
                    <a id="view_org_page" type="button" class="btn btn-default" target="_blank" href="/org/<?php echo $o_username; ?>"><i class="fa fa-eye" aria-hidden="true"></i>&nbsp; View</a>
                  </div>
                </h2>

                <h2 class="hidden-lg hidden-md hidden-sm visible-xs">Organization Page
                  <div class="btn-group pull-right">
                    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                      Menu <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu">
                      <li><a href="#" data-toggle="modal" data-target="#editOrg"><i class="fa fa-fw fa-pencil" aria-hidden="true"></i>&nbsp; Edit</a></li>
                      <li><a href="#" data-toggle="modal" data-target="#editOrgPhotos"><i class="fa fa-fw fa-picture-o" aria-hidden="true"></i>&nbsp; Change Photo</a></li>
                      <li><a href="#" data-toggle="modal" data-target="#shareOrg"><i class="fa fa-fw fa-share" aria-hidden="true"></i>&nbsp; Share</a></li>
                      <li><a id="view_org_page" target="_blank" href="/org/<?php echo $o_username; ?>"><i class="fa fa-fw fa-eye" aria-hidden="true"></i>&nbsp; View</a></li>
                    </ul>
                  </div>
                </h2>
              </div>

              <!-- raw content -->
              <div class="row">
                <div class="col-md-12">

                  <form class="form-horizontal">

                    <div class="form-group">
                      <label class="col-sm-3 control-label">Page Link</label>
                      <div class="col-sm-9">
                        <p class="form-control-static"><a id="view_org_page_1" href="https://no-shave.org/org/<?php echo $o_username; ?>">no-shave.org/org/<?php echo $o_username; ?></a></p>
                      </div>
                    </div>

                    <div class="form-group">
                      <label class="col-sm-3 control-label">Page Title</label>
                      <div class="col-sm-9">
                        <p id="org_title" class="form-control-static"><?php echo $o_page_title; ?></p>
                      </div>
                    </div>

                    <div class="form-group">
                      <label class="col-sm-3 control-label">Page Photo</label>
                      <div class="col-sm-9">
                        <img id="current_org_page_photo" style="width: 100%; height: 100%;" src="<?php echo $o_pic_0; ?>" alt="<?php echo $o_name; ?> No Shave November 2017" class="img-rounded">
                      </div>
                    </div>

                    <div class="form-group">
                      <label class="col-sm-3 control-label">Fundraising Goal</label>
                      <div class="col-sm-9">
                        <p id="org_goal" class="form-control-static">$<?php echo number_format($o_page_goal); ?></p>
                      </div>
                    </div>

                    <div class="form-group">
                      <label class="col-sm-3 control-label">Page Description</label>
                      <div class="col-sm-9">
                        <p id="org_description" class="description-text form-control-static"><?php echo $o_page_description; ?><br></p>
                      </div>
                    </div>

                  </form>

                </div>
              </div>
              <!-- ORG OWNER -->

              <?php  elseif (org_member($user_id, $mysqli) == true) : ?>

              <!-- ORG MEMBER -->
              <div class="page-header no-top-margin">
                <h2 class="visible-lg visible-md visible-sm hidden-xs">Organization Page
                  <div class="pull-right">

                    <?php if (team_owner($user_id, $mysqli) == true) : ?>
                      <button type="button" class="btn btn-default" data-toggle="modal" data-target="#leaveOrg"><i class="fa fa-user-times" aria-hidden="true"></i>&nbsp; Leave Organization</button>
                    <?php  else : ?>
                    <?php  endif; ?>
                    <button type="button" class="btn btn-default" data-toggle="modal" data-target="#shareOrg"><i class="fa fa-share" aria-hidden="true"></i>&nbsp; Share</button>
                    <a type="button" class="btn btn-default" target="_blank" href="/org/<?php echo $o_username; ?>"><i class="fa fa-eye" aria-hidden="true"></i>&nbsp; View</a>
                  </div>
                </h2>

                <h2 class="hidden-lg hidden-md hidden-sm visible-xs">Organization Page
                  <div class="btn-group pull-right">
                    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                      Menu <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu">
                      <?php if (team_owner($user_id, $mysqli) == true) : ?>
                      <li><a href="#" data-toggle="modal" data-target="#leaveOrg"><i class="fa fa-fw fa-user-times" aria-hidden="true"></i>&nbsp; Leave Organization</a></li>
                      <?php  else : ?>
                      <?php  endif; ?>
                      <li><a href="#" data-toggle="modal" data-target="#shareOrg"><i class="fa fa-fw fa-share" aria-hidden="true"></i>&nbsp; Share</a></li>
                      <li><a target="_blank" href="/org/<?php echo $o_username; ?>"><i class="fa fa-fw fa-eye" aria-hidden="true"></i>&nbsp; View</a></li>
                    </ul>
                  </div>
                </h2>
              </div>

              <!-- raw content -->
              <div class="row">
                <div class="col-md-12">

                  <form class="form-horizontal">

                    <div class="form-group">
                      <label class="col-sm-3 control-label">Page Link</label>
                      <div class="col-sm-9">
                        <p class="form-control-static"><a href="https://no-shave.org/org/<?php echo $o_username; ?>">no-shave.org/org/<?php echo $o_username; ?></a></p>
                      </div>
                    </div>

                    <div class="form-group">
                      <label class="col-sm-3 control-label">Page Title</label>
                      <div class="col-sm-9">
                        <p class="form-control-static"><?php echo $o_page_title; ?></p>
                      </div>
                    </div>

                    <div class="form-group">
                      <label class="col-sm-3 control-label">Page Photo</label>
                      <div class="col-sm-9">
                        <img style="width: 100%; height: 100%;" src="<?php echo $o_pic_0; ?>" alt="<?php echo $o_name; ?> No Shave November 2017" class="img-rounded">
                      </div>
                    </div>

                    <div class="form-group">
                      <label class="col-sm-3 control-label">Fundraising Goal</label>
                      <div class="col-sm-9">
                        <p class="form-control-static">$<?php echo number_format($o_page_goal); ?></p>
                      </div>
                    </div>

                    <div class="form-group">
                      <label class="col-sm-3 control-label">Page Description</label>
                      <div class="col-sm-9">
                        <p class="description-text form-control-static"><?php echo $o_page_description; ?><br></p>
                      </div>
                    </div>

                  </form>

                </div>
              </div>
              <!-- ORG MEMBER -->

              <?php  else : ?>

              <!-- NO ORG, YET -->
              <div class="page-header no-top-margin">
                <h2>Create or Join an Organization?
                </h2>
              </div>

              <div class="row">
                <h2 class="centered">Select an Option Below to Get Started</h2>
                <p class="centered">If you want to join or create an Organization, select an option below. Please note, only teams can belong to organizations.</p>
                <br>

                <div class="centered">
                  <div class="centered btn-table" data-toggle="buttons">
                    <label class="btn btn-default btn-checkbox">
                      <input type="radio" name="create_or_join_org_select" id="create_or_join_org_select" value="create" checked> <i class="fa fa-sitemap fa-5x" aria-hidden="true"></i><br><br>Create Organization
                    </label>
                    <!-- Need some room, geeze -->
                    &nbsp;&nbsp;&nbsp;&nbsp;
                    <label class="btn btn-default btn-checkbox">
                      <input type="radio" name="create_or_join_org_select" id="create_or_join_org_select" value="join"> <i class="fa fa-plus fa-5x" aria-hidden="true"></i><br><br>Join Organization
                    </label>
                  </div>
                </div>

              <br><br>
              <button name="create_or_join_org" id="create_or_join_org" type="button" class="btn btn-success center-block" onclick="create_or_join_org_modal()">Get Started</button>
              
              </div>
              <!-- NO ORG, YET -->

              <?php  endif; ?>

            </div>


            <div role="tabpanel" class="tab-pane fade" id="donations">
              <div class="page-header no-top-margin">
                <h2>Donations
                  <div class="pull-right">
                    <button type="button" class="btn btn-default visible-lg visible-md visible-sm hidden-xs" data-toggle="modal" data-target="#offlineDonation"><i class="fa fa-plus" aria-hidden="true"></i>&nbsp; Add Offline Contribution</button>
                    <button type="button" class="btn btn-default hidden-lg hidden-md hidden-sm visible-xs" data-toggle="modal" data-target="#offlineDonation"><i class="fa fa-plus" aria-hidden="true"></i>&nbsp; Offline Donation</button>
                  </div>
                </h2>
              </div>

              <!-- raw content -->
              <div class="row">
                <div class="col-md-12">

                  <form class="form-horizontal">

                    <?php echo $personal_donation_table; ?>
                    <?php echo $team_donation_table; ?>
                    <?php echo $org_donation_table; ?>

                  </form>

                </div>
              </div>


            </div>

            <div role="tabpanel" class="tab-pane fade" id="account">
              <div class="page-header no-top-margin">
                <h2 class="visible-lg visible-md visible-sm hidden-xs">Account
                  <div class="pull-right">
                    <a type="button" class="btn btn-default" href="/password" target="_blank"><i class="fa fa-unlock-alt" aria-hidden="true"></i>&nbsp; Change Password</a>
                    <button type="button" class="btn btn-default" data-toggle="modal" data-target="#changeProfilePicture"><i class="fa fa-picture-o" aria-hidden="true"></i>&nbsp; Change Picture</button>
                    <button type="button" class="btn btn-default" data-toggle="modal" data-target="#editAccount"><i class="fa fa-pencil" aria-hidden="true"></i>&nbsp; Edit</button>
                  </div>
                </h2>

                <h2 class="hidden-lg hidden-md hidden-sm visible-xs">Account
                  <div class="btn-group pull-right">
                    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                      Menu <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu">
                      <li><a href="/password" target="_blank"><i class="fa fa-fw fa-unlock-alt" aria-hidden="true"></i>&nbsp; Change Password</a></li>
                      <li><a href="#" data-toggle="modal" data-target="#changeProfilePicture"><i class="fa fa-fw fa-picture-o" aria-hidden="true"></i>&nbsp; Change Picture</a></li>
                      <li><a href="#" data-toggle="modal" data-target="#editAccount"><i class="fa fa-fw fa-pencil" aria-hidden="true"></i>&nbsp; Edit</a></li>
                    </ul>
                  </div>
                </h2>
              </div>

              <!-- raw content -->
              <div class="row">
                <div class="col-md-12">

                  <form class="form-horizontal">

                    <div class="form-group">
                      <label class="col-sm-3 control-label">Full Name</label>
                      <div class="col-sm-9">
                        <p id="show_full_name" class="form-control-static"><?php echo $m_full_name; ?></p>
                      </div>
                    </div>

                    <div class="form-group">
                      <label class="col-sm-3 control-label">Profile Picture</label>
                      <div class="col-sm-9">
                        <p class="form-control-static"><img id="preview_profile_picture_image" class="img-rounded" height="42" width="42" src="<?php echo $m_profile_pic; ?>"></p>
                      </div>
                    </div>

                    <div class="form-group">
                      <label class="col-sm-3 control-label">Email Address</label>
                      <div class="col-sm-9">
                        <p id="show_email_address" class="form-control-static"><?php echo $m_email; ?></p>
                      </div>
                    </div>

                    <div class="form-group">
                      <label class="col-sm-3 control-label">Password</label>
                      <div class="col-sm-9">
                        <p class="form-control-static">********</p>
                      </div>
                    </div>

                    <div class="form-group">
                      <label class="col-sm-3 control-label">City</label>
                      <div class="col-sm-9">
                        <p id="show_city" class="form-control-static"><?php echo $m_city; ?></p>
                      </div>
                    </div>

                    <div class="form-group">
                      <label class="col-sm-3 control-label">State</label>
                      <div class="col-sm-9">
                        <p id="show_state" class="form-control-static"><?php echo $m_state; ?></p>
                      </div>
                    </div>

                    <div class="form-group">
                      <label class="col-sm-3 control-label">Country</label>
                      <div class="col-sm-9">
                        <p id="show_country" class="form-control-static"><?php echo $m_country; ?></p>
                      </div>
                    </div>

                    <div class="form-group">
                      <label class="col-sm-3 control-label">Got Screened</label>
                      <div class="col-sm-9">
                        <p id="show_got_screen" class="form-control-static"><?php echo $member_got_screened; ?></p>
                      </div>
                    </div>

                  </form>
                </div>
              </div>
            </div>        
          </div>
        </div>
      </div>
    </div>

    <!-- - - - - - -  -->
    <!-- PAGE CONTENT -->
    <!-- - - - - - -  -->

    <!-- - - - -->
    <!-- MODAL -->
    <!-- - - - -->

    <!-- EDIT PERSONAL PAGE -->
    <div class="modal fade" tabindex="-1" role="dialog" id="editPersonal">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          
          <form><!-- form -->

            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
              <h4 class="modal-title">Edit Personal Page</h4>
            </div>

            <div class="modal-body">

              <div class="form-group">
                <label>Username</label>
                <input type="text" class="form-control" id="personal_username" name="personal_username" placeholder="Username" value="<?php echo $m_username; ?>">
              </div>

              <div class="form-group">
                <label>Location Visibility</label>
                <div class="radio">
                  <label>
                    <input type="radio" name="location_visibility" id="location_visibility" value="1" <?php echo $show_location; ?>>
                    Show Location
                  </label>
                </div>
                <div class="radio">
                  <label>
                    <input type="radio" name="location_visibility" id="location_visibility" value="0" <?php echo $hide_location; ?>>
                    Hide Location
                  </label>
                </div>
              </div>

              <div class="form-group">
                <label>Location Format</label>
                <div class="radio">
                  <label>
                    <input type="radio" name="location_format" id="location_format" value="0" <?php echo $city_state; ?>>
                    City, State
                  </label>
                </div>
                <div class="radio">
                  <label>
                    <input type="radio" name="location_format" id="location_format" value="1" <?php echo $city_country; ?>>
                    City, Country
                  </label>
                </div>
                <div class="radio">
                  <label>
                    <input type="radio" name="location_format" id="location_format" value="2" <?php echo $city_only; ?>>
                    City
                  </label>
                </div>
                <div class="radio">
                  <label>
                    <input type="radio" name="location_format" id="location_format" value="3" <?php echo $country_only; ?>>
                    Country
                  </label>
                </div>
              </div>

              <div class="form-group">
                <label>Page Title</label>
                <input type="text" class="form-control" id="personal_page_title" name="personal_page_title" placeholder="Page Title" value="<?php echo $m_page_title; ?>">
              </div>

              <div class="form-group">
                <label>Fundraising Goal</label>
                <input type="number" class="form-control" id="personal_goal" name="personal_goal" placeholder="250" value="<?php echo $m_page_goal; ?>">
              </div>

              <div class="form-group">
                <label>Page Description</label>
                <textarea id="personal_page_description" name="personal_page_description" class="form-control" rows="8" placeholder="Page Description"><?php echo $m_page_description; ?></textarea>
              </div>

            </div>

            <div class="modal-footer">
              <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
              <button type="button" class="btn btn-primary" onclick="update_personal_page()">Save changes</button>
            </div>

          </form><!-- form -->

        </div><!-- /.modal-content -->
      </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
    <!-- EDIT PERSONAL PAGE -->

    <!-- EDIT TEAM PAGE -->
    <div class="modal fade" tabindex="-1" role="dialog" id="editTeam">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          
          <form><!-- form -->

            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
              <h4 class="modal-title">Edit Team Page</h4>
            </div>

            <div class="modal-body">

              <div class="form-group">
                <label>Team Name</label>
                <input type="text" class="form-control" id="team_name_edit" name="team_name_edit" placeholder="Team Name" value="<?php echo $t_name; ?>">
              </div>

              <div class="form-group">
                <label>Team Username</label>
                <input type="text" class="form-control" id="team_username_edit" name="team_username_edit" placeholder="Team Username" value="<?php echo $t_username; ?>">
              </div>

              <div class="form-group">
                <label>Team Page Title</label>
                <input type="text" class="form-control" id="team_title_edit" name="team_title_edit" placeholder="Team Page Title" value="<?php echo $t_page_title; ?>">
              </div>

              <div class="form-group">
                <label>Team Fundraising Goal</label>
                <input type="number" class="form-control" id="team_goal_edit" name="team_goal_edit" placeholder="2500" value="<?php echo $t_page_goal; ?>">
              </div>

              <div class="form-group">
                <label>Team Page Description</label>
                <textarea id="team_description_edit" name="team_description_edit" class="form-control" rows="8" placeholder="Team Page Description"><?php echo $t_page_description; ?></textarea>
              </div>

            </div>

            <div class="modal-footer">
              <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
              <button type="button" class="btn btn-primary" onclick="update_team_page()">Save changes</button>
            </div>

          </form><!-- form -->

        </div><!-- /.modal-content -->
      </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
    <!-- EDIT TEAM PAGE -->

    <!-- EDIT TEAM PAGE -->
    <div class="modal fade" tabindex="-1" role="dialog" id="editOrg">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          
          <form><!-- form -->

            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
              <h4 class="modal-title">Edit Organization Page</h4>
            </div>

            <div class="modal-body">

              <div class="form-group">
                <label>Organization Name</label>
                <input type="text" class="form-control" id="org_name_edit" name="org_name_edit" placeholder="Organization Name" value="<?php echo $o_name; ?>">
              </div>

              <div class="form-group">
                <label>Organization Username</label>
                <input type="text" class="form-control" id="org_username_edit" name="org_username_edit" placeholder="Organization Username" value="<?php echo $o_username; ?>">
              </div>

              <div class="form-group">
                <label>Organization Page Title</label>
                <input type="text" class="form-control" id="org_title_edit" name="org_title_edit" placeholder="Organization Page Title" value="<?php echo $o_page_title; ?>">
              </div>

              <div class="form-group">
                <label>Organization Fundraising Goal</label>
                <input type="number" class="form-control" id="org_goal_edit" name="org_goal_edit" placeholder="5000" value="<?php echo $o_page_goal; ?>">
              </div>

              <div class="form-group">
                <label>Organization Page Description</label>
                <textarea id="org_description_edit" name="org_description_edit" class="form-control" rows="8" placeholder="Organization Page Description"><?php echo $o_page_description; ?></textarea>
              </div>

            </div>

            <div class="modal-footer">
              <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
              <button type="button" class="btn btn-primary" onclick="update_org_page()">Save changes</button>
            </div>

          </form><!-- form -->

        </div><!-- /.modal-content -->
      </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
    <!-- EDIT TEAM PAGE -->

    <!-- EDIT ACCOUNT -->
    <div class="modal fade" tabindex="-1" role="dialog" id="editAccount">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          
          <form><!-- form -->

            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
              <h4 class="modal-title">Edit Account</h4>
            </div>

            <div class="modal-body">

              <div class="form-group">
                <label>Full Name</label>
                <input type="text" class="form-control" id="full_name" name="full_name" placeholder="Full Name" value="<?php echo $m_full_name; ?>">
              </div>

              <div class="form-group">
                <label>Email Address</label>
                <input type="text" class="form-control" id="email_address" name="email_address" placeholder="Email Address" value="<?php echo $m_email; ?>">
              </div>

              <div class="form-group">
                <label>City</label>
                <input type="text" class="form-control" id="city" name="city" placeholder="City" value="<?php echo $m_city; ?>">
              </div>

              <div class="form-group">
                <label>State / Region</label>
                <input type="text" class="form-control" id="state" name="state" placeholder="State" value="<?php echo $m_state; ?>">
              </div>

              <div class="form-group">
                <label>Country</label>
                <input type="text" class="form-control" id="country" name="country" placeholder="Country" value="<?php echo $m_country; ?>">
              </div>

              <div class="form-group">
                <label>Cancer Screening</label>
                <div class="radio">
                  <label>
                    <input type="radio" name="got_screened" id="got_screened" value="yes" <?php echo $yes_screened; ?>>
                    Yes, I got a cancer screening
                  </label>
                </div>
                <div class="radio">
                  <label>
                    <input type="radio" name="got_screened" id="got_screened" value="no" <?php echo $no_screened; ?>>
                    No, not yet
                  </label>
                </div>
              </div>

            </div>

            <div class="modal-footer">
              <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
              <button type="button" class="btn btn-primary" onclick="update_account()">Save changes</button>
            </div>

          </form><!-- form -->

        </div><!-- /.modal-content -->
      </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
    <!-- EDIT ACCOUNT -->

    <!-- OFFLINE DONATION -->
    <div class="modal fade" tabindex="-1" role="dialog" id="offlineDonation">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          
          <form><!-- form -->

            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
              <h4 class="modal-title">Add Offline Contribution</h4>
            </div>

            <div class="modal-body">

              <div class="form-group">
                <label>Donation Amount</label>
                <input type="number" class="form-control" id="donation_amount" name="donation_amount" placeholder="Amount">
              </div>

              <div class="form-group">
                <label>Full Name</label>
                <input type="text" class="form-control" id="donation_name" name="donation_name" placeholder="Full Name">
              </div>

              <div class="form-group">
                <label>Company (optional)</label>
                <input type="text" class="form-control" id="donation_company" name="donation_company" placeholder="Company">
              </div>

              <div class="form-group">
                <label>Message (optional)</label>
                <textarea class="form-control" id="donation_message" name="donation_message" placeholder="Message" rows="4"></textarea>
              </div>

              <div class="form-group">
                <label>Visible on Page</label>
                <div class="radio">
                  <label>
                    <input type="radio" name="donation_visbile" id="donation_visbile" value="1" >
                    Yes, display this donation
                  </label>
                </div>
                <div class="radio">
                  <label>
                    <input type="radio" name="donation_visbile" id="donation_visbile" value="0" >
                    No, please hide this donation
                  </label>
                </div>
              </div>

              <div class="form-group">
                <label>Donation Attribution</label>

                <div class="radio">
                  <label>
                    <input type="radio" name="donation_attribution" id="donation_attribution" value="1" >
                    Add the donation to my personal page
                  </label>
                </div>

              <?php if (team_member($user_id, $mysqli) == true) : ?>

                <div class="radio">
                  <label>
                    <input type="radio" name="donation_attribution" id="donation_attribution" value="2" >
                    Add the donation to my team's page
                  </label>
                </div>

              <?php  endif; ?>

              <?php  if (org_member($user_id, $mysqli) == true) : ?>

                <div class="radio">
                  <label>
                    <input type="radio" name="donation_attribution" id="donation_attribution" value="3" >
                    Add the donation to my organization's page
                  </label>
                </div>

              <?php  endif; ?>

              </div>
            </div>

            <div class="modal-footer">
              <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
              <button type="button" class="btn btn-primary" onclick="offline_donation()">Save changes</button>
            </div>

          </form><!-- form -->

        </div><!-- /.modal-content -->
      </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
    <!-- OFFLINE DONATION -->

    <!-- SHARE -->
    <div class="modal fade" tabindex="-1" role="dialog" id="sharePersonal">
      <div class="modal-dialog" role="document">
        <div class="modal-content">

            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
              <h4 class="modal-title">Share Personal Page</h4>
            </div>

            <div class="modal-body centered">
              <div class="visible-lg visible-md visible-sm hidden-xs">
                <br>
                <a onclick="twitter()">
                  <button class="btn btn-info btn-lg">
                    <i class="fa fa-twitter share"></i>&nbsp; Twitter
                  </button>
                </a>&nbsp;&nbsp;&nbsp;&nbsp;

                <a onclick="facebook()">
                  <button class="btn btn-primary btn-lg">
                    <i class="fa fa-facebook share"></i>&nbsp; Facebook
                  </button>
                </a>&nbsp;&nbsp;&nbsp;&nbsp;

                <a href="mailto:name@email.com?Subject=Let%20it%20Grow%21&Body=During%20the%20month%20of%20November%2C%20thousands%20of%20people%20will%20partake%20in%20what%20is%20now%20commonly%20referred%20to%20as%20No-Shave%20November.%20%20%20I%20am%20doing%20my%20part%20by%20letting%20my%20hair%20grow%20free%20to%20embrace%20what%20most%20cancer%20patients%20will%20lose%20during%20their%20battle.%20%20%20%0A%0AThis%20year%27s%20campaign%20will%20support%20programs%20at%20Prevent%20Cancer%20Foundation%2C%20Fight%20Colorectal%20Cancer%2C%20and%20St.%20Jude%20Children%27s%20Research%20Hospital.%20Each%20of%20the%20foundations%20listed%20are%20making%20great%20strides%20in%20fighting%2C%20researching%2C%20and%20preventing%20cancer.%0A%0APlease%20help%20me%20reach%20my%20fundraising%20goal%20by%20following%20the%20link%20below%3A%20https%3A//no-shave.org/member/<?php echo $m_username; ?>%0A%0AFor%20more%20information%2C%20please%20visit%20no-shave.org.%0A%0AThank%20you%20for%20your%20generosity%2C%0A%0A<?php echo $m_full_name; ?>">
                  <button class="btn btn-default btn-lg">
                    <i class="fa fa-envelope-o"></i>&nbsp; Email
                  </button>
                </a>
                <br>
                <br>
              </div>

              <div class="hidden-lg hidden-md hidden-sm visible-xs">
                <br>
                <div class="btn-group centered">
                  <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Share Page <span class="caret"></span>
                  </button>
                  <ul class="dropdown-menu">
                    <li><a onclick="twitter()">
                        <i style="color: #555;" class="fa fa-fw fa-twitter"></i>&nbsp; Twitter
                    </a></li>

                    <li><a onclick="facebook()">
                        <i style="color: #555;" class="fa fa-fw fa-facebook"></i>&nbsp; Facebook
                    </a></li>

                    <li><a href="mailto:name@email.com?Subject=Let%20it%20Grow%21&Body=During%20the%20month%20of%20November%2C%20thousands%20of%20people%20will%20partake%20in%20what%20is%20now%20commonly%20referred%20to%20as%20No-Shave%20November.%20%20%20I%20am%20doing%20my%20part%20by%20letting%20my%20hair%20grow%20free%20to%20embrace%20what%20most%20cancer%20patients%20will%20lose%20during%20their%20battle.%20%20%20%0A%0AThis%20year%27s%20campaign%20will%20support%20programs%20at%20Prevent%20Cancer%20Foundation%2C%20Fight%20Colorectal%20Cancer%2C%20and%20St.%20Jude%20Children%27s%20Research%20Hospital.%20Each%20of%20the%20foundations%20listed%20are%20making%20great%20strides%20in%20fighting%2C%20researching%2C%20and%20preventing%20cancer.%0A%0APlease%20help%20me%20reach%20my%20fundraising%20goal%20by%20following%20the%20link%20below%3A%20https%3A//no-shave.org/member/<?php echo $m_username; ?>%0A%0AFor%20more%20information%2C%20please%20visit%20no-shave.org.%0A%0AThank%20you%20for%20your%20generosity%2C%0A%0A<?php echo $m_full_name; ?>">
                        <i class="fa fa-fw fa-envelope-o"></i>&nbsp; Email
                    </a></li>
                  </ul>
                </div>
                <br>
                <br>
              </div>

              <div class="row">
                <div class="col-md-10 col-md-offset-1 centered">
                  <label>Page Link</label>
                  <input type="text" class="form-control text-center" placeholder="https://no-shave.org/member/<?php echo $m_username; ?>" value="https://no-shave.org/member/<?php echo $m_username; ?>" onClick="this.select();">
                </div>
              </div>

            </div>

            <div class="modal-footer">
              <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>

        </div><!-- /.modal-content -->
      </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
    <!-- SHARE -->

    <!-- SHARE -->
    <div class="modal fade" tabindex="-1" role="dialog" id="shareTeam">
      <div class="modal-dialog" role="document">
        <div class="modal-content">

            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
              <h4 class="modal-title">Share Team Page</h4>
            </div>

            <div class="modal-body centered">
              <br>
              <a onclick="twitter_team()">
                <button class="btn btn-info btn-lg">
                  <i class="fa fa-twitter share"></i>&nbsp; Twitter
                </button>
              </a>&nbsp;&nbsp;&nbsp;&nbsp;

              <a onclick="facebook_team()">
                <button class="btn btn-primary btn-lg">
                  <i class="fa fa-facebook share"></i>&nbsp; Facebook
                </button>
              </a>&nbsp;&nbsp;&nbsp;&nbsp;

              <a href="mailto:name@email.com?Subject=Let%20it%20Grow%21&Body=During%20the%20month%20of%20November%2C%20thousands%20of%20people%20will%20partake%20in%20what%20is%20now%20commonly%20referred%20to%20as%20No-Shave%20November.%20%20%20I%20am%20doing%20my%20part%20by%20letting%20my%20hair%20grow%20free%20to%20embrace%20what%20most%20cancer%20patients%20will%20lose%20during%20their%20battle%20and%20inviting%20you%20to%20join%20my%20efforts.%0A%0AThis%20year%27s%20campaign%20will%20support%20programs%20at%20Prevent%20Cancer%20Foundation%2C%20Fight%20Colorectal%20Cancer%2C%20and%20St.%20Jude%20Children%27s%20Research%20Hospital.%20Each%20of%20the%20foundations%20listed%20are%20making%20great%20strides%20in%20fighting%2C%20researching%2C%20and%20preventing%20cancer.%0A%0AClick%20the%20link%20below%20to%20sign-up%20and%20join%20my%20team%3A%20https%3A//no-shave.org/team/<?php echo $t_username;?>%0A%0AIf%20you%20would%20like%20to%20help%20me%20reach%20my%20goal%2C%20click%20here%3A%20https%3A//no-shave.org/member/<?php echo $m_username;?>%0A%0AFor%20more%20information%2C%20please%20visit%20no-shave.org.%0A%0AThank%20you%20for%20your%20generosity%2C%0A%0A<?php echo $m_full_name;?>">
                <button class="btn btn-default btn-lg">
                  <i class="fa fa-envelope-o"></i>&nbsp; Email
                </button>
              </a>

              <br><br>

              <div class="row">
                <div class="col-md-10 col-md-offset-1 centered">
                  <label>Page Link</label>
                  <input type="text" class="form-control text-center" placeholder="https://no-shave.org/team/<?php echo $t_username; ?>" value="https://no-shave.org/team/<?php echo $t_username; ?>" onClick="this.select();">
                </div>
              </div>

            </div>

            <div class="modal-footer">
              <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>

        </div><!-- /.modal-content -->
      </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
    <!-- SHARE -->

    <!-- SHARE -->
    <div class="modal fade" tabindex="-1" role="dialog" id="inviteTeam">
      <div class="modal-dialog" role="document">
        <div class="modal-content">

            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
              <h4 class="modal-title">Invite People to Join Your Team</h4>
            </div>

            <div class="modal-body centered">
              <br>
              <a onclick="twitter_team_invite()">
                <button class="btn btn-info btn-lg">
                  <i class="fa fa-twitter share"></i>&nbsp; Twitter
                </button>
              </a>&nbsp;&nbsp;&nbsp;&nbsp;

              <a onclick="facebook_team_invite()">
                <button class="btn btn-primary btn-lg">
                  <i class="fa fa-facebook share"></i>&nbsp; Facebook
                </button>
              </a>&nbsp;&nbsp;&nbsp;&nbsp;

              <a href="mailto:name@email.com?Subject=Let%20it%20Grow%21&Body=During%20the%20month%20of%20November%2C%20thousands%20of%20people%20will%20partake%20in%20what%20is%20now%20commonly%20referred%20to%20as%20No-Shave%20November.%20%20%20I%20am%20doing%20my%20part%20by%20letting%20my%20hair%20grow%20free%20to%20embrace%20what%20most%20cancer%20patients%20will%20lose%20during%20their%20battle%20and%20inviting%20you%20to%20join%20my%20efforts.%0A%0AThis%20year%27s%20campaign%20will%20support%20programs%20at%20Prevent%20Cancer%20Foundation%2C%20Fight%20Colorectal%20Cancer%2C%20and%20St.%20Jude%20Children%27s%20Research%20Hospital.%20Each%20of%20the%20foundations%20listed%20are%20making%20great%20strides%20in%20fighting%2C%20researching%2C%20and%20preventing%20cancer.%0A%0AClick%20the%20link%20below%20to%20sign-up%20and%20join%20my%20team%3A%20https%3A//no-shave.org/team/<?php echo $t_username;?>%0A%0AIf%20you%20would%20like%20to%20help%20me%20reach%20my%20goal%2C%20click%20here%3A%20https%3A//no-shave.org/member/<?php echo $m_username;?>%0A%0AFor%20more%20information%2C%20please%20visit%20no-shave.org.%0A%0AThank%20you%20for%20your%20generosity%2C%0A%0A<?php echo $m_full_name;?>">
                <button class="btn btn-default btn-lg">
                  <i class="fa fa-envelope-o"></i>&nbsp; Email
                </button>
              </a>

              <br><br>

              <div class="row">
                <div class="col-md-10 col-md-offset-1 centered">
                  <label>Page Link</label>
                  <input type="text" class="form-control text-center" placeholder="https://no-shave.org/register?t=<?php echo $t_username; ?>" value="https://no-shave.org/register?t=<?php echo $t_username; ?>" onClick="this.select();">
                </div>
              </div>

            </div>

            <div class="modal-footer">
              <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>

        </div><!-- /.modal-content -->
      </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
    <!-- SHARE -->

    <!-- SHARE -->
    <div class="modal fade" tabindex="-1" role="dialog" id="shareOrg">
      <div class="modal-dialog" role="document">
        <div class="modal-content">

            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
              <h4 class="modal-title">Share Organization Page</h4>
            </div>

            <div class="modal-body centered">
              <br>
              <a onclick="twitter_org()">
                <button class="btn btn-info btn-lg">
                  <i class="fa fa-twitter share"></i>&nbsp; Twitter
                </button>
              </a>&nbsp;&nbsp;&nbsp;&nbsp;

              <a onclick="facebook_org()">
                <button class="btn btn-primary btn-lg">
                  <i class="fa fa-facebook share"></i>&nbsp; Facebook
                </button>
              </a>&nbsp;&nbsp;&nbsp;&nbsp;

              <a href="mailto:name@email.com?Subject=Let%20it%20Grow%21&Body=During%20the%20month%20of%20November%2C%20thousands%20of%20people%20will%20partake%20in%20what%20is%20now%20commonly%20referred%20to%20as%20No-Shave%20November.%20%20Foregoing%20shaving%20or%20skipping%20a%20hair%20appointment%20is%20one%20of%20many%20ways%20people%20can%20get%20involved%20in%20helping%20the%20cause.%20%20<?php echo $o_name; ?>%20is%20doing%20our%20part%20by%20raising%20funds%20and%20inviting%20you%20to%20create%20a%20team%20to%20join%20our%20efforts.%0A%0AThis%20year%27s%20campaign%20will%20support%20programs%20at%20Prevent%20Cancer%20Foundation%2C%20Fight%20Colorectal%20Cancer%2C%20and%20St.%20Jude%20Children%27s%20Research%20Hospital.%20Each%20of%20the%20foundations%20listed%20are%20making%20great%20strides%20in%20fighting%2C%20researching%2C%20and%20preventing%20cancer.%0A%0AIf%20you%20would%20like%20to%20donate%20towards%20our%20goal%2C%20click%20here%3A%20https%3A//no-shave.org/org/<?php echo $o_username; ?>%0A%0AFor%20more%20information%2C%20please%20visit%20no-shave.org.%0A%0AThank%20you%20for%20your%20generosity%2C%0A%0A<?php echo $m_full_name; ?>">
                <button class="btn btn-default btn-lg">
                  <i class="fa fa-envelope-o"></i>&nbsp; Email
                </button>
              </a>

              <br><br>

              <div class="row">
                <div class="col-md-10 col-md-offset-1 centered">
                  <label>Page Link</label>
                  <input type="text" class="form-control text-center" placeholder="https://no-shave.org/org/<?php echo $o_username; ?>" value="https://no-shave.org/org/<?php echo $o_username; ?>" onClick="this.select();">
                </div>
              </div>

            </div>

            <div class="modal-footer">
              <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>

        </div><!-- /.modal-content -->
      </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
    <!-- SHARE -->

    <!-- EDIT PERSONAL PHOTO -->
    <div class="modal fade" tabindex="-1" role="dialog" id="editPersonalPhotos">
      <div class="modal-dialog" role="document">
        <div class="modal-content">

            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
              <h4 class="modal-title">Change Personal Page Photo</h4>
            </div>

            <div class="modal-body centered">
              <form id="personal_photo">
                  <label class="btn btn-default btn-file">
                      Select New Photo <input onchange="readURL(this)" type="file" id="personalImageBrowse" name="personalImageBrowse" style="display: none;">
                  </label>
                  <br><br>

                  <img id="personal_image_preview" name="personal_image_preview" style="width: 100%; height: 100%;" src="<?php echo $m_pic_0;?>" alt="<?php echo $m_full_name; ?> No Shave November 2017" class="img-rounded">
                </div>
              </form>
            <div class="modal-footer">
              <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
              <button id="upload_personal" type="button" class="btn btn-primary" onclick="update_personal_photo()">Upload</button>
              <button id="upload_personal_load" style="display:none;" type="button" class="btn btn-primary disabled"><i class="fa fa-spinner fa-pulse fa-fw"></i>&nbsp; Uploading...</button>
            </div>

        </div><!-- /.modal-content -->
      </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
    <!-- EDIT PERSONAL PHOTO -->

    <!-- EDIT TEAM PHOTO -->
    <div class="modal fade" tabindex="-1" role="dialog" id="editTeamPhotos">
      <div class="modal-dialog" role="document">
        <div class="modal-content">

            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
              <h4 class="modal-title">Change Team Page Photo</h4>
            </div>

            <div class="modal-body centered">
              <form id="personal_photo">
                  <label class="btn btn-default btn-file">
                      Select New Photo <input onchange="readURL_team(this)" type="file" id="teamImageBrowse" name="teamImageBrowse" style="display: none;">
                  </label>
                  <br><br>

                  <img id="team_image_preview" name="team_image_preview" style="width: 100%; height: 100%;" src="<?php echo $t_pic_0;?>" alt="<?php echo $t_name; ?> No Shave November 2017" class="img-rounded">
                </div>
              </form>
            <div class="modal-footer">
              <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
              <button id="upload_team" type="button" class="btn btn-primary" onclick="update_team_photo()">Upload</button>
              <button id="upload_team_load" style="display:none;" type="button" class="btn btn-primary disabled"><i class="fa fa-spinner fa-pulse fa-fw"></i>&nbsp; Uploading...</button>
            </div>

        </div><!-- /.modal-content -->
      </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
    <!-- EDIT TEAM PHOTO -->

    <!-- EDIT ORG PHOTO -->
    <div class="modal fade" tabindex="-1" role="dialog" id="editOrgPhotos">
      <div class="modal-dialog" role="document">
        <div class="modal-content">

            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
              <h4 class="modal-title">Change Organization Page Photo</h4>
            </div>

            <div class="modal-body centered">
              <form id="personal_photo">
                  <label class="btn btn-default btn-file">
                      Select New Photo <input onchange="readURL_org(this)" type="file" id="orgImageBrowse" name="orgImageBrowse" style="display: none;">
                  </label>
                  <br><br>

                  <img id="org_image_preview" name="org_image_preview" style="width: 100%; height: 100%;" src="<?php echo $o_pic_0;?>" alt="<?php echo $o_name; ?> No Shave November 2017" class="img-rounded">
                </div>
              </form>
            <div class="modal-footer">
              <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
              <button id="upload_org" type="button" class="btn btn-primary" onclick="update_org_photo()">Upload</button>
              <button id="upload_org_load" style="display:none;" type="button" class="btn btn-primary disabled"><i class="fa fa-spinner fa-pulse fa-fw"></i>&nbsp; Uploading...</button>
            </div>

        </div><!-- /.modal-content -->
      </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
    <!-- EDIT ORG PHOTO -->

    <!-- EDIT ACCOUNT PHOTO -->
    <div class="modal fade" tabindex="-1" role="dialog" id="changeProfilePicture">
      <div class="modal-dialog" role="document">
        <div class="modal-content">

            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
              <h4 class="modal-title">Change Profile Picture</h4>
            </div>

            <div class="modal-body centered">
              <form id="personal_photo">
                  <label class="btn btn-default btn-file">
                      Select New Photo <input onchange="readURL_profile_picture(this)" type="file" id="profilePictureImageBrowse" name="profilePictureImageBrowse" style="display: none;">
                  </label>
                  <br><br>

                  <img id="profile_picture_image_preview" name="profile_picture_image_preview" style="width: 100%; height: 100%;" src="<?php echo $m_profile_pic;?>" alt="<?php echo $m_full_name; ?> No Shave November 2017" class="img-rounded">
                </div>
              </form>
            <div class="modal-footer">
              <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
              <button id="upload_profile" type="button" class="btn btn-primary" onclick="update_profile_picture_photo()">Upload</button>
              <button id="upload_profile_load" style="display:none;" type="button" class="btn btn-primary disabled"><i class="fa fa-spinner fa-pulse fa-fw"></i>&nbsp; Uploading...</button>
            </div>

        </div><!-- /.modal-content -->
      </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
    <!-- EDIT ACCOUNT PHOTO -->

    <!-- JOIN TEAM -->
    <div class="modal fade" tabindex="-1" role="dialog" id="joinTeam">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          
          <form><!-- form -->

            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
              <h4 class="modal-title">Join a Team</h4>
            </div>

            <div class="modal-body">

              <div class="form-group">
                <label>Search Teams</label>
                <input type="text" class="form-control full-input-width" id="search_input" name="search_input" placeholder="Search Teams" onkeyup="search_teams()" autocomplete="off">
              </div>

              <div class="form-group">
                <div id="hide-label" style="display:none;"><label>Select a Team Below</label></div>
                <div id="search_select"></div>
              </div>

            </div>

            <div class="modal-footer">
              <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
              <button type="button" class="btn btn-primary" onclick="join_team()">Join Team</button>
            </div>

          </form><!-- form -->

        </div><!-- /.modal-content -->
      </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
    <!-- JOIN TEAM -->

    <!-- JOIN ORG -->
    <div class="modal fade" tabindex="-1" role="dialog" id="joinOrg">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          
          <form><!-- form -->

            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
              <h4 class="modal-title">Join an Organization</h4>
            </div>

            <div class="modal-body">

      <?php if (team_owner($user_id, $mysqli) == true) : ?>

                <div class="form-group">
                  <label>Search Organizations</label>
                  <input type="text" class="form-control full-input-width" id="search_org_input" name="search_org_input" placeholder="Search Organizations" onkeyup="search_orgs()" autocomplete="off">
                </div>

                <div class="form-group">
                  <div id="hide-label-org" style="display:none;"><label>Select an Organization Below</label></div>
                  <div id="search_org_select"></div>
                </div>
            </div>

            <div class="modal-footer">
              <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
              <button type="button" class="btn btn-primary" onclick="join_org()">Join Organization</button>
            </div>

          </form><!-- form -->

      <?php  elseif (team_member($user_id, $mysqli) == true) : ?>

                <div class="form-group">
                  <p>Only teams captains can add their team to an organization.</p>
                </div>
            </div>

            <div class="modal-footer">
              <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>

          </form><!-- form -->

      <?php  else : ?>

                <div class="form-group">
                  <p>Only teams can be added to organizations. You need to create a team before you can continue.</p>
                </div>
            </div>

            <div class="modal-footer">
              <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>

          </form><!-- form -->

      <?php  endif; ?>

        </div><!-- /.modal-content -->
      </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
    <!-- JOIN ORG -->

    <!-- LEAVE TEAM -->
    <div class="modal fade" tabindex="-1" role="dialog" id="leaveTeam">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          
          <form><!-- form -->

            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
              <h4 class="modal-title">Leave <?php echo $t_name; ?></h4>
            </div>

            <div class="modal-body">
              <p>Are you sure you want to leave <?php echo $t_name; ?>?  No hard feelings.  You can always re-join the team in case you change your mind!</p>
            </div>

            <div class="modal-footer">
              <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
              <button type="button" class="btn btn-danger" onclick="leave_team()">Leave Team</button>
            </div>

          </form><!-- form -->

        </div><!-- /.modal-content -->
      </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
    <!-- LEAVE TEAM -->

    <!-- LEAVE ORG -->
    <div class="modal fade" tabindex="-1" role="dialog" id="leaveOrg">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          
          <form><!-- form -->

            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
              <h4 class="modal-title">Leave <?php echo $o_name; ?></h4>
            </div>

            <div class="modal-body">
              <p>Are you sure you want to leave <?php echo $o_name; ?>?  No hard feelings.  You can always re-join this organization in case you change your mind!</p>
            </div>

            <div class="modal-footer">
              <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
              <button type="button" class="btn btn-danger" onclick="leave_org()">Leave Organization</button>
            </div>

          </form><!-- form -->

        </div><!-- /.modal-content -->
      </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
    <!-- LEAVE ORG -->

    <!-- CREATE TEAM -->
    <div class="modal fade" tabindex="-1" role="dialog" id="createTeam">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          
          <form><!-- form -->

            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
              <h4 class="modal-title">Create a Team</h4>
            </div>

            <div class="modal-body">

              <div class="form-group">
                <label>Team Name</label>
                <input type="text" class="form-control" id="team_name" name="team_name" placeholder="Team Name" maxlength="128" value="">
              </div>

              <div class="form-group">
                <label>Team Username</label>
                <input type="text" class="form-control" id="team_username" name="team_username" placeholder="Team Username" pattern="^([_A-z0-9]){1,}$" maxlength="128" value="">
              </div>

            </div>

            <div class="modal-footer">
              <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
              <button type="button" class="btn btn-primary" onclick="create_team()">Create Team</button>
            </div>

          </form><!-- form -->

        </div><!-- /.modal-content -->
      </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
    <!-- CREATE TEAM -->

    <!-- CREATE ORG -->
    <div class="modal fade" tabindex="-1" role="dialog" id="createOrg">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          
          <form><!-- form -->

            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
              <h4 class="modal-title">Create an Organization</h4>
            </div>

            <div class="modal-body">

              <div class="form-group">
                <label>Organization Name</label>
                <input type="text" class="form-control" id="org_name" name="org_name" placeholder="Organization Name" maxlength="128" value="">
              </div>

              <div class="form-group">
                <label>Organization Username</label>
                <input type="text" class="form-control" id="org_username" name="org_username" placeholder="Organization Username" pattern="^([_A-z0-9]){1,}$" maxlength="128" value="">
              </div>

            </div>

            <div class="modal-footer">
              <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
              <button type="button" class="btn btn-primary" onclick="create_org()">Create Organization</button>
            </div>

          </form><!-- form -->

        </div><!-- /.modal-content -->
      </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
    <!-- CREATE ORG -->

    <div id="emailTeam" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="emailTeam">
      <div class="modal-dialog modal-sm">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">Email Team Members</h4>
          </div>

          <div class="modal-body">

              <div class="form-group">
                <label>Message Subject</label>
                <input type="text" class="form-control" id="message_subject" name="message_subject" placeholder="Message Subject" maxlength="128" value="">
              </div>

              <div class="form-group">
                <label>Message Body</label>
                <textarea id="message_body" name="message_body" class="form-control" rows="8" placeholder="Message Body"></textarea>
              </div>

          </div>

          <div class="modal-footer">
              <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
              <button type="button" class="btn btn-primary" onclick="email_team()"><i class="fa fa-paper-plane-o" aria-hidden="true"></i>&nbsp; Send</button>
          </div>
        </div><!-- /.modal-content -->
      </div><!-- /.modal-dialog -->
    </div>

    <div id="failed_emailed_team" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="failed_emailed_team">
      <div class="modal-dialog modal-sm">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">Failed to Email Team</h4>
          </div>
          <div class="modal-body">
            <h4 id="failed_emailed_team_message" name="failed_emailed_team_message"></h4>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Try Again</button>
          </div>
        </div><!-- /.modal-content -->
      </div><!-- /.modal-dialog -->
    </div>


    <div id="failed_update_personal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="failed_update_personal">
      <div class="modal-dialog modal-sm">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">Failed to Update Personal Page</h4>
          </div>
          <div class="modal-body">
            <h4 id="failed_update_personal_message" name="failed_update_personal_message"></h4>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Try Again</button>
          </div>
        </div><!-- /.modal-content -->
      </div><!-- /.modal-dialog -->
    </div>

    <div id="failed_update_account" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="failed_update_account">
      <div class="modal-dialog modal-sm">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">Failed to Update Account</h4>
          </div>
          <div class="modal-body">
            <h4 id="failed_update_account_message" name="failed_update_account_message"></h4>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Try Again</button>
          </div>
        </div><!-- /.modal-content -->
      </div><!-- /.modal-dialog -->
    </div>

    <div id="failed_offline_donation" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="failed_offline_donation">
      <div class="modal-dialog modal-sm">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">Failed to Add Offline Contribution</h4>
          </div>
          <div class="modal-body">
            <h4 id="failed_offline_donation_message" name="failed_offline_donation_message"></h4>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Try Again</button>
          </div>
        </div><!-- /.modal-content -->
      </div><!-- /.modal-dialog -->
    </div>

    <div id="failed_join_team" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="failed_join_team">
      <div class="modal-dialog modal-sm">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">Failed to Join Team</h4>
          </div>
          <div class="modal-body">
            <h4 id="failed_join_team_message" name="failed_join_team_message"></h4>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Try Again</button>
          </div>
        </div><!-- /.modal-content -->
      </div><!-- /.modal-dialog -->
    </div>

    <div id="failed_join_org" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="failed_join_org">
      <div class="modal-dialog modal-sm">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">Failed to Join Organization</h4>
          </div>
          <div class="modal-body">
            <h4 id="failed_join_org_message" name="failed_join_org_message"></h4>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Try Again</button>
          </div>
        </div><!-- /.modal-content -->
      </div><!-- /.modal-dialog -->
    </div>

    <div id="failed_create_team" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="failed_create_team">
      <div class="modal-dialog modal-sm">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">Failed to Create Team</h4>
          </div>
          <div class="modal-body">
            <h4 id="failed_create_team_message" name="failed_create_team_message"></h4>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Try Again</button>
          </div>
        </div><!-- /.modal-content -->
      </div><!-- /.modal-dialog -->
    </div>

    <div id="failed_create_org" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="failed_create_org">
      <div class="modal-dialog modal-sm">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">Failed to Create Organization</h4>
          </div>
          <div class="modal-body">
            <h4 id="failed_create_org_message" name="failed_create_org_message"></h4>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Try Again</button>
          </div>
        </div><!-- /.modal-content -->
      </div><!-- /.modal-dialog -->
    </div>

    <div id="failed_update_team" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="failed_update_team">
      <div class="modal-dialog modal-sm">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">Failed to Update Team</h4>
          </div>
          <div class="modal-body">
            <h4 id="failed_update_team_message" name="failed_update_team_message"></h4>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Try Again</button>
          </div>
        </div><!-- /.modal-content -->
      </div><!-- /.modal-dialog -->
    </div>

    <div id="failed_update_org" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="failed_update_org">
      <div class="modal-dialog modal-sm">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">Failed to Organization</h4>
          </div>
          <div class="modal-body">
            <h4 id="failed_update_org_message" name="failed_update_org_message"></h4>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Try Again</button>
          </div>
        </div><!-- /.modal-content -->
      </div><!-- /.modal-dialog -->
    </div>

    <div id="failed_update_personal_photo" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="failed_update_personal_photo">
      <div class="modal-dialog modal-sm">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">Failed to Update Page Photo</h4>
          </div>
          <div class="modal-body">
            <h4 id="failed_update_personal_photo_message" name="failed_update_personal_photo_message"></h4>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Try Again</button>
          </div>
        </div><!-- /.modal-content -->
      </div><!-- /.modal-dialog -->
    </div>

    <div id="failed_update_team_photo" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="failed_update_team_photo">
      <div class="modal-dialog modal-sm">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">Failed to Update Team Page Photo</h4>
          </div>
          <div class="modal-body">
            <h4 id="failed_update_team_photo_message" name="failed_update_team_photo_message"></h4>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Try Again</button>
          </div>
        </div><!-- /.modal-content -->
      </div><!-- /.modal-dialog -->
    </div>

    <div id="failed_update_org_photo" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="failed_update_org_photo">
      <div class="modal-dialog modal-sm">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">Failed to Update Organization Page Photo</h4>
          </div>
          <div class="modal-body">
            <h4 id="failed_update_org_photo_message" name="failed_update_org_photo_message"></h4>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Try Again</button>
          </div>
        </div><!-- /.modal-content -->
      </div><!-- /.modal-dialog -->
    </div>

    <div id="failed_update_profile_picture" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="failed_update_profile_picture">
      <div class="modal-dialog modal-sm">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">Failed to Update Organization Page Photo</h4>
          </div>
          <div class="modal-body">
            <h4 id="failed_update_profile_picture_message" name="failed_update_profile_picture_message"></h4>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Try Again</button>
          </div>
        </div><!-- /.modal-content -->
      </div><!-- /.modal-dialog -->
    </div>

    <div id="previous_coming_soon" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="previous_coming_soon">
      <div class="modal-dialog modal-sm">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">Previous Campiang Contributions (Coming soon)</h4>
          </div>
          <div class="modal-body">
            <h4 id="previous_coming_soon_message" name="previous_coming_soon_message">We're working hard to gather this data for you. Please check back soon.</h4>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          </div>
        </div><!-- /.modal-content -->
      </div><!-- /.modal-dialog -->
    </div>

    <!-- - - - -->
    <!-- MODALS -->
    <!-- - - -  -->

    <!-- GIMME A BREAK -->
    <br><br>
    <!-- GIMME A BREAK -->

    <!-- FOOTER -->
    <div id="footer"></div>
    <!-- FOOTER -->

    <script>
      function twitter(){
        window.open("https://twitter.com/intent/tweet?text=Put%20down%20your%20razor%20and%20join%20me%20in%20the%20fight%20against%20cancer!%20%20Help%20me%20reach%20my%20fundraising%20goal%20at%20https%3A%2F%2Fno-shave.org%2Fmember%2F<?php echo $m_username;?>%20%23LetItGrow&source=clicktotweet", "newwindow", "width=600, height=250");
        return false;
      }
      function twitter_team(){
        window.open("https://twitter.com/intent/tweet?text=Put%20down%20your%20razor%20and%20join%20us%20in%20the%20fight%20against%20cancer!%20%20Help%20us%20reach%20our%20fundraising%20goal%20at%20https%3A%2F%2Fno-shave.org%2Fteam%2F<?php echo $t_username;?>%20%23LetItGrow&source=clicktotweet", "newwindow", "width=600, height=250");
        return false;
      }
      function twitter_team_invite(){
        window.open("https://twitter.com/intent/tweet?text=You%E2%80%99re%20invited%20to%20join%20<?php echo $t_name; ?>%20this%20November%20in%20an%20effort%20to%20raise%20cancer%20awareness%20and%20funds!%20%20Join%20here%3A%20https%3A%2F%2Fno-shave.org%2Fregister%3Ft%3D<?php echo $t_username; ?>&source=clicktotweet", "newwindow", "width=600, height=250");
        return false;
      }     
      function twitter_org(){
        window.open("https://twitter.com/intent/tweet?text=Put%20down%20your%20razor%20and%20join%20us%20in%20the%20fight%20against%20cancer!%20%20Help%20us%20reach%20our%20fundraising%20goal%20at%20https%3A%2F%2Fno-shave.org%2Forg%2F<?php echo $o_username;?>%20%23LetItGrow&source=clicktotweet", "newwindow", "width=600, height=250");
        return false;
      }
      function facebook(){
        window.open("https://www.facebook.com/sharer/sharer.php?u=https://no-shave.org/member/<?php echo $m_username; ?>", "newwindow", "width=500, height=400");
        return false;
      }
      function facebook_team(){
        window.open("https://www.facebook.com/sharer/sharer.php?u=https://no-shave.org/team/<?php echo $t_username; ?>", "newwindow", "width=500, height=400");
        return false;
      }
      function facebook_team_invite(){
        window.open("https://www.facebook.com/sharer/sharer.php?u=https://no-shave.org/register?t=<?php echo $t_username; ?>", "newwindow", "width=500, height=400");
        return false;
      }
      function facebook_org(){
        window.open("https://www.facebook.com/sharer/sharer.php?u=https://no-shave.org/org/<?php echo $o_username; ?>", "newwindow", "width=500, height=400");
        return false;
      }
    </script>

    <script src="assets/js/bootstrap.min.js" type="text/javascript"></script>
    <script src="assets/js/loadingoverlay.js" type="text/javascript"></script>
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.6.0/css/font-awesome.min.css" rel="stylesheet">
    <?php include_once("analyticstracking.php") ?>
  </body>
</html>

<?php  else : ?>

<meta http-equiv="refresh" content="0;url=login?logged_out=true" />

<?php  endif; ?>
