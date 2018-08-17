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
    <title>Awarness | No-Shave November</title>

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
    <section class="leaderboard awareness">
            <div class="container">
                <h2 class="text-center">Working Together for Cancer Awareness <br/><span>We can make a difference!</span></h2>
                <p class="sub-heading">Visit our websites to learn more.</p>
                <div class="row pt-5">
                	<div class="col-md-7">
                		<p class="text-justify">No-Shave November is a cancer awareness campaign in which individuals, teams, and organizations put down their razors in solidarity with those battling cancer. These participants donate what they would normally spend on shaving or grooming products and services to our non-profit organization, while building camaraderie with their fellow No-Shavers. We grant no less than 80% of the funds raised to the following foundations: Fight Colorectal Cancer, Prevent Cancer Foundation, and St. Jude Children's Research Hospital, which in turn use the funds to support cancer research, prevention, and education.</p> 
                	</div>
                  <div class="col-md-4 offset-md-1 d-flex justify-content-center align-self-center">
                  	<div class="text-center">
                  		<img src="<?php echo base_url; ?>/assets/images/nsn-full-logo.png" alt=""  class="img-fluid"/>
                  		<a href="" class="mt-3 d-block">no-shave.org</a>
                  	</div>
                  </div>             
                  <div class="col-md-12">   
                  	<h4>Why November? </h4>
                  	
                  	<p class="text-justify">On November 1, 2007, Matthew Hill was admitted to the hospital with extreme pain due to his battle with colorectal cancer. He fought his last 30 days in the hospital and passed away on November 30, 2007. This month not only turns not shaving for No Shave November into a charitable endeavor; it's a month that celebrates Matthew's life and his courage while battling cancer.</p>
                  	
                  </div>             
                </div>
                <hr/>
                <div class="row pt-5">
                	<div class="col-md-12">
                		<p class="text-justify">Founded in 2003, the Movember Foundation is the only global charity focused solely on men’s health, funding over 1,200 innovative projects across 21 countries. The Foundation raises funds and awareness for men’s health programs supporting these critical areas: prostate cancer, testicular cancer, mental health and suicide prevention. </p>
                	</div>                	
                  <div class="col-md-3 d-flex justify-content-center align-self-center">
                  	<div class="text-center">
                  		<img src="<?php echo base_url; ?>/assets/images/foundation_logo_lack.jpg" alt=""  class="img-fluid"/>
                  		<a href="" class="mt-3 d-block">movember.com</a>
                  	</div>
                  </div>             
                  <div class="col-md-8 offset-md-1">
                		
                		<p class="text-justify">We’ve created a men’s health movement of over 5 million supporters across the world. Through the moustaches grown and the conversations generated, we’ve provided funding for more than 1,200 innovative men’s health projects. By 2030, we aim to reduce the number of men dying prematurely by 25%. </p>
                		<p class="text-justify">Best known for our annual Movember fundraising campaign that takes place every November, we encourage men and women to sign up at Movember.com to be the difference and stop men dying too young. Supports can grow a moustache, set a Move physical activity goal, host an event, or make a donation. </p>                	
                		
                		<p>The Movember Foundation was recognized as one of the top 100 non-governmental- organizations around the world. </p>
                	</div>
                </div>
                <hr/>
                <div class="row">
                	<div class="col-md-12 text-center">
                		<p>Both the Movember Foundation and Matthew Hill Foundation are public charities which are exempt from federal income tax as organizations described in Section 501(c)(3) of the Internal Revenue Code, as amended.</p>
                	</div> 
                </div>    
            </div>
        </section>
        <!--end of leaderboard-->       
      
       <?php include_once('footer.php')?>
       <?php include_once("analyticstracking.php") ?>

  </body>
</html>

