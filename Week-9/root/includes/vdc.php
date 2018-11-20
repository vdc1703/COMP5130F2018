<?php
    session_start();
    
    $error = "";    
    
	define("ROOT_PATH", sprintf("%s/", realpath(".")));
    
    $root_path = ROOT_PATH;       
    
	require_once "{$root_path}includes/config.php";
    require_once "{$root_path}includes/resize_img.php"; 
    
    if (isset($_SESSION['login_user'])) {
        $user_check = $_SESSION['login_user'];
        $ses_sql = mysqli_query($conn ,"SELECT user_id, username FROM tbl_user WHERE username = '$user_check' ");
        $row = mysqli_fetch_array($ses_sql,MYSQLI_ASSOC);
        $logged_userid = $row['user_id'];
        $logged_username = $row['username'];
    }
    
    function includeHeader($filePath, $variables = array(), $print = true)
    {
        $output = NULL;
        if(file_exists($filePath)){
            extract($variables);
            ob_start();
            require_once $filePath;
            $output = ob_get_clean();
        }
        if ($print) {
            print $output;
        }
        return $output;
    }    
?>