<?php
	require_once "../../includes/vdc.php";
	
	
	$vdc->templ->page_header = $vdc->templ->parse_template("admin/page_header");
	
	$vdc->templ->templ_vars[] = array("VERSION" => $vdc->info->version);
	$vdc->templ->page_footer = $vdc->templ->parse_template("admin/page_footer");
	
	if ($vdc->info->is_admin == false) {
		$vdc->templ->error($vdc->lang['002'], true);	
	}
	
	switch ($vdc->input->get_vars['task']) {
		case "delete_files":
			if ($vdc->funcs->is_null($vdc->input->get_vars['files']) == true || $vdc->funcs->is_null($vdc->input->get_vars['lb_div']) == true) {
				exit($vdc->templ->lightbox_error($vdc->lang['009']));
			} else {
				$files2delete = $vdc->image->basename(explode(",", $vdc->input->get_vars['files']));
				
				foreach ($files2delete as $id => $img_name) {
					if ($vdc->funcs->is_null($img_name) == true) {
						exit($vdc->templ->lightbox_error($vdc->lang['530']));
					} elseif ($vdc->funcs->is_file($img_name, $root_path.$vdc->info->config['upload_path'], true) == false) {
						exit($vdc->templ->lightbox_error(sprintf($vdc->lang['843'], $img_name)));
					}
				}
				
				$vdc->templ->templ_vars[] = array(
					"LIGHTBOX_ID" => $vdc->input->get_vars['lb_div'],
					"FILES2DELETE" => $vdc->input->get_vars['files'],
					"RETURN_URL" => urldecode($vdc->input->get_vars['return']),
				);
				
				exit($vdc->templ->parse_template("admin/admin", "delete_files_lightbox"));
			}
		case "delete_files-d":
			$file_list = $vdc->image->basename(explode(",", (($vdc->funcs->is_null($vdc->input->get_vars['d']) == false) ? $vdc->input->get_vars['files'] : $vdc->input->post_vars['files'])));
		
			if ($vdc->funcs->is_null($file_list) == true) {
				$vdc->templ->error($vdc->lang['009'], true);
			} else {
				foreach ($file_list as $id => $img_name) {
					if ($vdc->funcs->is_null($img_name) == true) {
						$vdc->templ->error($vdc->lang['530'], true);
					} elseif ($vdc->funcs->is_file($img_name, $root_path.$vdc->info->config['upload_path'], true) == false) {
						$vdc->templ->error(sprintf($vdc->lang['843'], $img_name), true);
					} else {
						if (unlink($root_path.$vdc->info->config['upload_path'].$img_name) == false) {
							$vdc->templ->error(sprintf($vdc->lang['460'], $img_name), true);
						}
						
						if ($vdc->funcs->file_exists($root_path.$vdc->info->config['upload_path'].($thumbnail = $vdc->image->thumbnail_name($img_name))) == true) {
							if (unlink($root_path.$vdc->info->config['upload_path'].$thumbnail) == false) {
								$vdc->templ->error(sprintf($vdc->lang['687'], $img_name), true);
							}
						}
						
						$vdc->db->query("DELETE FROM `[1]` WHERE `img_name` = '[2]';", array(tbl_img, $img_name));
					}
				}
				
				$vdc->templ->message(sprintf($vdc->lang['565'], (($vdc->funcs->is_null($vdc->input->post_vars['return']) == true) ? base64_encode($vdc->info->base_url) : $vdc->input->post_vars['return'])), true);
			}
			break;
		case "user_list":
			$sql = $vdc->db->query("SELECT * FROM `[1]` ORDER BY `user_id` DESC LIMIT <# QUERY_LIMIT #>;", array(tbl_user));
		
			while ($row = $vdc->db->fetch_array($sql)) {
				$vdc->templ->templ_globals['get_whileloop'] = true;
				
				$vdc->templ->templ_vars[] = array(
					"USER_ID" => $row['user_id'],
					"USERNAME" => $row['username'],
					"IP_ADDRESS" => $row['ip_address'],
					"EMAIL_ADDRESS" => $row['email_address'],
					"IP_HOSTNAME" => gethostbyaddr($row['ip_address']),
					"TDCLASS" => $tdclass = (($tdclass == "tdrow1") ? "tdrow2" : "tdrow1"),
					"TIME_JOINED" => date($vdc->info->config['date_format'], $row['register_time']),
					"GALLERY_STATUS" => (($row['private_gallery'] == 1) ? $vdc->lang['481'] : $vdc->lang['646']),
					"TOTAL_UPLOADS" => $vdc->funcs->number_format($vdc->db->total_rows($vdc->db->query("SELECT * FROM `[1]` WHERE `user_id` = '[2]';", array(tbl_img, $row['user_id'])))),
				);
				
				$vdc->templ->templ_globals['user_list_whileloop'] .= $vdc->templ->parse_template("admin/admin", "user_list_page");
				unset($vdc->templ->templ_globals['get_whileloop'], $vdc->templ->templ_vars);	
			}
			
			$vdc->templ->templ_vars[] = array("PAGINATION_LINKS" => $vdc->templ->pagelinks("admin.php?task=user_list", $vdc->db->total_rows($vdc->db->query("SELECT * FROM `[1]`;", array(tbl_user)))));
			
			$vdc->templ->output("admin/admin", "user_list_page");
			break;
		case "users-s":
			$sql = $vdc->db->query("SELECT * FROM `[1]` WHERE `user_id` = '[2]' LIMIT 1;", array(tbl_user, $vdc->input->get_vars['id']));
			
			if ($vdc->funcs->is_null($vdc->input->get_vars['id']) == true) {
				$vdc->templ->error($vdc->lang['009'], true);	
			} elseif ($vdc->db->total_rows($sql) !== 1) {
				$vdc->templ->error($vdc->lang['278'], true);
			} else {
				$user_data = $vdc->db->fetch_array($sql);
				
				if ($user_data['user_group'] === "admin" && $vdc->info->is_root == false) {
					$vdc->templ->error($vdc->lang['772'], true);
				} else {
					$vdc->templ->templ_globals['is_root'] = (($user_data['user_group'] === "admin") ? true : false);
					
					$vdc->templ->templ_vars[] = array(
						"USER_ID" => $user_data['user_id'],
						"USERNAME" => $user_data['username'],
						"IP_ADDRESS" => $user_data['ip_address'],	   
						"EMAIL_ADDRESS" => $user_data['email_address'],
						"IP_HOSTNAME" => gethostbyaddr($user_data['ip_address']),
						"TIME_JOINED" => date($vdc->info->config['date_format'], $user_data['register_time']),
						"BOXED_UPLOAD_YES" => (($user_data['upload_type'] == "boxed") ? "checked=\"checked\"" : NULL),
						"PRIVATE_GALLERY_NO" => (($user_data['private_gallery'] == 0) ? "checked=\"checked\"" : NULL),
						"PRIVATE_GALLERY_YES" => (($user_data['private_gallery'] == 1) ? "checked=\"checked\"" : NULL),
						"STANDARD_UPLOAD_YES" => (($user_data['upload_type'] == "standard") ? "checked=\"checked\"" : NULL),
						"ADMIN_USER_YES" => (($user_data['user_group'] === "normal_admin") ? "selected=\"selected\"" : NULL),
						"NORMAL_USER_YES" => (($user_data['user_group'] === "normal_user") ? "selected=\"selected\"" : NULL),
						"ACCOUNT_COUNT" => $vdc->db->total_rows($vdc->db->query("SELECT * FROM `[1]` WHERE `ip_address` = '[2]';", array(tbl_user, $user_data['ip_address']))),
					);
					
					$vdc->templ->output("admin/admin", "user_settings_page");
				}
			}
			break;
		case "users-s-s":
			$sql = $vdc->db->query("SELECT * FROM `[1]` WHERE `user_id` = '[2]' LIMIT 1;", array(tbl_user, $vdc->input->post_vars['user_id']));
			
			if ($vdc->funcs->is_null($vdc->input->post_vars['user_id']) == true) {
				$vdc->templ->error($vdc->lang['009'], true);	
			} elseif ($vdc->db->total_rows($sql) !== 1) {
				$vdc->templ->error($vdc->lang['278'], true);
			} elseif ($vdc->funcs->is_null($vdc->input->post_vars['email_address']) == true) {
				$vdc->templ->error($vdc->lang['362'], true);
			} elseif ($vdc->funcs->valid_email($vdc->input->post_vars['email_address']) == false) {
				$vdc->templ->error(sprintf($vdc->lang['112'], strtolower($vdc->input->post_vars['email_address'])), true);
			} elseif ($vdc->funcs->is_null($vdc->input->post_vars['password']) == false && strlen($vdc->input->post_vars['password']) < 6 || strlen($vdc->input->post_vars['password']) > 30) {
				$vdc->templ->error($vdc->lang['337'], true);
			} else {
				$user_data = $vdc->db->fetch_array($sql);
				
				if ($user_data['user_group'] === "admin" && $vdc->info->is_root == false) {
					$vdc->templ->error($vdc->lang['772'], true);
				} else {
					$vdc->db->query("UPDATE `[1]` SET `email_address` = '[2]', `private_gallery` = '[3]', `upload_type` = '[4]' WHERE `user_id` = '[5]';", array(tbl_user, strtolower($vdc->input->post_vars['email_address']), $vdc->input->post_vars['private_gallery'], $vdc->input->post_vars['upload_type'], $user_data['user_id']));
					
					if ($vdc->info->is_root == true && $user_data['user_group'] !== "admin") {
						$vdc->db->query("UPDATE `[1]` SET `user_group` = '[2]' WHERE `user_id` = '[3]';", array(tbl_user, (($vdc->input->post_vars['user_group'] == 1) ? "normal_user" : "normal_admin"), $user_data['user_id']));
					}
					
					if ($vdc->funcs->is_null($vdc->input->post_vars['password']) == false && $vdc->input->post_vars['password'] !== "*************") {
						$vdc->db->query("UPDATE `[1]` SET `password` = '[2]' WHERE `user_id` = '[3]';", array(tbl_user, md5($vdc->input->post_vars['password']), $user_data['user_id']));
					}
					
					$vdc->templ->message($vdc->lang['330'], true);
				}
			}
			break;
		case "users-d":
			$sql = $vdc->db->query("SELECT * FROM `[1]` WHERE `user_id` = '[2]' LIMIT 1;", array(tbl_user, $vdc->input->get_vars['id']));
			
			if ($vdc->funcs->is_null($vdc->input->get_vars['id']) == true) {
				exit($vdc->templ->lightbox_error($vdc->lang['009']));	
			} elseif ($vdc->db->total_rows($sql) !== 1) {
				exit($vdc->templ->lightbox_error($vdc->lang['278']));
			} else {
				$user_data = $vdc->db->fetch_array($sql);
				
				if ($user_data['user_group'] === "admin") {
					exit($vdc->templ->lightbox_error($vdc->lang['478']));
				} else {
					$vdc->templ->templ_vars[] = array(
						"USER2DELETE" => $vdc->input->get_vars['id'],
						"LIGHTBOX_ID" => $vdc->input->get_vars['lb_div'],
					);
					
					exit($vdc->templ->parse_template("admin/admin", "delete_user_lightbox"));
				}
			}
			break;
		case "users-d-d":
			$sql = $vdc->db->query("SELECT * FROM `[1]` WHERE `user_id` = '[2]' LIMIT 1;", array(tbl_user, $vdc->input->post_vars['id']));
			
			if ($vdc->funcs->is_null($vdc->input->post_vars['id']) == true) {
				$vdc->templ->error($vdc->lang['009'], true);	
			} elseif ($vdc->db->total_rows($sql) !== 1) {
				$vdc->templ->error($vdc->lang['278'], true);
			} else {
				$user_data = $vdc->db->fetch_array($sql);
				
				if ($user_data['user_group'] === "admin") {
					$vdc->templ->error($vdc->lang['478'], true);
				} else {
					$sql = $vdc->db->query("SELECT * FROM `[1]` WHERE `user_id` = '[2]';", array(tbl_img, $user_data['user_id']));
					
					while ($row = $vdc->db->fetch_array($sql)) {
						if ($vdc->funcs->is_file($row['img_name'], $root_path.$vdc->info->config['upload_path'], true) == true) {
							unlink($root_path.$vdc->info->config['upload_path'].$row['img_name']);
									
							if ($vdc->funcs->file_exists($root_path.$vdc->info->config['upload_path'].($thumbnail = $vdc->image->thumbnail_name($row['img_name']))) == true) {
								unlink($root_path.$vdc->info->config['upload_path'].$thumbnail);
							}
									
							$vdc->db->query("DELETE FROM `[1]` WHERE `img_name` = '[2]';", array(tbl_img, $row['img_name']));
						}
					}
					
					$vdc->db->query("DELETE FROM `[1]` WHERE `user_id` = '[2]';", array(tbl_user, $user_data['user_id']));
					
					$vdc->templ->message($vdc->lang['157'], true);
				}
			}
			break;
		case "rename_img_title":
			if ($vdc->funcs->is_null($vdc->input->get_vars['file']) == true) {
				$vdc->templ->error($vdc->lang['009'], true);
			} elseif ($vdc->funcs->is_file($vdc->input->get_vars['file'], $root_path.$vdc->info->config['upload_path'], true) == false) {
				$vdc->templ->error(sprintf($vdc->lang['843'], $vdc->image->basename($vdc->input->get_vars['file'])), true);
			} else {			
				$new_title = htmlentities($vdc->input->get_vars['title']);
				
				$vdc->db->query("UPDATE `[1]` SET `img_title` = '[2]' WHERE `img_name` = '[3]';", array(tbl_img, $new_title, $vdc->image->basename($vdc->input->get_vars['file'])));
				
				exit($new_title);
			}
			break;
		case "move_files":
			if ($vdc->funcs->is_null($vdc->input->get_vars['id']) == true || $vdc->funcs->is_null($vdc->input->get_vars['files']) == true || $vdc->funcs->is_null($vdc->input->get_vars['lb_div']) == true) {
				exit($vdc->templ->lightbox_error($vdc->lang['009']));
			} else {
				$sql = $vdc->db->query("SELECT * FROM `[1]` WHERE `user_id` = '[2]' LIMIT 1;", array(tbl_user, $vdc->input->get_vars['id']));
				
				if ($vdc->db->total_rows($sql) !== 1) {
					exit($vdc->templ->lightbox_error($vdc->lang['278']));
				} else {
					$user_data = $vdc->db->fetch_array($sql);
				
					$files2move = $vdc->image->basename(explode(",", $vdc->input->get_vars['files']));
					
					foreach ($files2move as $id => $img_name) {
						if ($vdc->funcs->is_null($img_name) == true) {
							exit($vdc->templ->lightbox_error($vdc->lang['530']));
						} elseif ($vdc->funcs->is_file($img_name, $root_path.$vdc->info->config['upload_path'], true, $user_data['user_id']) == false) {
							exit($vdc->templ->lightbox_error(sprintf($vdc->lang['843'], $img_name)));
						} 
					}
					
					$sql = $vdc->db->query("SELECT * FROM `[1]` WHERE `user_id` = '[2]';", array(tbl_album, $user_data['user_id']));
				
					while ($row = $vdc->db->fetch_array($sql)) {
						$vdc->templ->templ_globals['get_whileloop'] = true;
						
						$vdc->templ->templ_vars[] = array(
							"ALBUM_ID" => $row['album_id'],
							"ALBUM_NAME" => $row['album_title'],
						);
						
						$vdc->templ->templ_globals['album_options_whileloop'] .= $vdc->templ->parse_template("admin/admin", "move_files_lightbox");
						unset($vdc->templ->templ_vars, $vdc->templ->templ_globals['get_whileloop']);
					}
					
					$vdc->templ->templ_vars[] = array(
						"USER_ID" => $user_data['user_id'],
						"FILES2MOVE" => $vdc->input->get_vars['files'],
						"LIGHTBOX_ID" => $vdc->input->get_vars['lb_div'],
						"RETURN_URL" => urldecode($vdc->input->get_vars['return']),
					);
					
					exit($vdc->templ->parse_template("admin/admin", "move_files_lightbox"));
				}
			}
			break;
		case "move_files-d":
			if ($vdc->funcs->is_null($vdc->input->post_vars['move_to']) == true || $vdc->funcs->is_null($vdc->input->post_vars['user_id']) == true || $vdc->funcs->is_null($vdc->input->post_vars['files']) == true) {
				$vdc->templ->error($vdc->lang['009'], true);
			} else {
				$sql = $vdc->db->query("SELECT * FROM `[1]` WHERE `user_id` = '[2]' LIMIT 1;", array(tbl_user, $vdc->input->post_vars['user_id']));
				
				if ($vdc->db->total_rows($sql) !== 1) {
					$vdc->templ->error($vdc->lang['278'], true);
				} else {
					$user_data = $vdc->db->fetch_array($sql);
						
					$files2move = $vdc->image->basename(explode(",", $vdc->input->post_vars['files']));
					
					foreach ($files2move as $id => $img_name) {
						if ($vdc->funcs->is_null($img_name) == true) {
							$vdc->templ->error($vdc->lang['530'], true);
						} elseif ($vdc->funcs->is_file($img_name, $root_path.$vdc->info->config['upload_path'], true, $user_data['user_id']) == false) {
							$vdc->templ->error(sprintf($vdc->lang['843'], $img_name), true);
						} else {
							$vdc->db->query("UPDATE `[1]` SET `album_id` = '[2]' WHERE `img_name` = '[3]';", array(tbl_img, $vdc->input->post_vars['move_to'], $img_name));
						}
					}
					
					$vdc->templ->message(sprintf($vdc->lang['413'], (($vdc->funcs->is_null($vdc->input->post_vars['return']) == true) ? base64_encode($vdc->info->base_url) : $vdc->input->post_vars['return']), $user_data['user_id'], $vdc->input->post_vars['move_to']), true);
				}
			}
			break;
		case "albums-c":
			if ($vdc->funcs->is_null($vdc->input->get_vars['id']) == true || $vdc->funcs->is_null($vdc->input->get_vars['lb_div']) == true) {
				exit($vdc->templ->lightbox_error($vdc->lang['009']));
			} else {
				$sql = $vdc->db->query("SELECT * FROM `[1]` WHERE `user_id` = '[2]' LIMIT 1;", array(tbl_user, $vdc->input->get_vars['id']));
				
				if ($vdc->db->total_rows($sql) !== 1) {
					exit($vdc->templ->lightbox_error($vdc->lang['278']));
				} else {
					$user_data = $vdc->db->fetch_array($sql);
					
					$vdc->templ->templ_vars[] = array(
						"USER_ID" => $user_data['user_id'],
						"LIGHTBOX_ID" => $vdc->input->get_vars['lb_div'],
						"RETURN_URL" => urldecode($vdc->input->get_vars['return']),
					 );
					
					exit($vdc->templ->parse_template("admin/admin", "new_album_lightbox"));
				}
			}
			break;
		case "albums-c-d":
			$album_title = htmlspecialchars($vdc->input->post_vars['album_title']);
			
			if ($vdc->funcs->is_null($album_title) == true) {
				$vdc->templ->error($vdc->lang['362'], true);
			} elseif ($vdc->funcs->is_null($vdc->input->post_vars['user_id']) == true) {
				$vdc->templ->error($vdc->lang['009'], true);
			} else {
				$sql = $vdc->db->query("SELECT * FROM `[1]` WHERE `user_id` = '[2]' LIMIT 1;", array(tbl_user, $vdc->input->post_vars['user_id']));
				
				if ($vdc->db->total_rows($sql) !== 1) {
					$vdc->templ->error($vdc->lang['278'], true);
				} else {
					$user_data = $vdc->db->fetch_array($sql);
					
					if ($vdc->db->total_rows($vdc->db->query("SELECT * FROM `[1]` WHERE `album_title` = '[2]' AND `user_id` = '[3]' LIMIT 1;", array(tbl_album, $album_title, $user_data['user_id']))) == 1) {
						$vdc->templ->error(sprintf($vdc->lang['746'], $album_title), true);
					} else {
						$vdc->db->query("INSERT INTO `[1]` (`album_title`, `user_id`) VALUES ('[2]', '[3]');", array(tbl_album, $album_title, $user_data['user_id']));
						
						$newalbum = $vdc->db->fetch_array($vdc->db->query("SELECT * FROM `[1]` WHERE `album_title` = '[2]' AND `user_id` = '[3]' LIMIT 1;", array(tbl_album, $album_title, $user_data['user_id'])));
						
						$vdc->templ->message(sprintf($vdc->lang['412'], $album_title, (($vdc->funcs->is_null($vdc->input->post_vars['return']) == true) ? base64_encode($vdc->info->base_url) : $vdc->input->post_vars['return']), $user_data['user_id'], $newalbum['album_id']), true);
					}
				}
			}
			break;
		case "albums-r":
			if ($vdc->funcs->is_null($vdc->input->get_vars['id']) == true || $vdc->funcs->is_null($vdc->input->get_vars['album']) == true || $vdc->funcs->is_null($vdc->input->get_vars['lb_div']) == true) {
				exit($vdc->templ->lightbox_error($vdc->lang['009']));
			} else {
				$sql = $vdc->db->query("SELECT * FROM `[1]` WHERE `user_id` = '[2]' LIMIT 1;", array(tbl_user, $vdc->input->get_vars['id']));
			
				if ($vdc->db->total_rows($sql) !== 1) {
					exit($vdc->templ->lightbox_error($vdc->lang['278']));
				} else {
					$user_data = $vdc->db->fetch_array($sql);
					
					$sql = $vdc->db->query("SELECT * FROM `[1]` WHERE `album_id` = '[2]' AND `user_id` = '[3]' LIMIT 1;", array(tbl_album, $vdc->input->get_vars['album'], $user_data['user_id']));
					
					if ($vdc->db->total_rows($sql) !== 1) {
						exit($vdc->templ->lightbox_error($vdc->lang['338']));
					} else {
						$oldalbum = $vdc->db->fetch_array($sql);
						
						$vdc->templ->templ_vars[] = array(
							"USER_ID" => $user_data['user_id'],
							"ALBUM_ID" => $oldalbum['album_id'],
							"OLD_TITLE" => $oldalbum['album_title'],
							"LIGHTBOX_ID" => $vdc->input->get_vars['lb_div'],
							"RETURN_URL" => urldecode($vdc->input->get_vars['return']),
						);
						
						exit($vdc->templ->parse_template("admin/admin", "rename_album_lightbox"));
					}
				}
			}
			break;
		case "albums-r-d":
			$album_title = htmlspecialchars($vdc->input->post_vars['album_title']);
			
			if ($vdc->funcs->is_null($vdc->input->post_vars['album']) == true || $vdc->funcs->is_null($vdc->input->post_vars['user_id']) == true) {
				$vdc->templ->error($vdc->lang['009'], true);
			} elseif ($vdc->funcs->is_null($album_title) == true) {
				$vdc->templ->error($vdc->lang['362'], true);
			} else {
				$sql = $vdc->db->query("SELECT * FROM `[1]` WHERE `user_id` = '[2]' LIMIT 1;", array(tbl_user, $vdc->input->post_vars['user_id']));
				
				if ($vdc->db->total_rows($sql) !== 1) {
					$vdc->templ->error($vdc->lang['278'], true);
				} else {
					$user_data = $vdc->db->fetch_array($sql);
					
					if ($vdc->db->total_rows($vdc->db->query("SELECT * FROM `[1]` WHERE `album_title` = '[2]' AND `user_id` = '[3]' LIMIT 1;", array(tbl_album, $album_title, $user_data['user_id']))) == 1) {
						$vdc->templ->error(sprintf($vdc->lang['746'], $album_title), true);
					} else {
						if ($vdc->db->total_rows(($albumsql = $vdc->db->query("SELECT * FROM `[1]` WHERE `album_id` = '[2]' AND `user_id` = '[3]' LIMIT 1;", array(tbl_album, $vdc->input->post_vars['album'], $user_data['user_id'])))) !== 1) {
							$vdc->templ->error($vdc->lang['338'], true);
						} else {
							$oldalbum = $vdc->db->fetch_array($albumsql);
							
							$vdc->db->query("UPDATE `[1]` SET `album_title` = '[2]' WHERE `album_id` = '[3]';", array(tbl_album, $album_title, $oldalbum['album_id']));
							
							$vdc->templ->message(sprintf($vdc->lang['101'], $oldalbum['album_title'], $album_title, (($vdc->funcs->is_null($vdc->input->post_vars['return']) == true) ? base64_encode($vdc->info->base_url) : $vdc->input->post_vars['return']), $user_data['user_id'], $oldalbum['album_id']), true);
						}
					}
				}
			}
			break;
		case "albums-d":
			if ($vdc->funcs->is_null($vdc->input->get_vars['id']) == true || $vdc->funcs->is_null($vdc->input->get_vars['album']) == true || $vdc->funcs->is_null($vdc->input->get_vars['lb_div']) == true) {
				exit($vdc->templ->lightbox_error($vdc->lang['009']));
			} else {
				$sql = $vdc->db->query("SELECT * FROM `[1]` WHERE `user_id` = '[2]' LIMIT 1;", array(tbl_user, $vdc->input->get_vars['id']));
				
				if ($vdc->db->total_rows($sql) !== 1) {
					exit($vdc->templ->lightbox_error($vdc->lang['278']));
				} else {
					$user_data = $vdc->db->fetch_array($sql);
					
					if ($vdc->db->total_rows(($albumsql = $vdc->db->query("SELECT * FROM `[1]` WHERE `album_id` = '[2]' AND `user_id` = '[3]' LIMIT 1;", array(tbl_album, $vdc->input->get_vars['album'], $user_data['user_id'])))) !== 1) {
						exit($vdc->templ->lightbox_error($vdc->lang['442']));
					} else {
						$oldalbum = $vdc->db->fetch_array($albumsql);
							
						$vdc->templ->templ_vars[] = array(
							"USER_ID" => $user_data['user_id'],
							"ALBUM2DELETE" => $oldalbum['album_id'],
							"LIGHTBOX_ID" => $vdc->input->get_vars['lb_div'],
						);
						
						exit($vdc->templ->parse_template("admin/admin", "delete_album_lightbox"));
					}
				}
			}
			break;
		case "albums-d-d":
			if ($vdc->funcs->is_null($vdc->input->post_vars['album']) == true || $vdc->funcs->is_null($vdc->input->post_vars['user_id']) == true) {
				$vdc->templ->error($vdc->lang['009'], true);
			} else {
				$sql = $vdc->db->query("SELECT * FROM `[1]` WHERE `user_id` = '[2]' LIMIT 1;", array(tbl_user, $vdc->input->post_vars['user_id']));
				
				if ($vdc->db->total_rows($sql) !== 1) {
					$vdc->templ->error($vdc->lang['278'], true);
				} else {
					$user_data = $vdc->db->fetch_array($sql);
					
					if ($vdc->db->total_rows($vdc->db->query("SELECT * FROM `[1]` WHERE `album_id` = '[2]' AND `user_id` = '[3]' LIMIT 1;", array(tbl_album, $vdc->input->post_vars['album'], $user_data['user_id']))) !== 1) {
						$vdc->templ->error($vdc->lang['442'], true);
					} else {
						$vdc->db->query("DELETE FROM `[1]` WHERE `album_id` = '[2]' AND `user_id`  = '[3]';", array(tbl_album, $vdc->input->post_vars['album'], $user_data['user_id']));
						$vdc->db->query("UPDATE `[1]` SET `album_id` = '0' WHERE `album_id` = '[2]' AND `user_id` = '[3]';", array(tbl_img, $vdc->input->post_vars['album'], $user_data['user_id']));
						
						$vdc->templ->message(sprintf($vdc->lang['738'], $user_data['user_id']), true);
					}
				}
			}
			break;	
		default:
			$vdc->info->selected_album = (int)$vdc->input->get_vars['cat'];
			$vdc->info->selected_gallery = (int)$vdc->input->get_vars['gal'];
			
			$vdc->info->user_owned_gallery = (($vdc->funcs->is_null($vdc->info->selected_gallery) == false) ? true : false);
			$vdc->info->gallery_owner_data = (($vdc->info->user_owned_gallery == true) ? $vdc->db->fetch_array($vdc->db->query("SELECT * FROM `[1]` WHERE `user_id` = '[2]' LIMIT 1;", array(tbl_user, $vdc->info->selected_gallery))) : array("user_id" => 0, "username" => $vdc->info->config['site_name']));
			
			$vdc->info->gallery_url = sprintf("%sadmin.php?gal=%s", $vdc->info->base_url, $vdc->info->gallery_owner_data['user_id']);
			$vdc->info->gallery_url_full = sprintf("%s%s", $vdc->info->gallery_url, (($vdc->funcs->is_null($vdc->info->selected_album) == true) ? NULL : "&amp;cat={$vdc->info->selected_album}"));
			
			if ($vdc->funcs->is_null($vdc->info->gallery_owner_data['user_id']) == true && $vdc->funcs->is_null($vdc->info->selected_gallery) == false) {
				$vdc->templ->error($vdc->lang['383'], true);
			} else {
				$sql = $vdc->db->query("SELECT * FROM `[1]` WHERE `user_id` = '[2]' AND `album_id` = '[3]' AND (`img_name` LIKE '%[4]%' OR `img_title` LIKE '%[4]%') ORDER BY `img_id` DESC LIMIT <# QUERY_LIMIT #>;", array(tbl_img, $vdc->info->gallery_owner_data['user_id'], $vdc->info->selected_album, urldecode($vdc->input->get_vars['search'])));
				
				if ($vdc->db->total_rows($sql) < 1) {
					$vdc->templ->templ_globals['empty_gallery'] = true;
				} else {
					$vdc->templ->templ_globals['file_options'] = true;
					
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
				
				if ($vdc->info->user_owned_gallery == true) {
					$sql = $vdc->db->query("SELECT * FROM `[1]` WHERE `user_id` = '[2]' LIMIT 50;", array(tbl_album, $vdc->info->gallery_owner_data['user_id']));
					
					while ($row = $vdc->db->fetch_array($sql)) {
						$vdc->templ->templ_globals['get_whileloop'] = true;
						
						if ($vdc->info->selected_album == $row['album_id']) {
							$curalbum = $row;
						}
						
						$vdc->templ->templ_vars[] = array(
							"ALBUM_ID" => $row['album_id'],
							"ALBUM_NAME" => $row['album_title'],
							"GALLERY_URL" => $vdc->info->gallery_url,
							"FULL_GALLERY_URL" => $vdc->info->gallery_url_full,
							"RETURN_URL" => base64_encode($vdc->info->page_url),
							"GALLERY_ID" => $vdc->info->gallery_owner_data['user_id'],
							"TOTAL_UPLOADS" => $vdc->funcs->number_format($vdc->db->total_rows($vdc->db->query("SELECT * FROM `[1]` WHERE `user_id` = '[2]' AND `album_id` = '[3]';", array(tbl_img, $vdc->info->gallery_owner_data['user_id'], $row['album_id'])))),
						);
						
						$vdc->templ->templ_globals['album_pulldown_whileloop'] .= $vdc->templ->parse_template("admin/admin", "admin_gallery_page");
						unset($vdc->templ->templ_vars, $vdc->templ->templ_globals['get_whileloop']);
					}
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
					"EMPTY_GALLERY" => $vdc->templ->message((($vdc->funcs->is_null($vdc->input->get_vars['search']) == false) ? $vdc->lang['598'] : $vdc->lang['463']), false),
					"TOTAL_UPLOADS" => $vdc->funcs->number_format($vdc->db->total_rows($vdc->db->query("SELECT * FROM `[1]` WHERE `user_id` = '[2]';", array(tbl_img, $vdc->info->gallery_owner_data['user_id'])))),
					"TOTAL_ROOT_UPLOADS" => $vdc->funcs->number_format($vdc->db->total_rows($vdc->db->query("SELECT * FROM `[1]` WHERE `user_id` = '[2]' AND `album_id` = '0';", array(tbl_img, $vdc->info->gallery_owner_data['user_id'])))),
					"PAGINATION_LINKS" => $vdc->templ->pagelinks(sprintf("%s%s", $vdc->info->gallery_url_full, (($vdc->funcs->is_null($vdc->input->get_vars['search']) == true) ? NULL : sprintf("&amp;search=%s", urldecode($vdc->input->get_vars['search'])))), $vdc->db->total_rows($vdc->db->query("SELECT * FROM `[1]` WHERE `user_id` = '[2]' AND `album_id` = '[3]' AND (`img_name` LIKE '%[4]%' OR `img_title` LIKE '%[4]%') ORDER BY `img_id` DESC;", array(tbl_img, $vdc->info->gallery_owner_data['user_id'], $vdc->info->selected_album, urldecode($vdc->input->get_vars['search']))))),	
				);
				
				$vdc->templ->output("admin/admin", "admin_gallery_page");
			}
	}

?>