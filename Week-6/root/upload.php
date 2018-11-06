<?php
	
	require_once "./source/includes/data.php";
	require_once "{$vdcclass->info->root_path}source/language/upload.php";
	
	$vdcclass->templ->page_title = sprintf($vdcclass->lang['001'], $vdcclass->info->config['site_name']);
	
	if ($vdcclass->info->config['uploading_disabled'] == true && $vdcclass->info->is_admin == false) {
		$vdcclass->templ->page_title = $vdcclass->lang['005'];
		$vdcclass->templ->error($vdcclass->lang['004'], true);
	} else {
		if ($vdcclass->info->config['useronly_uploading'] == true && $vdcclass->info->is_user == false) {
			$vdcclass->templ->page_title = $vdcclass->lang['005'];
			$vdcclass->templ->error($vdcclass->lang['007'], true);
		}
	}
	
	switch ($vdcclass->input->post_vars['upload_type']) {
		case "url-boxed":
		case "url-standard":
			if (REMOTE_FOPEN_ENABLED == false && USE_CURL_LIBRARY == false) {
				$vdcclass->templ->error($vdcclass->lang['011'], true);
			} else {
				$files = $vdcclass->input->post_vars['userfile'];
				$vdcclass->input->post_vars['userfile'] = array();
				
				switch ($vdcclass->input->post_vars['url_upload_type']) {
					case "paste_upload":
						$vdcclass->input->post_vars['userfile'] = array_map("trim", explode("\n", $vdcclass->input->post_vars['paste_upload'], $vdcclass->info->config['max_results']));
						break;
					case "webpage_upload":
						if ($vdcclass->funcs->is_null($vdcclass->input->post_vars['webpage_upload']) == false) {
							$urlparts = parse_url($vdcclass->input->post_vars['webpage_upload']);
							
							$webpage_headers = $vdcclass->funcs->get_headers($vdcclass->input->post_vars['webpage_upload']);
							$webpage_content = $vdcclass->funcs->get_http_content($webpage_headers['Address'], 2);
							
							if ($vdcclass->funcs->is_null($webpage_content) == true) {
								$vdcclass->templ->error($vdcclass->lang['743'], true);
							} else {
								$file_extensions = implode("|", $vdcclass->info->config['file_extensions']);
								
								preg_match_all(sprintf("#<img([^\>]+)src=('|\"|)([^\s]+)\.((%s)[^\?]+)('|\"|)#Ui", $file_extensions), $webpage_content, $image_matches);
								
								$image_matches['3'] = array_unique($image_matches['3']);
								
								foreach ($image_matches['3'] as $id => $url) {
									if ($id < $vdcclass->info->config['max_results']) {
										if (preg_match("#^(http|https):\/\/([^\s]+)$#i", $url) >= 1) {
											$vdcclass->input->post_vars['userfile'][] = sprintf("%s.%s", $url, $image_matches['5'][$id]);
										} elseif (preg_match("#^\/([^\s]+)$#", $url) >= 1) {
											$vdcclass->input->post_vars['userfile'][] = sprintf("%s://%s%s.%s", $urlparts['scheme'], $urlparts['host'], $url, $image_matches['5'][$id]);
										} else {
											$vdcclass->input->post_vars['userfile'][] = sprintf("%s://%s%s%s.%s", $urlparts['scheme'], $urlparts['host'], sprintf("%s/", dirname($urlparts['path'])), $url, $image_matches['5'][$id]);
										}
									}
								}
							}
							
							if ($vdcclass->funcs->is_null($vdcclass->input->post_vars['userfile']) == true) {
								$vdcclass->templ->error($vdcclass->lang['254'], true);
							} else {
								foreach ($vdcclass->input->post_vars['userfile'] as $imageurl) {
									$vdcclass->templ->templ_globals['get_whileloop'] = true;
									
									$break_line = (($tdcount >= 4) ? true : false);
									$tdcount = (($tdcount >= 4) ? 0 : $tdcount);
									$tdcount++;
									
									$vdcclass->templ->templ_vars[] = array(
										"IMAGE_URL" => $imageurl,
										"FILENAME" => $vdcclass->image->basename($imageurl),
										"MAX_WIDTH" => $vdcclass->info->config['thumbnail_width'],
										"TABLE_BREAK" => (($break_line == true) ? "</tr><tr>" : NULL),
										"TDCLASS" => $tdclass = (($tdclass == "tdrow1") ? "tdrow2" : "tdrow1"),
									);
									
									$vdcclass->templ->templ_globals['urlupload_gallery_layout'] .= $vdcclass->templ->parse_template("upload", "webpage_upload_image_select");
									unset($vdcclass->templ->templ_vars, $break_line, $vdcclass->templ->templ_globals['get_whileloop']);	
								}
							
								$vdcclass->templ->templ_vars[] = array(
									"WEBPAGE_URL" => $webpage_headers['Address'],
									"UPLOAD_TO" => $vdcclass->input->post_vars['upload_to'],
									"UPLOAD_TYPE" => $vdcclass->input->post_vars['upload_type'],
									"IMAGE_RESIZE" => $vdcclass->input->post_vars['image_resize'],
									"PRIVATE_UPLOAD" => $vdcclass->input->post_vars['private_upload'],
									"WEBPAGE_URL_SMALL" => $vdcclass->funcs->shorten_url($webpage_headers['Address'], 60),
								);
							
								$vdcclass->templ->output("upload", "webpage_upload_image_select");	
							}
						}
						break;
					default:
						$vdcclass->input->post_vars['userfile'] = $files;
				}
				
				$total_files = count($vdcclass->input->post_vars['userfile']);
				
				foreach ($vdcclass->input->post_vars['userfile'] as $i => $name) {
					if ($vdcclass->funcs->is_null($vdcclass->input->post_vars['userfile'][$i]) == false && $vdcclass->input->post_vars['userfile'][$i] !== "http://") {
						if ($total_file_uploads < $total_files) {
							$origname = $vdcclass->image->basename($vdcclass->input->post_vars['userfile'][$i]);
							
							$filetitle = strip_tags((strlen($origname) > 20) ? sprintf("%s...", substr($origname, 0, 20)) : $origname);
							$filename = sprintf("%s.%s", $vdcclass->funcs->random_string(20, "0123456789"), ($extension = $vdcclass->image->file_extension($origname)));
							
							$file_headers = $vdcclass->funcs->get_headers($vdcclass->input->post_vars['userfile'][$i]);
							$file_content = ((in_array("HTTP/1.0 200 OK", $file_headers) == true || in_array("HTTP/1.1 200 OK", $file_headers) == true) ? $vdcclass->funcs->get_http_content($file_headers['Address'], 2) : NULL);
							
							if ($vdcclass->funcs->is_url($file_headers['Address']) == false) {
								$uploadinfo[]['error'] = array(sprintf($vdcclass->lang['012'], $origname), "error");
							} elseif ($vdcclass->funcs->is_null($file_content) == true) {
								$uploadinfo[]['error'] = array(sprintf($vdcclass->lang['013'], $origname), "error");
							} elseif (in_array($extension, $vdcclass->info->config['file_extensions']) == false) {
								$uploadinfo[]['error'] = array(sprintf($vdcclass->lang['002'], $origname, $extension), "message");
							} elseif (($filesize = strlen($file_content)) > $vdcclass->info->config['max_filesize']) {
								$uploadinfo[]['error'] = array(sprintf($vdcclass->lang['003'], $origname, $vdcclass->image->format_filesize($vdcclass->info->config['max_filesize'])), "message");
							} elseif ($vdcclass->funcs->file_exists($vdcclass->info->root_path.$vdcclass->info->config['upload_path'].$filename) == true) {
								$uploadinfo[]['error'] = array(sprintf($vdcclass->lang['009'], $origname), "error");
							} elseif ($vdcclass->funcs->write_file($vdcclass->info->root_path.$vdcclass->info->config['upload_path'].$filename, $file_content) == false) {
								$uploadinfo[]['error'] = array(sprintf($vdcclass->lang['010'], $origname), "error");
							} else {
								if ($vdcclass->input->post_vars['image_resize'] > 0 && $vdcclass->input->post_vars['image_resize'] <= 8) {
									$vdcclass->image->create_thumbnail($filename, true, $vdcclass->input->post_vars['image_resize']);
								}
								
								chmod($vdcclass->info->root_path.$vdcclass->info->config['upload_path'].$filename, 0644);
								
								$vdcclass->db->query("INSERT INTO `[1]` (`filename`, `total_rating`, `total_votes`, `voted_by`, `gallery_id`, `is_private`) VALUES ('[2]', '0', '0', '', '[3]', '[4]');", array(MYSQL_FILE_RATINGS_TABLE, $filename, $vdcclass->info->user_data['user_id'], $vdcclass->input->post_vars['private_upload']));
								$vdcclass->db->query("INSERT INTO `[1]` (`filename`, `is_private`, `gallery_id`, `file_title`, `album_id`) VALUES ('[2]', '[3]', '[4]', '[5]', '[6]'); ", array(MYSQL_FILE_STORAGE_TABLE, $filename, $vdcclass->input->post_vars['private_upload'], $vdcclass->info->user_data['user_id'], $filetitle, $vdcclass->input->post_vars['upload_to']));																																							
								$vdcclass->db->query("INSERT INTO `[1]` (`filename`, `filesize`, `ip_address`, `user_agent`, `time_uploaded`, `gallery_id`, `is_private`, `original_filename`, `upload_type`, `bandwidth`, `image_views`) VALUES ('[2]', '[3]', '[4]', '[5]', '[6]', '[7]', '[8]', '[9]', 'url', '[3]', '1'); ", array(MYSQL_FILE_LOGS_TABLE, $filename, $filesize, $vdcclass->input->server_vars['remote_addr'], $vdcclass->input->server_vars['http_user_agent'], time(), $vdcclass->info->user_data['user_id'], $vdcclass->input->post_vars['private_upload'], strip_tags($origname)));
								
								$vdcclass->image->create_thumbnail($filename);
								
								$uploadinfo[]['result'] = $filename;							
							
								unset($origname, $filetitle, $filename, $file_headers, $file_content, $filesize, $extension);
							}
							
							$total_file_uploads++;
						}
					}
				}
			}
			break;
		case "standard":
		case "normal-boxed":
			$total_files = count($vdcclass->input->file_vars['userfile']['name']);
			
			foreach ($vdcclass->input->file_vars['userfile']['name'] as $i => $name) {
				if (array_key_exists($i, $vdcclass->input->file_vars['userfile']['error']) == false && array_key_exists($i, $vdcclass->input->file_vars['userfile']['name']) == true || array_key_exists($i, $vdcclass->input->file_vars['userfile']['error']) == true && array_key_exists($i, $vdcclass->input->file_vars['userfile']['name']) == true) {
					if (array_key_exists($i, $vdcclass->input->file_vars['userfile']['error']) == false && $vdcclass->funcs->is_null($vdcclass->input->file_vars['userfile']['name'][$i]) == false || $vdcclass->input->file_vars['userfile']['error'][$i] !== 4 && $vdcclass->funcs->is_null($vdcclass->input->file_vars['userfile']['name'][$i]) == false) {
						if ($total_file_uploads < $total_files) {
							$origname = $vdcclass->image->basename($vdcclass->input->file_vars['userfile']['name'][$i]);
							
							$filetitle = strip_tags((strlen($origname) > 20) ? sprintf("%s...", substr($origname, 0, 20)) : $origname);
							$filename = sprintf("%s.%s", $vdcclass->funcs->random_string(20, "0123456789"), ($extension = $vdcclass->image->file_extension($origname)));
							
							if (in_array($extension, $vdcclass->info->config['file_extensions']) == false) {
								$uploadinfo[]['error'] = array(sprintf($vdcclass->lang['002'], $origname, $extension), "message");
							} elseif ($vdcclass->input->file_vars['userfile']['size'][$i] > $vdcclass->info->config['max_filesize']) {
								$uploadinfo[]['error'] = array(sprintf($vdcclass->lang['003'], $origname, $vdcclass->image->format_filesize($vdcclass->info->config['max_filesize'])), "message");
							} elseif ($vdcclass->image->is_image($vdcclass->input->file_vars['userfile']['tmp_name'][$i]) == false) {
								$uploadinfo[]['error'] = array(sprintf($vdcclass->lang['006'], $origname), "message");
							} elseif ($vdcclass->input->file_vars['userfile']['error'][$i] > 0) {
								$uploadinfo[]['error'] = array(sprintf($vdcclass->lang['008'][$vdcclass->input->file_vars['userfile']['error'][$i]], $origname), "error");
							} elseif ($vdcclass->funcs->file_exists($vdcclass->info->root_path.$vdcclass->info->config['upload_path'].$filename) == true) {
								$uploadinfo[]['error'] = array(sprintf($vdcclass->lang['009'], $filename), "error");
							} elseif (move_uploaded_file($vdcclass->input->file_vars['userfile']['tmp_name'][$i], $vdcclass->info->root_path.$vdcclass->info->config['upload_path'].$filename) == false) {
								$uploadinfo[]['error'] = array(sprintf($vdcclass->lang['010'], $origname), "error");
							} else {
								if ($vdcclass->input->post_vars['image_resize'] > 0 && $vdcclass->input->post_vars['image_resize'] <= 8) {
									$vdcclass->image->create_thumbnail($filename, true, $vdcclass->input->post_vars['image_resize']);
								}
								
								chmod($vdcclass->info->root_path.$vdcclass->info->config['upload_path'].$filename, 0644);
								
								$vdcclass->db->query("INSERT INTO `[1]` (`filename`, `total_rating`, `total_votes`, `voted_by`, `gallery_id`, `is_private`) VALUES ('[2]', '0', '0', '', '[3]', '[4]');", array(MYSQL_FILE_RATINGS_TABLE, $filename, $vdcclass->info->user_data['user_id'], $vdcclass->input->post_vars['private_upload']));
								$vdcclass->db->query("INSERT INTO `[1]` (`filename`, `is_private`, `gallery_id`, `file_title`, `album_id`) VALUES ('[2]', '[3]', '[4]', '[5]', '[6]'); ", array(MYSQL_FILE_STORAGE_TABLE, $filename, $vdcclass->input->post_vars['private_upload'], $vdcclass->info->user_data['user_id'], $filetitle, $vdcclass->input->post_vars['upload_to']));																																							
								$vdcclass->db->query("INSERT INTO `[1]` (`filename`, `filesize`, `ip_address`, `user_agent`, `time_uploaded`, `gallery_id`, `is_private`, `original_filename`, `upload_type`, `bandwidth`, `image_views`) VALUES ('[2]', '[3]', '[4]', '[5]', '[6]', '[7]', '[8]', '[9]', 'normal', '[3]', '1'); ", array(MYSQL_FILE_LOGS_TABLE, $filename, $vdcclass->input->file_vars['userfile']['size'][$i], $vdcclass->input->server_vars['remote_addr'], $vdcclass->input->server_vars['http_user_agent'], time(), $vdcclass->info->user_data['user_id'], $vdcclass->input->post_vars['private_upload'], strip_tags($origname)));
								
								$vdcclass->image->create_thumbnail($filename);
								
								$uploadinfo[]['result'] = $filename; 	
								
								unset($origname, $filetitle, $filename, $extension);
							}
							
							$total_file_uploads++;
						}
					}
				}
			}
			break;
	}
	
	if (in_array($vdcclass->input->post_vars['upload_type'], array("standard", "url-standard")) == true) {
		if ($vdcclass->funcs->is_null($uploadinfo) == false) {
			$vdcclass->templ->html = NULL;
			
			foreach ($uploadinfo as $id => $value) {
				$vdcclass->templ->html .= (($total_file_uploads > 1 && $id !== 0) ? "<hr />" : NULL);
				$vdcclass->templ->html .= ((is_array($uploadinfo[$id]['error']) == true) ? $vdcclass->templ->$uploadinfo[$id]['error']['1']($uploadinfo[$id]['error']['0'], false) : $vdcclass->templ->file_results($uploadinfo[$id]['result']));
			}
		}
	} else {
		if ($vdcclass->funcs->is_null($uploadinfo) == false) {
			foreach ($uploadinfo as $id => $value) {
				if (is_array($uploadinfo[$id]['error']) == false) {
					$vdcclass->templ->templ_globals['uploadinfo'][] = $uploadinfo[$id]['result'];
				} else {
					$vdcclass->templ->templ_globals['errorinfo'][] = $uploadinfo[$id]['error']['0'];
				}
			}
		
			if ($vdcclass->funcs->is_null($vdcclass->templ->templ_globals['uploadinfo']) == false) {
				for ($i = 1; $i < 6; $i++) {
					foreach ($vdcclass->templ->templ_globals['uploadinfo'] as $filename) {
						$vdcclass->templ->templ_globals['get_whileloop']["uploadinfo_whileloop_{$i}"] = true;
						
						$thumbnail = $vdcclass->image->thumbnail_name($filename);
						
						$vdcclass->templ->templ_vars[] = array(
							"FILENAME" => $filename,
							"BASE_URL" => $vdcclass->info->base_url,
							"SITE_NAME" => $vdcclass->info->config['site_name'],
							"UPLOAD_PATH" => $vdcclass->info->config['upload_path'],
							"THUMBNAIL" => (($vdcclass->funcs->file_exists($vdcclass->info->root_path.$vdcclass->info->config['upload_path'].$thumbnail) == false) ? "{$vdcclass->info->base_url}css/images/no_thumbnail.png" : $vdcclass->info->base_url.$vdcclass->info->config['upload_path'].$thumbnail),
						);
						
						$vdcclass->templ->templ_globals["uploadinfo_whileloop_{$i}"] .= $vdcclass->templ->parse_template("upload", "boxed_file_results");
						unset($vdcclass->templ->templ_globals['get_whileloop'], $vdcclass->templ->templ_vars, $thumbnail);		
					}
				}
				
				foreach ($vdcclass->templ->templ_globals['uploadinfo'] as $filename) {
					$break_line = (($tdcount >= 4) ? true : false);
					$tdcount = (($tdcount >= 4) ? 0 : $tdcount);
					$tdcount++;
					
					$vdcclass->templ->templ_vars[] = array(
						"FILENAME" => $filename,
						"FILE_TITLE" => $filename,
						"TABLE_BREAK" => (($break_line == true) ? "</tr><tr>" : NULL),
						"TDCLASS" => $tdclass = (($tdclass == "tdrow1") ? "tdrow2" : "tdrow1"),
					);
					
					$gallery_html .= $vdcclass->templ->parse_template("global", "global_gallery_layout");
					unset($vdcclass->templ->templ_vars, $break_line);	
				}
			}
			
			if ($vdcclass->funcs->is_null($vdcclass->templ->templ_globals['errorinfo']) == false) {
				foreach ($vdcclass->templ->templ_globals['errorinfo'] as $errmsg) {
					$vdcclass->templ->templ_globals['get_whileloop']['errorinfo_whileloop'] = true;
					
					$vdcclass->templ->templ_vars[] = array("ERROR_MESSAGE" => $errmsg['0']);
					
					$vdcclass->templ->templ_globals['errorinfo_whileloop'] .= $vdcclass->templ->parse_template("upload", "boxed_file_results");
					
					unset($vdcclass->templ->templ_globals['get_whileloop'], $vdcclass->templ->templ_vars);	
				}
			}
			
			$vdcclass->templ->templ_vars[] = array(
				"GALLERY_HTML" => $gallery_html,
				"BASE_URL" => $vdcclass->info->base_url,
				"SITE_NAME" => $vdcclass->info->config['site_name'],
			);
			
			$vdcclass->templ->output("upload", "boxed_file_results");
		}
	}
	
	if ($total_file_uploads < 1 && $vdcclass->funcs->is_null($vdcclass->templ->templ_globals['errorinfo']) == true) {
		$vdcclass->templ->error($vdcclass->lang['014'], true);
	} else {	
		if (in_array($vdcclass->input->post_vars['upload_type'], array("standard", "url-standard")) == true) {
			$vdcclass->templ->output();
		}
	}
	
?>