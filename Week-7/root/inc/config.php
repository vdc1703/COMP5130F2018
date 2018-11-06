<?php	
	// If run in Windows Localhost																				  
if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
	$dbHost = "localhost";	//Database Location
	$dbuser= "root";	//Database User
	$dbpwd= "root";	//Database User Password
	$dbname = "img";	//Account Database Name
} else {
	// Win in Linux or other Web server
	$dbHost = "weblab.cs.uml.edu";	//Database Location
	$dbuser= "cvu";	//Database User
	$dbpwd= "Shae4sae";	//Database User Password
	$dbname = "test";	//Account Database Name
}


?>