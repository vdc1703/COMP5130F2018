<?php
	
	require_once "./source/includes/data.php";
	require_once "{$vdcclass->info->root_path}source/language/home.php";
	
	// Module file loader
	if (isset($vdcclass->input->get_vars['module']) == true) {
		$module_name = $vdcclass->image->basename($vdcclass->input->get_vars['module']);
		
		if ($vdcclass->funcs->file_exists("{$vdcclass->info->root_path}source/modules/{$module_name}.php") == true) {
			require_once "{$vdcclass->info->root_path}source/modules/{$module_name}.php"; 
			
			exit;	
		}
	}
	
	// Upload progress bar
	if ($vdcclass->input->get_vars['act'] == "upload_in_progress") {
		exit($vdcclass->templ->parse_template("home", "upload_in_progress_lightbox"));
	}
	
	// Random Image
	if (isset($vdcclass->input->get_vars['do_random']) == true) {
		$sql = $vdcclass->db->query("SELECT * FROM `[1]` WHERE `is_private` = '0' AND `gallery_id` = '0' ORDER BY RAND() LIMIT 1;", array(MYSQL_FILE_STORAGE_TABLE));
		
		if ($vdcclass->db->total_rows($sql) !== 1) {
			$vdcclass->templ->error($vdcclass->lang['006'], true);
		} else {	
			$file_info = $vdcclass->db->fetch_array($sql);
			
			header("Location: {$vdcclass->info->base_url}viewer.php?is_random={$file_info['file_id']}&file={$file_info['filename']}");
			
			exit;
		}
	}

	// Disable uploading? -- Does not apply to administrators
	if ($vdcclass->info->config['uploading_disabled'] == true && $vdcclass->info->is_admin == false) {
		$vdcclass->templ->page_title = $vdcclass->lang['005'];
		
		$vdcclass->templ->error($vdcclass->lang['004'], true);
	}

	// Disable uploading for Guests only?
	if ($vdcclass->info->config['useronly_uploading'] == true && $vdcclass->info->is_user == false) {
		$vdcclass->templ->page_title = sprintf($vdcclass->lang['001'], $vdcclass->info->config['site_name']);
		
		$vdcclass->templ->error($vdcclass->lang['007'], true);
	}
	
	// Upload Layout Preview Lightbox
	if (isset($vdcclass->input->get_vars['layoutprev']) == true) {
		$vdcclass->templ->templ_vars[] = array(
			"LIGHTBOX_ID" => $vdcclass->input->get_vars['lb_div'],
			"IMAGE_HEIGHT" => (($vdcclass->input->get_vars['layoutprev'] == "std") ? 280 : 454),
			"PREVIEW_TYPE" => (($vdcclass->input->get_vars['layoutprev'] == "std") ? "std" : "bx"),
		);
		
		exit($vdcclass->templ->parse_template("home", "upload_layout_preview_lightbox"));
	}
		
	// Normal and URL upload page
	$last_extension = end($vdcclass->info->config['file_extensions']);
	
	foreach ($vdcclass->info->config['file_extensions'] as $this_extension) {
		$file_extensions .= sprintf((($last_extension == $this_extension) ? "{$vdcclass->lang['003']} .%s" : ".%s, "), strtoupper($this_extension));
	}
	
	/* "Upload To" addon developed by Josh D. of www.hostmine.us */
	if ($vdcclass->info->is_user == true) {
		$sql = $vdcclass->db->query("SELECT * FROM `[1]` WHERE `gallery_id` = '[2]' LIMIT 50;", array(MYSQL_GALLERY_ALBUMS_TABLE, $vdcclass->info->user_data['user_id']));
		
		if ($vdcclass->db->total_rows($sql) < 1) {
			$vdcclass->templ->templ_globals['hide_upload_to'] = true;
		} else {
			$template_id = ((isset($vdcclass->input->get_vars['url']) == false) ? "normal_upload_page" : "url_upload_page");
			
			while ($row = $vdcclass->db->fetch_array($sql)) {
				$vdcclass->templ->templ_globals['get_whileloop'] = true;
				
				$vdcclass->templ->templ_vars[] = array(
					"ALBUM_ID" => $row['album_id'],
					"ALBUM_NAME" => $row['album_title'],
				);
				
				$vdcclass->templ->templ_globals['albums_pulldown_whileloop'] .= $vdcclass->templ->parse_template("home", $template_id);
				unset($vdcclass->templ->templ_vars, $vdcclass->templ->templ_globals['get_whileloop']);
			}
		}
	}

	$vdcclass->templ->templ_vars[] = array(
		"FILE_EXTENSIONS" => $file_extensions,
		"SITE_NAME" => $vdcclass->info->config['site_name'],
		"MAX_RESULTS" => $vdcclass->info->config['max_results'],
		"MAX_FILESIZE" => $vdcclass->image->format_filesize($vdcclass->info->config['max_filesize']),
		"BOXED_UPLOAD_YES" => (($vdcclass->info->user_data['upload_type'] == "boxed") ? "checked=\"checked\"" : NULL),
		"STANDARD_UPLOAD_YES" => (($vdcclass->info->user_data['upload_type'] == "standard" || $vdcclass->info->is_user == false) ? "checked=\"checked\"" : NULL),
	);
	
	if ($vdcclass->funcs->is_null($vdcclass->input->get_vars['url']) == true) {
		$vdcclass->templ->page_title = sprintf($vdcclass->lang['001'], $vdcclass->info->config['site_name']);
		$vdcclass->templ->output("home", "normal_upload_page");
	} else {
		$vdcclass->templ->page_title = sprintf($vdcclass->lang['002'], $vdcclass->info->config['site_name']);
		$vdcclass->templ->output("home", "url_upload_page");
	}
	
?>