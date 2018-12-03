<?php
require_once "./includes/vdc.php";

$page_title = "User Control Panel";
includeHeader('./template/site_header.php', array('page_title' => $page_title));

require_once "./template/site_body.php";

if(!isset($_SESSION['login_user'])){
    header("location:login.php");
}

if($_SERVER["REQUEST_METHOD"] == "POST") {
  
  $current_password = md5(mysqli_real_escape_string($conn,$_POST['current-password']));
  $new_password = md5(mysqli_real_escape_string($conn,$_POST['new-password'])); 
  
  $sql = "SELECT user_id FROM tbl_user WHERE username = '$logged_username' and password = '$current_password' LIMIT 1";
  $result = mysqli_query($conn,$sql);
  $row = mysqli_fetch_array($result,MYSQLI_ASSOC);
  
  $count = mysqli_num_rows($result);
	
  if($count == 1) {
    $sql = "UPDATE tbl_user SET password = '$new_password' WHERE username = '$logged_username'";
    mysqli_query($conn,$sql);    
    
    $msg = "Change password success";
  }else {
     $msg = "Your current password is wrong";
  }
}
?>
<div class="form-group">
    <label for="username">Username</label> <span><?php echo $logged_username; ?></span>
</div>
<form method="post">
    <div class="form-group">
        <label for="current-password">Current Password</label>
        <input type="password" class="form-control" name="current-password" id="current-password" placeholder="Enter your current password" />
    </div>
    <div class="form-group">
        <label for="new-password">New Password</label>
        <input type="password" class="form-control" name="new-password" id="new-password" placeholder="Enter your new password" />
    </div>    
    <button type="submit" class="btn btn-primary">Change Password</button>   
</form>
<div style = "font-size:11px; color:#cc0000; margin-top:10px"><?php echo $msg; ?></div>
<?php require_once "./template/site_footer.php"; ?>