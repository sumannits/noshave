<?php
include_once 'includes/db_connect.php';
include_once 'includes/functions.php';
sec_session_start();
?>
<?php if (login_check($mysqli) == true) : ?>

    <div id="nav" class="navbar navbar-default navbar-fixed-top">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="<?php echo base_url; ?>/"><b>No-Shave November</b></a>
        </div>
        <div class="navbar-collapse collapse">
          <ul class="nav navbar-nav navbar-right">
            <li><a href="<?php echo base_url; ?>/#about" class="visible-lg visible-md visible-sm hidden-xs">About</a></li>
            <li><a href="<?php echo base_url; ?>/#participate" class="visible-lg visible-md visible-sm hidden-xs">Participate</a></li>
            <li><a href="<?php echo base_url; ?>/leaderboard" class="visible-lg visible-md visible-sm hidden-xs">Leaderboard</a></li>
            <li><a href="<?php echo base_url; ?>/#fundedprograms" class="visible-lg visible-md hidden-sm hidden-xs">Funded Programs</a></li>
            <li><a href="<?php echo base_url; ?>/#share" class="visible-lg visible-md hidden-sm hidden-xs">Share</a></li>
            <li><a href="<?php echo base_url; ?>/#about" class="hidden-lg hidden-md hidden-sm visible-xs" data-toggle="collapse" data-target=".navbar-collapse">About</a></li>
            <li><a href="<?php echo base_url; ?>/#participate" class="hidden-lg hidden-md hidden-sm visible-xs" data-toggle="collapse" data-target=".navbar-collapse">Participate</a></li>
            <li><a href="<?php echo base_url; ?>/leaderboard" class="hidden-lg hidden-md hidden-sm visible-xs" data-toggle="collapse" data-target=".navbar-collapse">Leaderboard</a></li>
            <li><a href="<?php echo base_url; ?>/#fundedprograms" class="hidden-lg hidden-md hidden-sm visible-xs" data-toggle="collapse" data-target=".navbar-collapse">Funded Programs</a></li>
            <li><a href="<?php echo base_url; ?>/#share" class="hidden-lg hidden-md hidden-sm visible-xs" data-toggle="collapse" data-target=".navbar-collapse">Share</a></li>
            <li class="dropdown">
                  <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">More <span class="caret"></span></a>
                  <ul class="dropdown-menu">
                    <li><a href="<?php echo base_url; ?>/#shop" class="visible-lg visible-md visible-sm hidden-xs">Shop</a></li>
                    <li><a href="<?php echo base_url; ?>/#partners" class="visible-lg visible-md visible-sm hidden-xs">Partnerships</a></li>
                    <li><a href="<?php echo base_url; ?>/#story" class="visible-lg visible-md visible-sm hidden-xs">Our Story</a></li>
                    <li><a href="<?php echo base_url; ?>/#shop" class="hidden-lg hidden-md hidden-sm visible-xs" data-toggle="collapse" data-target=".navbar-collapse">Shop</a></li>
                    <li><a href="<?php echo base_url; ?>/#partners" class="hidden-lg hidden-md hidden-sm visible-xs" data-toggle="collapse" data-target=".navbar-collapse">Partnerships</a></li>
                    <li><a href="<?php echo base_url; ?>/#story" class="hidden-lg hidden-md hidden-sm visible-xs" data-toggle="collapse" data-target=".navbar-collapse">Our Story</a></li>
                    <li role="separator" class="divider"></li>
                    <li><a href="" data-toggle="modal" data-target="#2015_financials">2015 Financials</a></li>
                    <li><a href="" data-toggle="modal" data-target="#contactUS">Contact Us</a></li>
                  </ul>
                </li>
            <hr class="hidden-lg hidden-md hidden-sm visible-xs">
            <li class="hidden-lg hidden-md hidden-sm visible-xs"><a href="/dashboard">Home</a></li>
            <li class="visible-lg visible-md visible-sm hidden-xs"><p class="navbar-btn">&nbsp;&nbsp;<a href="/dashboard" class="btn btn-success"><i class="fa fa-home" style="color: #fff;" aria-hidden="true"></i> &nbsp;Home</a></p></li>
            <li class="hidden-lg hidden-md hidden-sm visible-xs"><a href="/logout">Sign Out</a></li>
            <li class="visible-lg visible-md visible-sm hidden-xs"><p class="navbar-btn">&nbsp;&nbsp;<a href="/logout" class="btn btn-danger">Sign out</a></p></li>
          </ul>
        </div>
      </div>
    </div>

<?php else : ?>

    <div id="nav" class="navbar navbar-default navbar-fixed-top">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="<?php echo base_url; ?>/"><b>No-Shave November</b></a>
        </div>
        <div class="navbar-collapse collapse">
          <ul class="nav navbar-nav navbar-right">
            <li><a href="<?php echo base_url; ?>/#about" class="visible-lg visible-md visible-sm hidden-xs">About</a></li>
            <li><a href="<?php echo base_url; ?>/#participate" class="visible-lg visible-md visible-sm hidden-xs">Participate</a></li>
            <li><a href="<?php echo base_url; ?>/leaderboard" class="visible-lg visible-md visible-sm hidden-xs">Leaderboard</a></li>
            <li><a href="<?php echo base_url; ?>/#fundedprograms" class="visible-lg visible-md hidden-sm hidden-xs">Funded Programs</a></li>
            <li><a href="<?php echo base_url; ?>/#share" class="visible-lg visible-md hidden-sm hidden-xs">Share</a></li>
            <li><a href="<?php echo base_url; ?>/#about" class="hidden-lg hidden-md hidden-sm visible-xs" data-toggle="collapse" data-target=".navbar-collapse">About</a></li>
            <li><a href="<?php echo base_url; ?>/#participate" class="hidden-lg hidden-md hidden-sm visible-xs" data-toggle="collapse" data-target=".navbar-collapse">Participate</a></li>
            <li><a href="<?php echo base_url; ?>/leaderboard" class="hidden-lg hidden-md hidden-sm visible-xs" data-toggle="collapse" data-target=".navbar-collapse">Leaderboard</a></li>
            <li><a href="<?php echo base_url; ?>/#fundedprograms" class="hidden-lg hidden-md hidden-sm visible-xs" data-toggle="collapse" data-target=".navbar-collapse">Funded Programs</a></li>
            <li><a href="<?php echo base_url; ?>/#share" class="hidden-lg hidden-md hidden-sm visible-xs" data-toggle="collapse" data-target=".navbar-collapse">Share</a></li>
            <li class="dropdown">
                  <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">More <span class="caret"></span></a>
                  <ul class="dropdown-menu">
                    <li><a href="<?php echo base_url; ?>/#shop" class="visible-lg visible-md visible-sm hidden-xs">Shop</a></li>
                    <li><a href="<?php echo base_url; ?>/#partners" class="visible-lg visible-md visible-sm hidden-xs">Partnerships</a></li>
                    <li><a href="<?php echo base_url; ?>/#story" class="visible-lg visible-md visible-sm hidden-xs">Our Story</a></li>
                    <li><a href="<?php echo base_url; ?>/#shop" class="hidden-lg hidden-md hidden-sm visible-xs" data-toggle="collapse" data-target=".navbar-collapse">Shop</a></li>
                    <li><a href="<?php echo base_url; ?>/#partners" class="hidden-lg hidden-md hidden-sm visible-xs" data-toggle="collapse" data-target=".navbar-collapse">Partnerships</a></li>
                    <li><a href="<?php echo base_url; ?>/#story" class="hidden-lg hidden-md hidden-sm visible-xs" data-toggle="collapse" data-target=".navbar-collapse">Our Story</a></li>
                    <li role="separator" class="divider"></li>
                    <li><a href="" data-toggle="modal" data-target="#2015_financials">2015 Financials</a></li>
                    <li><a href="" data-toggle="modal" data-target="#contactUS">Contact Us</a></li>
                  </ul>
                </li>
            <hr class="hidden-lg hidden-md hidden-sm visible-xs">
            <li class="hidden-lg hidden-md hidden-sm visible-xs"><a href="<?php echo base_url; ?>/login">Sign in</a></li>
            <li class="visible-lg visible-md visible-sm hidden-xs"><p class="navbar-btn">&nbsp;&nbsp;<a href="<?php echo base_url; ?>/login" class="btn btn-success">Sign in</a></p></li>
            <li class="hidden-lg hidden-md hidden-sm visible-xs"><a href="<?php echo base_url; ?>/register">Register</a></li>
            <li class="visible-lg visible-md visible-sm hidden-xs"><p class="navbar-btn">&nbsp;&nbsp;<a href="<?php echo base_url; ?>/register" class="btn btn-primary">Register</a></p></li>
          </ul>
        </div>
      </div>
    </div>

<?php endif; ?>
