<?php
  // Load database connection and php functions
  //include_once 'includes/db_connect.php';
  include_once 'includes/functions.php';
  // Start secure session
  sec_session_start();
?>

<?php
$username = $_GET['p'];

// get this out of the way
$reached_goal = "";
$donate_trohpy = "";


// get user data
if ($stmt = $mysqli->prepare("SELECT m_id, m_full_name, m_email, m_username, m_team_id, m_city, m_state, m_country, m_display_location, m_location_format, m_pic_0, m_page_title, m_page_description, m_page_goal, m_got_screen, m_2017 FROM member WHERE m_username = ? LIMIT 1")) {
  $stmt->bind_param('s', $username);
  $stmt->execute();
  $stmt->store_result();
  $stmt->bind_result($m_id, $m_full_name, $m_email, $m_username, $m_team_id, $m_city, $m_state, $m_country, $m_display_location, $m_location_format, $m_pic_0, $m_page_title, $m_page_description, $m_page_goal, $m_got_screen, $m_2017);
  $stmt->fetch();
  $stmt->close();

  if ($m_id == 0 || $m_2017 == 0){
    header('Location: /404');
  }

} else {
  // give error 5590 - unable to get data from DB using user ID from session
  header('Location: /error?id=5590');
}

if ($m_id != 0 && $m_2017 != 0) {

  // get donations
  if ($stmt = $mysqli->prepare("SELECT d_name, d_email, d_amount, d_message, d_anonymous, d_message_on_page, d_visible_on_page FROM donation WHERE d_classifier = 1 AND d_classifier_id = ? AND d_verified_payment = 1")) {
    $stmt->bind_param('s', $m_id);
    $stmt->execute();
    $stmt->bind_result($d_name, $d_email,$d_amount, $d_message, $d_anonymous, $d_message_on_page, $d_visible_on_page);

    // donation table
    $donation_table = "";
    $total_raised = 0;

    while ($stmt->fetch()) {

      if ($d_email == $m_email) {
        $donate_trohpy = 1;
      }

      if ($d_anonymous == 1) {
        $d_name = "Anonymous";
      }

      if ($d_message_on_page == 0){
        $d_message = "";
      }

      // add to total
      $total_raised += $d_amount;

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
        <h2 class=""> Be the first to donate to ' . $m_full_name . '! </h2>
        <p><a href="'.base_url.'/donate?id=' . $m_id . '&amp;c=1" class="btn btn-success"><i class="fa fa-heart" aria-hidden="true"></i>&nbsp; Donate</a></p>
      </td>
    </tr>';

    }

  } else {
    // give error 5590 - unable to get data from DB using user ID from session
    header('Location: /error?id=5590');
  }


  // get team
  if ($m_team_id != 0){
    if ($stmt = $mysqli->prepare("SELECT t_name, t_username, t_pic_0 FROM team WHERE t_id = ? LIMIT 1")) {
      $stmt->bind_param('i', $m_team_id);
      $stmt->execute();
      $stmt->store_result();
      $stmt->bind_result($t_name, $t_username, $t_pic_0);
      $stmt->fetch();
      $stmt->close();

    } else {
      // give error 5590 - unable to get data from DB using user ID from session
      header('Location: /error?id=23490');
    }

  }

  // calculate goal
  $total_raised_percent = ceil(($total_raised / $m_page_goal) * 100) . "%";

  if ($total_raised_percent > 100) {
    $total_raised_percent = "100%+";
  }

  $total_raised_width = ($total_raised / $m_page_goal) * 100;

  if ($total_raised_width < 20) {
    $total_raised_width = 20;
  } else if ($total_raised_width >= 100){
    $reached_goal = 1;
    $total_raised_width = 100;
  } else {
    // nada
  }

  if ($m_display_location == 1){

    if ($m_location_format == 0) {

      $personal_location = '<i class="fa fa-map-marker" aria-hidden="true"></i>&nbsp; ' . $m_city . ", " . $m_state;

    } elseif ($m_location_format == 1) {

      $personal_location = '<i class="fa fa-map-marker" aria-hidden="true"></i>&nbsp; ' . $m_city . ", " . $m_country;

    } elseif ($m_location_format == 2) {

      $personal_location = '<i class="fa fa-map-marker" aria-hidden="true"></i>&nbsp; ' . $m_city;

    } elseif ($m_location_format == 3) {

      $personal_location = '<i class="fa fa-map-marker" aria-hidden="true"></i>&nbsp; ' . $m_country;

    } else {

      $personal_location = '<i class="fa fa-map-marker" aria-hidden="true"></i>&nbsp;  ' . $m_country;

    }

  } else {

    $personal_location = "";
    
  }

  // TORPHIES
  $achievements = "";

  // did they donate?
  if ($donate_trohpy == 1){
    $achievements .= '<li>
                          <a class="text-warning">
                              <i class="fa fa-star fa-3x" aria-hidden="true" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="Made a Donation"></i>
                          </a>
                      </li>';
  }

  // did they get screened?
  if ($m_got_screen == 1){
    $achievements .= '<li>
    <a class="text-warning">
        <i class="fa fa-user-md fa-3x" aria-hidden="true" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="Got Screened"></i>
    </a>
</li>';
  }

  // did they reach their goal?

  if ($reached_goal == 1){
    $achievements .= '<li>
    <a class="text-warning">
        <i class="fa fa-flag fa-3x" aria-hidden="true" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="Reached Fundraising Goal"></i>
    </a>
</li>';
  }

  if (!$donate_trohpy && ! $m_got_screen && !$reached_goal){
    $achievements .= '<li>
    <a class="text-warning">
        <i class="fa fa-smile-o fa-3x" aria-hidden="true" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="No trophies yet :)"></i>
    </a>
</li>';
  }

  // GET TEAM

  if ($m_team_id != 0){

    // set team page info
    $team_link = '<div class="page-header">
    <h4><i class="fa fa-users" aria-hidden="true"></i>&nbsp; Team</h4>
</div>
<div class="list-group">
    <h3><a href="'.base_url.'/team/' . $t_username . '" class="text-center text-dark d-block">' . $t_name . '<br><br><img class="img-rounded fill-section img-fluid" src="' . $t_pic_0 . '" alt="' . $t_name . '"></a>
    </h3>
</div>';

  } else {
    // no team
    $team_link = '';

  }
} else {
  
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
    <meta name="description" content="<?php echo $m_full_name;?> | No-Shave November Fundraising Page">
    <title><?php echo $m_full_name; ?> | No-Shave November</title>
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
    .fa-map-marker {
      color: #999;
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
    .fa-smile-o {
      color: #FFD700;
    }
    .fa-flag {
      color: #5cb85c;
    }
    .donation-green {
      color: #5cb85c;
    }
    .carousel-inner img {
      margin: auto;
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
                              <a href="<?php echo base_url; ?>/donate?id=<?php echo $m_id; ?>&amp;c=1" class="btn btn-primary btn-block btn-sm"><i class="fa fa-heart" aria-hidden="true"></i>&nbsp; Make a Donation</a>
                              <a href="Javascript:void(0);" class="btn btn-success btn-block btn-sm" data-toggle="modal" data-target="#sharePersonal"><i class="fa fa-share" aria-hidden="true"></i>&nbsp; Share this Page</a>
                          </p>
                          <p>No-Shave November is a web-based, non-profit organization devoted to growing cancer awareness and raising funds to support cancer prevention, research, and education.</p>
                     
                            <div class="page-header">
                                <h4><i class="fa fa-flag-checkered" aria-hidden="true"></i>&nbsp; Fundraising Goal</h4>
                            </div>
                          <h3><strong>$<?php echo number_format($total_raised); ?></strong><small> of $<?php echo number_format($m_page_goal); ?></small></h3>
                          <div class="progress">
                            <div class="progress-bar" role="progressbar" aria-valuenow="<?php echo $total_raised_percent; ?>" aria-valuemin="0" aria-valuemax="100" style="width:<?php echo $total_raised_width; ?>%;">
                            <?php echo $total_raised_percent; ?></div>
                          </div>
                            <div class="page-header">
                                <h4><i class="fa fa-trophy" aria-hidden="true"></i>&nbsp; Achievements</h4>
                            </div>
                          <ul class="list-inline text-center">
                            <?php echo $achievements; ?>
                          </ul>
                          <?php echo $team_link; ?>
                      </div>
                  </div>
                  <div class="col-lg-9 col-md-8 col-12">
                      <div class="right-board b-log">
                          <h2><?php echo $m_full_name; ?> <small><?php echo $personal_location; ?></small> </h2>
                          <figure class="">
                              <img src="<?php echo $m_pic_0;?>" class="img-fluid" alt="<?php echo $m_full_name; ?> No Shave November 2017">
                          </figure>
                          <div class="inner-txt p-4">
                              <h2> <?php echo $m_page_title; ?> </h2>
                              <p><?php echo $m_page_description; ?></p>
                              
                          </div>
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
      </section>
    <!-- MODALS -->

    <!-- SHARE -->
    <div class="modal fade" tabindex="-1" role="dialog" id="sharePersonal">
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
                  <input type="text" class="form-control text-center" placeholder="<?php echo base_url;?>/member/<?php echo $m_username; ?>" value="<?php echo base_url;?>/member/<?php echo $m_username; ?>" onClick="this.select();">
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
        window.open("https://twitter.com/intent/tweet?text=Join%20<?php echo $m_full_name; ?>%20in%20the%20fight%20against%20cancer!%20https%3A%2F%2Fno-shave.org%2Fmember%2F<?php echo $m_username; ?>%20%23LetItGrow&source=clicktotweet", "newwindow", "width=600, height=250");
        return false;
      }
      function facebook(){
        window.open("https://www.facebook.com/sharer/sharer.php?u=<?php echo base_url;?>/member/<?php echo $m_username; ?>", "newwindow", "width=500, height=400");
        return false;
      }
    </script>

    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.6.0/css/font-awesome.min.css" rel="stylesheet">
    <?php include_once('footer.php')?>
    <?php include_once("analyticstracking.php") ?>
  </body>
</html>
