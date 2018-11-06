<?php
	$vdcclass = new stdClass;
	$vdcclass->info = new stdClass;
	$vdcclass->input = new stdClass;

	ini_set("log_errors", 1);
	ini_set("display_errors", 0);
	ini_set("memory_limit", "128M");
	ini_set("post_max_size", "128M");
	
	define("ROOT_PATH", sprintf("%s/", realpath(".")));

	$vdcclass->info->root_path = ROOT_PATH; // Backwards compatibility

	require_once "{$vdcclass->info->root_path}source/includes/catcherror.php";
	
	set_error_handler("error_handler", E_ALL);
	register_shutdown_function("shutdown_error_handler"); 
	
	if (is_file("{$vdcclass->info->root_path}source/includes/config.php") == true) {
		require_once "{$vdcclass->info->root_path}source/includes/config.php";
	}
	
	require_once "{$vdcclass->info->root_path}source/includes/database.php";
	require_once "{$vdcclass->info->root_path}source/includes/template.php";
	require_once "{$vdcclass->info->root_path}source/includes/functions.php";
	require_once "{$vdcclass->info->root_path}source/includes/imagemagick.php";
	require_once "{$vdcclass->info->root_path}source/includes/recaptchalib.php";

	$vdcclass->funcs = new vdcclass_core_functions();
	$vdcclass->templ = new vdcclass_template_engine();
	$vdcclass->image = new vdcclass_image_functions();
	$vdcclass->db = new vdcclass_mysql_driver();
	 
	require_once "{$vdcclass->info->root_path}source/includes/initindex.php";
	require_once "{$vdcclass->info->root_path}source/language/core/data.php";
	require_once "{$vdcclass->info->root_path}source/language/core/template.php";
	require_once "{$vdcclass->info->root_path}source/language/core/imagemagick.php";
	
	$vdcclass->input->get_vars = $vdcclass->funcs->clean_array($_GET);  
	$vdcclass->input->post_vars = $vdcclass->funcs->clean_array($_POST);
	$vdcclass->input->server_vars = $vdcclass->funcs->clean_array($_SERVER);
	$vdcclass->input->cookie_vars = $vdcclass->funcs->clean_array($_COOKIE);
	$vdcclass->input->session_vars = $vdcclass->funcs->clean_array($_SESSION);
	$vdcclass->input->file_vars = $_FILES; // $_FILES no longer is cleaned so backslashes not removed

	$vdcclass->info->version = COREVERSION;
	$vdcclass->info->init_time = $vdcclass->funcs->microtime_float();
	$vdcclass->info->page_url = $vdcclass->funcs->fetch_url(true, false, true);
	$vdcclass->info->base_url = $vdcclass->funcs->fetch_url(false, false, false);
	$vdcclass->info->current_page = round(($vdcclass->input->get_vars['page'] >= 1) ? $vdcclass->input->get_vars['page'] : 1);
	$vdcclass->info->script_path = ((($path = dirname($vdcclass->input->server_vars['php_self'])) == "/") ? $path : "{$path}/");
			
	$vdcclass->image->manipulator = ((USE_IMAGICK_LIBRARY == true) ? "imagick" : ((USE_GD_LIBRARY == true || USE_GD2_LIBRARY == true) ? "gd" : $vdcclass->templ->fatal_error($vdcclass->lang['7414'])));
	 
	if (version_compare(PHPVERSION, "5.0.0", "<") == true) { 
		$vdcclass->templ->fatal_error(sprintf($vdcclass->lang['9553'], $vdcclass->info->version));
	}	
	
	if (version_compare(PHPVERSION, "5.1.0", ">=") == true) { 
		if (ini_get("date.timezone") == false) {
			date_default_timezone_set(DEFAULT_TIME_ZONE);
		}
	}
	
	if (MONITOR_CPU_LOAD == true && IS_WINDOWS_OS == false) {
		$load_average = sys_getloadavg();
		
		if ($load_average['0'] > MAX_CPU_LOAD) {
			// Header and exit taken right from the PHP Manual
			
			header("HTTP/1.1 503 Too busy, try again later");
   			output_fatal_error("Server too busy. Please try again later.");
		}
	}
	
	if (ENABLE_GZHANDLER_COMPRESSION == true) {
		if (ini_get("zlib.output_compression") == false) {
			ob_start(array("ob_gzhandler", GZHANDLER_COMPRESSION_LEVEL));
		}
	}
	
	if ($vdcclass->info->site_installed == false) {
		if ($vdcclass->image->basename($vdcclass->input->server_vars['php_self']) !== "install.php") {
			$vdcclass->templ->page_title = $vdcclass->lang['6897'];
			$vdcclass->templ->message($vdcclass->lang['5435'], true);
		}
	} else {
		if (isset($vdcclass->input->get_vars['rurl']) == true) {
			header(sprintf("Location: %s", base64_decode($vdcclass->input->get_vars['rurl']))); exit;
		}
		
		$vdcclass->db->query("UPDATE `[1]` SET `cache_value` = `cache_value` + 1 WHERE `cache_id` = 'page_views';", array(MYSQL_SITE_CACHE_TABLE)); 

		$sql = $vdcclass->db->query("SELECT * FROM `[1]`;", array(MYSQL_SITE_CACHE_TABLE));
		while ($row = $vdcclass->db->fetch_array($sql)) {
			$vdcclass->info->site_cache[$row['cache_id']] = $row['cache_value'];
		}

		$sql = $vdcclass->db->query("SELECT * FROM `[1]`;", array(MYSQL_SITE_SETTINGS_TABLE));
		while ($row = $vdcclass->db->fetch_array($sql)) {
			$vdcclass->info->config[$row['config_key']] = $row['config_value'];
		}
		
		// Robot logging can slow large hosts down.
		
		if (LOG_ROBOTS == true) {
			$sql = $vdcclass->db->query("SELECT * FROM `[1]`;", array(MYSQL_ROBOT_INFO_TABLE));
			
			while ($row = $vdcclass->db->fetch_array($sql)) {
				if (stripos(html_entity_decode($vdcclass->input->server_vars['http_user_agent']), $row['preg_match']) !== false) {
					$vdcclass->db->query("INSERT INTO `[7]` (`robot_id`, `page_indexed`, `time_indexed`, `ip_address`, `user_agent`, `http_referer`) VALUES ('[1]', '[2]', '[3]', '[4]', '[5]', '[6]');", array($row['robot_id'], str_replace($vdcclass->info->base_url, NULL, $vdcclass->info->page_url), time(), $vdcclass->input->server_vars['remote_addr'], $vdcclass->input->server_vars['http_user_agent'], $vdcclass->input->server_vars['http_referer'], MYSQL_ROBOT_LOGS_TABLE));
					$vdcclass->info->is_robot = true; 
				}
			}
		}

		if (isset($vdcclass->input->cookie_vars['vdc_user_session']) == true && $vdcclass->info->is_robot == false) {
			$vdcclass->info->user_session = unserialize(stripslashes(base64_decode($vdcclass->input->cookie_vars['vdc_user_session'])));
			
			$sql = $vdcclass->db->query("SELECT * FROM `[1]` WHERE `user_id` = '[2]' AND `session_id` = '[3]' AND `ip_address` = '[4]' LIMIT 1;", array(MYSQL_USER_SESSIONS_TABLE, $vdcclass->info->user_session['user_id'], $vdcclass->info->user_session['session_id'], $vdcclass->input->server_vars['remote_addr']));
			
			if ($vdcclass->db->total_rows($sql) === 1) {
				$sql = $vdcclass->db->query("SELECT * FROM `[1]` WHERE `user_id` = '[2]' AND `ip_address` = '[3]' LIMIT 1;", array(MYSQL_USER_INFO_TABLE, $vdcclass->info->user_session['user_id'], $vdcclass->input->server_vars['remote_addr']));
				
				if ($vdcclass->db->total_rows($sql) === 1) {
					$vdcclass->info->user_data = $vdcclass->db->fetch_array($sql); 
					
					$vdcclass->info->is_user = (($vdcclass->funcs->is_null($vdcclass->info->user_data['username']) == false) ? true : false); 
					$vdcclass->info->is_root = (($vdcclass->info->user_data['user_group'] === "root_admin" && $vdcclass->info->is_user == true) ? true : false);
					$vdcclass->info->is_admin = (($vdcclass->info->is_root == true || $vdcclass->info->user_data['user_group'] === "normal_admin" && $vdcclass->info->is_user == true) ? true : false);
				}
			}
		}
		
		if ($vdcclass->info->is_user == true) {
			$vdcclass->info->config['max_filesize'] = $vdcclass->info->config['user_max_filesize'];
			$vdcclass->info->config['max_bandwidth'] = $vdcclass->info->config['user_max_bandwidth'];
			$vdcclass->info->config['file_extensions'] = $vdcclass->info->config['user_file_extensions'];
			
			unset($vdcclass->info->config['user_file_extensions'], $vdcclass->info->config['user_max_filesize'], $vdcclass->info->config['user_max_bandwidth']);
		}

		$vdcclass->info->config['file_extensions'] = explode(",", $vdcclass->info->config['file_extensions']);

		if ($vdcclass->info->is_root == false) {
			
			//
			// Using preformed matches seems easier than regular expression
			// although the site owner will still think that it is regex. 
			// 
			// Match types:
			//		1. 123.123.*.*
			//		2. 123.123.*.123
			//		3. 123.123.123.*
			//		
			
			$ip_parts = explode(".", $vdcclass->input->server_vars['remote_addr'], 4);

			$sql = $vdcclass->db->query("SELECT `ban_value` FROM `[1]` WHERE `ban_type` = '1' AND (`ban_value` = '[2]' OR `ban_value` = '[3]' OR `ban_value` = '[4]' OR `ban_value` = '[5]') LIMIT 1;", array(MYSQL_BAN_FILTER_TABLE, $vdcclass->input->server_vars['remote_addr'], "{$ip_parts['0']}.{$ip_parts['1']}.*.*", "{$ip_parts['0']}.{$ip_parts['1']}.*.{$ip_parts['3']}", "{$ip_parts['0']}.{$ip_parts['1']}.{$ip_parts['2']}.*"));
			
			if ($vdcclass->db->total_rows($sql) == 1) {
				$vdcclass->templ->error(sprintf($vdcclass->lang['4648'], $vdcclass->input->server_vars['remote_addr'], $vdcclass->info->config['site_name']), true); 	
			} else {
				if ($vdcclass->info->is_user == true) {
					$sql = $vdcclass->db->query("SELECT * FROM `[1]` WHERE `ban_type` = '2' AND `ban_value` = '[2]' LIMIT 1;", array(MYSQL_BAN_FILTER_TABLE, $vdcclass->info->user_data['username']));
					
					if ($vdcclass->db->total_rows($sql) == 1) {
						$vdcclass->templ->error(sprintf($vdcclass->lang['1188'], $vdcclass->info->user_data['username'], $vdcclass->info->config['site_name']), true);	
					}
				}
			}
		}
	}

?>