<?php
require_once "./includes/vdc.php";

    if(isset($_SESSION['login_user'])){
        header("location:about.php");
    }

$page_title = "Register";
includeHeader('./template/site_header.php', array('page_title' => $page_title));

    if($_SERVER["REQUEST_METHOD"] == "POST") {      
        $username = mysqli_real_escape_string($conn,$_POST['username']);
        $password = md5(mysqli_real_escape_string($conn,$_POST['password']));
        
        $sql = "SELECT * FROM tbl_user WHERE username = '$username' LIMIT 1";
        $result = mysqli_query($conn,$sql);
        $total = mysqli_num_rows($result); 
        
        if ($total == 1) {
            $msg = $username." is exist, please choose another username";
        } else {
            $sql_insert = "INSERT INTO tbl_user (username, password, user_group) VALUES ('$username', '$password', 'member')";
            mysqli_query($conn,$sql_insert);
            $msg = "sucess";		
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
<!--    This will be for future update
    <div class="form-group">
        <label for="password">Email</label>
        <input type="text" class="form-control" name="email" id="email" placeholder="email" />
    </div>
-->
    <button type="submit" class="btn btn-primary">Register</button>   
</form>
<div style = "font-size:14px; color:#CC0000; margin-top:10px; text-align: center;"><?php echo $msg; ?></div>
<?php require_once "./template/site_footer.php"; ?>	