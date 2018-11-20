<?php

	
	require_once "./includes/vdc.php";
	require_once "{$root_path}language/users.php";
	
	$vdc->templ->page_title = sprintf($vdc->lang['001'], $vdc->info->config['site_name']);
	
	switch ($vdc->input->get_vars['task']) {
		case "user_list":
			$vdc->templ->page_title .= $vdc->lang['034'];
			
			$sql = $vdc->db->query("SELECT * FROM `[1]` ORDER BY `user_id` DESC LIMIT <# QUERY_LIMIT #>;", array(tbl_user));
			
			while ($row = $vdc->db->fetch_array($sql)) {
				$vdc->templ->templ_globals['get_whileloop'] = true;
				
				$vdc->templ->templ_vars[] = array(
					"USER_ID" => $row['user_id'],
					"USERNAME" => $row['username'],
					"TDCLASS" => $tdclass = (($tdclass == "tdrow1") ? "tdrow2" : "tdrow1"),
					"TIME_JOINED" => date($vdc->info->config['date_format'], $row['register_time']),
					"GALLERY_STATUS" => (($row['private_gallery'] == 1) ? $vdc->lang['035'] : $vdc->lang['036']),
					"TOTAL_UPLOADS" => $vdc->funcs->number_format($vdc->db->total_rows($vdc->db->query("SELECT * FROM `[1]` WHERE `user_id` = '[2]' AND `is_private` = '0';", array(tbl_img, $row['user_id'])))),
				);
				
				$vdc->templ->templ_globals['user_list_whileloop'] .= $vdc->templ->parse_template("users", "user_list_page");
				unset($vdc->templ->templ_globals['get_whileloop'], $vdc->templ->templ_vars);	
			}
			
			$vdc->templ->templ_vars[] = array("PAGINATION_LINKS" => $vdc->templ->pagelinks("users.php?task=user_list", $vdc->db->total_rows($vdc->db->query("SELECT * FROM `[1]`;", array(tbl_user)))));
		
			$vdc->templ->output("users", "user_list_page");
			break;
		case "gallery":
			$vdc->templ->page_title .= $vdc->lang['033'];
			
			$vdc->info->selected_album = (int)$vdc->input->get_vars['cat'];
			$vdc->info->selected_gallery = (int)$vdc->input->get_vars['gal'];
			
			$vdc->info->user_owned_gallery = (($vdc->funcs->is_null($vdc->info->selected_gallery) == true || $vdc->info->user_data['user_id'] == $vdc->info->selected_gallery) ? true : false);
			$vdc->info->gallery_owner_data = (($vdc->info->user_owned_gallery == false) ? $vdc->db->fetch_array($vdc->db->query("SELECT * FROM `[1]` WHERE `user_id` = '[2]' LIMIT 1;", array(tbl_user, $vdc->info->selected_gallery))) : $vdc->info->user_data);
			
			$vdc->info->gallery_url = sprintf("%susers.php?task=gallery%s", $vdc->info->base_url, (($vdc->info->user_owned_gallery == true) ? NULL : "&amp;gal={$vdc->info->gallery_owner_data['user_id']}"));
			$vdc->info->gallery_url_full = sprintf("%s%s", $vdc->info->gallery_url, (($vdc->funcs->is_null($vdc->info->selected_album) == true) ? NULL : "&amp;cat={$vdc->info->selected_album}"));
			
			if ($vdc->info->user_owned_gallery == true && $vdc->info->is_user == false) {
				$vdc->templ->error($vdc->lang['002'], true);
			} elseif ($vdc->funcs->is_null($vdc->info->gallery_owner_data['user_id']) == true && $vdc->funcs->is_null($vdc->info->selected_gallery) == false) {
				$vdc->templ->error($vdc->lang['062'], true);
			} elseif ($vdc->info->is_admin == false && $vdc->info->user_owned_gallery == false && $vdc->info->gallery_owner_data['private_gallery'] == 1) {
				$vdc->templ->error($vdc->lang['059'], true);
			} else {
				$sql = $vdc->db->query("SELECT * FROM `[1]` WHERE `user_id` = '[2]' AND `album_id` = '[3]' AND (`img_name` LIKE '%[4]%' OR `img_title` LIKE '%[4]%') [[1]] ORDER BY `img_id` DESC LIMIT <# QUERY_LIMIT #>;", array(tbl_img, $vdc->info->gallery_owner_data['user_id'], $vdc->info->selected_album, urldecode($vdc->input->get_vars['search'])), array(($vdc->info->user_owned_gallery == false) ? " AND `is_private` = 0" : NULL));
				
				if ($vdc->db->total_rows($sql) < 1) {
					$vdc->templ->templ_globals['empty_gallery'] = true;
				} else {
					$vdc->templ->templ_globals['file_options'] = (($vdc->info->user_owned_gallery == true) ? true : false);
						
					while ($row = $vdc->db->fetch_array($sql)) {
						$break_line = (($tdcount >= 4) ? true : false);
						$tdcount = (($tdcount >= 4) ? 0 : $tdcount);
						$tdcount++;
						
						$vdc->templ->templ_vars[] = array(
							"FILE_ID" => $row['img_id'],
							"FILENAME" => $row['img_name'],
							"FILE_TITLE" => $row['img_title'],
							"TABLE_BREAK" => (($break_line == true) ? "</tr><tr>" : NULL),
							"TDCLASS" => $tdclass = (($tdclass == "tdrow1") ? "tdrow2" : "tdrow1"),
						);
						
						$gallery_html .= $vdc->templ->parse_template("global", "global_gallery_layout");
						unset($break_line, $vdc->templ->templ_globals['get_whileloop'], $vdc->templ->templ_vars);	
					}
				}
				
				$sql = $vdc->db->query("SELECT * FROM `[1]` WHERE `user_id` = '[2]' LIMIT 50;", array(tbl_album, $vdc->info->gallery_owner_data['user_id']));
				
				while ($row = $vdc->db->fetch_array($sql)) {
					$vdc->templ->templ_globals['get_whileloop'] = true;
					
					if ($row['album_id'] == $vdc->info->selected_album) {
						$curalbum = $row;
					}
					
					$vdc->templ->templ_vars[] = array(
						"ALBUM_ID" => $row['album_id'],
						"ALBUM_NAME" => $row['album_title'],
						"GALLERY_URL" => $vdc->info->gallery_url,
						"FULL_GALLERY_URL" => $vdc->info->gallery_url_full,
						"RETURN_URL" => base64_encode($vdc->info->page_url),
						"TOTAL_UPLOADS" => $vdc->funcs->number_format($vdc->db->total_rows($vdc->db->query("SELECT * FROM `[1]` WHERE `user_id` = '[2]' AND `album_id` = '[3]' [[1]];", array(tbl_img, $vdc->info->gallery_owner_data['user_id'], $row['album_id']), array(($vdc->info->user_owned_gallery == false) ? " AND `is_private` = 0" : NULL)))),
					);
					
					$vdc->templ->templ_globals['album_pulldown_whileloop'] .= $vdc->templ->parse_template("users", "my_gallery_page");
					unset($vdc->templ->templ_vars, $vdc->templ->templ_globals['get_whileloop']);
				}
				
				$vdc->templ->templ_vars[] = array(
					"GALLERY_HTML" => $gallery_html,		
					"GALLERY_URL" => $vdc->info->gallery_url,
					"CURRENT_PAGE" => $vdc->info->current_page,
					"FULL_GALLERY_URL" => $vdc->info->gallery_url_full,
					"RETURN_URL" => base64_encode($vdc->info->page_url),
					"GALLERY_ID" => $vdc->info->gallery_owner_data['user_id'],
					"IMAGE_SEARCH" => urldecode($vdc->input->get_vars['search']),
					"GALLERY_OWNER" => $vdc->info->gallery_owner_data['username'],
					"ALBUM_NAME" => (($vdc->funcs->is_null($curalbum['album_title']) == true) ? NULL : "&raquo; {$curalbum['album_title']}"),
					"EMPTY_GALLERY" => $vdc->templ->message((($vdc->funcs->is_null($vdc->input->get_vars['search']) == false) ? $vdc->lang['675'] : $vdc->lang['058']), false),
					"TOTAL_UPLOADS" => $vdc->funcs->number_format($vdc->db->total_rows($vdc->db->query("SELECT * FROM `[1]` WHERE `user_id` = '[2]' [[1]];", array(tbl_img, $vdc->info->gallery_owner_data['user_id']), array(($vdc->info->user_owned_gallery == false) ? " AND `is_private` = 0" : NULL)))),
					"TOTAL_ROOT_UPLOADS" => $vdc->funcs->number_format($vdc->db->total_rows($vdc->db->query("SELECT * FROM `[1]` WHERE `user_id` = '[2]' AND `album_id` = '0' [[1]];", array(tbl_img, $vdc->info->gallery_owner_data['user_id']), array(($vdc->info->user_owned_gallery == false) ? " AND `is_private` = 0" : NULL)))),
					"PAGINATION_LINKS" => $vdc->templ->pagelinks(sprintf("%s%s", $vdc->info->gallery_url_full, (($vdc->funcs->is_null($vdc->input->get_vars['search']) == true) ? NULL : sprintf("&amp;search=%s", urldecode($vdc->input->get_vars['search'])))), $vdc->db->total_rows($vdc->db->query("SELECT * FROM `[1]` WHERE `user_id` = '[2]' AND `album_id` = '[3]' AND (`img_name` LIKE '%[4]%' OR `img_title` LIKE '%[4]%') [[1]] ORDER BY `img_id` DESC;", array(tbl_img, $vdc->info->gallery_owner_data['user_id'], $vdc->info->selected_album, urldecode($vdc->input->get_vars['search'])), array(($vdc->info->user_owned_gallery == false) ? " AND `is_private` = 0" : NULL)))),	
				);
				
				$vdc->templ->output("users", "my_gallery_page");
			}
			break;
		case "rename_img_title":
			if ($vdc->info->is_user == false) {
				$vdc->templ->error($vdc->lang['002'], true);
			} elseif ($vdc->funcs->is_null($vdc->input->get_vars['file']) == true) {
				$vdc->templ->error($vdc->lang['023'], true);
			} elseif ($vdc->funcs->is_file($vdc->input->get_vars['file'], $root_path.$vdc->info->config['upload_path'], true, $vdc->info->user_data['user_id']) == false) {
				$vdc->templ->error(sprintf($vdc->lang['024'], $vdc->image->basename($vdc->input->get_vars['file'])), true);
			} else {			
				$new_title = htmlentities($vdc->input->get_vars['title']);
				
				$vdc->db->query("UPDATE `[1]` SET `img_title` = '[2]' WHERE `img_name` = '[3]';", array(tbl_img, $new_title, $vdc->image->basename($vdc->input->get_vars['file'])));
				
				exit($new_title);
			}
			break;
		case "move_files":
			if ($vdc->info->is_user == false) {
				exit($vdc->templ->lightbox_error($vdc->lang['002']));
			} elseif ($vdc->funcs->is_null($vdc->input->get_vars['files']) == true || $vdc->funcs->is_null($vdc->input->get_vars['lb_div']) == true) {
				exit($vdc->templ->lightbox_error($vdc->lang['013']));
			} else {
				$files2move = $vdc->image->basename(explode(",", $vdc->input->get_vars['files']));
				
				foreach ($files2move as $id => $img_name) {
					if ($vdc->funcs->is_null($img_name) == true) {
						exit($vdc->templ->lightbox_error($vdc->lang['023']));
					} elseif ($vdc->funcs->is_file($img_name, $root_path.$vdc->info->config['upload_path'], true, $vdc->info->user_data['user_id']) == false) {
						exit($vdc->templ->lightbox_error(sprintf($vdc->lang['024'], $img_name)));
					} 
				}
				
				$sql = $vdc->db->query("SELECT * FROM `[1]` WHERE `user_id` = '[2]';", array(tbl_album, $vdc->info->user_data['user_id']));
				
				while ($row = $vdc->db->fetch_array($sql)) {
					$vdc->templ->templ_globals['get_whileloop'] = true;
					
					$vdc->templ->templ_vars[] = array(
						"ALBUM_ID" => $row['album_id'],
						"ALBUM_NAME" => $row['album_title'],
					);
					
					$vdc->templ->templ_globals['album_options_whileloop'] .= $vdc->templ->parse_template("users", "move_files_lightbox");
					unset($vdc->templ->templ_vars, $vdc->templ->templ_globals['get_whileloop']);
				}
				
				$vdc->templ->templ_vars[] = array(
					"FILES2MOVE" => $vdc->input->get_vars['files'],
					"LIGHTBOX_ID" => $vdc->input->get_vars['lb_div'],
					"RETURN_URL" => urldecode($vdc->input->get_vars['return']),
				);
				
				exit($vdc->templ->parse_template("users", "move_files_lightbox"));
			}
			break;
		case "move_files-d":
			$vdc->templ->page_title .= $vdc->lang['031'];
			
			if ($vdc->info->is_user == false) {
				$vdc->templ->error($vdc->lang['002'], true);
			} elseif ($vdc->funcs->is_null($vdc->input->post_vars['files']) == true) {
				$vdc->templ->error($vdc->lang['013'], true);
			} elseif ($vdc->funcs->is_null($vdc->input->post_vars['move_to']) == true) {
				$vdc->templ->error($vdc->lang['004'], true);
			} else {
				$sql = $vdc->db->query("SELECT * FROM `[1]` WHERE `album_id` = '[2]' AND `user_id` = '[3]' LIMIT 1;", array(tbl_album, $vdc->input->post_vars['move_to'], $vdc->info->user_data['user_id']));
				
				if ($vdc->db->total_rows($sql) !== 1 && $vdc->input->post_vars['move_to'] !== "root") {
					$vdc->templ->error($vdc->lang['949'], true);
				} else {
					$files2move = $vdc->image->basename(explode(",", $vdc->input->post_vars['files']));
					
					foreach ($files2move as $id => $img_name) {
						if ($vdc->funcs->is_null($img_name) == true) {
							$vdc->templ->error($vdc->lang['023'], true);
						} elseif ($vdc->funcs->is_file($img_name, $root_path.$vdc->info->config['upload_path'], true, $vdc->info->user_data['user_id']) == false) {
							$vdc->templ->error(sprintf($vdc->lang['024'], $img_name), true);
						} else {
							$vdc->db->query("UPDATE `[1]` SET `album_id` = '[2]' WHERE `img_name` = '[3]';", array(tbl_img, $vdc->input->post_vars['move_to'], $img_name));
						}
					}
					
					$vdc->templ->message(sprintf($vdc->lang['032'], (($vdc->funcs->is_null($vdc->input->post_vars['return']) == true) ? base64_encode($vdc->info->base_url) : $vdc->input->post_vars['return']), $vdc->input->post_vars['move_to']), true);
				}
			}
			break;
		case "delete_files":
			if ($vdc->info->is_user == false) {
				exit($vdc->templ->lightbox_error($vdc->lang['002']));
			} elseif ($vdc->funcs->is_null($vdc->input->get_vars['files']) == true || $vdc->funcs->is_null($vdc->input->get_vars['lb_div']) == true) {
				exit($vdc->templ->lightbox_error($vdc->lang['013']));
			} else {
				$files2delete = $vdc->image->basename(explode(",", $vdc->input->get_vars['files']));
				
				foreach ($files2delete as $id => $img_name) {
					if ($vdc->funcs->is_null($img_name) == true) {
						exit($vdc->templ->lightbox_error($vdc->lang['023']));
					} elseif ($vdc->funcs->is_file($img_name, $root_path.$vdc->info->config['upload_path'], true, $vdc->info->user_data['user_id']) == false) {
						exit($vdc->templ->lightbox_error(sprintf($vdc->lang['024'], $img_name)));
					}
				}
				
				$vdc->templ->templ_vars[] = array(
					"LIGHTBOX_ID" => $vdc->input->get_vars['lb_div'],
					"FILES2DELETE" => $vdc->input->get_vars['files'],
					"RETURN_URL" => urldecode($vdc->input->get_vars['return']),
				);
				
				exit($vdc->templ->parse_template("users", "delete_files_lightbox"));
			}
			break;
		case "delete_files-d":
			$vdc->templ->page_title .= $vdc->lang['026'];
			
			if ($vdc->info->is_user == false) {
				$vdc->templ->error($vdc->lang['002'], true);
			} elseif ($vdc->funcs->is_null($vdc->input->post_vars['files']) == true) {
				$vdc->templ->error($vdc->lang['013'], true);
			} else {
				$files2delete = $vdc->image->basename(explode(",", $vdc->input->post_vars['files']));
				
				foreach ($files2delete as $id => $img_name) {
					if ($vdc->funcs->is_null($img_name) == true) {
						$vdc->templ->error($vdc->lang['023'], true);
					} elseif ($vdc->funcs->is_file($img_name, $root_path.$vdc->info->config['upload_path'], true, $vdc->info->user_data['user_id']) == false) {
						$vdc->templ->error(sprintf($vdc->lang['024'], $img_name), true);
					} else {
						if (unlink($root_path.$vdc->info->config['upload_path'].$img_name) == false) {
							$vdc->templ->error(sprintf($vdc->lang['027'], $img_name), true);
						}
						
						if ($vdc->funcs->file_exists($root_path.$vdc->info->config['upload_path'].($thumbnail = $vdc->image->thumbnail_name($img_name))) == true) {
							if (unlink($root_path.$vdc->info->config['upload_path'].$thumbnail) == false) {
								$vdc->templ->error(sprintf($vdc->lang['028'], $img_name), true);
							}
						}
						
//						$vdc->db->query("DELETE FROM `[1]` WHERE `img_name` = '[2]';", array(MYSQL_FILE_RATINGS_TABLE, $img_name));
						$vdc->db->query("DELETE FROM `[1]` WHERE `img_name` = '[2]';", array(tbl_img, $img_name));
					}
				}
				
				$vdc->templ->message(sprintf($vdc->lang['029'], (($vdc->funcs->is_null($vdc->input->post_vars['return']) == true) ? base64_encode($vdc->info->base_url) : $vdc->input->post_vars['return'])), true);
			}
			break;
		case "albums-c":
			if ($vdc->info->is_user == false) {
				exit($vdc->templ->lightbox_error($vdc->lang['002']));
			} elseif ($vdc->funcs->is_null($vdc->input->get_vars['lb_div']) == true) {
				exit($vdc->templ->lightbox_error($vdc->lang['013']));
			} else {
				$vdc->templ->templ_vars[] = array(
					"LIGHTBOX_ID" => $vdc->input->get_vars['lb_div'],
					"RETURN_URL" => urldecode($vdc->input->get_vars['return']),
				 );
				
				exit($vdc->templ->parse_template("users", "new_album_lightbox"));
			}
			break;
		case "albums-c-d":
			$vdc->templ->page_title .= $vdc->lang['020'];
			
			$album_title = htmlspecialchars($vdc->input->post_vars['album_title']);
			
			if ($vdc->info->is_user == false) {
				$vdc->templ->error($vdc->lang['002'], true);
			} elseif ($vdc->funcs->is_null($album_title) == true) {
				$vdc->templ->error($vdc->lang['004'], true);
			} elseif ($vdc->db->total_rows($vdc->db->query("SELECT * FROM `[1]` WHERE `album_title` = '[2]' AND `user_id` = '[3]' LIMIT 1;", array(tbl_album, $album_title, $vdc->info->user_data['user_id']))) == 1) {
				$vdc->templ->error(sprintf($vdc->lang['022'], $album_title), true);
			} else {
				$vdc->db->query("INSERT INTO `[1]` (`album_title`, `user_id`) VALUES ('[2]', '[3]');", array(tbl_album, $album_title, $vdc->info->user_data['user_id']));
				
				$newalbum = $vdc->db->fetch_array($vdc->db->query("SELECT * FROM `[1]` WHERE `album_title` = '[2]' AND `user_id` = '[3]' LIMIT 1;", array(tbl_album, $album_title, $vdc->info->user_data['user_id'])));
				
				$vdc->templ->message(sprintf($vdc->lang['021'], $album_title, (($vdc->funcs->is_null($vdc->input->post_vars['return']) == true) ? base64_encode($vdc->info->base_url) : $vdc->input->post_vars['return']), $newalbum['album_id']), true);
			}
			break;
		case "albums-r":
			if ($vdc->info->is_user == false) {
				exit($vdc->templ->lightbox_error($vdc->lang['002']));
			} elseif ($vdc->funcs->is_null($vdc->input->get_vars['album']) == true || $vdc->funcs->is_null($vdc->input->get_vars['lb_div']) == true) {
				exit($vdc->templ->lightbox_error($vdc->lang['013']));
			} else {
				$sql = $vdc->db->query("SELECT * FROM `[1]` WHERE `album_id` = '[2]' AND `user_id` = '[3]' LIMIT 1;", array(tbl_album, $vdc->input->get_vars['album'], $vdc->info->user_data['user_id']));
				
				if ($vdc->db->total_rows($sql) !== 1) {
					exit($vdc->templ->lightbox_error($vdc->lang['017']));
				} else {
					$oldalbum = $vdc->db->fetch_array($sql);
					
					$vdc->templ->templ_vars[] = array(
						"ALBUM_ID" => $oldalbum['album_id'],
						"OLD_TITLE" => $oldalbum['album_title'],
						"LIGHTBOX_ID" => $vdc->input->get_vars['lb_div'],
						"RETURN_URL" => urldecode($vdc->input->get_vars['return']),
					);
					
					exit($vdc->templ->parse_template("users", "rename_album_lightbox"));
				}
			}
			break;
		case "albums-r-d":
			$vdc->templ->page_title .= $vdc->lang['018'];
			
			$album_title = htmlspecialchars($vdc->input->post_vars['album_title']);
			
			if ($vdc->info->is_user == false) {
				$vdc->templ->error($vdc->lang['002'], true);
			} elseif ($vdc->funcs->is_null($vdc->input->post_vars['album']) == true) {
				$vdc->templ->error($vdc->lang['013'], true);
			} elseif ($vdc->funcs->is_null($album_title) == true) {
				$vdc->templ->error($vdc->lang['004'], true);
			} elseif ($vdc->db->total_rows($vdc->db->query("SELECT * FROM `[1]` WHERE `album_title` = '[2]' AND `user_id` = '[3]' LIMIT 1;", array(tbl_album, $album_title, $vdc->info->user_data['user_id']))) == 1) {
				$vdc->templ->error(sprintf($vdc->lang['022'], $album_title), true);
			} else {
				if ($vdc->db->total_rows(($albumsql = $vdc->db->query("SELECT * FROM `[1]` WHERE `album_id` = '[2]' AND `user_id` = '[3]' LIMIT 1;", array(tbl_album, $vdc->input->post_vars['album'], $vdc->info->user_data['user_id'])))) !== 1) {
					$vdc->templ->error($vdc->lang['017'], true);
				} else {
					$oldalbum = $vdc->db->fetch_array($albumsql);
					
					$vdc->db->query("UPDATE `[1]` SET `album_title` = '[2]' WHERE `album_id` = '[3]';", array(tbl_album, $album_title, $oldalbum['album_id']));
					
					$vdc->templ->message(sprintf($vdc->lang['019'], $oldalbum['album_title'], $album_title, (($vdc->funcs->is_null($vdc->input->post_vars['return']) == true) ? base64_encode($vdc->info->base_url) : $vdc->input->post_vars['return']), $oldalbum['album_id']), true);
				}
			}
			break;
		case "albums-d":
			if ($vdc->info->is_user == false) {
				exit($vdc->templ->lightbox_error($vdc->lang['002']));
			} elseif ($vdc->funcs->is_null($vdc->input->get_vars['album']) == true || $vdc->funcs->is_null($vdc->input->get_vars['lb_div']) == true) {
				exit($vdc->templ->lightbox_error($vdc->lang['013']));
			} else {
				if ($vdc->db->total_rows(($albumsql = $vdc->db->query("SELECT * FROM `[1]` WHERE `album_id` = '[2]' AND `user_id` = '[3]' LIMIT 1;", array(tbl_album, $vdc->input->get_vars['album'], $vdc->info->user_data['user_id'])))) !== 1) {
					exit($vdc->templ->lightbox_error($vdc->lang['014']));
				} else {
					$oldalbum = $vdc->db->fetch_array($albumsql);
					
					$vdc->templ->templ_vars[] = array(
						"ALBUM2DELETE" => $oldalbum['album_id'],
						"LIGHTBOX_ID" => $vdc->input->get_vars['lb_div'],
					);
					
					exit($vdc->templ->parse_template("users", "delete_album_lightbox"));
				}
			}
			break;
		case "albums-d-d":
			$vdc->templ->page_title .= $vdc->lang['015'];
			
			if ($vdc->info->is_user == false) {
				$vdc->templ->error($vdc->lang['002'], true);
			} elseif ($vdc->funcs->is_null($vdc->input->post_vars['album']) == true) {
				$vdc->templ->error($vdc->lang['013'], true);
			} elseif ($vdc->db->total_rows($vdc->db->query("SELECT * FROM `[1]` WHERE `album_id` = '[2]' AND `user_id` = '[3]' LIMIT 1;", array(tbl_album, $vdc->input->post_vars['album'], $vdc->info->user_data['user_id']))) !== 1) {
				$vdc->templ->error($vdc->lang['014'], true);
			} else {
				$vdc->db->query("DELETE FROM `[1]` WHERE `album_id` = '[2]' AND `user_id`  = '[3]';", array(tbl_album, $vdc->input->post_vars['album'], $vdc->info->user_data['user_id']));
				$vdc->db->query("UPDATE `[1]` SET `album_id` = '0' WHERE `album_id` = '[2]' AND `user_id`  = '[3]';", array(tbl_img, $vdc->input->post_vars['album'], $vdc->info->user_data['user_id']));
			
				$vdc->templ->message($vdc->lang['016'], true);
			}
			break;	
		case "settings":
			$vdc->templ->page_title .= sprintf($vdc->lang['003'], $vdc->info->user_data['username']);
			
			if ($vdc->info->is_user == false) {
				$vdc->templ->error($vdc->lang['002'], true);
			} else {
				$vdc->templ->templ_vars[] = array(
				   	"USER_ID" => $vdc->info->user_data['user_id'],
				   	"USERNAME" => $vdc->info->user_data['username'],
				   	"IP_ADDRESS" => $vdc->info->user_data['ip_address'],
				   	"EMAIL_ADDRESS" => $vdc->info->user_data['email_address'],
					"IP_HOSTNAME" => gethostbyaddr($vdc->info->user_data['ip_address']),
				   	"TIME_JOINED" => date($vdc->info->config['date_format'], $vdc->info->user_data['register_time']),
				   	"BOXED_UPLOAD_YES" => (($vdc->info->user_data['upload_type'] == "boxed") ? "checked=\"checked\"" : NULL),
				   	"PRIVATE_GALLERY_NO" => (($vdc->info->user_data['private_gallery'] == 0) ? "checked=\"checked\"" : NULL),
				   	"PRIVATE_GALLERY_YES" => (($vdc->info->user_data['private_gallery'] == 1) ? "checked=\"checked\"" : NULL),
				   	"STANDARD_UPLOAD_YES" => (($vdc->info->user_data['upload_type'] == "standard") ? "checked=\"checked\"" : NULL),
				   	"USER_GROUP" => ((strpos($vdc->info->user_data['user_group'], "admin") == true) ? (($vdc->info->is_root == false) ? $vdc->lang['010'] : $vdc->lang['012']) : $vdc->lang['011']),
				);
				
				$vdc->templ->output("users", "user_settings_page");
			}
			break;
		case "settings-s":
			$vdc->templ->page_title .= sprintf($vdc->lang['003'], $vdc->info->user_data['username']);
			
			if ($vdc->info->is_user == false) {
				$vdc->templ->error($vdc->lang['002'], true);
			} elseif ($vdc->funcs->is_null($vdc->input->post_vars['email_address']) == true) {
				$vdc->templ->error($vdc->lang['004'], true);
			} elseif ($vdc->funcs->valid_email($vdc->input->post_vars['email_address']) == false) {
				$vdc->templ->error(sprintf($vdc->lang['005'], strtolower($vdc->input->post_vars['email_address'])), true);
			} elseif ($vdc->funcs->is_null($vdc->input->post_vars['password']) == false && strlen($vdc->input->post_vars['password']) < 6 || strlen($vdc->input->post_vars['password']) > 30) {
				$vdc->templ->error($vdc->lang['006'], true);
			} elseif (strtolower($vdc->input->post_vars['email_address']) !== $vdc->info->user_data['email_address'] && $vdc->db->total_rows($vdc->db->query("SELECT * FROM `[1]` WHERE `email_address` = '[2]' LIMIT 1;", array(tbl_user, strtolower($vdc->input->post_vars['email_address'])))) == 1) {
				$vdc->templ->error(sprintf($vdc->lang['007'], strtolower($vdc->input->post_vars['email_address'])), true);
			} else {
				$vdc->db->query("UPDATE `[1]` SET `email_address` = '[2]', `private_gallery` = '[3]', `upload_type` = '[4]' WHERE `user_id` = '[5]';", array(tbl_user, strtolower($vdc->input->post_vars['email_address']), $vdc->input->post_vars['private_gallery'], $vdc->input->post_vars['upload_type'], $vdc->info->user_data['user_id']));
			
				if ($vdc->funcs->is_null($vdc->input->post_vars['password']) == false && $vdc->input->post_vars['password'] !== "*************") {
					$vdc->db->query("UPDATE `[1]` SET `password` = '[2]' WHERE `user_id` = '[3]';", array(tbl_user, md5($vdc->input->post_vars['password']), $vdc->info->user_data['user_id']));
				}
				
				$vdc->templ->message($vdc->lang['008'], true);
			}
			break;
		default: 
			$vdc->templ->error($vdc->lang['009'], true);
	}
	
?>