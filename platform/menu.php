<?php  
$currentPage = basename($_SERVER['PHP_SELF']);
if($currentPage!=''){
    $pageName = explode('.',$currentPage);

}
?>
<?php if (login_check($mysqli) == true) : ?>        
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
                            <a class="nav-link" href="#">Shop</a>
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
                        <li class="nav-item ">
                            <a class="nav-link" href="#"> More</a>             
                        </li>
                    </ul>
                </div>
                <div class="ml-auto right-log clearfix">
                    <span class="donate-top"><a href="#">Donate</a></span>
			        <ul class="list-inline">
                        <li class="dropdown"> <a class="dropdown-toggle"  href="#" id="profile" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Alina Wilson</a>
                            <div class="dropdown-menu" aria-labelledby="profile">
                                <a class="dropdown-item" href="#">My Account</a>
                                <a class="dropdown-item" href="#">Dashboard</a>
                                <a class="dropdown-item" href="#">Logout</a>
                            </div>
                        </li>
                    </ul>

                </div>
              </div>
            </nav>
            <!--end of navigation after login-->
	    <?php else : ?>
	    <!-- Navigation before login-->
            <nav class="navbar navbar-expand-lg">
              <div class="container">
                   <a class="navbar-brand" href="<?php echo base_url; ?>"><img src="assets/images/logo.png" class="img-fluid" alt="logo"/></a>
                <button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
                  <span class="navbar-toggler-icon"><i class="ti-align-right"></i></span>
                </button>
               
                <div class="collapse navbar-collapse" id="navbarResponsive">
                    <ul class="navbar-nav mr-auto ml-4">
                        <li class="nav-item">
                          <a class="nav-link" href="<?php echo base_url; ?>/#about">About</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">Shop</a>
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
                            <a class="btn dropdown-toggle" href="dashboard.html" id="moremenu" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                More
                            </a>    
                            <div class="dropdown-menu" aria-labelledby="moremenu">
                               <a class="dropdown-item" href="<?php echo base_url; ?>/#press">Press Releases</a>
                               <a class="dropdown-item" href="#">Shop</a>
                               <!--<a class="dropdown-item" href="">Partnerships</a>-->
                               <a class="dropdown-item" href="<?php echo base_url; ?>/#story">Our Story</a>
                               <a class="dropdown-item" href="<?php echo base_url; ?>/awarness.html">Awareness Initiative</a>
                               <div class="dropdown-divider"></div>
                               <a class="dropdown-item" href="" data-toggle="modal" data-target="#2015_financials">2015 Financials</a>
                               <a class="dropdown-item" href="" data-toggle="modal" data-target="#2016_financials">2016 Financials</a>
                               <a class="dropdown-item" href="" data-toggle="modal" data-target="#contactUS">Contact Us</a>
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
	    <?php endif; ?>