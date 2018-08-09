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
                          <h4>Be the first to donate to ' . $m_full_name . '!</h4>
                          <p><a href="/donate?id=' . $m_id . '&amp;c=1" class="btn btn-success"><i class="fa fa-heart" aria-hidden="true"></i>&nbsp; Donate</a>
                        </td>
                      </tr>
                    ';

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
    $achievements .= '
                      <div class="col-sm-3">
                        <i class="fa fa-star fa-3x" aria-hidden="true" data-toggle="tooltip" data-placement="bottom" title="Made a Donation"></i>
                      </div>
                      ';
  }

  // did they get screened?
  if ($m_got_screen == 1){
    $achievements .= '
                      <div class="col-sm-3">
                        <i class="fa fa-user-md fa-3x" aria-hidden="true" data-toggle="tooltip" data-placement="bottom" title="Got Screened"></i>
                      </div>
                      ';
  }

  // did they reach their goal?

  if ($reached_goal == 1){
    $achievements .= '
                      <div class="col-sm-3">
                        <i class="fa fa-flag fa-3x" aria-hidden="true" data-toggle="tooltip" data-placement="bottom" title="Reached Fundraising Goal"></i>
                      </div>
                      ';
  }

  if (!$donate_trohpy && ! $m_got_screen && !$reached_goal){
    $achievements .= '
                      <div class="col-sm-3">
                        <i class="fa fa-smile-o fa-3x" aria-hidden="true" data-toggle="tooltip" data-placement="bottom" title="No trophies yet :)"></i>
                      </div>
                      ';
  }

  // GET TEAM

  if ($m_team_id != 0){

    // set team page info
    $team_link = '
                  <div class="page-header">
                    <h4><i class="fa fa-users" aria-hidden="true"></i>&nbsp; Team</h4>
                  </div>
                  <div class="list-group">
                    <a href="/team/' . $t_username . '" class="list-group-item centered">' . $t_name . '<br><br><img class="img-rounded fill-section" src="' . $t_pic_0 . '" alt="' . $t_name . '"></a>
                  </div>
                  ';

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
    <link rel="icon" type="image/png" href="/favicon.png">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" />
    <meta name="author" content="No-Shave November">
    <meta name="description" content="<?php echo $m_full_name;?> | No-Shave November Fundraising Page">
    <title><?php echo $m_full_name; ?> | No-Shave November</title>
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
            <h1><?php echo $m_full_name; ?>&nbsp;&nbsp; <small><?php echo $personal_location; ?></small></h1>
          </div>

          <div class="col-sm-9">
            <img style="width: 100%; height: 100%;" src="<?php echo $m_pic_0;?>" alt="<?php echo $m_full_name; ?> No Shave November 2017" class="img-rounded">


            <h2><strong><?php echo $m_page_title; ?></strong></h2>
            <p id="page_description"><?php echo $m_page_description; ?></p>

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

          <div class="col-sm-3">
            <center><a href="/"><img class="img-responsive" src="/img/nsn_full_stacked.png" alt="No-Shave November"></a></center>
            <p><a href="/donate?id=<?php echo $m_id; ?>&amp;c=1" class="btn btn-success btn-block"><i class="fa fa-heart" aria-hidden="true"></i>&nbsp; Make a Donation</a>
            <a href="" class="btn btn-primary btn-block" data-toggle="modal" data-target="#sharePersonal"><i class="fa fa-share" aria-hidden="true"></i>&nbsp; Share this Page</a></p>
            <p>No-Shave November is a web-based, non-profit organization devoted to growing cancer awareness and raising funds to support cancer prevention, research, and education.</p>
            <div class="page-header">
              <h4><i class="fa fa-flag-checkered" aria-hidden="true"></i>&nbsp; Fundraising Goal</h4>
            </div>
            <h1><strong>$<?php echo number_format($total_raised); ?></strong><small> of $<?php echo number_format($m_page_goal); ?></small></h1>
            <div class="progress">
              <div class="progress-bar" role="progressbar" aria-valuenow="<?php echo $total_raised_percent; ?>" aria-valuemin="0" aria-valuemax="100" style="width:<?php echo $total_raised_width; ?>%;">
                <?php echo $total_raised_percent; ?>
              </div>
            </div>

            <div class="page-header">
              <h4><i class="fa fa-trophy" aria-hidden="true"></i>&nbsp; Achievements</h4>
            </div>

            <div class="row">
              <?php echo $achievements; ?>
            </div>

            <?php echo $team_link; ?>

        </div> <!-- col md 12 -->
      </div> <!-- row -->
    </div> <!-- container -->

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
    <div class="modal fade" tabindex="-1" role="dialog" id="sharePersonal">
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

    <script src="/assets/js/bootstrap.min.js" type="text/javascript"></script>
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.6.0/css/font-awesome.min.css" rel="stylesheet">
    <?php include_once("analyticstracking.php") ?>
  </body>
</html>
