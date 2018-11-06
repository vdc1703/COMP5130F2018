<?php

	
	class vdcclass_template_engine
	{
		// Class Initialization Method
		function __construct() { 
			global $vdcclass; $this->vdcclass = &$vdcclass; 
			$this->templ_vars = $this->templ_globals = array();
			
			$this->cif_check = array(
				"thefile" => "",
				"thefoot" => "",
				"thematch" => "",
				"theurl" => "",
				"therror" => "",
			);
		}
	
		function output($filename = NULL, $template = NULL)
		{
			$page_header = $this->page_header();
			$page_footer = $this->page_footer();
			
			$copyright = sprintf("\n%s", base64_decode($this->cif_check['thefoot']));
			$html = ((isset($this->html) == true) ? $this->html : $this->parse_template($filename, $template));
			
			$template_html = sprintf("%s%s%s%s", $page_header, $html, $page_footer, $copyright);
		
			$this->vdcclass->db->close(); 
			
			exit($this->tidy_html($template_html)); 
		}
		
		function parse_template($filename, $template = NULL)
		{
			if ($this->vdcclass->funcs->file_exists("{$this->vdcclass->info->root_path}source/public_html/{$filename}.tpl") == false) {
				$this->fatal_error("The template file 'source/public_html/{$filename}.tpl' does not exist.");
			} else {
				$html2parse = $this->vdcclass->funcs->read_file("{$this->vdcclass->info->root_path}source/public_html/{$filename}.tpl");
				
				if ($this->vdcclass->funcs->is_null($template) == false) {
					if (preg_match("#<template id=\"{$template}\">(.*)</template>#Usi", $html2parse, $template_matches) == true) {
						$html2parse = $template_matches['1'];
					} else {
						$this->fatal_error("Template ID '{$template}' does not exist in the template file 'source/public_html/{$filename}.tpl'.");	
					}
				}
				
				if (is_array($this->templ_vars) == true && $this->vdcclass->funcs->is_null($this->templ_vars) == false) {
					foreach ($this->templ_vars as $index => $variable_block) {
						foreach ($variable_block as $variable => $replacement) {
							if (stripos($html2parse, "<# {$variable} #>") !== false) {
								$html2parse = str_replace("<# {$variable} #>", $replacement, $html2parse);
								unset($this->templ_vars[$index][$variable]);
							}
						}
					}
				}
				
				$html2parse = preg_replace(array('#<([\?%])=?.*?\1>#s', '#<script\s+language\s*=\s*(["\']?)php\1\s*>.*?</script\s*>#s', '#<\?php(?:\r\n?|[ \n\t]).*?\?>#s', "#<!-- (BEGIN|END): (.*) -->#", "#<\\$(.*?)\\$>#Us"), NULL, $html2parse);
				$html2parse = ((md5($filename) == $this->cif_check['thefile']) ? $this->bug_fix_56941($html2parse) : $html2parse);
				
				if (strpos($html2parse, "<foreach=") == true) {
					$parse_html2php = true;
					
					$html2parse = preg_replace("#</endforeach>#", '<?php } ?>', $html2parse);
					$html2parse = preg_replace("#<foraech=\"([^\n]+)\">#", '<?php foreach ($1) { ?>', $html2parse);
				}
				
				if (strpos($html2parse, "<if=") ==  true) {
					$parse_html2php = true;
					
					$html2parse = preg_replace("#</endif>#", '<?php } ?>', $html2parse);
					$html2parse = preg_replace("#<else>#", '<?php } else { ?>', $html2parse);
					$html2parse = preg_replace("#<if=\"([^\n]+)\">#", '<?php if ($1) { ?>', $html2parse);
					$html2parse = preg_replace("#<elseif=\"([^\n]+)\">#", '<?php } elseif ($1) { ?>', $html2parse);
				}
				
				if (strpos($html2parse, "<php>") == true) {
					$parse_html2php = true;
					
					$html2parse = preg_replace("#</php>#", '?>', $html2parse);
					$html2parse = preg_replace("#<php>#", '<?php', $html2parse);
				}
				
				if (strpos($html2parse, "<while id=") == true) {
					preg_match_all("#<while id=\"([^\s]+)\">(.*)</endwhile>#Us", $html2parse, $whileloop_matches);
					
					foreach ($whileloop_matches['1'] as $id => $ident) {
						$doreplace = ((count($whileloop_matches['1']) > 1) ? $this->templ_globals['get_whileloop'][$ident] : $this->templ_globals['get_whileloop']);
						$html2parse = (($doreplace == false) ? preg_replace("#<while id=\"{$ident}\">(.*)</endwhile>#Us", $this->templ_globals[$ident], $html2parse) : $whileloop_matches['2'][$id]);
					}
				}	
				
				if ($parse_html2php == true) {
					$vdcclass = $this->vdcclass;
					
					ob_start(); 
					
					eval("?>{$html2parse}");
					
					$html2parse = ob_get_clean();
				}	
				
				return $html2parse;
			}
		}
		
		function tidy_html($html) 
		{
			if (ENABLE_TEMPLATE_TIDY_HTML == true) {
				$tidy_config = array( 
					"wrap" => 0, 
					"tab-size" => 4,
					"clean" => true, 
					"tidy-mark" => true,
					"indent-cdata" => true,
					"force-output" => true,
					"output-xhtml" => true, 
					"merge-divs" => false,
					"merge-spans" => false,
					"sort-attributes" => true,
				); 
				
				$html = tidy_parse_string($html, $tidy_config, "UTF8"); 
				
				$html->cleanRepair();
			}
			
			return trim($html);
		}

		function page_header()
		{
			if (isset($this->page_header) == false) {
				$this->templ_vars[] = array(
					"VERSION" => $this->vdcclass->info->version,
					"BASE_URL" => $this->vdcclass->info->base_url,
					"SITE_NAME" => $this->vdcclass->info->config['site_name'],
					"USERNAME" => $this->vdcclass->info->user_data['username'],
					"RETURN_URL" => base64_encode((binary)$this->vdcclass->info->page_url),
					"PAGE_TITLE" => ((isset($this->page_title) == true) ? $this->page_title : $this->vdcclass->info->config['site_name']),
				);
				
				return $this->parse_template("page_header");
			} else {
				return $this->page_header;
			}
		}

		function page_footer()
		{
			if ($this->vdcclass->funcs->is_null($this->page_footer) == true) {
				$this->templ_vars[] = array(
					"GOOGLE_ANALYTICS_ID" => $this->vdcclass->info->config['google_analytics'],
					"PAGE_LOAD" => substr(($this->vdcclass->funcs->microtime_float() - $this->vdcclass->info->init_time), 0, 5),	
					"TOTAL_PAGE_VIEWS" => ((isset($this->vdcclass->info->site_cache['page_views']) == false) ? $this->vdcclass->lang['6697'] : $this->vdcclass->funcs->format_number($this->vdcclass->info->site_cache['page_views'])),
				);
				
				return $this->parse_template("page_footer");
			} else {
				return $this->page_footer;
			}
		}

		function lightbox_error($error, $output_html = false)
		{
			$this->templ_vars[] = array("ERROR" => $error);
			
			$function = (($output_html == true) ? "output" : "parse_template");
			
			return $this->$function("global", "global_lightbox_warning");
		}
		
		function error($error, $output_html = true)
		{
			$this->templ_vars[] = array("ERROR" => $error);
			
			$function = (($output_html == true) ? "output" : "parse_template");
			
			return $this->$function("global", "global_warning_box");
		}
		
		function message($message, $output_html = false)
		{
			$this->templ_vars[] = array("MESSAGE" => $message);
			
			$function = (($output_html == true) ? "output" : "parse_template");
			
			return $this->$function("global", "global_message_box");
		}
		
		function fatal_error($error) {
			exit("\t\t\t<!DOCTYPE HTML PUBLIC \"-//IETF//DTD HTML 2.0//EN\">
			<html>
				<head>
					<meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\" />
					<title>Fatal Error (Powered by ChuongVu Images Server)</title>
					<style type=\"text/css\">
					    * { font-size: 100%; margin: 0; padding: 0; }
						body { font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 75%; margin: 10px; background: #FFFFFF; color: #000000; }
						a:link, a:visited { text-decoration: none; color: #005fa9; background-color: transparent; }
						a:active, a:hover { text-decoration: underline; }						
						textarea { font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 11px; border: 1px dashed #000000; background: #FFFFFF; padding: 5px; background: #f4f4f4; }
					</style>
				</head>
				<body>
					<p><strong>Fatal Error</strong>
					<br /><br />
					{$error}
					<br /><br />
					Application Exited</p>
				</body>
			</html>"); 
		}	
		
		function bug_fix_56941($html)
		{
            $skipcheck = true;
			
			return $html;
		}
		
		/* ============================================================================================
		The following functions are a few basic global implementations of the template engine. They are 
		located in this file because there is not really any other place that makes sense to place them. 
		============================================================================================ */

		function pagelinks($base_url, $total_results)
		{ 
			$base_url .= ((strpos($base_url, "?") === false) ? "?" : "&amp;");
			$total_pages = ceil($total_results / $this->vdcclass->info->config['max_results']);
			$current_page = (($this->vdcclass->info->current_page > $total_pages) ? $total_pages : $this->vdcclass->info->current_page); 
			
			if ($total_pages < 2) {
				$template_html = $this->vdcclass->lang['3384'];
			} else {
				$template_html = (($current_page > 1) ? sprintf($this->vdcclass->lang['3484'], sprintf("%spage=%s", $base_url, ($this->vdcclass->info->current_page - 1))) : NULL);
				
				for ($i = 1; $i <= $total_pages; $i++) {
					if ($i == $current_page) {
						$template_html .= sprintf("<strong>%s</strong>", $this->vdcclass->funcs->format_number($i));
					} else {
						if ($i < ($current_page - 5)) { continue; }
						if ($i > ($current_page + 5)) { break; }
						
						$template_html .= sprintf("<a href=\"%spage=%s\">%s</a>", $base_url, $i, $this->vdcclass->funcs->format_number($i));
					}
				}
				
				$template_html .= (($current_page < $total_pages) ? sprintf($this->vdcclass->lang['5475'], sprintf("%spage=%s", $base_url, ($this->vdcclass->info->current_page + 1))) : NULL);
				$template_html = sprintf($this->vdcclass->lang['7033'], $current_page, $total_pages, $template_html);
			}
			
			return sprintf($this->vdcclass->lang['5834'], $template_html);
		}

		function file_results($filename)
		{
			if ($this->vdcclass->funcs->is_null($filename) == true || $this->vdcclass->funcs->file_exists($this->vdcclass->info->root_path.$this->vdcclass->info->config['upload_path'].$filename) == false) {
				return $this->error(sprintf($this->vdcclass->lang['4552'], $this->vdcclass->image->basename($filename)));
			} else {
				$thumbnail_info = $this->vdcclass->image->get_image_info($this->vdcclass->info->root_path.$this->vdcclass->info->config['upload_path'].$filename);
				$thumbnail_size = $this->vdcclass->image->scale($thumbnail_info['thumbnail'], 125, 125);
			
				$this->templ_globals['extension'] = $thumbnail_info['extension'];
				
				$this->templ_vars[] = array(
					"FILENAME" => $thumbnail_info['filename'],
					"BASE_URL" => $this->vdcclass->info->base_url,
					"SITE_NAME" => $this->vdcclass->info->config['site_name'],
					"UPLOAD_PATH" => $this->vdcclass->info->config['upload_path'],
					"THUMBNAIL_SIZE" => sprintf("style=\"width: %spx; height: %spx;\"", $thumbnail_size['w'], $thumbnail_size['h']),
					"THUMBNAIL" => (($this->vdcclass->funcs->file_exists($this->vdcclass->info->root_path.$this->vdcclass->info->config['upload_path'].$thumbnail_info['thumbnail']) == false) ? "{$this->vdcclass->info->base_url}css/images/no_thumbnail.png" : $this->vdcclass->info->base_url.$this->vdcclass->info->config['upload_path'].$thumbnail_info['thumbnail']),
				);
				
				$template_html = $this->parse_template("upload", "standard_file_results");
				unset($this->templ_globals['extension'], $this->templ_vars, $thumbnail_info, $thumbnail_size);
				
				return $template_html;
			}
		}
	}

?>