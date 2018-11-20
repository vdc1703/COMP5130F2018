<?php	
	require_once "./includes/vdc.php";
	require_once "{$root_path}language/viewer.php";
	
	if ($vdc->funcs->is_null($vdc->input->get_vars['file']) == true) {
		$vdc->templ->error($vdc->lang['002'], true);
	} elseif ($vdc->funcs->is_file($vdc->input->get_vars['file'], $root_path.$vdc->info->config['upload_path'], true) == false) {
		$vdc->templ->error(sprintf($vdc->lang['003'], $vdc->image->basename($vdc->input->get_vars['file'])), true);
	} elseif ($vdc->image->is_image($root_path.$vdc->info->config['upload_path'].$vdc->input->get_vars['file']) == false) {
		$vdc->templ->error(sprintf($vdc->lang['004'], $vdc->image->basename($vdc->input->get_vars['file'])), true);
	} else {
		$img_name = $vdc->image->basename($vdc->input->get_vars['file']);
		
		$file_info = $vdc->image->get_image_info($root_path.$vdc->info->config['upload_path'].$img_name, true);
		
		$original_img_name = (($vdc->funcs->is_null($file_info['logs']['original_img_name']) == false) ? $file_info['logs']['original_img_name'] : $img_name);
		
		$vdc->templ->page_title = sprintf($vdc->lang['001'], $vdc->info->config['site_name'], $original_img_name);
		
		if ($vdc->input->get_vars['task'] == "rate_it" && isset($vdc->input->post_vars['rating_id']) == true) {
			$vdc->templ->templ_globals['new_file_rating'] = true;
			
			if (in_array($vdc->input->server_vars['remote_addr'], explode("|", $file_info['rating']['voted_by'])) == true) {
				$new_rating_html = $failed_image_rating = $vdc->templ->error($vdc->lang['005'], false);
			} else {
				if ($vdc->funcs->is_null($file_info['rating']['rating_id']) == true) {
//					$vdc->db->query("INSERT INTO `[1]` (`img_name`, `total_rating`, `total_votes`, `voted_by`) VALUES ('[2]', '0', '0', '');", array(MYSQL_FILE_RATINGS_TABLE, $img_name));
				}
				
//				$vdc->db->query("UPDATE `[1]` SET `total_rating` = `total_rating` + '[2]', `total_votes` = `total_votes` + 1, `voted_by` = '[3]' WHERE `img_name` = '[4]';", array(MYSQL_FILE_RATINGS_TABLE, $vdc->input->post_vars['rating_id'], "{$file_info['rating']['voted_by']}|{$vdc->input->server_vars['remote_addr']}", $img_name));
				
				$new_rating_html = $vdc->templ->message(sprintf($vdc->lang['006'], $img_name), false);
			}
		}
		
		$vdc->templ->templ_globals['file_info'] = $file_info;
		$image_size = $vdc->image->scaleby_maxwidth($img_name, 940);
		
		$vdc->templ->templ_vars[] = array(
			 "FILENAME" => $img_name,
			 "MIME_TYPE" => $file_info['mime'],
			 "IMAGE_WIDTH" => $file_info['width'],
			 "REAL_FILENAME" => $original_img_name,	
			 "NEW_RATING_HTML" => $new_rating_html,
			 "IMAGE_HEIGHT" => $file_info['height'],
			 "HIDDEN_COMMENT" => $file_info['comment'],
			 "FILE_EXTENSION" => $file_info['extension'],
			 "UPLOAD_PATH" => $vdc->info->config['upload_path'],
			 "FILE_LINKS" => $vdc->templ->file_results($img_name),
			 "TOTAL_FILESIZE" => $vdc->image->format_filesize($file_info['bits']),
			 "IMAGE_VIEWS" => $vdc->funcs->number_format($file_info['logs']['image_views']),
			 "DATE_UPLOADED" => date($vdc->info->config['date_format'], $file_info['mtime']),
			 "IMAGE_RESIZE" => (($vdc->funcs->is_null($image_size['h']) == false) ? "width: {$image_size['w']}px; height: {$image_size['h']}px;" : NULL),
			 "TOTAL_RATINGS" => $vdc->funcs->number_format((isset($new_rating_html) == true && isset($failed_image_rating) == false) ? ($file_info['rating']['total_votes'] + 1) : $file_info['rating']['total_votes']),
	  	);
		
		$vdc->templ->output("viewer");
	}
	
?>