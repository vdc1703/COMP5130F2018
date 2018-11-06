<?php
	require_once "{$vdcclass->info->root_path}source/language/modules/fileinfo.php";
	
	header("Content-Type: text/plain;");
	header(sprintf("Content-Disposition: inline; filename=fileinfo_html_%s.txt;", mt_rand(1000, 9999)));
	
	if ($vdcclass->funcs->is_null($vdcclass->input->get_vars['file']) == true || $vdcclass->funcs->is_null($vdcclass->input->get_vars['lb_div']) == true) {
		exit($vdcclass->templ->lightbox_error($vdcclass->lang['001']));
	} elseif ($vdcclass->funcs->is_file($vdcclass->input->get_vars['file'], $vdcclass->info->root_path.$vdcclass->info->config['upload_path'], true) == false) {
		exit($vdcclass->templ->lightbox_error(sprintf($vdcclass->lang['002'], $vdcclass->image->basename($vdcclass->input->get_vars['file']))));
	} else {
		$filename = $vdcclass->image->basename($vdcclass->input->get_vars['file']);
		
		$file_info = $vdcclass->image->get_image_info($vdcclass->info->root_path.$vdcclass->info->config['upload_path'].$filename, true);
		
		$thumbnail_info = $vdcclass->image->get_image_info($vdcclass->info->root_path.$vdcclass->info->config['upload_path'].$file_info['thumbnail']);
		
		$vdcclass->templ->templ_vars[] = array(
			"FILENAME" => $filename,
			"MIME_TYPE" => $file_info['mime'],
			"IMAGE_WIDTH" => $file_info['width'],
			"IMAGE_HEIGHT" => $file_info['height'],
			"FILE_EXTENSION" => $file_info['extension'],
			"THUMBNAIL_HEIGHT" => $thumbnail_info['height'],
			"BANDWIDTH_USAGE" => $file_info['logs']['bandwidth'],
			"LIGHTBOX_ID" => $vdcclass->input->get_vars['lb_div'],
			"UPLOAD_PATH" => $vdcclass->info->config['upload_path'],
			"TOTAL_FILESIZE" => $vdcclass->image->format_filesize($file_info['bits']),
			"DATE_UPLOADED" => date($vdcclass->info->config['date_format'], $file_info['mtime']),
			"TOTAL_RATINGS" => $vdcclass->funcs->format_number($file_info['rating']['total_votes']),
			"BANDWIDTH_USAGE_FORMATTED" => $vdcclass->image->format_filesize($file_info['logs']['bandwidth']),
			"REAL_FILENAME" => (($vdcclass->funcs->is_null($file_info['logs']['original_filename']) == false) ? $file_info['logs']['original_filename'] : $filename),
		);
		
		exit($vdcclass->templ->parse_template("fileinfo"));
	}
	
?>