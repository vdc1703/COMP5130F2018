<?php
	
	$filename = $vdcclass->image->basename($vdcclass->input->get_vars['file']);
	
	if ($vdcclass->funcs->is_file($filename, $vdcclass->info->root_path.$vdcclass->info->config['upload_path'], true) == false) {
		header("Content-Type: image/gif;");
		header("Content-Disposition: inline; filename=error404.gif;");
		
		readfile("{$vdcclass->info->root_path}css/images/error404.gif");
	} else {
		$file_info = $vdcclass->image->get_image_info($vdcclass->info->root_path.$vdcclass->info->config['upload_path'].$filename, true);
		
		header("Content-Type: {$file_info['mime']};");
		header("Content-Disposition: inline; filename={$filename};");
		
		if ($file_info['logs']['bandwidth'] > $vdcclass->info->config['max_bandwidth']) {
			readfile("{$vdcclass->info->root_path}css/images/error509.gif");
		} else {			
			if ($vdcclass->info->config['proxy_images'] == true) {
				$vdcclass->db->query("UPDATE `[1]` SET `bandwidth` = `bandwidth` + '[2]', `image_views` = `image_views` + 1 WHERE `filename` = '[3]';", array(MYSQL_FILE_LOGS_TABLE, $file_info['bits'], $filename));
			}
			
			readfile($vdcclass->info->root_path.$vdcclass->info->config['upload_path'].$filename);
		}
	}

	exit;

?>