<?php 

// Get user ID from the link
if (!isset($_GET['id']))
    die("No user information!!");
else {
    $user_id = $_GET['id'];
    // Now use $id to query the database and get user information
}

//echo "<a href='" . $user_id . "'>test</a>  ";
$curfolder =  '//'.$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']);
$linkxml =  $curfolder . "/" . $user_id . "/user.xml";


include_once("createXML.php");

?>