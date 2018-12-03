<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light fixed-top" id="mainNav">
      <div class="container">
        <a class="navbar-brand" href="index">VDC Gallery</a>
        <button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
          Menu
          <i class="fas fa-bars"></i>
        </button>
        <div class="collapse navbar-collapse" id="navbarResponsive">
          <ul class="navbar-nav ml-auto">
            <li class="nav-item">
              <a class="nav-link" href="index">Home</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="about">About</a>
            </li>
    		<?php if ($logged_username) { ?>
            <li class="nav-item">
              <a class="nav-link" href="upload">Upload</a>
            </li>            
            <li class="dropdown nav-item">
                <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                    Welcome <?php echo $logged_usergroup.' '.$logged_username; ?>
                </a>
                <ul class="dropdown-menu dropdown-user">
                    <li>
                        <a href="gallery?name=<?php echo $logged_username; ?>"><i class="fa fa-user fa-fw"></i>Your Gallery</a>
                    </li>
                    <li>
                        <a href="setting"><i class="fa fa-cogs fa-fw"></i>Settings</a>
                    </li>
                    <li class="divider"></li>
                    <li>
                        <a href="logout"><i class="fa fa-sign-out-alt fa-fw"></i> Logout</a>
                    </li>
                </ul>
            </li>                         
    		<?php } else { ?>
                <li class="nav-item">
                  <a class="nav-link" href="login">Login</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" href="register">Register</a>
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