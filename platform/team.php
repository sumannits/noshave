<?php
  // Load database connection and php functions
  include_once 'includes/db_connect.php';
  include_once 'includes/functions.php';
  // Start secure session
  sec_session_start();
?>
<?php

// team id in the get
$t_username = $_GET['p'];

// init vars
$member_table = "";
$total_team_raised = "";
$org_header = "";
$org_link = "";

// get team data
if ($stmt = $mysqli->prepare("SELECT t_id, t_name, t_username, t_org_id, t_pic_0, t_page_title, t_page_description, t_page_goal FROM team WHERE t_username = ? LIMIT 1")) {
  $stmt->bind_param('s', $t_username);
  $stmt->execute();
  $stmt->store_result();
  $stmt->bind_result($t_id, $t_name, $t_username, $t_org_id, $t_pic_0, $t_page_title, $t_page_description, $t_page_goal);
  $stmt->fetch();
  $stmt->close();

  if ($t_id == 0){
    header('Location: /404');
  }

} else {
  // give error 5590 - unable to get data from DB using user ID from session
  header('Location: /error?id=5590');
}

if ($t_id != 0) {
  // get team members
  if ($stmt = $mysqli->prepare("SELECT m_id, m_full_name, m_username, m_profile_pic FROM member WHERE m_team_id = ? AND m_2017 = 1")) {
    $stmt->bind_param('i', $t_id);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($m_id, $m_full_name, $m_username, $m_profile_pic);

    while ($stmt->fetch()) {

      // get donation total for each member
      if ($stmt_donation = $mysqli->prepare("SELECT sum(d_amount) FROM donation WHERE d_classifier_id = ? AND d_verified_payment = 1")) {
        $stmt_donation->bind_param('i', $m_id);
        $stmt_donation->execute();
        $stmt_donation->store_result();
        $stmt_donation->bind_result($total_raised_member);
        $stmt_donation->fetch();
        $stmt_donation->close();

      } else {
        // unable to grab total
        $total_raised_member = 0;
      }

      $member_table .= '
                        <tr>
                          <td class="col-md-1">
                            <img class="img-rounded" height="42" width="42" src="' . $m_profile_pic . '">
                          </td>
                          <td class="vert-align col-md-8">
                            <h4><a href="/member/' . $m_username . '">' . $m_full_name . '</a></h4>
                          </td>
                          <td class="vert-align col-md-4">
                            <h4 class="donation-green">$' . number_format($total_raised_member) . '</h4>
                          </td>
                        </tr>
                        ';

      $total_team_raised += $total_raised_member;

    }

    // done
    $stmt->close();

    if ($member_table == ""){
      // no members
      $member_table .= '
                        <tr>
                          <td colspan="3" class="vert-align">
                            <h4 class="centered">There are no members, yet :)</h4>
                          </td>
                        </tr>
                        ';

    }

  } else {
    // give error 5590 - unable to get data from DB using user ID from session
    header('Location: /error?id=5590');
  }


  // get team donation
  if ($stmt = $mysqli->prepare("SELECT d_name, d_email, d_amount, d_message, d_anonymous, d_message_on_page, d_visible_on_page FROM donation WHERE d_classifier = 2 AND d_classifier_id = ? AND d_verified_payment = 1")) {
    $stmt->bind_param('i', $t_id);
    $stmt->execute();
    $stmt->bind_result($d_name, $d_email,$d_amount, $d_message, $d_anonymous, $d_message_on_page, $d_visible_on_page);

    // donation table
    $donation_table = "";

    while ($stmt->fetch()) {

      if ($d_anonymous == 1) {
        $d_name = "Anonymous";
      }

      if ($d_message_on_page == 0){
        $d_message = "";
      }

      // add to total
      $total_team_raised += $d_amount;

      if ($d_visible_on_page == 1){

        $donation_table .= '
                              <tr>
                                <td class="vert-align">
                                  <h2 class="donation-green">$'. number_format($d_amount) .'</h2>
                                  <h4>'. $d_name .'</h4>
                                </td>
                                <td class="vert-align">
                                  <p>'. $d_message .'</p>
                                </td>
                              </tr>
                            ';

      } else {
        // no!
      }
    }

    $stmt->close();

    if ($donation_table == "") {

      $donation_table = '
                      <tr>
                        <td colspan="2" class="vert-align centered">
                          <br>
                          <h4>Be the first to donate to ' . $t_name . '!</h4>
                          <p><a href="/donate?id=' . $t_id . '&amp;c=2" class="btn btn-success"><i class="fa fa-heart" aria-hidden="true"></i>&nbsp; Donate</a>
                        </td>
                      </tr>
                    ';

    }

  } else {
    // give error 5590 - unable to get data from DB using user ID from session
    header('Location: /error?id=5590');
  }

  // GET ORG
  if ($t_org_id != 0){

    // get the org data
    if ($stmt_donation = $mysqli->prepare("SELECT o_name, o_username, o_pic_0 FROM org WHERE o_id = ?")) {
      $stmt_donation->bind_param('i', $t_org_id);
      $stmt_donation->execute();
      $stmt_donation->store_result();
      $stmt_donation->bind_result($o_name, $o_username, $o_pic_0);
      $stmt_donation->fetch();
      $stmt_donation->close();

    } else {
      // unable to grab total
      $total_raised_member = 0;
    }


    // org header
    $org_header = '<small><i class="fa fa-sitemap" aria-hidden="true"></i>&nbsp; ' . $o_name . '</small>';


    // set team page info
    $org_link = '
                  <div class="page-header">
                    <h4><i class="fa fa-users" aria-hidden="true"></i>&nbsp; Organization</h4>
                  </div>
                  <div class="list-group">
                    <a href="/org/' . $o_username . '" class="list-group-item centered">' . $o_name . '<br><br><img class="img-rounded fill-section" src="' . $o_pic_0 . '" alt="' . $o_name . '"></a>
                  </div>
                  ';

  } else {
    // no org

  }

  // calculate goal
  $total_raised_percent = ceil(($total_team_raised / $t_page_goal) * 100) . "%";

  if ($total_raised_percent > 100) {
    $total_raised_percent = "100%+";
  }

  $total_raised_width = ($total_team_raised / $t_page_goal) * 100;

  if ($total_raised_width < 20){
    $total_raised_width = 20;
  } else if ($total_raised_width >= 100){
    $reached_goal = 1;
    $total_raised_width = 100;
  } else {
    // nada
  }
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
    <meta name="description" content="<?php echo $t_name; ?> | No-Shave November Fundraising Page">
    <title><?php echo $t_name; ?> | No-Shave November</title>
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
    <script>
    $(function () {
      $('[data-toggle="tooltip"]').tooltip()
    })
    </script>
    <style>
    .carousel {
    border-radius: 10px 10px 10px 10px;
      overflow: hidden;
    }
    .carousel-control.left, .carousel-control.right {
       background-image:none !important;
       filter:none !important;
    }
    .fill-section {
      width: 100%;
      height: 100%;
    }
    .fa-sitemap {
      color: #999;
    }
    .fa-sitemap.double-icon {
      color: #000;
    }
    .fa-users {
      color: #000;
    }
    .fa-flag-checkered {
      color: #000;
    }
    .fa-comment {
      color: #000;
    }
    .table tbody>tr>td.vert-align{
        vertical-align: middle;
    }
    .fa-usd {
      color: #000;
    }
    .fa-share {
      color: #fff;
    }
    .fa-heart {
      color: #fff;
    }
    .fa-trophy {
      color: #000;
    }
    .fa-user-md {
      color: #428bca;
    }
    .fa-star {
      color: #FFD700;
    }
    .fa-flag {
      color: #5cb85c;
    }
    .donation-green {
      color: #5cb85c;
    }
    .fa-user-plus {
      color: #fff;
    }
    .fa-user {
      color: #000;
    }
    .fa-users {
      color: #000;
    }
    .black-link {
      color: #000;
    }
    #page_description {
      white-space: pre-line;
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
    .form-control.text-center {
      width: 100%;
    }
    </style>
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
            <h1><?php echo $t_name; ?>&nbsp;&nbsp; <?php echo $org_header; ?></h1>
          </div>

          <div class="col-sm-9">

            <img style="width: 100%; height: 100%;" src="<?php echo $t_pic_0; ?>" alt="<?php echo $t_name; ?> No Shave November 2017" class="img-rounded">

            <h2><strong><?php echo $t_page_title; ?></strong></h2>
            <p id="page_description"><?php echo $t_page_description; ?><br></p>

              <!-- Nav tabs -->
              <ul class="nav nav-tabs" role="tablist">
                <li role="presentation" class="active visible-lg visible-md visible-sm hidden-xs"><a class="black-link" href="#users" aria-controls="users" role="tab" data-toggle="tab"><i class="fa fa-users" aria-hidden="true"></i>&nbsp; Team Members</a></li>
                <li role="presentation" class="visible-lg visible-md visible-sm hidden-xs"><a class="black-link" href="#donations" aria-controls="donations" role="tab" data-toggle="tab"><i class="fa fa-usd" aria-hidden="true"></i>&nbsp; Team Donations</a></li>

                <li role="presentation" class="active hidden-lg hidden-md hidden-sm visible-xs"><a class="black-link" href="#users" aria-controls="users" role="tab" data-toggle="tab"><i class="fa fa-users" aria-hidden="true"></i>&nbsp; Members</a></li>
                <li role="presentation" class="hidden-lg hidden-md hidden-sm visible-xs"><a class="black-link" href="#donations" aria-controls="donations" role="tab" data-toggle="tab"><i class="fa fa-usd" aria-hidden="true"></i>&nbsp; Donations</a></li>
              </ul>

              <!-- Tab panes -->
              <div class="tab-content">

                <div role="tabpanel" class="tab-pane fade in active" id="users">
                  <br>
                  <table class="table table-hover">
                    <thead>
                      <tr>
                        <th class="col-md-8" colspan="2"><h4><i class="fa fa-user" aria-hidden="true"></i>&nbsp; Member</h4></th>
                        <th class="col-md-4"><h4>Raised</h4></th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php echo $member_table ; ?>
                    </tbody>
                  </table>
                </div>
              

                <div role="tabpanel" class="tab-pane fade in" id="donations">
                  <br>
                  <table class="table table-hover">
                    <thead>
                      <tr>
                        <th class="col-md-4"><h4><i class="fa fa-usd" aria-hidden="true"></i>&nbsp; Donation</h4></th>
                        <th class="col-md-8"><h4><i class="fa fa-comment" aria-hidden="true"></i>&nbsp; Comment</h4></th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                        echo $donation_table;
                      ?>
                    </tbody>
                  </table>
                </div>

              </div> <!-- end tab content-->

            </div>

          <div class="col-sm-3">
            <center><a href="/"><img class="img-responsive" src="/img/nsn_full_stacked.png" alt="No-Shave November"></a></center>
            <p><a href="/donate?id=<?php echo $t_id; ?>&amp;c=2" class="btn btn-success btn-block"><i class="fa fa-heart" aria-hidden="true"></i>&nbsp; Make a Donation</a>

            <?php  if (login_check($mysqli) == true) : ?>
            <a href="/dashboard#team_page" class="btn btn-info btn-block"><i class="fa fa-user-plus" aria-hidden="true"></i>&nbsp; Join Team</a>
            <?php  else : ?>
            <a href="/register?t=<?php echo $t_username; ?>" class="btn btn-info btn-block"><i class="fa fa-user-plus" aria-hidden="true"></i>&nbsp; Join Team</a>
            <?php  endif; ?>

            <a href="" class="btn btn-primary btn-block" data-toggle="modal" data-target="#shareTeam"><i class="fa fa-share" aria-hidden="true"></i>&nbsp; Share this Page</a></p>
            <p>No-Shave November is a web-based, non-profit organization devoted to growing cancer awareness and raising funds to support cancer prevention, research, and education.</p>
            <div class="page-header">
              <h4><i class="fa fa-flag-checkered" aria-hidden="true"></i>&nbsp; Fundraising Goal</h4>
            </div>
            <h1><strong>$<?php echo number_format($total_team_raised); ?></strong><small> of $<?php echo number_format($t_page_goal); ?></small></h1>
            <div class="progress">
              <div class="progress-bar" role="progressbar" aria-valuenow="<?php echo $total_raised_percent; ?>" aria-valuemin="0" aria-valuemax="100" style="width:<?php echo $total_raised_width; ?>%;">
                <?php echo $total_raised_percent; ?>
              </div>
            </div>

            <?php echo $org_link; ?>

        </div>
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

    <!-- MODALS -->

    <!-- SHARE -->
    <div class="modal fade" tabindex="-1" role="dialog" id="shareTeam">
      <div class="modal-dialog" role="document">
        <div class="modal-content">

            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
              <h4 class="modal-title">Share This Page</h4>
            </div>

            <div class="modal-body centered">
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

    <!-- MODALS -->
    <script>
      function twitter(){
        window.open("https://twitter.com/intent/tweet?text=Join%20<?php echo $t_name; ?>%20in%20the%20fight%20against%20cancer!%20https%3A%2F%2Fno-shave.org%2Fteam%2F<?php echo $t_username; ?>%20%23LetItGrow&source=clicktotweet", "newwindow", "width=600, height=250");
        return false;
      }
      function facebook(){
        window.open("https://www.facebook.com/sharer/sharer.php?u=https://no-shave.org/team/<?php echo $t_username; ?>", "newwindow", "width=500, height=400");
        return false;
      }
    </script>

    <script src="/assets/js/bootstrap.min.js" type="text/javascript"></script>
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.6.0/css/font-awesome.min.css" rel="stylesheet">
    <?php include_once("analyticstracking.php") ?>
  </body>
</html>
