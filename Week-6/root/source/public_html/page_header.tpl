<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" lang="en-us" xml:lang="en-us">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta http-equiv="Content-Style-Type" content="text/css" />
    <meta http-equiv="Content-Language" content="en-us" />
    
    <title><# PAGE_TITLE #></title>
    
    <base href="<# BASE_URL #>" />
    
    <link href="css/style.css" rel="stylesheet" type="text/css" media="screen" />
    
    <script type="text/javascript" src="source/includes/scripts/jquery.js"></script>
    <script type="text/javascript" src="source/includes/scripts/genjscript.js"></script>
    <script type="text/javascript" src="source/includes/scripts/phpjs_00029.js"></script>
    <script type="text/javascript" src="source/includes/scripts/jquery.jdMenu.js"></script>
    <script type="text/javascript" src="source/includes/scripts/jquery.bgiframe.js"></script>
    <script type="text/javascript" src="source/includes/scripts/jquery.positionBy.js"></script>
    <script type="text/javascript" src="source/includes/scripts/jquery.dimensions.js"></script>
</head>
<body class="page_cell">
	<a href="index.php" style="text-decoration: none;"><div class="logo">&nbsp;</div></a>
    
	<div class="nav_menu">
		<ul>
			<li><a href="index.php">Home</a></li>
			<li><a href="info.php?act=about_us">About Us</a></li>
			<li><a href="gallery.php">Public Gallery</a></li>
			<li><a href="index.php?do_random=true">Random Image</a></li>
		</ul>
	</div>
    
	<div class="members_bar">
		<if="$vdcclass->info->is_user == true">
			<div class="align_left">
				Logged in as: <a href="users.php?act=gallery"><# USERNAME #></a> 
			</div>
            
			<div class="align_right">
				<if="$vdcclass->info->is_admin == true">
					<a href="admin.php">Admin Control Panel</a> &bull;
				</endif>
                
				<a href="users.php?act=gallery">My Gallery</a> &bull;
				<a href="users.php?act=settings">Settings</a> &bull;
				<a href="users.php?act=logout">Log Out</a>
			</div>
		<else>
			<div class="guest_links">
				Welcome Guest
				( <a href="javascript:void(0);" onclick="toggle_lightbox('users.php?act=login', 'login_lightbox');">Log In</a> | 
				<a href="users.php?act=register&amp;return=<# RETURN_URL #>">Register</a> )
			</div>
		</endif>
	</div>
        
    <div style="clear: both;"></div>
    
	<div id="page_body" class="page_body">