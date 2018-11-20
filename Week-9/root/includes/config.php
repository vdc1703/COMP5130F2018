<?php
    /* DEBUG INFORMATION */
	ini_set("log_errors", 1);
	ini_set("display_errors", 1);
    																					  													  
	/* DATABASE INFORMATION */
	
	if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
		define('dbHost', 'localhost:3306');
		define('dbUser', 'root');
		define('dbPass', 'root');
		define('dbName', 'img');
	} else {	
		define('dbHost', 'weblab.cs.uml.edu:3306');
		define('dbUser', 'cvu');
		define('dbPass', 'Shae4sae');
		define('dbName', 'test');
	}
    
    $conn = new mysqli(dbHost,dbUser,dbPass,dbName);
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }        																	   	
    
    /* SITE CONFIG */
    define('upload_path', 'images/');
    define('file_extensions', 'jpeg,jpg,gif,png');
?>