<?php

	$vdc->info->config                 = array(); 

	/* DATABASE INFORMATION */
	// If run in Windows Localhost		
	if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
		$vdc->info->config['dbHost'] = "localhost";	//Database Location
		$vdc->info->config['dbPort'] = "3306";
		$vdc->info->config['dbUser'] = "root";	//Database User
		$vdc->info->config['dbPass'] = "root";	//Database User Password
		$vdc->info->config['dbName'] = "img";	//Account Database Name
	} else {
		// Win in Linux or other Web server
		$vdc->info->config['dbHost'] = "weblab.cs.uml.edu";	//Database Location
		$vdc->info->config['dbPort'] = "3306";
		$vdc->info->config['dbUser'] = "cvu";	//Database User
		$vdc->info->config['dbPass'] = "Shae4sae";	//Database User Password
		$vdc->info->config['dbName'] = "test";	//Account Database Name
	}

	/* SITE CONFIG */
    $vdc->info->config['site_name'] = "VDC Gallery";
    $vdc->info->config['upload_path'] = "images/";
    $vdc->info->config['file_extensions'] = "jpeg,jpg,gif,png";
?>


