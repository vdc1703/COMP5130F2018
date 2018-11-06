<?php
include('config.php');
$conn = new mysqli($dbHost, $dbuser, $dbpwd, $dbname);
// Check connection
if ($conn->connect_error) {
	die("Connection failed: " . $conn->connect_error);
}


?>
