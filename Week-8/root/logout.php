<?php
	require_once "./includes/vdc.php";
    
	if (setcookie("user_session", "session_delete", (time() - 60000)) == true) {
        $vdc_usersession_sessionid = $vdc->info->user_session['session_id'];
    	$vdc->db->query("DELETE FROM `tbl_session` WHERE `session_id` = '$vdc_usersession_sessionid';");
    	$vdc->info->is_user = $vdc->info->is_admin = $vdc->info->is_root = false;
    	echo "Logout sucess";
	} else {
		echo "Logout fail";
	}
	
?>