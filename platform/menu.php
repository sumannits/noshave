<?php  
$currentPage = basename($_SERVER['PHP_SELF']);
if($currentPage!=''){
    $pageName = explode('.',$currentPage);

}
//print_r($mysqli);
?>
<?php 
if (login_check($mysqli) == true){ 
    $log_user_id = $_SESSION['user_id'];

// grab data
// get user data
if ($stmt = $mysqli->prepare("SELECT m_full_name, m_email, m_username, m_team_id FROM member WHERE m_id = ? LIMIT 1")) {
  $stmt->bind_param('i', $log_user_id);
  $stmt->execute();
  $stmt->store_result();
  $stmt->bind_result($user_m_full_name, $user_m_email, $user_m_username, $user_m_team_id);
  $stmt->fetch();
  $stmt->close();

} else {
  // give error 5590 - unable to get data from DB using user ID from session
  //header('Location: /error?id=1');
}
    ?>        
            <!-- Navigation after login-->
            <nav class="navbar navbar-expand-lg">
              <div class="container">
                   <a class="navbar-brand" href="<?php echo base_url; ?>"><img src="<?php echo base_url; ?>/assets/images/logo.png" class="img-fluid" alt="logo"/></a>
                <button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
                  <span class="navbar-toggler-icon"><i class="ti-align-right"></i></span>
                </button>
               
                <div class="collapse navbar-collapse" id="navbarResponsive">
                    <ul class="navbar-nav mr-auto ml-4">
                        <li class="nav-item">
                          <a class="nav-link" href="<?php echo base_url; ?>/#about">About</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo base_url; ?>/#shop">Shop</a>
                        </li>
                        <li class="nav-item">
                          <a class="nav-link" href="<?php echo base_url; ?>/#participate">Participate</a>
                        </li>
                        <li class="nav-item ">
                          <a class="nav-link" href="<?php echo base_url; ?>/leaderboard"> Leaderboard</a>             
                        </li>
                        <li class="nav-item ">
                          <a class="nav-link" href="<?php echo base_url; ?>/#fundedprogram">Funded Programs</a>             
                        </li>
                        <li class="nav-item ">
                          <a class="nav-link" href="<?php echo base_url; ?>/#share">Share</a>             
                        </li>
                        <li class="dropdown nav-item">
                            <a class="btn dropdown-toggle" href="Javascript:void(0);" id="moremenu" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                More
                            </a>    
                            <div class="dropdown-menu" aria-labelledby="moremenu">
                               <a class="dropdown-item" href="<?php echo base_url; ?>/#press">Press Releases</a>
                               <a class="dropdown-item" href="<?php echo base_url; ?>/#shop">Shop</a>
                               <a class="dropdown-item" href="<?php echo base_url; ?>/#story">Our Story</a>
                               <a class="dropdown-item" href="<?php echo base_url; ?>/awarness.html">Awareness Initiative</a>
                               <div class="dropdown-divider"></div>
                               <!--<a class="dropdown-item" href="Javascript:void(0);" data-toggle="modal" data-target="#2015_financials">2015 Financials</a>
                               <a class="dropdown-item" href="Javascript:void(0);" data-toggle="modal" data-target="#2016_financials">2016 Financials</a>-->
                               <a class="dropdown-item" href="<?php echo base_url; ?>/financials">Financials</a>
                               <a class="dropdown-item" href="<?php echo base_url; ?>/contact_us">Contact Us</a>
                            </div>        
                        </li> 
                        
                    </ul>
                </div>
                <div class="ml-auto right-log clearfix">
                    <span class="donate-top"><a href="<?php echo base_url; ?>/donate">Donate</a></span>
			        <ul class="list-inline">
                        <li class="dropdown"> <a class="dropdown-toggle"  href="#" id="profile" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <?php
                        $fname = explode( ' ', $user_m_full_name ); 
                        echo isset($fname[0])?$fname[0]:'';?> </a>
                            <div class="dropdown-menu" aria-labelledby="profile">
                                <a class="dropdown-item" href="<?php echo base_url; ?>/dashboard">My Account</a>
                                <a class="dropdown-item" href="<?php echo base_url; ?>/dashboard">Dashboard</a>
                                <a class="dropdown-item" href="<?php echo base_url; ?>/logout">Logout</a>
                            </div>
                        </li>
                    </ul>

                </div>
              </div>
            </nav>
            <!--end of navigation after login-->
<?php }else{ ?>
	    <!-- Navigation before login-->
            <nav class="navbar navbar-expand-lg">
              <div class="container">
                   <a class="navbar-brand" href="<?php echo base_url; ?>"><img src="<?php echo base_url; ?>/assets/images/logo.png" class="img-fluid" alt="logo"/></a>
                <button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
                  <span class="navbar-toggler-icon"><i class="ti-align-right"></i></span>
                </button>
               
                <div class="collapse navbar-collapse" id="navbarResponsive">
                    <ul class="navbar-nav mr-auto ml-4">
                        <li class="nav-item">
                          <a class="nav-link" href="<?php echo base_url; ?>/#about">About</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo base_url; ?>/#shop">Shop</a>
                        </li>
                        <li class="nav-item">
                          <a class="nav-link" href="<?php echo base_url; ?>/#participate">Participate</a>
                        </li>
                        <li class="nav-item ">
                          <a class="nav-link" href="<?php echo base_url; ?>/leaderboard"> Leaderboard</a>             
                        </li>
                        <li class="nav-item ">
                          <a class="nav-link" href="<?php echo base_url; ?>/#fundedprogram">Funded Programs</a>             
                        </li>
                        <li class="nav-item ">
                          <a class="nav-link" href="<?php echo base_url; ?>/#share">Share</a>             
                        </li>
                        <li class="dropdown">
                            <a class="btn dropdown-toggle" href="Javascript:void(0);" id="moremenu" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                More
                            </a>    
                            <div class="dropdown-menu" aria-labelledby="moremenu">
                               <a class="dropdown-item" href="<?php echo base_url; ?>/#press">Press Releases</a>
                               <a class="dropdown-item" href="<?php echo base_url; ?>/#shop">Shop</a>
                               <!--<a class="dropdown-item" href="">Partnerships</a>-->
                               <a class="dropdown-item" href="<?php echo base_url; ?>/#story">Our Story</a>
                               <a class="dropdown-item" href="<?php echo base_url; ?>/awarness.html">Awareness Initiative</a>
                               <div class="dropdown-divider"></div>
                               <!--<a class="dropdown-item" href="Javascript:void(0);" data-toggle="modal" data-target="#2015_financials">2015 Financials</a>
                               <a class="dropdown-item" href="Javascript:void(0);" data-toggle="modal" data-target="#2016_financials">2016 Financials</a>-->
                               <a class="dropdown-item" href="<?php echo base_url; ?>/financials">Financials</a>
                               <a class="dropdown-item" href="<?php echo base_url; ?>/contact_us">Contact Us</a>
                            </div>        
                        </li> 
                    </ul>
                </div>
                <div class="ml-auto right-log clearfix">
                    <span class="donate-top"><a href="<?php echo base_url; ?>/donate">Donate</a></span>
                    <ul class="list-inline">
                        <li><a href="<?php echo base_url; ?>/login">Login</a> / <a href="<?php echo base_url; ?>/register">Signup</a></li>
                    </ul>
                    <?php
                    if(isset($pageName[0]) && $pageName[0]!='' && $pageName[0]=='index'){
                    ?>
                    <div class="curve-bg-div text-center">
                        <p>A Unique Way to Grow</p>
                        <h2 class="text-bold">Cancer Awareness</h2>
                    </div>
                    <?php } ?>
                </div>
              </div>
            </nav>
            <!--end of navigation before login -->		
                    <?php } ?>
