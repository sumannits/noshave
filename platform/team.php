<?php
  // Load database connection and php functions
  //include_once 'includes/db_connect.php';
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

      $member_table .= '<tr>
      <td colspan="2" style="text-align: left;"><img src="' . $m_profile_pic . '" alt=""><span><a href="'.base_url.'/member/' . $m_username . '">' . $m_full_name . '</a></span></td>
      <td><b>$'.number_format($total_raised_member).'</b></td>
  </tr>';
      $total_team_raised += $total_raised_member;

    }

    // done
    $stmt->close();

    if ($member_table == ""){
      // no members
      $member_table .= '
                        <tr>
                          <td colspan="3">
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

        $donation_table .= '<tr>
        <td>
          <h2 class="">$'.number_format($d_amount).'</h2>
          <h3>'. $d_name .'</h3>
        </td>
        <td><p>'. $d_message .'</p></td>
      </tr>';

      } else {
        // no!
      }
    }

    $stmt->close();

    if ($donation_table == "") {

      $donation_table = '<tr>
      <td colspan="2">
        <h2 class=""> Be the first to donate to ' . $t_name . '! </h2>
        <p><a href="'.base_url.'/donate?id=' . $t_id . '&amp;c=2" class="btn btn-success"><i class="fa fa-heart" aria-hidden="true"></i>&nbsp; Donate</a></p>
      </td>
    </tr>';

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
    $org_link = '<div class="page-header">
    <h4><i class="fa fa-users" aria-hidden="true"></i>&nbsp; Organization</h4>
</div>
<div class="list-group">
    <h3><a href="'.base_url.'/org/' . $o_username . '" class="text-center text-dark d-block">' . $o_name . '<br><br><img class="img-rounded fill-section img-fluid" src="' . $o_pic_0 . '" alt="' . $o_name . '"></a>
    </h3>
</div>';

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
    <link rel="icon" type="image/png" href="<?php echo base_url; ?>/img/favicon.png">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" />
    <meta name="author" content="No-Shave November">
    <meta name="description" content="<?php echo $t_name; ?> | No-Shave November Fundraising Page">
    <title><?php echo $t_name; ?> | No-Shave November</title>
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
    
    <script src="<?php echo base_url; ?>/assets/js/bootstrap.min.js" type="text/javascript"></script>
    <script src="<?php echo base_url; ?>/assets/js/loadingoverlay.js" type="text/javascript"></script>
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
    .modal-body h4{
        color: #333;
        font-weight: 400;
        font-size: 20px;
      }
    </style>
  </head>
  <body>
  <header>
    <?php include_once('menu.php'); ?>
  </header>
    
  <section class="blog-page py-3 py-md-5">
          <div class="container">
              <div class="row">
                  <div class="col-lg-3 col-md-4 col-12 white-bg shadow">
                      <div class="b-sidebar py-4">
                          <a href="<?php echo base_url; ?>" class="side-logo d-block text-center"><img src="<?php echo base_url;?>/assets/images/sidebar-logo.png" class="img-fluid" alt="" style="max-width:150px"></a>
                          <p>
                              <a href="<?php echo base_url; ?>/donate?id=<?php echo $t_id; ?>&amp;c=2" class="btn btn-success btn-block btn-sm"><i class="fa fa-heart" aria-hidden="true"></i>&nbsp; Make a Donation</a>
                              <?php  if (login_check($mysqli) == true) : ?>
                              <a href="<?php echo base_url; ?>/dashboard#team_page" class="btn btn-info btn-block btn-sm"><i class="fa fa-user-plus" aria-hidden="true"></i>&nbsp; Join Team</a>
                              <?php  else : ?>
                              <a href="<?php echo base_url; ?>/register?t=<?php echo $t_username; ?>" class="btn btn-info btn-block btn-sm"><i class="fa fa-user-plus" aria-hidden="true"></i>&nbsp; Join Team</a>
                              <?php  endif; ?>

                              <a href="Javascript:void(0);" class="btn btn-primary btn-block btn-sm" data-toggle="modal" data-target="#shareTeam"><i class="fa fa-share" aria-hidden="true"></i>&nbsp; Share this Page</a>
                          </p>
                          <p>No-Shave November is a web-based, non-profit organization devoted to growing cancer awareness and raising funds to support cancer prevention, research, and education.</p>
                     
                            <div class="page-header">
                                <h4><i class="fa fa-flag-checkered" aria-hidden="true"></i>&nbsp; Fundraising Goal</h4>
                            </div>
                          <h3><strong>$<?php echo number_format($total_team_raised); ?></strong><small> of $<?php echo number_format($t_page_goal); ?></small></h3>
                          <div class="progress">
                            <div class="progress-bar" role="progressbar" aria-valuenow="<?php echo $total_raised_percent; ?>" aria-valuemin="0" aria-valuemax="100" style="width:<?php echo $total_raised_width; ?>%;">
                            <?php echo $total_raised_percent; ?></div>
                          </div>
                            
                          <?php echo $org_link; ?>
                      </div>
                  </div>
                  <div class="col-lg-9 col-md-8 col-12">
                      <div class="right-board b-log">
                          <h2> <?php echo $t_name; ?>&nbsp;&nbsp; <?php echo $org_header; ?> </h2>
                          <figure class="">
                              <img src="<?php echo $t_pic_0; ?>" class="img-fluid" alt="<?php echo $t_name; ?> No Shave November 2017">
                          </figure>
                          <div class="inner-txt p-4">
                              <h2> <?php echo $t_page_title; ?> </h2>
                              <p><?php echo $t_page_description; ?></p>
                              
                          </div>

                <ul class="nav table-tab mb-5" id="myTab" role="tablist">
                    <li>
                      <a class="active" id="home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="home" aria-selected="true">Team Member</a>
                    </li>
                    <li>
                      <a class="" id="profile-tab" data-toggle="tab" href="#profile" role="tab" aria-controls="profile" aria-selected="false">Team Donations</a>
                    </li>
                </ul>
                <div class="tab-content" id="myTabContent">
                    <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th colspan="2" scope="col" class="pl-5">Member</th>
                                    <th scope="col" class="pl-5">Raised</th>  
                                </tr>
                            </thead>
                            <tbody>
                                <?php echo $member_table ; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                          <table class="table table-striped">
                              <thead>
                                  <tr>
                                    <th><h4><i class="fa fa-usd" aria-hidden="true"></i>&nbsp; Donation</h4></th>
                                    <th><h4><i class="fa fa-comment" aria-hidden="true"></i>&nbsp; Comment</h4></th>
                                  </tr>
                              </thead>
                              <tbody>
                                <?php echo $donation_table; ?>
                              </tbody>
                          </table>
                    </div>
                </div>
                      </div>
                  </div>
              </div>
            </div>
      </section>

    <!-- MODALS -->

    <!-- SHARE -->
    <div class="modal fade" tabindex="-1" role="dialog" id="shareTeam">
      <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">

            <div class="modal-header">
              <h4 class="modal-title">Share This Page</h4>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
              
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
                  <input type="text" class="form-control text-center" placeholder="<?php echo base_url; ?>/team/<?php echo $t_username; ?>" value="<?php echo base_url; ?>/team/<?php echo $t_username; ?>" onClick="this.select();">
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
        window.open("https://www.facebook.com/sharer/sharer.php?u=<?php echo base_url; ?>/team/<?php echo $t_username; ?>", "newwindow", "width=500, height=400");
        return false;
      }
    </script>
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.6.0/css/font-awesome.min.css" rel="stylesheet">
    <?php include_once('footer.php')?>
    <?php include_once("analyticstracking.php") ?>
  </body>
</html>
