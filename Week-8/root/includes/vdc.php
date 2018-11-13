<?php
	ini_set("log_errors", 1);
	ini_set("display_errors", 0);
	ini_set("memory_limit", "128M");
	ini_set("post_max_size", "128M"); 

    session_start();
    
	define("IS_HTTPS_REQUEST", isset($_SERVER['HTTPS']));
	define("DEFAULT_SOCKET_TIMEOUT", 2);
	define("REMOTE_FOPEN_ENABLED", ini_get("allow_url_fopen")); 
    
	$vdc = new stdClass;
	$vdc->info = new stdClass;
	$vdc->input = new stdClass;
    
	define("ROOT_PATH", sprintf("%s/", realpath(".")));
    
    $root_path = ROOT_PATH;        
    
	require_once "{$root_path}includes/config.php";
	require_once "{$root_path}includes/database.php";
	require_once "{$root_path}includes/functions.php";

    
	$vdc->funcs = new vdc_core_functions();
	$vdc->db = new vdc_mysql_driver();     
	  
	$vdc->input->get_vars = $vdc->funcs->clean_array($_GET);  
	$vdc->input->post_vars = $vdc->funcs->clean_array($_POST);
	$vdc->input->server_vars = $vdc->funcs->clean_array($_SERVER);
	$vdc->input->cookie_vars = $vdc->funcs->clean_array($_COOKIE);
	$vdc->input->session_vars = $vdc->funcs->clean_array($_SESSION);
	$vdc->input->file_vars = $_FILES;
    
	$vdc->info->page_url = $vdc->funcs->fetch_url(true, false, true);
	$vdc->info->base_url = $vdc->funcs->fetch_url(false, false, false);
	$vdc->info->current_page = round(($vdc->input->get_vars['page'] >= 1) ? $vdc->input->get_vars['page'] : 1);
	$vdc->info->script_path = ((($path = dirname($vdc->input->server_vars['php_self'])) == "/") ? $path : "{$path}/");
	
	if (isset($vdc->input->get_vars['rurl']) == true) {
		header(sprintf("Location: %s", base64_decode($vdc->input->get_vars['rurl']))); exit;
	}
		

	if (isset($vdc->input->cookie_vars['vdc_user_session']) == true) {
		$vdc->info->user_session = unserialize(stripslashes(base64_decode($vdc->input->cookie_vars['vdc_user_session'])));
        
        $vdc_session_userid = $vdc->info->user_session['user_id'];
        $vdc_session_sessionid = $vdc->info->user_session['session_id'];
        $vdc_session_remoteaddr = $vdc->info->user_session['remote_addr'];
		
		$sql = $vdc->db->query("SELECT * FROM `tbl_session` WHERE `user_id` = '$vdc_session_userid' AND `session_id` = '$vdc_session_sessionid' AND `ip_address` = '$vdc_session_remoteaddr' LIMIT 1;");
		
		if ($vdc->db->total_rows($sql) === 1) {
			$sql = $vdc->db->query("SELECT * FROM `tbl_user` WHERE `user_id` = '$vdc_session_userid' AND `ip_address` = '$vdc_session_remoteaddr' LIMIT 1;");
			
			if ($vdc->db->total_rows($sql) === 1) {
				$vdc->info->user_data = $vdc->db->fetch_array($sql); 
				
				$vdc->info->is_user = (($vdc->funcs->is_null($vdc->info->user_data['username']) == false) ? true : false); 
				$vdc->info->is_root = (($vdc->info->user_data['user_group'] === "admin" && $vdc->info->is_user == true) ? true : false);
				$vdc->info->is_admin = (($vdc->info->is_root == true || $vdc->info->user_data['user_group'] === "normal_admin" && $vdc->info->is_user == true) ? true : false);
			}
		}
	}
?>