<?php 
    $return_url = base64_encode((binary)$this->vdc->info->page_url);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" lang="en-us" xml:lang="en-us">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta http-equiv="Content-Style-Type" content="text/css" />
    <meta http-equiv="Content-Language" content="en-us" />
    
    <title>
        <?php
        if ($page_title) {
            echo $page_title; 
        } else {
            echo $vdc->info->config['site_name'];    
        }
        ?>
    </title>
    
    <base href="<# BASE_URL #>" />
    
    <!-- Bootstrap core CSS -->
    <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet" />

    <!-- Custom fonts for this template -->
    <link href="assets/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css" />
    <link href='https://fonts.googleapis.com/css?family=Lora:400,700,400italic,700italic' rel='stylesheet' type='text/css' />
    <link href='https://fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,600italic,700italic,800italic,400,300,600,700,800' rel='stylesheet' type='text/css' />

    <!-- Custom styles for this template -->
    <link href="assets/css/style.css" rel="stylesheet" />
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light fixed-top" id="mainNav">
      <div class="container">
        <a class="navbar-brand" href="index.php">VDC Gallery</a>
        <button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
          Menu
          <i class="fas fa-bars"></i>
        </button>
        <div class="collapse navbar-collapse" id="navbarResponsive">
          <ul class="navbar-nav ml-auto">
            <li class="nav-item">
              <a class="nav-link" href="index.php">Home</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="about.php">About</a>
            </li>
            <li class="nav-item">
              <!-- <a class="nav-link" href="gallery.php">Gallery</a> -->
			  <a class="nav-link" href="lib\gallery.php">Gallery</a>
            </li>
    		<?php if ($vdc->info->is_user == true) { ?>
            <li class="dropdown nav-item">
                <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                    <# USERNAME #>
                </a>
                <ul class="dropdown-menu dropdown-user">
                    <li>
                        <a href="users.php?task=gallery"><i class="fa fa-user fa-fw"></i>Your Gallery</a>
                    </li>
                    <li>
                        <a href="users.php?task=setting"><i class="fa fa-gear fa-fw"></i>Settings</a>
                    </li>
                    <li class="divider"></li>
                    <li>
                        <a href="users.php?task=logout"><i class="fa fa-sign-out fa-fw"></i> Logout</a>
                    </li>
                </ul>
            </li>                         
    		<?php } else { ?>
                <li class="nav-item">
                  <a class="nav-link" href="login.php?return=<?php echo $return_url; ?>">Login</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" href="register.php?return=<?php echo $return_url; ?>">Register</a>
                </li>                           
    		<?php } ?>                        
          </ul>
        </div>
      </div>
    </nav>

    <!-- Page Header -->
    <header class="masthead" style="background-image: url('assets/img/vdc.png')">
      <div class="overlay"></div>
      <div class="container">
        <div class="row">
          <div class="col-lg-8 col-md-10 mx-auto">
            <div class="site-heading">
              <h1>VDC Production</h1>
              <span class="subheading">You will see many beautiful pictures that from my website</span>
            </div>
          </div>
        </div>
      </div>
    </header>
    <div class="container">