<?php
  // Load database connection and php functions
  //include_once 'includes/psl-config.php';
  //include_once 'includes/db_connect.php';
  include_once 'includes/functions.php';
  // Start secure session
  sec_session_start();
?>

<?php
$member_count = 0;
$member_table1 = '';
//SELECT count(o_id) FROM org;
if ($stmt = $mysqli->prepare("SELECT total_raised, total_members, total_teams, total_orgs, top_members, top_teams, top_orgs FROM leaderboard")) {
  $stmt->execute();
  //print_r($stmt);
  $stmt->store_result();
  $stmt->bind_result($total_raised, $total_members, $total_teams, $total_orgs, $member_table, $team_table, $org_table);
  $stmt->fetch();
  $stmt->close();

} else {
  // unable to get data
  $total_raised = 0;
  $total_members = 0;
  $total_teams = 0;
  $total_orgs = 0;
  $member_table = "Something's not right.";
  $team_table = "Something's not right.";
  $org_table = "Something's not right.";
}

if ($stmt = $mysqli->prepare("SELECT m_username, m_full_name, m_profile_pic, sum(d_amount) FROM donation, member WHERE m_id = d_classifier_id GROUP BY d_classifier_id ORDER BY sum(d_amount) DESC LIMIT 10")) {
  $stmt->execute();
  $stmt->store_result();
  $stmt->bind_result($m_username, $m_full_name, $m_profile_pic, $m_total_raised);

  while ($stmt->fetch()) {

    $member_count += 1;

                $member_table1 .= '
                    <tr>
                      <td>' . $member_count . '</td>
                      <td>
                        <img src="' . $m_profile_pic . '" alt=""> <span> <a href="/member/' . $m_username . '">' . $m_full_name . '</a> </span>
                      </td>
                      <td>
                      <b>$' . number_format($m_total_raised) . '</b>
                      </td>
                    </tr>
                  ';                

  }

} else {
  // unable to get data
  $member_table1 = "Something went wrong.";
}
//print_r($member_table);
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <link rel="icon" type="image/png" href="<?php echo base_url; ?>/img/favicon.png">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" />
    <meta name="author" content="No-Shave November">
    <meta name="description" content="Check out who's leading this year's No-Shave November fundraising efforts! Looking for someoneone specific? Search and find them!">
    <title>Leaderboard | No-Shave November</title>
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
    
    <script>
    $(function () {
      $('[data-toggle="tooltip"]').tooltip()
    })
    </script>
    <script>

      function search1() {
        // grab field
        var args = {
          q: $('#q').val()
        }

        // make call
        $.ajax({ 
          type: 'POST',
          url: './platform/api/search.php', 
          data: args, 
          dataType: 'json',
          success: function (data) { 
            if (data['status'] == "success") {

              // open search modal
              $('#searchModal').modal('toggle');

              // carry over var
              $('#q2').val($('#q').val());

              // show the results
              document.getElementById("search_results").innerHTML = data['results'];

            } else {

              // we don't know what happened
              $('#failed_search_message').text(data['reason']);
              $('#failed_search').modal(); // this should show the modal

            }
          }
        });
      }

      function search_q2() {

        // grab field
        var args = {
          q: $('#q2').val()
        }

        // make call
        $.ajax({ 
          type: 'POST',
          url: './platform/api/search.php', 
          data: args, 
          dataType: 'json',
          success: function (data) { 
            if (data['status'] == "success") {

              // open search modal
              //$('#searchModal').modal('toggle');

              // show the results
              document.getElementById("search_results").innerHTML = data['results'];

            } else {

              // we don't know what happened
              $('#failed_search_message').text(data['reason']);
              $('#failed_search').modal(); // this should show the modal

            }
          }
        });
      }

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
      color: #555;
    }
    .fa-user {
      color: #555;
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
    .fa-sitemap {
      color: #555;
    }
    .black-link {
      color: #000;
    }
    .fa-search {
      color: #555;
    }
    .modal-body h4{
      color: #333;
      font-weight: 400;
      font-size: 20px;
    }
    .hidden{display: none;}
    </style>
  </head>
  <body>
      <header>           
        <?php include_once('menu.php');?>
      </header>

      <section class="leaderboard text-center">
            <div class="container">
                <h2>Leaderboard</h2>
                <h3>This year $<?php echo number_format($total_raised); ?> has been raised by <?php echo number_format($total_members); ?> members, <?php echo number_format($total_teams); ?> teams and <?php echo number_format($total_orgs); ?> organizations.</h3>
                <div class="row">
                  <div class="col-md-12">
                <?php
            if (isset($_GET['d'])) {
              echo '
                  <div class="alert alert-success alert-dismissible" role="alert" style="margin-bottom: -15px; margin-top: 25px;">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                      <p class="centered">Thank you for donating to No-Shave November! We\'ve received your donation.</p>
                  </div>
              ';
            }
          ?>
                  </div>
                </div>
                <div class="d-flex justify-content-end">
                    <div class="input-group mt-4 mb-5">
                        <input type="text" class="form-control" placeholder="Search Members, Teams and Organizations" name="q" id="q" onKeyDown="if(event.keyCode==13) search1();">
                        <div class="input-group-append">
                          <a href="Javascript:void(0);" class="btn btn-dark" id="basic-addon2" onclick="search1()">Search</a>
                        </div>
                    </div>
                </div>                
                <ul class="nav table-tab mb-5" id="myTab" role="tablist">
                    <li>
                      <a class="active" id="home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="home" aria-selected="true">Member Leaderboard</a>
                    </li>
                    <li>
                      <a class="" id="profile-tab" data-toggle="tab" href="#profile" role="tab" aria-controls="profile" aria-selected="false">Team Leaderboard</a>
                    </li>
                    <li>
                      <a class="" id="contact-tab" data-toggle="tab" href="#contact" role="tab" aria-controls="contact" aria-selected="false">Organization Leaderboard</a>
                    </li>
                    <!--<li>
                      <a class="" id="contact-tab" data-toggle="tab" href="#contact" role="tab" aria-controls="contact" aria-selected="false">Organization Leaderboard</a>
                    </li>
                    <li>
                      <a class="" id="contact-tab" data-toggle="tab" href="#contact" role="tab" aria-controls="contact" aria-selected="false">Organization Leaderboard</a>
                    </li>-->
                </ul>
                <div class="tab-content" id="myTabContent">
                    <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th scope="col">Serial No</th>
                                    <th scope="col" class="pl-5">Member</th>
                                    <th scope="col" class="pl-5">Raised</th>                                   
                                </tr>
                            </thead>
                            <tbody>
                            <?php echo $member_table1; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th scope="col">Serial No</th>
                                    <th scope="col" class="pl-5">Team</th>
                                    <th scope="col" class="pl-5">Raised</th>                                   
                                </tr>
                            </thead>
                            <tbody>
                            <?php echo $team_table; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="tab-pane fade" id="contact" role="tabpanel" aria-labelledby="contact-tab">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th scope="col">Serial No</th>
                                    <th scope="col" class="pl-5">Organization</th>
                                    <th scope="col" class="pl-5">Raised</th>                                   
                                </tr>
                            </thead>
                            <tbody>
                            <?php echo $org_table; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                
            </div>
        </section>
        <!--end of leaderboard-->       
       <section class="funded-programs" style="background-image:url(./img/cans-bg.jpg);">
           <div class="container">              
               <div class="row">
                   <div class="col-12 col-md-4">
                       <div class="fund-box text-center">
                           <figure>
                               <img src="./img/img1.png" class="img-fluid" alt="funding logo">
                           </figure>
                           <div class="clearfix text-center">
                               <h4>Prevent Cancer Foundation</h4>
                               <p>Prevent Cancer Foundation is the only U.S. nonprofit solely focused on the prevention and early detection of cancer. </p>
                               <a href="" class="text-white">Learn More</a>
                           </div>
                       </div>
                   </div>
                   <div class="col-12 col-md-4">
                       <div class="fund-box text-center">
                           <figure>
                               <img src="./img/img2.png" class="img-fluid" alt="funding logo">
                           </figure>
                           <div class="clearfix text-center">
                               <h4>Fight Colorectal Cancer</h4>
                               <p>Fight Colorectal Cancer is a community of activists committed to fighting colorectal cancer until there's a cure.</p>
                               <a href="" class="text-white">Learn More</a>
                           </div>
                       </div>
                   </div>
                   <div class="col-12 col-md-4">
                       <div class="fund-box text-center">
                           <figure>
                               <img src="./img/img3.png" class="img-fluid" alt="funding logo">
                           </figure>
                           <div class="clearfix text-center">
                               <h4>St. Jude Children's Research Hospital</h4>
                               <p>St. Jude Children's Research Hospital is leading the way the world understands, treats and defeats childhood cancer. </p>
                               <a href="" class="text-white">Learn More</a>
                           </div>
                       </div>
                   </div>
               </div>
           </div>
       </section>      
       <!--end of funded-->
       <section class="press-release">
           <div class="container">             
               <div class="brand-wrap">
                   <ul class="list-inline brand-slide">
                       <li><figure><img src="./img/brand1.png" class="img-fluid" alt="partner"></figure></li>
                       <li><figure><img src="./img/brand2.png" class="img-fluid" alt="partner"></figure></li>
                       <li><figure><img src="./img/brand3.png" class="img-fluid" alt="partner"></figure></li>
                       <li><figure><img src="./img/brand4.png" class="img-fluid" alt="partner"></figure></li>
                       <li><figure><img src="./img/brand5.png" class="img-fluid" alt="partner"></figure></li>
                       <li><figure><img src="./img/brand6.png" class="img-fluid" alt="partner"></figure></li>
                       <li><figure><img src="./img/brand1.png" class="img-fluid" alt="partner"></figure></li>
                       <li><figure><img src="./img/brand2.png" class="img-fluid" alt="partner"></figure></li>
                       <li><figure><img src="./img/brand3.png" class="img-fluid" alt="partner"></figure></li>
                       <li><figure><img src="./img/brand4.png" class="img-fluid" alt="partner"></figure></li>
                       <li><figure><img src="./img/brand5.png" class="img-fluid" alt="partner"></figure></li>
                       <li><figure><img src="./img/brand6.png" class="img-fluid" alt="partner"></figure></li>
                   </ul>
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
    <!-- MODALS -->

    <!-- SEARCH -->
    <div class="modal fade" tabindex="-1" role="dialog" id="searchModal">
      <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">

            <div class="modal-header">
            <h4 class="modal-title">Search Members, Teams and Organizations</h4>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
              
            </div>

            <div class="modal-body">

              <div class="input-group">
                <input type="text" class="form-control" name="q2" id="q2" placeholder="Search Members, Teams and Organizations" onKeyDown="if(event.keyCode==13) search_q2();">
                <span class="input-group-btn">
                  <button class="btn btn-default btn-add form-control" type="button" onclick="search_q2()">
                    <span class="glyphicon glyphicon-search"></span> Search
                  </button>
                </span>
              </div>
              <br><br>

              <div id="search_results" name="search_results">
              </div>

            </div>

            <div class="modal-footer">
              <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>


        </div><!-- /.modal-content -->
      </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
    <!-- SEARCH -->

    <div id="failed_search" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="failed_search">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">Unable to Search</h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            
          </div>
          <div class="modal-body">
            <h4 id="failed_search_message" name="failed_search_message"></h4>
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
