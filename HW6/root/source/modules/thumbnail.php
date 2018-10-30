<?php
	
	$filename = $vdcclass->image->basename($vdcclass->input->get_vars['file']);
	
	$file_info = $vdcclass->image->get_image_info($vdcclass->info->root_path.$vdcclass->info->config['upload_path'].$filename);

	header("Content-Type: image/{$file_info['mime']};");
	header("Content-Disposition: inline; filename={$file_info['thumbnail']};");

	if ($vdcclass->funcs->is_file($filename, $vdcclass->info->root_path.$vdcclass->info->config['upload_path'], true) == false) {
		readfile("{$vdcclass->info->root_path}css/images/error404.gif");
	} elseif ($vdcclass->funcs->file_exists($vdcclass->info->root_path.$vdcclass->info->config['upload_path'].$file_info['thumbnail']) == false) {
		readfile("{$vdcclass->info->root_path}css/images/no_thumbnail.png");
	} else {
		readfile($vdcclass->info->root_path.$vdcclass->info->config['upload_path'].$file_info['thumbnail']);
	}
	
	exit;

?>