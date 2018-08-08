<?php
  //include_once 'includes/db_connect.php';
  include_once 'includes/functions.php';
  sec_session_start();
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <link rel="icon" type="image/png" href="<?php echo base_url; ?>/img/favicon.png">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" />
    <meta name="author" content="No-Shave November">
    <meta name="description" content="Login to No-Shave November. Update your personal, team and organization fundraising pages, track your fundraising progress, and update your account information.">
    <title>Financials | No-Shave November</title>

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
    <script src="<?php echo base_url; ?>/assets/js/loadingoverlay.js" type="text/javascript"></script>
  </head>
  <body>
    <header>           
    <?php include_once('menu.php')?>
    </header>
    <section class="leaderboard text-center">
            <div class="container">
                <h2>Financials</h2>
                <h3> 2015 Donation Breakdown </h3>
                <div class="row centered">
                  <div class="col-md-12">
                    <img class="img-fluid" src="https://storage.googleapis.com/nsn-misc/2015_financial_chart.png" alt="No-Shave November 2015 Financials">

                    <p>
                        <h4 style="color: #396cc6;">Programs</h4>
                        Benefiting charities and other miscellaneous program costs<br><br>
                        <h4 style="color: #d83c2a;">Fundraising</h4>
                        Payroll, payment processing and technology fees<br><br>
                        <h4 style="color: #fb9738;">Reserve</h4>
                        Funds held for future programs and contingency of foundation<br><br>
                        <h4 style="color: #25942c;">General &amp; Administrative</h4>
                        Marketing, charity registration, payroll, and office expenses<br><br>
                        <!-- Financial statement PDFs-->
                        <h4 style="color: #33691E;">Financial Statements</h4>
                        <a class="btn btn-default" style="background-color:#DCEDC8" href="https://storage.googleapis.com/nsn-misc/2015_990_form.pdf" target="_blank">990</a>
                        <a class="btn btn-default" style="background-color:#DCEDC8" href="https://storage.googleapis.com/nsn-misc/2015_financials.pdf" target="_blank">2015 Financial Statement</a>
                    </p>
                  </div>
                </div>
                <div class="col-md-12">&nbsp;</div>
                <div class="col-md-12">&nbsp;</div>
                <h3> 2016 Donation Breakdown </h3>
                <div class="row centered">
                    <div class="col-md-12">
                        <img class="img-fluid" src="https://storage.googleapis.com/nsn-misc/2016_financial_chart.png" alt="No-Shave November 2015 Financials">

                        <p>
                            <h4 style="color: #396cc6;">Programs</h4>
                            Benefiting charities and other miscellaneous program costs<br><br>
                            <h4 style="color: #d83c2a;">Fundraising</h4>
                            Payroll, payment processing and technology fees<br><br>
                            <h4 style="color: #fb9738;">Reserve</h4>
                            Funds held for future programs and contingency of foundation<br><br>
                            <h4 style="color: #25942c;">General &amp; Administrative</h4>
                            Marketing, charity registration, payroll, and office expenses<br><br>
                            <!-- Financial statement PDFs-->
                            <h4 style="color: #33691E;">Financial Statements</h4>
                            <a class="btn btn-default" style="background-color:#DCEDC8" href="https://storage.googleapis.com/nsn-misc/2016_990_form.pdf" target="_blank">990</a>
                            <a class="btn btn-default" style="background-color:#DCEDC8" href="https://storage.googleapis.com/nsn-misc/2016_financials.pdf" target="_blank">2016 Financial Statement</a>
                        </p>
                    </div>
                </div>

            </div>
        </section>
        <!--end of leaderboard-->       
      
       <?php include_once('footer.php')?>
       <?php include_once("analyticstracking.php") ?>

  </body>
</html>

