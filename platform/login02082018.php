<?php
  // Load database connection and php functions
  include_once 'includes/psl-config.php';
  include_once 'includes/db_connect.php';
  include_once 'includes/functions.php';
  // Start secure session
  sec_session_start();
?>

<?php

//SELECT count(o_id) FROM org;
if ($stmt = $mysqli->prepare("SELECT total_raised, total_members, total_teams, total_orgs, top_members, top_teams, top_orgs FROM leaderboard")) {
  $stmt->execute();
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

?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <link rel="icon" type="image/png" href="/favicon.png">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" />
    <meta name="author" content="No-Shave November">
    <meta name="description" content="Check out who's leading this year's No-Shave November fundraising efforts! Looking for someoneone specific? Search and find them!">
    <title>Leaderboard | No-Shave November</title>
    <link href="assets/css/bootstrap.css" rel="stylesheet">    
    <link href="assets/css/main.css" rel="stylesheet">
    <link href='assets/css/font.css' rel='stylesheet' type='text/css'>
    <script src="assets/js/jquery.min.js" type="text/javascript"></script>
    <script type="text/javascript">
      $(function(){
        $("#menu").load("platform/platform_menu.php"); 
      });
      $(function(){
        $("#footer").load("platform/platform_footer.html"); 
      });
    </script>
    <script>
    $(function () {
      $('[data-toggle="tooltip"]').tooltip()
    })
    </script>
    <script>

      function search() {

        // grab field
        var args = {
          q: $('#q').val()
        }

        // make call
        $.ajax({ 
          type: 'POST',
          url: 'platform/api/search.php', 
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
          url: 'platform/api/search.php', 
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

          <br><br>
          <img style="width: 100%; height: 100%;" src="https://storage.googleapis.com/nsn-misc/nsn-full-logo.png" alt="No Shave November 2017" class="img-rounded">
          <br><br>

          <div class="row">
            <div class="col-md-4 col-xs-4">
                <img style="width: 100%; height: 100%;" src="img/partners/pcf.gif" alt="" class="img-rounded">
            </div>
            <div class="col-md-4 col-xs-4">
                <img style="width: 100%; height: 100%;" src="img/partners/fcc.png" alt="" class="img-rounded">
            </div>
            <div class="col-md-4 col-xs-4">
                <img style="width: 100%; height: 100%;" src="img/partners/stj-2.png" alt="" class="img-rounded">
            </div>
          </div>

          <br><br>

          <h2 class="centered visible-lg visible-md visible-sm hidden-xs">This year <big class="donation-green">$<?php echo number_format($total_raised); ?></big> has been raised by <big class="donation-green"><?php echo number_format($total_members); ?></big> members, <big class="donation-green"><?php echo number_format($total_teams); ?></big> teams and <big class="donation-green"><?php echo number_format($total_orgs); ?></big> organizations.</h2>
          
          <h4 class="centered hidden-lg hidden-md hidden-sm visible-xs">This year <big class="donation-green">$<?php echo number_format($total_raised); ?></big> has been raised by <big class="donation-green"><?php echo number_format($total_members); ?></big> members, <big class="donation-green"><?php echo number_format($total_teams); ?></big> teams and <big class="donation-green"><?php echo number_format($total_orgs); ?></big> organizations.</h4>

          <br><br>
          <div class="row">
            <div class="col-md-8 col-md-offset-2">
              <div class="form-inline">
                <div class="input-group">
                  <input type="text" class="form-control" name="q" id="q" placeholder="Search Members, Teams and Organizations" onKeyDown="if(event.keyCode==13) search();">
                  <span class="input-group-btn">
                    <button class="btn btn-default btn-add form-control" onclick="search()">
                      <span class="glyphicon glyphicon-search"></span> Search
                    </button>
                  </span>
                </div>
              </div>
            </div>
          </div>
          <br><br><br>

          <!-- LEADERBOARD SELECTION -->
          <ul class="nav nav-tabs" role="tablist">
            <li role="presentation" class="active visible-lg visible-md visible-sm hidden-xs"><a class="black-link" href="#member" aria-controls="member" role="tab" data-toggle="tab"><i class="fa fa-user" aria-hidden="true"></i>&nbsp; Member Leaderboard</a></li>
            <li role="presentation" class="visible-lg visible-md visible-sm hidden-xs"><a class="black-link" href="#team" aria-controls="team" role="tab" data-toggle="tab"><i class="fa fa-users" aria-hidden="true"></i>&nbsp; Team Leaderboard</a></li>
            <li role="presentation" class="visible-lg visible-md visible-sm hidden-xs"><a class="black-link" href="#org" aria-controls="org" role="tab" data-toggle="tab"><i class="fa fa-sitemap" aria-hidden="true"></i>&nbsp; Organization Leaderboard</a></li>

            <li role="presentation" class="active hidden-lg hidden-md hidden-sm visible-xs"><a class="black-link" href="#member" aria-controls="member" role="tab" data-toggle="tab"><i class="fa fa-user" aria-hidden="true"></i></a></li>
            <li role="presentation" class="hidden-lg hidden-md hidden-sm visible-xs"><a class="black-link" href="#team" aria-controls="team" role="tab" data-toggle="tab"><i class="fa fa-users" aria-hidden="true"></i>&nbsp; Teams</a></li>
            <li role="presentation" class="hidden-lg hidden-md hidden-sm visible-xs"><a class="black-link" href="#org" aria-controls="org" role="tab" data-toggle="tab"><i class="fa fa-sitemap" aria-hidden="true"></i>&nbsp; Orgs</a></li>            
          </ul>
          <!-- LEADERBOARD SELECTION -->

          <!-- Tab panes -->
          <div class="tab-content">
            <div role="tabpanel" class="tab-pane fade in active" id="member">
              <br>
              <table class="table table-hover">
                <thead>
                  <tr class="visible-lg visible-md visible-sm hidden-xs">
                    <th class="col-md-1"><h4>#</h4></th>
                    <th class="col-md-7" colspan="2"><h4><i class="fa fa-user" aria-hidden="true"></i>&nbsp; Member</h4></th>
                    <th class="col-md-2"><h4>Raised</h4></th>
                  </tr>

                  <tr class="hidden-lg hidden-md hidden-sm visible-xs">
                    <th class="col-md-1"><h4>#</h4></th>
                    <th class="col-md-7" colspan="2"><h4>Member</h4></th>
                    <th class="col-md-2"><h4>Raised</h4></th>
                  </tr>
                </thead>
                <tbody>
                  <?php echo $member_table; ?>
                </tbody>
              </table>
            </div>

            <div role="tabpanel" class="tab-pane fade in" id="team">
              <br>
              <table class="table table-hover">
                <thead>
                  <tr>
                    <th class="col-md-1"><h4>#</h4></th>
                    <th class="col-md-7"><h4><i class="fa fa-users" aria-hidden="true"></i>&nbsp; Team</h4></th>
                    <th class="col-md-4"><h4>Raised</h4></th>
                  </tr>
                </thead>
                <tbody> 
                  <?php echo $team_table; ?>
                </tbody>
              </table>
            </div>

            <div role="tabpanel" class="tab-pane fade in" id="org">
              <br>
              <table class="table table-hover">
                <thead>
                  <tr>
                    <th class="col-md-1"><h4>#</h4></th>
                    <th class="col-md-7"><h4><i class="fa fa-sitemap" aria-hidden="true"></i>&nbsp; Organization</h4></th>
                    <th class="col-md-4"><h4>Raised</h4></th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                    echo $org_table;
                  ?>
                </tbody>
              </table>
            </div>
          </div>
          <!-- Tab panes -->


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

    <!-- SEARCH -->
    <div class="modal fade" tabindex="-1" role="dialog" id="searchModal">
      <div class="modal-dialog" role="document">
        <div class="modal-content">

            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
              <h4 class="modal-title">Search Members, Teams and Organizations</h4>
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
      <div class="modal-dialog modal-sm">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">Unable to Search</h4>
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

    <script src="assets/js/bootstrap.min.js" type="text/javascript"></script>
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.6.0/css/font-awesome.min.css" rel="stylesheet">
    <?php include_once("analyticstracking.php") ?>
  </body>
</html>
