<?php

	
	require_once "./source/includes/data.php";
	require_once "{$vdcclass->info->root_path}source/language/users.php";
	
	$vdcclass->templ->page_title = sprintf($vdcclass->lang['001'], $vdcclass->info->config['site_name']);
	
	switch ($vdcclass->input->get_vars['act']) {
		case "register":
			$vdcclass->templ->page_title .= $vdcclass->lang['041'];
			
			if ($vdcclass->info->config['registration_disabled'] == true) {
				$vdcclass->templ->error($vdcclass->lang['040'], true);
			} else {
				$vdcclass->templ->templ_vars[] = array(
					"SITE_NAME" => $vdcclass->info->config['site_name'],
					"CAPTCHA_CODE" => recaptcha_get_html($vdcclass->info->config['recaptcha_public']),
					"RETURN_URL" => (($vdcclass->funcs->is_null($vdcclass->input->get_vars['return']) == true) ? base64_encode($vdcclass->info->base_url) : $vdcclass->input->get_vars['return']),
				);
				
				$vdcclass->templ->output("users", "registration_page");
			}
			break;
		case "register-d":
			$vdcclass->templ->page_title .= $vdcclass->lang['041'];
			
			$recaptcha_check = recaptcha_check_answer($vdcclass->info->config['recaptcha_private'], $vdcclass->input->server_vars['remote_addr'], $vdcclass->input->post_vars["recaptcha_challenge_field"], $vdcclass->input->post_vars["recaptcha_response_field"]);
		
			// Lot of checks for keeping your site secure. :-)
			
			if ($vdcclass->info->config['registration_disabled'] == true) {
				$vdcclass->templ->error($vdcclass->lang['040'], true);
			} elseif ($vdcclass->funcs->is_null($vdcclass->input->post_vars['username'])  == true || $vdcclass->funcs->is_null($vdcclass->input->post_vars['password']) == true || $vdcclass->funcs->is_null($vdcclass->input->post_vars['password-c']) == true || $vdcclass->funcs->is_null($vdcclass->input->post_vars['email_address']) == true || $vdcclass->funcs->is_null($vdcclass->input->post_vars['iagree']) == true) {
				$vdcclass->templ->error($vdcclass->lang['004'], true);
			} elseif ($recaptcha_check->is_valid == false) {
				$vdcclass->templ->error($vdcclass->lang['061'], true);
			} elseif ($vdcclass->funcs->valid_email($vdcclass->input->post_vars['email_address']) == false) {
				$vdcclass->templ->error(sprintf($vdcclass->lang['005'], strtolower($vdcclass->input->post_vars['email_address'])), true);
			} elseif (strlen($vdcclass->input->post_vars['password']) < 6 || strlen($vdcclass->input->post_vars['password']) > 30) {
				$vdcclass->templ->error($vdcclass->lang['006'], true);
			} elseif ($vdcclass->input->post_vars['password'] !== $vdcclass->input->post_vars['password-c']) {
				$vdcclass->templ->error($vdcclass->lang['042'], true);
			} elseif ($vdcclass->funcs->valid_string($vdcclass->input->post_vars['username']) == false || strlen($vdcclass->input->post_vars['username']) < 3 || strlen($vdcclass->input->post_vars['username']) > 30) {
				$vdcclass->templ->error($vdcclass->lang['043'], true);
			} elseif ($vdcclass->db->total_rows($vdcclass->db->query("SELECT * FROM `[1]` WHERE `username` = '[2]' LIMIT 1;", array(MYSQL_USER_INFO_TABLE, $vdcclass->input->post_vars['username']))) == 1) {
				$vdcclass->templ->error(sprintf($vdcclass->lang['044'], $vdcclass->input->post_vars['username']), true);
			} elseif ($vdcclass->db->total_rows($vdcclass->db->query("SELECT * FROM `[1]` WHERE `email_address` = '[2]' LIMIT 1;", array(MYSQL_USER_INFO_TABLE, $vdcclass->input->post_vars['email_address']))) == 1) {
				$vdcclass->templ->error(sprintf($vdcclass->lang['007'], strtolower($vdcclass->input->post_vars['email_address'])), true);
			} elseif ($vdcclass->db->total_rows($vdcclass->db->query("SELECT * FROM `[1]` WHERE `ip_address` = '[2]' LIMIT 5;", array(MYSQL_USER_INFO_TABLE, $vdcclass->input->server_vars['remote_addr']))) >= 5) {
				$vdcclass->templ->templ_vars[] = array(
					"BASE_URL" => $vdcclass->info->base_url,
					"SITE_NAME" => $vdcclass->info->config['site_name'],
					"IP_ADDRESS" => $vdcclass->input->server_vars['remote_addr'],
				);
				
				$email_headers = "MIME-Version: 1.0\r\n";
				$email_headers .= "Content-type: text/html; charset=iso-8859-1\r\n";
				$email_headers .= "From: {$vdcclass->info->config['site_name']} <{$vdcclass->info->config['email_out']}>\r\n";
				
				mail($vdcclass->info->config['email_in'], $vdcclass->lang['704'], $vdcclass->templ->parse_template("users", "user_registration_hard_limit"), $email_headers);
				
				$vdcclass->templ->error($vdcclass->lang['992'], true);
			} else {
				$vdcclass->db->query("INSERT INTO `[1]` (`username`, `password`, `email_address`, `ip_address`, `private_gallery`, `time_joined`, `user_group`, `upload_type`) VALUES ('[2]', '[3]', '[4]', '[5]', '0', '[6]', 'normal_user', 'standard');", array(MYSQL_USER_INFO_TABLE, $vdcclass->input->post_vars['username'], md5($vdcclass->input->post_vars['password']), strtolower($vdcclass->input->post_vars['email_address']), $vdcclass->input->server_vars['remote_addr'], time()));
				
				$vdcclass->templ->message(sprintf($vdcclass->lang['045'], $vdcclass->input->post_vars['username'], $vdcclass->input->post_vars['return']), true);		
			}	
			break;
		case "check_username":
			if ($vdcclass->funcs->is_null($vdcclass->input->get_vars['username']) == false) {
				header("Content-Type: text/plain;"); 
				header("Content-Disposition: inline; filename=username_check.txt;");
				
				echo $vdcclass->db->total_rows($vdcclass->db->query("SELECT * FROM `[1]` WHERE `username` = '[2]' LIMIT 1;", array(MYSQL_USER_INFO_TABLE, $vdcclass->input->get_vars['username']))); exit;
			}
			break;
		case "login":
			if ($vdcclass->funcs->is_null($vdcclass->input->get_vars['lb_div']) == true) {
				exit($vdcclass->templ->lightbox_error($vdcclass->lang['013']));
			} else {
				$vdcclass->templ->templ_vars[] = array(
					"LIGHTBOX_ID" => $vdcclass->input->get_vars['lb_div'],
					"RETURN_URL" => urlencode($vdcclass->input->get_vars['return']),
				);
				
				exit($vdcclass->templ->parse_template("users", "login_lightbox"));
			}
			break;
		case "login-d":
			$vdcclass->templ->page_title .= $vdcclass->lang['046'];
			
			if ($vdcclass->funcs->is_null($vdcclass->input->post_vars['username']) == true || $vdcclass->funcs->is_null($vdcclass->input->post_vars['password']) == true) {
				$vdcclass->templ->error($vdcclass->lang['004'], true);
			} elseif ($vdcclass->db->total_rows(($user_data = $vdcclass->db->query("SELECT * FROM `[1]` WHERE `username` = '[2]' AND `password` = '[3]' LIMIT 1;", array(MYSQL_USER_INFO_TABLE, $vdcclass->input->post_vars['username'], md5($vdcclass->input->post_vars['password']))))) !== 1) {
				$vdcclass->templ->error($vdcclass->lang['047'], true);
			} else {
				setcookie("vdc_user_session", "session_delete", (time() - 60000)); // Delete old cookie with negative expiration time.
				
				$session_id = md5($vdcclass->funcs->random_string(30));
				$vdcclass->info->user_data = $vdcclass->db->fetch_array($user_data);
				$vdcclass->db->query("UPDATE `[1]` SET `ip_address` = '[2]' WHERE `user_id` = '[3]';", array(MYSQL_USER_INFO_TABLE, $vdcclass->input->server_vars['remote_addr'], $vdcclass->info->user_data['user_id']));
				$vdcclass->db->query("INSERT INTO `[1]` (session_id, session_start, user_id, user_agent, ip_address) VALUES ('[2]', '[3]', '[4]', '[5]', '[6]');", array(MYSQL_USER_SESSIONS_TABLE, $session_id, time(), $vdcclass->info->user_data['user_id'], $vdcclass->input->server_vars['http_user_agent'], $vdcclass->input->server_vars['remote_addr']));
				
				// The base64 is kinda redundant but serialization is good. - Expire time is now one month. Used to be one year. 
			
				if (setcookie("vdc_user_session", base64_encode(serialize(array("session_id" => $session_id, "user_id" => $vdcclass->info->user_data['user_id']))), (time() + 2629743), $vdcclass->info->script_path, NULL, IS_HTTPS_REQUEST, true) == true) {
					$vdcclass->info->is_user = true;
					$vdcclass->info->is_root = (($vdcclass->info->user_data['user_group'] === "root_admin") ? true : false);
					$vdcclass->info->is_admin = (($vdcclass->info->is_root == true || $vdcclass->info->user_data['user_group'] === "normal_admin") ? true : false);
				
					$vdcclass->templ->message(sprintf($vdcclass->lang['048'], (($vdcclass->funcs->is_null($vdcclass->input->post_vars['return']) == true) ? base64_encode($vdcclass->info->base_url) : $vdcclass->input->post_vars['return'])), true);
				} else {
					$vdcclass->templ->error($vdcclass->lang['049'], true);
				}
			}
			break;
		case "logout":
			$vdcclass->templ->page_title .= $vdcclass->lang['037'];
			
			if (setcookie("vdc_user_session", "session_delete", (time() - 60000)) == true) {
				// It would probably be a better security practice to delete all sesseions of this user, but we'll just do one. 
				
				$vdcclass->db->query("DELETE FROM `[1]` WHERE `session_id` = '[2]';", array(MYSQL_USER_SESSIONS_TABLE, $vdcclass->info->user_session['session_id']));
				
				$vdcclass->info->is_user = $vdcclass->info->is_admin = $vdcclass->info->is_root = false;
				
				$vdcclass->templ->message($vdcclass->lang['038'], true);
			} else {
				$vdcclass->templ->error($vdcclass->lang['039'], true);
			}
			break;
		// if for rest password
		case "lost_password":
			if ($vdcclass->funcs->is_null($vdcclass->input->get_vars['lb_div']) == true) {
				exit($vdcclass->templ->lightbox_error($vdcclass->lang['013']));
			} else {
				$vdcclass->templ->templ_vars[] = array("LIGHTBOX_ID" => $vdcclass->input->get_vars['lb_div']);
				
				exit($vdcclass->templ->parse_template("users", "forgotten_password_lightbox"));
			}
			break;
		case "lost_password-d":
			$vdcclass->templ->page_title .= $vdcclass->lang['050'];
			
			if ($vdcclass->funcs->is_null($vdcclass->input->post_vars['username']) == true || $vdcclass->funcs->is_null($vdcclass->input->post_vars['email_address']) == true) {
				$vdcclass->templ->error($vdcclass->lang['004'], true);
			} elseif ($vdcclass->db->total_rows(($user_data = $vdcclass->db->query("SELECT * FROM `[1]` WHERE `username` = '[2]' AND `email_address` = '[3]' LIMIT 1;", array(MYSQL_USER_INFO_TABLE, $vdcclass->input->post_vars['username'], strtolower($vdcclass->input->post_vars['email_address']))))) !== 1) {
				$vdcclass->templ->error($vdcclass->lang['051'], true);
			} else {
				$user_data = $vdcclass->db->fetch_array($user_data);
				$new_password = $vdcclass->funcs->random_string(12);
				$auth_key = md5($vdcclass->funcs->random_string(50));
				
				$vdcclass->db->query("INSERT INTO `[1]` (auth_key, new_password, user_id, time_requested, ip_address) VALUES ('[2]', '[3]', '[4]', '[5]', '[6]');", array(MYSQL_USER_PASSWORDS_TABLE, $auth_key, md5($new_password), $user_data['user_id'], time(), $vdcclass->input->server_vars['remote_addr']));
				
				$vdcclass->templ->templ_vars[] = array(
					"AUTH_KEY" => $auth_key,
					"NEW_PASSWORD" => $new_password,
					"USERNAME" => $user_data['username'],
					"BASE_URL" => $vdcclass->info->base_url,
					"SITE_NAME" => $vdcclass->info->config['site_name'],
					"ADMIN_EMAIL" => $vdcclass->info->config['email_in'],
				);
				
				$email_headers = "MIME-Version: 1.0\r\n";
				$email_headers .= "Content-type: text/html; charset=iso-8859-1\r\n";
				$email_headers .= "From: {$vdcclass->info->config['site_name']} <{$vdcclass->info->config['email_out']}>\r\n";
				
				if (mail($user_data['email_address'], sprintf($vdcclass->lang['052'], $vdcclass->info->config['site_name'], mt_rand(1000, 9999)), $vdcclass->templ->parse_template("users", "forgotten_password_email"), $email_headers) == true) {
					$vdcclass->templ->message(sprintf($vdcclass->lang['053'], $user_data['email_address']), true);
				} else {
					$vdcclass->templ->error($vdcclass->lang['054']);
				}
			}
			break;
		case "lost_password-a":
			$vdcclass->templ->page_title .= $vdcclass->lang['055'];
			
			if ($vdcclass->funcs->is_null($vdcclass->input->get_vars['id']) == true || $vdcclass->db->total_rows(($new_password = $vdcclass->db->query("SELECT * FROM `[1]` WHERE `auth_key` = '[2]' LIMIT 1;", array(MYSQL_USER_PASSWORDS_TABLE, $vdcclass->input->get_vars['id'])))) !== 1) {
				$vdcclass->templ->error($vdcclass->lang['056'], true);
			} else {
				$new_password = $vdcclass->db->fetch_array($new_password);
				
				$vdcclass->db->query("DELETE FROM `[1]` WHERE `auth_key` = '[2]';", array(MYSQL_USER_PASSWORDS_TABLE, $new_password['auth_key']));
				$vdcclass->db->query("UPDATE `[1]` SET `password` = '[2]' WHERE `user_id` = '[3]';", array(MYSQL_USER_INFO_TABLE, $new_password['new_password'], $new_password['user_id']));
				
				$vdcclass->templ->message($vdcclass->lang['057'], true);
			}
			break;
		case "user_list":
			$vdcclass->templ->page_title .= $vdcclass->lang['034'];
			
			$sql = $vdcclass->db->query("SELECT * FROM `[1]` ORDER BY `user_id` DESC LIMIT <# QUERY_LIMIT #>;", array(MYSQL_USER_INFO_TABLE));
			
			while ($row = $vdcclass->db->fetch_array($sql)) {
				$vdcclass->templ->templ_globals['get_whileloop'] = true;
				
				$vdcclass->templ->templ_vars[] = array(
					"USER_ID" => $row['user_id'],
					"USERNAME" => $row['username'],
					"TDCLASS" => $tdclass = (($tdclass == "tdrow1") ? "tdrow2" : "tdrow1"),
					"TIME_JOINED" => date($vdcclass->info->config['date_format'], $row['time_joined']),
					"GALLERY_STATUS" => (($row['private_gallery'] == 1) ? $vdcclass->lang['035'] : $vdcclass->lang['036']),
					"TOTAL_UPLOADS" => $vdcclass->funcs->format_number($vdcclass->db->total_rows($vdcclass->db->query("SELECT * FROM `[1]` WHERE `gallery_id` = '[2]' AND `is_private` = '0';", array(MYSQL_FILE_STORAGE_TABLE, $row['user_id'])))),
				);
				
				$vdcclass->templ->templ_globals['user_list_whileloop'] .= $vdcclass->templ->parse_template("users", "user_list_page");
				unset($vdcclass->templ->templ_globals['get_whileloop'], $vdcclass->templ->templ_vars);	
			}
			
			$vdcclass->templ->templ_vars[] = array("PAGINATION_LINKS" => $vdcclass->templ->pagelinks("users.php?act=user_list", $vdcclass->db->total_rows($vdcclass->db->query("SELECT * FROM `[1]`;", array(MYSQL_USER_INFO_TABLE)))));
		
			$vdcclass->templ->output("users", "user_list_page");
			break;
		case "gallery":
			$vdcclass->templ->page_title .= $vdcclass->lang['033'];
			
			$vdcclass->info->selected_album = (int)$vdcclass->input->get_vars['cat'];
			$vdcclass->info->selected_gallery = (int)$vdcclass->input->get_vars['gal'];
			
			$vdcclass->info->user_owned_gallery = (($vdcclass->funcs->is_null($vdcclass->info->selected_gallery) == true || $vdcclass->info->user_data['user_id'] == $vdcclass->info->selected_gallery) ? true : false);
			$vdcclass->info->gallery_owner_data = (($vdcclass->info->user_owned_gallery == false) ? $vdcclass->db->fetch_array($vdcclass->db->query("SELECT * FROM `[1]` WHERE `user_id` = '[2]' LIMIT 1;", array(MYSQL_USER_INFO_TABLE, $vdcclass->info->selected_gallery))) : $vdcclass->info->user_data);
			
			$vdcclass->info->gallery_url = sprintf("%susers.php?act=gallery%s", $vdcclass->info->base_url, (($vdcclass->info->user_owned_gallery == true) ? NULL : "&amp;gal={$vdcclass->info->gallery_owner_data['user_id']}"));
			$vdcclass->info->gallery_url_full = sprintf("%s%s", $vdcclass->info->gallery_url, (($vdcclass->funcs->is_null($vdcclass->info->selected_album) == true) ? NULL : "&amp;cat={$vdcclass->info->selected_album}"));
			
			if ($vdcclass->info->user_owned_gallery == true && $vdcclass->info->is_user == false) {
				$vdcclass->templ->error($vdcclass->lang['002'], true);
			} elseif ($vdcclass->funcs->is_null($vdcclass->info->gallery_owner_data['user_id']) == true && $vdcclass->funcs->is_null($vdcclass->info->selected_gallery) == false) {
				$vdcclass->templ->error($vdcclass->lang['062'], true);
			} elseif ($vdcclass->info->is_admin == false && $vdcclass->info->user_owned_gallery == false && $vdcclass->info->gallery_owner_data['private_gallery'] == 1) {
				$vdcclass->templ->error($vdcclass->lang['059'], true);
			} else {
				$sql = $vdcclass->db->query("SELECT * FROM `[1]` WHERE `gallery_id` = '[2]' AND `album_id` = '[3]' AND (`filename` LIKE '%[4]%' OR `file_title` LIKE '%[4]%') [[1]] ORDER BY `file_id` DESC LIMIT <# QUERY_LIMIT #>;", array(MYSQL_FILE_STORAGE_TABLE, $vdcclass->info->gallery_owner_data['user_id'], $vdcclass->info->selected_album, urldecode($vdcclass->input->get_vars['search'])), array(($vdcclass->info->user_owned_gallery == false) ? " AND `is_private` = 0" : NULL));
				
				if ($vdcclass->db->total_rows($sql) < 1) {
					$vdcclass->templ->templ_globals['empty_gallery'] = true;
				} else {
					$vdcclass->templ->templ_globals['file_options'] = (($vdcclass->info->user_owned_gallery == true) ? true : false);
						
					while ($row = $vdcclass->db->fetch_array($sql)) {
						$break_line = (($tdcount >= 4) ? true : false);
						$tdcount = (($tdcount >= 4) ? 0 : $tdcount);
						$tdcount++;
						
						$vdcclass->templ->templ_vars[] = array(
							"FILE_ID" => $row['file_id'],
							"FILENAME" => $row['filename'],
							"FILE_TITLE" => $row['file_title'],
							"TABLE_BREAK" => (($break_line == true) ? "</tr><tr>" : NULL),
							"TDCLASS" => $tdclass = (($tdclass == "tdrow1") ? "tdrow2" : "tdrow1"),
						);
						
						$gallery_html .= $vdcclass->templ->parse_template("global", "global_gallery_layout");
						unset($break_line, $vdcclass->templ->templ_globals['get_whileloop'], $vdcclass->templ->templ_vars);	
					}
				}
				
				$sql = $vdcclass->db->query("SELECT * FROM `[1]` WHERE `gallery_id` = '[2]' LIMIT 50;", array(MYSQL_GALLERY_ALBUMS_TABLE, $vdcclass->info->gallery_owner_data['user_id']));
				
				while ($row = $vdcclass->db->fetch_array($sql)) {
					$vdcclass->templ->templ_globals['get_whileloop'] = true;
					
					if ($row['album_id'] == $vdcclass->info->selected_album) {
						$curalbum = $row;
					}
					
					$vdcclass->templ->templ_vars[] = array(
						"ALBUM_ID" => $row['album_id'],
						"ALBUM_NAME" => $row['album_title'],
						"GALLERY_URL" => $vdcclass->info->gallery_url,
						"FULL_GALLERY_URL" => $vdcclass->info->gallery_url_full,
						"RETURN_URL" => base64_encode($vdcclass->info->page_url),
						"TOTAL_UPLOADS" => $vdcclass->funcs->format_number($vdcclass->db->total_rows($vdcclass->db->query("SELECT * FROM `[1]` WHERE `gallery_id` = '[2]' AND `album_id` = '[3]' [[1]];", array(MYSQL_FILE_STORAGE_TABLE, $vdcclass->info->gallery_owner_data['user_id'], $row['album_id']), array(($vdcclass->info->user_owned_gallery == false) ? " AND `is_private` = 0" : NULL)))),
					);
					
					$vdcclass->templ->templ_globals['album_pulldown_whileloop'] .= $vdcclass->templ->parse_template("users", "my_gallery_page");
					unset($vdcclass->templ->templ_vars, $vdcclass->templ->templ_globals['get_whileloop']);
				}
				
				$vdcclass->templ->templ_vars[] = array(
					"GALLERY_HTML" => $gallery_html,		
					"GALLERY_URL" => $vdcclass->info->gallery_url,
					"CURRENT_PAGE" => $vdcclass->info->current_page,
					"FULL_GALLERY_URL" => $vdcclass->info->gallery_url_full,
					"RETURN_URL" => base64_encode($vdcclass->info->page_url),
					"GALLERY_ID" => $vdcclass->info->gallery_owner_data['user_id'],
					"IMAGE_SEARCH" => urldecode($vdcclass->input->get_vars['search']),
					"GALLERY_OWNER" => $vdcclass->info->gallery_owner_data['username'],
					"ALBUM_NAME" => (($vdcclass->funcs->is_null($curalbum['album_title']) == true) ? NULL : "&raquo; {$curalbum['album_title']}"),
					"EMPTY_GALLERY" => $vdcclass->templ->message((($vdcclass->funcs->is_null($vdcclass->input->get_vars['search']) == false) ? $vdcclass->lang['675'] : $vdcclass->lang['058']), false),
					"TOTAL_UPLOADS" => $vdcclass->funcs->format_number($vdcclass->db->total_rows($vdcclass->db->query("SELECT * FROM `[1]` WHERE `gallery_id` = '[2]' [[1]];", array(MYSQL_FILE_STORAGE_TABLE, $vdcclass->info->gallery_owner_data['user_id']), array(($vdcclass->info->user_owned_gallery == false) ? " AND `is_private` = 0" : NULL)))),
					"TOTAL_ROOT_UPLOADS" => $vdcclass->funcs->format_number($vdcclass->db->total_rows($vdcclass->db->query("SELECT * FROM `[1]` WHERE `gallery_id` = '[2]' AND `album_id` = '0' [[1]];", array(MYSQL_FILE_STORAGE_TABLE, $vdcclass->info->gallery_owner_data['user_id']), array(($vdcclass->info->user_owned_gallery == false) ? " AND `is_private` = 0" : NULL)))),
					"PAGINATION_LINKS" => $vdcclass->templ->pagelinks(sprintf("%s%s", $vdcclass->info->gallery_url_full, (($vdcclass->funcs->is_null($vdcclass->input->get_vars['search']) == true) ? NULL : sprintf("&amp;search=%s", urldecode($vdcclass->input->get_vars['search'])))), $vdcclass->db->total_rows($vdcclass->db->query("SELECT * FROM `[1]` WHERE `gallery_id` = '[2]' AND `album_id` = '[3]' AND (`filename` LIKE '%[4]%' OR `file_title` LIKE '%[4]%') [[1]] ORDER BY `file_id` DESC;", array(MYSQL_FILE_STORAGE_TABLE, $vdcclass->info->gallery_owner_data['user_id'], $vdcclass->info->selected_album, urldecode($vdcclass->input->get_vars['search'])), array(($vdcclass->info->user_owned_gallery == false) ? " AND `is_private` = 0" : NULL)))),	
				);
				
				$vdcclass->templ->output("users", "my_gallery_page");
			}
			break;
		case "rename_file_title":
			if ($vdcclass->info->is_user == false) {
				$vdcclass->templ->error($vdcclass->lang['002'], true);
			} elseif ($vdcclass->funcs->is_null($vdcclass->input->get_vars['file']) == true) {
				$vdcclass->templ->error($vdcclass->lang['023'], true);
			} elseif ($vdcclass->funcs->is_file($vdcclass->input->get_vars['file'], $vdcclass->info->root_path.$vdcclass->info->config['upload_path'], true, $vdcclass->info->user_data['user_id']) == false) {
				$vdcclass->templ->error(sprintf($vdcclass->lang['024'], $vdcclass->image->basename($vdcclass->input->get_vars['file'])), true);
			} else {			
				$new_title = htmlentities($vdcclass->input->get_vars['title']);
				
				$vdcclass->db->query("UPDATE `[1]` SET `file_title` = '[2]' WHERE `filename` = '[3]';", array(MYSQL_FILE_STORAGE_TABLE, $new_title, $vdcclass->image->basename($vdcclass->input->get_vars['file'])));
				
				exit($new_title);
			}
			break;
		case "move_files":
			if ($vdcclass->info->is_user == false) {
				exit($vdcclass->templ->lightbox_error($vdcclass->lang['002']));
			} elseif ($vdcclass->funcs->is_null($vdcclass->input->get_vars['files']) == true || $vdcclass->funcs->is_null($vdcclass->input->get_vars['lb_div']) == true) {
				exit($vdcclass->templ->lightbox_error($vdcclass->lang['013']));
			} else {
				$files2move = $vdcclass->image->basename(explode(",", $vdcclass->input->get_vars['files']));
				
				foreach ($files2move as $id => $filename) {
					if ($vdcclass->funcs->is_null($filename) == true) {
						exit($vdcclass->templ->lightbox_error($vdcclass->lang['023']));
					} elseif ($vdcclass->funcs->is_file($filename, $vdcclass->info->root_path.$vdcclass->info->config['upload_path'], true, $vdcclass->info->user_data['user_id']) == false) {
						exit($vdcclass->templ->lightbox_error(sprintf($vdcclass->lang['024'], $filename)));
					} 
				}
				
				$sql = $vdcclass->db->query("SELECT * FROM `[1]` WHERE `gallery_id` = '[2]';", array(MYSQL_GALLERY_ALBUMS_TABLE, $vdcclass->info->user_data['user_id']));
				
				while ($row = $vdcclass->db->fetch_array($sql)) {
					$vdcclass->templ->templ_globals['get_whileloop'] = true;
					
					$vdcclass->templ->templ_vars[] = array(
						"ALBUM_ID" => $row['album_id'],
						"ALBUM_NAME" => $row['album_title'],
					);
					
					$vdcclass->templ->templ_globals['album_options_whileloop'] .= $vdcclass->templ->parse_template("users", "move_files_lightbox");
					unset($vdcclass->templ->templ_vars, $vdcclass->templ->templ_globals['get_whileloop']);
				}
				
				$vdcclass->templ->templ_vars[] = array(
					"FILES2MOVE" => $vdcclass->input->get_vars['files'],
					"LIGHTBOX_ID" => $vdcclass->input->get_vars['lb_div'],
					"RETURN_URL" => urldecode($vdcclass->input->get_vars['return']),
				);
				
				exit($vdcclass->templ->parse_template("users", "move_files_lightbox"));
			}
			break;
		case "move_files-d":
			$vdcclass->templ->page_title .= $vdcclass->lang['031'];
			
			if ($vdcclass->info->is_user == false) {
				$vdcclass->templ->error($vdcclass->lang['002'], true);
			} elseif ($vdcclass->funcs->is_null($vdcclass->input->post_vars['files']) == true) {
				$vdcclass->templ->error($vdcclass->lang['013'], true);
			} elseif ($vdcclass->funcs->is_null($vdcclass->input->post_vars['move_to']) == true) {
				$vdcclass->templ->error($vdcclass->lang['004'], true);
			} else {
				$sql = $vdcclass->db->query("SELECT * FROM `[1]` WHERE `album_id` = '[2]' AND `gallery_id` = '[3]' LIMIT 1;", array(MYSQL_GALLERY_ALBUMS_TABLE, $vdcclass->input->post_vars['move_to'], $vdcclass->info->user_data['user_id']));
				
				if ($vdcclass->db->total_rows($sql) !== 1 && $vdcclass->input->post_vars['move_to'] !== "root") {
					$vdcclass->templ->error($vdcclass->lang['949'], true);
				} else {
					$files2move = $vdcclass->image->basename(explode(",", $vdcclass->input->post_vars['files']));
					
					foreach ($files2move as $id => $filename) {
						if ($vdcclass->funcs->is_null($filename) == true) {
							$vdcclass->templ->error($vdcclass->lang['023'], true);
						} elseif ($vdcclass->funcs->is_file($filename, $vdcclass->info->root_path.$vdcclass->info->config['upload_path'], true, $vdcclass->info->user_data['user_id']) == false) {
							$vdcclass->templ->error(sprintf($vdcclass->lang['024'], $filename), true);
						} else {
							$vdcclass->db->query("UPDATE `[1]` SET `album_id` = '[2]' WHERE `filename` = '[3]';", array(MYSQL_FILE_STORAGE_TABLE, $vdcclass->input->post_vars['move_to'], $filename));
						}
					}
					
					$vdcclass->templ->message(sprintf($vdcclass->lang['032'], (($vdcclass->funcs->is_null($vdcclass->input->post_vars['return']) == true) ? base64_encode($vdcclass->info->base_url) : $vdcclass->input->post_vars['return']), $vdcclass->input->post_vars['move_to']), true);
				}
			}
			break;
		case "delete_files":
			if ($vdcclass->info->is_user == false) {
				exit($vdcclass->templ->lightbox_error($vdcclass->lang['002']));
			} elseif ($vdcclass->funcs->is_null($vdcclass->input->get_vars['files']) == true || $vdcclass->funcs->is_null($vdcclass->input->get_vars['lb_div']) == true) {
				exit($vdcclass->templ->lightbox_error($vdcclass->lang['013']));
			} else {
				$files2delete = $vdcclass->image->basename(explode(",", $vdcclass->input->get_vars['files']));
				
				foreach ($files2delete as $id => $filename) {
					if ($vdcclass->funcs->is_null($filename) == true) {
						exit($vdcclass->templ->lightbox_error($vdcclass->lang['023']));
					} elseif ($vdcclass->funcs->is_file($filename, $vdcclass->info->root_path.$vdcclass->info->config['upload_path'], true, $vdcclass->info->user_data['user_id']) == false) {
						exit($vdcclass->templ->lightbox_error(sprintf($vdcclass->lang['024'], $filename)));
					}
				}
				
				$vdcclass->templ->templ_vars[] = array(
					"LIGHTBOX_ID" => $vdcclass->input->get_vars['lb_div'],
					"FILES2DELETE" => $vdcclass->input->get_vars['files'],
					"RETURN_URL" => urldecode($vdcclass->input->get_vars['return']),
				);
				
				exit($vdcclass->templ->parse_template("users", "delete_files_lightbox"));
			}
			break;
		case "delete_files-d":
			$vdcclass->templ->page_title .= $vdcclass->lang['026'];
			
			if ($vdcclass->info->is_user == false) {
				$vdcclass->templ->error($vdcclass->lang['002'], true);
			} elseif ($vdcclass->funcs->is_null($vdcclass->input->post_vars['files']) == true) {
				$vdcclass->templ->error($vdcclass->lang['013'], true);
			} else {
				$files2delete = $vdcclass->image->basename(explode(",", $vdcclass->input->post_vars['files']));
				
				foreach ($files2delete as $id => $filename) {
					if ($vdcclass->funcs->is_null($filename) == true) {
						$vdcclass->templ->error($vdcclass->lang['023'], true);
					} elseif ($vdcclass->funcs->is_file($filename, $vdcclass->info->root_path.$vdcclass->info->config['upload_path'], true, $vdcclass->info->user_data['user_id']) == false) {
						$vdcclass->templ->error(sprintf($vdcclass->lang['024'], $filename), true);
					} else {
						if (unlink($vdcclass->info->root_path.$vdcclass->info->config['upload_path'].$filename) == false) {
							$vdcclass->templ->error(sprintf($vdcclass->lang['027'], $filename), true);
						}
						
						if ($vdcclass->funcs->file_exists($vdcclass->info->root_path.$vdcclass->info->config['upload_path'].($thumbnail = $vdcclass->image->thumbnail_name($filename))) == true) {
							if (unlink($vdcclass->info->root_path.$vdcclass->info->config['upload_path'].$thumbnail) == false) {
								$vdcclass->templ->error(sprintf($vdcclass->lang['028'], $filename), true);
							}
						}
						
						$vdcclass->db->query("DELETE FROM `[1]` WHERE `filename` = '[2]';", array(MYSQL_FILE_RATINGS_TABLE, $filename));
						$vdcclass->db->query("DELETE FROM `[1]` WHERE `filename` = '[2]';", array(MYSQL_FILE_STORAGE_TABLE, $filename));
					}
				}
				
				$vdcclass->templ->message(sprintf($vdcclass->lang['029'], (($vdcclass->funcs->is_null($vdcclass->input->post_vars['return']) == true) ? base64_encode($vdcclass->info->base_url) : $vdcclass->input->post_vars['return'])), true);
			}
			break;
		case "albums-c":
			if ($vdcclass->info->is_user == false) {
				exit($vdcclass->templ->lightbox_error($vdcclass->lang['002']));
			} elseif ($vdcclass->funcs->is_null($vdcclass->input->get_vars['lb_div']) == true) {
				exit($vdcclass->templ->lightbox_error($vdcclass->lang['013']));
			} else {
				$vdcclass->templ->templ_vars[] = array(
					"LIGHTBOX_ID" => $vdcclass->input->get_vars['lb_div'],
					"RETURN_URL" => urldecode($vdcclass->input->get_vars['return']),
				 );
				
				exit($vdcclass->templ->parse_template("users", "new_album_lightbox"));
			}
			break;
		case "albums-c-d":
			$vdcclass->templ->page_title .= $vdcclass->lang['020'];
			
			$album_title = htmlspecialchars($vdcclass->input->post_vars['album_title']);
			
			if ($vdcclass->info->is_user == false) {
				$vdcclass->templ->error($vdcclass->lang['002'], true);
			} elseif ($vdcclass->funcs->is_null($album_title) == true) {
				$vdcclass->templ->error($vdcclass->lang['004'], true);
			} elseif ($vdcclass->db->total_rows($vdcclass->db->query("SELECT * FROM `[1]` WHERE `album_title` = '[2]' AND `gallery_id` = '[3]' LIMIT 1;", array(MYSQL_GALLERY_ALBUMS_TABLE, $album_title, $vdcclass->info->user_data['user_id']))) == 1) {
				$vdcclass->templ->error(sprintf($vdcclass->lang['022'], $album_title), true);
			} else {
				$vdcclass->db->query("INSERT INTO `[1]` (`album_title`, `gallery_id`) VALUES ('[2]', '[3]');", array(MYSQL_GALLERY_ALBUMS_TABLE, $album_title, $vdcclass->info->user_data['user_id']));
				
				$newalbum = $vdcclass->db->fetch_array($vdcclass->db->query("SELECT * FROM `[1]` WHERE `album_title` = '[2]' AND `gallery_id` = '[3]' LIMIT 1;", array(MYSQL_GALLERY_ALBUMS_TABLE, $album_title, $vdcclass->info->user_data['user_id'])));
				
				$vdcclass->templ->message(sprintf($vdcclass->lang['021'], $album_title, (($vdcclass->funcs->is_null($vdcclass->input->post_vars['return']) == true) ? base64_encode($vdcclass->info->base_url) : $vdcclass->input->post_vars['return']), $newalbum['album_id']), true);
			}
			break;
		case "albums-r":
			if ($vdcclass->info->is_user == false) {
				exit($vdcclass->templ->lightbox_error($vdcclass->lang['002']));
			} elseif ($vdcclass->funcs->is_null($vdcclass->input->get_vars['album']) == true || $vdcclass->funcs->is_null($vdcclass->input->get_vars['lb_div']) == true) {
				exit($vdcclass->templ->lightbox_error($vdcclass->lang['013']));
			} else {
				$sql = $vdcclass->db->query("SELECT * FROM `[1]` WHERE `album_id` = '[2]' AND `gallery_id` = '[3]' LIMIT 1;", array(MYSQL_GALLERY_ALBUMS_TABLE, $vdcclass->input->get_vars['album'], $vdcclass->info->user_data['user_id']));
				
				if ($vdcclass->db->total_rows($sql) !== 1) {
					exit($vdcclass->templ->lightbox_error($vdcclass->lang['017']));
				} else {
					$oldalbum = $vdcclass->db->fetch_array($sql);
					
					$vdcclass->templ->templ_vars[] = array(
						"ALBUM_ID" => $oldalbum['album_id'],
						"OLD_TITLE" => $oldalbum['album_title'],
						"LIGHTBOX_ID" => $vdcclass->input->get_vars['lb_div'],
						"RETURN_URL" => urldecode($vdcclass->input->get_vars['return']),
					);
					
					exit($vdcclass->templ->parse_template("users", "rename_album_lightbox"));
				}
			}
			break;
		case "albums-r-d":
			$vdcclass->templ->page_title .= $vdcclass->lang['018'];
			
			$album_title = htmlspecialchars($vdcclass->input->post_vars['album_title']);
			
			if ($vdcclass->info->is_user == false) {
				$vdcclass->templ->error($vdcclass->lang['002'], true);
			} elseif ($vdcclass->funcs->is_null($vdcclass->input->post_vars['album']) == true) {
				$vdcclass->templ->error($vdcclass->lang['013'], true);
			} elseif ($vdcclass->funcs->is_null($album_title) == true) {
				$vdcclass->templ->error($vdcclass->lang['004'], true);
			} elseif ($vdcclass->db->total_rows($vdcclass->db->query("SELECT * FROM `[1]` WHERE `album_title` = '[2]' AND `gallery_id` = '[3]' LIMIT 1;", array(MYSQL_GALLERY_ALBUMS_TABLE, $album_title, $vdcclass->info->user_data['user_id']))) == 1) {
				$vdcclass->templ->error(sprintf($vdcclass->lang['022'], $album_title), true);
			} else {
				if ($vdcclass->db->total_rows(($albumsql = $vdcclass->db->query("SELECT * FROM `[1]` WHERE `album_id` = '[2]' AND `gallery_id` = '[3]' LIMIT 1;", array(MYSQL_GALLERY_ALBUMS_TABLE, $vdcclass->input->post_vars['album'], $vdcclass->info->user_data['user_id'])))) !== 1) {
					$vdcclass->templ->error($vdcclass->lang['017'], true);
				} else {
					$oldalbum = $vdcclass->db->fetch_array($albumsql);
					
					$vdcclass->db->query("UPDATE `[1]` SET `album_title` = '[2]' WHERE `album_id` = '[3]';", array(MYSQL_GALLERY_ALBUMS_TABLE, $album_title, $oldalbum['album_id']));
					
					$vdcclass->templ->message(sprintf($vdcclass->lang['019'], $oldalbum['album_title'], $album_title, (($vdcclass->funcs->is_null($vdcclass->input->post_vars['return']) == true) ? base64_encode($vdcclass->info->base_url) : $vdcclass->input->post_vars['return']), $oldalbum['album_id']), true);
				}
			}
			break;
		case "albums-d":
			if ($vdcclass->info->is_user == false) {
				exit($vdcclass->templ->lightbox_error($vdcclass->lang['002']));
			} elseif ($vdcclass->funcs->is_null($vdcclass->input->get_vars['album']) == true || $vdcclass->funcs->is_null($vdcclass->input->get_vars['lb_div']) == true) {
				exit($vdcclass->templ->lightbox_error($vdcclass->lang['013']));
			} else {
				if ($vdcclass->db->total_rows(($albumsql = $vdcclass->db->query("SELECT * FROM `[1]` WHERE `album_id` = '[2]' AND `gallery_id` = '[3]' LIMIT 1;", array(MYSQL_GALLERY_ALBUMS_TABLE, $vdcclass->input->get_vars['album'], $vdcclass->info->user_data['user_id'])))) !== 1) {
					exit($vdcclass->templ->lightbox_error($vdcclass->lang['014']));
				} else {
					$oldalbum = $vdcclass->db->fetch_array($albumsql);
					
					$vdcclass->templ->templ_vars[] = array(
						"ALBUM2DELETE" => $oldalbum['album_id'],
						"LIGHTBOX_ID" => $vdcclass->input->get_vars['lb_div'],
					);
					
					exit($vdcclass->templ->parse_template("users", "delete_album_lightbox"));
				}
			}
			break;
		case "albums-d-d":
			$vdcclass->templ->page_title .= $vdcclass->lang['015'];
			
			if ($vdcclass->info->is_user == false) {
				$vdcclass->templ->error($vdcclass->lang['002'], true);
			} elseif ($vdcclass->funcs->is_null($vdcclass->input->post_vars['album']) == true) {
				$vdcclass->templ->error($vdcclass->lang['013'], true);
			} elseif ($vdcclass->db->total_rows($vdcclass->db->query("SELECT * FROM `[1]` WHERE `album_id` = '[2]' AND `gallery_id` = '[3]' LIMIT 1;", array(MYSQL_GALLERY_ALBUMS_TABLE, $vdcclass->input->post_vars['album'], $vdcclass->info->user_data['user_id']))) !== 1) {
				$vdcclass->templ->error($vdcclass->lang['014'], true);
			} else {
				$vdcclass->db->query("DELETE FROM `[1]` WHERE `album_id` = '[2]' AND `gallery_id`  = '[3]';", array(MYSQL_GALLERY_ALBUMS_TABLE, $vdcclass->input->post_vars['album'], $vdcclass->info->user_data['user_id']));
				$vdcclass->db->query("UPDATE `[1]` SET `album_id` = '0' WHERE `album_id` = '[2]' AND `gallery_id`  = '[3]';", array(MYSQL_FILE_STORAGE_TABLE, $vdcclass->input->post_vars['album'], $vdcclass->info->user_data['user_id']));
			
				$vdcclass->templ->message($vdcclass->lang['016'], true);
			}
			break;	
		case "settings":
			$vdcclass->templ->page_title .= sprintf($vdcclass->lang['003'], $vdcclass->info->user_data['username']);
			
			if ($vdcclass->info->is_user == false) {
				$vdcclass->templ->error($vdcclass->lang['002'], true);
			} else {
				$vdcclass->templ->templ_vars[] = array(
				   	"USER_ID" => $vdcclass->info->user_data['user_id'],
				   	"USERNAME" => $vdcclass->info->user_data['username'],
				   	"IP_ADDRESS" => $vdcclass->info->user_data['ip_address'],
				   	"EMAIL_ADDRESS" => $vdcclass->info->user_data['email_address'],
					"IP_HOSTNAME" => gethostbyaddr($vdcclass->info->user_data['ip_address']),
				   	"TIME_JOINED" => date($vdcclass->info->config['date_format'], $vdcclass->info->user_data['time_joined']),
				   	"BOXED_UPLOAD_YES" => (($vdcclass->info->user_data['upload_type'] == "boxed") ? "checked=\"checked\"" : NULL),
				   	"PRIVATE_GALLERY_NO" => (($vdcclass->info->user_data['private_gallery'] == 0) ? "checked=\"checked\"" : NULL),
				   	"PRIVATE_GALLERY_YES" => (($vdcclass->info->user_data['private_gallery'] == 1) ? "checked=\"checked\"" : NULL),
				   	"STANDARD_UPLOAD_YES" => (($vdcclass->info->user_data['upload_type'] == "standard") ? "checked=\"checked\"" : NULL),
				   	"USER_GROUP" => ((strpos($vdcclass->info->user_data['user_group'], "admin") == true) ? (($vdcclass->info->is_root == false) ? $vdcclass->lang['010'] : $vdcclass->lang['012']) : $vdcclass->lang['011']),
				);
				
				$vdcclass->templ->output("users", "user_settings_page");
			}
			break;
		case "settings-s":
			$vdcclass->templ->page_title .= sprintf($vdcclass->lang['003'], $vdcclass->info->user_data['username']);
			
			if ($vdcclass->info->is_user == false) {
				$vdcclass->templ->error($vdcclass->lang['002'], true);
			} elseif ($vdcclass->funcs->is_null($vdcclass->input->post_vars['email_address']) == true) {
				$vdcclass->templ->error($vdcclass->lang['004'], true);
			} elseif ($vdcclass->funcs->valid_email($vdcclass->input->post_vars['email_address']) == false) {
				$vdcclass->templ->error(sprintf($vdcclass->lang['005'], strtolower($vdcclass->input->post_vars['email_address'])), true);
			} elseif ($vdcclass->funcs->is_null($vdcclass->input->post_vars['password']) == false && strlen($vdcclass->input->post_vars['password']) < 6 || strlen($vdcclass->input->post_vars['password']) > 30) {
				$vdcclass->templ->error($vdcclass->lang['006'], true);
			} elseif (strtolower($vdcclass->input->post_vars['email_address']) !== $vdcclass->info->user_data['email_address'] && $vdcclass->db->total_rows($vdcclass->db->query("SELECT * FROM `[1]` WHERE `email_address` = '[2]' LIMIT 1;", array(MYSQL_USER_INFO_TABLE, strtolower($vdcclass->input->post_vars['email_address'])))) == 1) {
				$vdcclass->templ->error(sprintf($vdcclass->lang['007'], strtolower($vdcclass->input->post_vars['email_address'])), true);
			} else {
				$vdcclass->db->query("UPDATE `[1]` SET `email_address` = '[2]', `private_gallery` = '[3]', `upload_type` = '[4]' WHERE `user_id` = '[5]';", array(MYSQL_USER_INFO_TABLE, strtolower($vdcclass->input->post_vars['email_address']), $vdcclass->input->post_vars['private_gallery'], $vdcclass->input->post_vars['upload_type'], $vdcclass->info->user_data['user_id']));
			
				if ($vdcclass->funcs->is_null($vdcclass->input->post_vars['password']) == false && $vdcclass->input->post_vars['password'] !== "*************") {
					$vdcclass->db->query("UPDATE `[1]` SET `password` = '[2]' WHERE `user_id` = '[3]';", array(MYSQL_USER_INFO_TABLE, md5($vdcclass->input->post_vars['password']), $vdcclass->info->user_data['user_id']));
				}
				
				$vdcclass->templ->message($vdcclass->lang['008'], true);
			}
			break;
		default: 
			$vdcclass->templ->error($vdcclass->lang['009'], true);
	}
	
?>