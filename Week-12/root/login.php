<?php
require_once "./includes/vdc.php";

    if(isset($_SESSION['login_user'])){
        header("location:index.php");
    }

$page_title = "Login";
includeHeader('./template/site_header.php', array('page_title' => $page_title));

   if($_SERVER["REQUEST_METHOD"] == "POST") {
      
      $username = mysqli_real_escape_string($conn,$_POST['username']);
      $password = md5(mysqli_real_escape_string($conn,$_POST['password'])); 
      
      $sql = "SELECT user_id, user_group FROM tbl_user WHERE username = '$username' and password = '$password' LIMIT 1";
      $result = mysqli_query($conn,$sql);
      $row = mysqli_fetch_array($result,MYSQLI_ASSOC);
      $usergroup = $row['user_group'];
      
      $count = mysqli_num_rows($result);
		
      if($count == 1) {
        $_SESSION['login_user'] = $username;      
        
        header("location: index.php");
      }else {
         $msg = "Your Login Name or Password is invalid";
      }
   }
require_once "./template/site_body.php";
?>
<form method="post">
    <div class="form-group">
        <label for="username">Username</label>
        <input type="text" class="form-control" name="username" id="username" placeholder="Enter username" />
    </div>
    <div class="form-group">
        <label for="password">Password</label>
        <input type="password" class="form-control" name="password" id="password" placeholder="Password" />
    </div>
    <button type="submit" class="btn btn-primary">Log In</button>   
</form>
<div style = "font-size:11px; color:#cc0000; margin-top:10px"><?php echo $msg; ?></div>
<?php require_once "./template/site_footer.php"; ?>