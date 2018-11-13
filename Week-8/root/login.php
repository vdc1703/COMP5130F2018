<?php
require_once "./includes/vdc.php";

$page_title = "Login";
$vdc->funcs->includeWithVariables('./template/site_header.php', array('page_title' => $page_title));

	switch ($vdc->input->get_vars['task']) {
        case "do-login":
        
        $vdc_post_username = $vdc->input->post_vars['username'];
        $vdc_post_password = md5($vdc->input->post_vars['password']);
    
    $user_data = $vdc->db->query("SELECT * FROM `tbl_user` WHERE `username` = '$vdc_post_username' AND `password` = '$vdc_post_password' LIMIT 1;");
    
    if ($vdc->db->total_rows($user_data) !== 1) {
		echo "Wrong username or password";
	} else {
		setcookie("user_session", "session_delete", (time() - 60000)); // Delete old cookie with negative expiration time.
		
		$session_id = md5($vdc->funcs->random_string(30));
		$vdc->info->user_data = $vdc->db->fetch_array($user_data);
        
        $vdc_server_remoteaddr = $vdc->input->server_vars['remote_addr'];
        $vdc_server_httpuseragent = $vdc->input->server_vars['http_user_agent'];
        $vdc_userdata_userid = $vdc->info->user_data['user_id'];
        $vdc_userdata_time = time();
                
		$vdc->db->query("UPDATE `tbl_user` SET `ip_address` = '$vdc_server_remoteaddr' WHERE `user_id` = '$vdc_userdata_userid';");
		$vdc->db->query("INSERT INTO `tbl_session` (session_id, session_start, user_id, user_agent, ip_address) VALUES ('$session_id', '$vdc_userdata_time', '$vdc_userdata_userid', '$vdc_server_httpuseragent', '$vdc_server_remoteaddr');"); 
	
		if (setcookie("user_session", base64_encode(serialize(array("session_id" => $session_id, "user_id" => $vdc->info->user_data['user_id']))), (time() + 2629743), $vdc->info->script_path, NULL, IS_HTTPS_REQUEST, true) == true) {
			$vdc->info->is_user = true;
			$vdc->info->is_root = (($vdc->info->user_data['user_group'] === "admin") ? true : false);
			$vdc->info->is_admin = (($vdc->info->is_root == true || $vdc->info->user_data['user_group'] === "normal_admin") ? true : false);
		
            echo $vdc->info->is_admin;
        
            echo "Login Successfuly";
		} else {
			echo "Login Failed";
		}
	}
break;
default:
     
?>
<form action="login.php?task=do-login" method="post">
    <div class="form-group">
        <label for="username">Email address</label>
        <input type="text" class="form-control" name="username" id="username" aria-describedby="emailHelp" placeholder="Enter email" />
    </div>
    <div class="form-group">
        <label for="password">Password</label>
        <input type="password" class="form-control" name="password" id="password" placeholder="Password" />
    </div>
    <input type="hidden" name="return" value="<?php echo $return_url ?>" />
    <button type="submit" class="btn btn-primary">Log In</button>   
</form>
<?php } 
require_once "./template/site_footer.php"; ?>