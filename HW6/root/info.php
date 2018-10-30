<?php

	require_once "./source/includes/data.php";
	require_once "{$vdcclass->info->root_path}source/language/info.php";

	$vdcclass->templ->page_title = sprintf($vdcclass->lang['001'], $vdcclass->info->config['site_name']);

	switch ($vdcclass->input->get_vars['act']) {
		case "about_us":
			$vdcclass->templ->page_title .= $vdcclass->lang['002'];
			
			
			$vdcclass->templ->output("info", "about_us_page");
			break;
		default: 
			$vdcclass->templ->error($vdcclass->lang['005'], true);
	}

?>