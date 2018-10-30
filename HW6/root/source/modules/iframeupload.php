<?php
	
	$vdcclass->templ->templ_globals['upload_type'] = ((isset($vdcclass->input->get_vars['url']) == true) ? "url" : "std");
	
	$vdcclass->templ->templ_vars[] = array("BASE_URL" => $vdcclass->info->base_url);
	
	exit($vdcclass->templ->parse_template("tools", "iframe_uploader"));

?>