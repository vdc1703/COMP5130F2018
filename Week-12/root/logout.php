<?php
	require_once "./includes/vdc.php";
    
   if(session_destroy()) {
      header("Location: index.php");
   }
	
?>