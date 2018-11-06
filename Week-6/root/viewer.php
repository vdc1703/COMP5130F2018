<?php	
	require_once "./source/includes/data.php";
	require_once "{$vdcclass->info->root_path}source/language/viewer.php";
	
	if ($vdcclass->funcs->is_null($vdcclass->input->get_vars['file']) == true) {
		$vdcclass->templ->error($vdcclass->lang['002'], true);
	} elseif ($vdcclass->funcs->is_file($vdcclass->input->get_vars['file'], $vdcclass->info->root_path.$vdcclass->info->config['upload_path'], true) == false) {
		$vdcclass->templ->error(sprintf($vdcclass->lang['003'], $vdcclass->image->basename($vdcclass->input->get_vars['file'])), true);
	} elseif ($vdcclass->image->is_image($vdcclass->info->root_path.$vdcclass->info->config['upload_path'].$vdcclass->input->get_vars['file']) == false) {
		$vdcclass->templ->error(sprintf($vdcclass->lang['004'], $vdcclass->image->basename($vdcclass->input->get_vars['file'])), true);
	} else {
		$filename = $vdcclass->image->basename($vdcclass->input->get_vars['file']);
		
		$file_info = $vdcclass->image->get_image_info($vdcclass->info->root_path.$vdcclass->info->config['upload_path'].$filename, true);
		
		$original_filename = (($vdcclass->funcs->is_null($file_info['logs']['original_filename']) == false) ? $file_info['logs']['original_filename'] : $filename);
		
		$vdcclass->templ->page_title = sprintf($vdcclass->lang['001'], $vdcclass->info->config['site_name'], $original_filename);
	
		if ($vdcclass->funcs->is_null($vdcclass->input->server_vars['http_referer']) == false && stripos($vdcclass->input->server_vars['http_referer'], $vdcclass->info->base_url) === false) {
			$new_viewer_click = $vdcclass->db->query("UPDATE `[1]` SET `viewer_clicks` = `viewer_clicks` + 1 WHERE `filename` = '[2]';", array(MYSQL_FILE_STORAGE_TABLE, $filename));
		}
		
		if ($vdcclass->input->get_vars['act'] == "rate_it" && isset($vdcclass->input->post_vars['rating_id']) == true) {
			$vdcclass->templ->templ_globals['new_file_rating'] = true;
			
			if (in_array($vdcclass->input->server_vars['remote_addr'], explode("|", $file_info['rating']['voted_by'])) == true) {
				$new_rating_html = $failed_image_rating = $vdcclass->templ->error($vdcclass->lang['005'], false);
			} else {
				if ($vdcclass->funcs->is_null($file_info['rating']['rating_id']) == true) {
					$vdcclass->db->query("INSERT INTO `[1]` (`filename`, `total_rating`, `total_votes`, `voted_by`) VALUES ('[2]', '0', '0', '');", array(MYSQL_FILE_RATINGS_TABLE, $filename));
				}
				
				$vdcclass->db->query("UPDATE `[1]` SET `total_rating` = `total_rating` + '[2]', `total_votes` = `total_votes` + 1, `voted_by` = '[3]' WHERE `filename` = '[4]';", array(MYSQL_FILE_RATINGS_TABLE, $vdcclass->input->post_vars['rating_id'], "{$file_info['rating']['voted_by']}|{$vdcclass->input->server_vars['remote_addr']}", $filename));
				
				$new_rating_html = $vdcclass->templ->message(sprintf($vdcclass->lang['006'], $filename), false);
			}
		}
		
		$vdcclass->templ->templ_globals['file_info'] = $file_info;
		$image_size = $vdcclass->image->scaleby_maxwidth($filename, 940);
		
		$vdcclass->templ->templ_vars[] = array(
			 "FILENAME" => $filename,
			 "MIME_TYPE" => $file_info['mime'],
			 "IMAGE_WIDTH" => $file_info['width'],
			 "REAL_FILENAME" => $original_filename,	
			 "NEW_RATING_HTML" => $new_rating_html,
			 "IMAGE_HEIGHT" => $file_info['height'],
			 "HIDDEN_COMMENT" => $file_info['comment'],
			 "FILE_EXTENSION" => $file_info['extension'],
			 "UPLOAD_PATH" => $vdcclass->info->config['upload_path'],
			 "FILE_LINKS" => $vdcclass->templ->file_results($filename),
			 "TOTAL_FILESIZE" => $vdcclass->image->format_filesize($file_info['bits']),
			 "IMAGE_VIEWS" => $vdcclass->funcs->format_number($file_info['logs']['image_views']),
			 "DATE_UPLOADED" => date($vdcclass->info->config['date_format'], $file_info['mtime']),
			 "IMAGE_RESIZE" => (($vdcclass->funcs->is_null($image_size['h']) == false) ? "width: {$image_size['w']}px; height: {$image_size['h']}px;" : NULL),
			 "VIEWER_CLICKS" => $vdcclass->funcs->format_number((isset($new_viewer_click) == false) ? $file_info['sinfo']['viewer_clicks'] : ($file_info['sinfo']['viewer_clicks'] + 1)),
			 "TOTAL_RATINGS" => $vdcclass->funcs->format_number((isset($new_rating_html) == true && isset($failed_image_rating) == false) ? ($file_info['rating']['total_votes'] + 1) : $file_info['rating']['total_votes']),
	  	);
		
		$vdcclass->templ->output("viewer");
	}
	
?>