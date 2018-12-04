<?php
session_start();

define("ROOT_PATH", sprintf("%s/", realpath(".")));
define('DS',DIRECTORY_SEPARATOR);

$root_path = ROOT_PATH;       

// MySQL config
require_once "{$root_path}includes/config.php";

// use to create thumbnail if it have not been created
require_once "{$root_path}includes/simpleimage.php";

$msg = "";    

if (isset($_SESSION['login_user'])) {
    $user_check = $_SESSION['login_user'];
    $ses_sql = mysqli_query($conn ,"SELECT user_id, username, user_group FROM tbl_user WHERE username = '$user_check' ");
    $row = mysqli_fetch_array($ses_sql,MYSQLI_ASSOC);
    $logged_userid = $row['user_id'];
    $logged_username = $row['username'];
    $logged_usergroup = $row['user_group'];
    if ($logged_usergroup == 'admin') {
        $isadmin = true;
    } else {
        $isadmin = false;
    }
} else {
    $logged_userid = $logged_username = $logged_usergroup = $isadmin = $isauthor = false;
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
function make_folder($rootpath, $imagefolder, $chmod = 0755){
	$folders = explode(DS,$imagefolder);         
	$tmppath = $rootpath;
	for($i=0;$i <= count($folders)-1; $i++){
        if(!file_exists($tmppath.$folders[$i])) {
            if(!mkdir($tmppath.$folders[$i],$chmod)) return 0; //can not create folder
        } 	//Folder exist
        $tmppath = $tmppath.$folders[$i].DS;
        //make a blank content
        $ffilename = $tmppath . 'index.html';
	        if(!file_exists($ffilename)){
	                $filecontent = '<html><body></body></html>';
	                $handle = fopen($ffilename, 'x+');
	                fwrite($handle, $filecontent);
	                fclose($handle);  
	        }       
        }
	return 1;
}
function isimage($image_src) {
	return in_array(strtolower(substr(strrchr($image_src, "."), 1)), array('gif','jpg','png','jpeg'));
}
function search_image($content) {		
	preg_match_all("#\<img(.*)src\=\"(.*)\"#Ui", $content, $mathes);		
	return isset($mathes[2][0]) ? $mathes[2][0] : '';			
}
function img_fixlink($image_thumb) {
	if(strpos($image_thumb, 'http://') === 0) return $image_thumb; 
	else return (ROOT_PATH.DS.str_replace('/',DS,$image_thumb));
}
function resize_img($image_src, $w='', $h='', $m='') {
	$w = (int) $w;
	$h = (int) $h;
	$folder_path = ROOT_PATH.DS.'cache'.DS;
	
	$image_thumb = '';
	
	if($image_src){			
		$filepart = explode('/', $image_src);
		$filename  = array_pop($filepart);
		$thumb = "thumb_{$w}_{$h}_".$filename;		
		$thenewimg = $folder_path.DS.$thumb;
		$imgx = img_fixlink($image_src);
		if(!file_exists($thenewimg) && isimage($imgx) ){	
			$image = new SimpleImage();								 
			$image->load($imgx);
			if (empty($w)) {
				$image->resizeToHeight($h);
			} else if (empty($h)) {
				$image->resizeToWidth($w);
			} else {
				$image->resize($w,$h,$m);
			}
			$returnvalue = $image->save($thenewimg);
			if(!$returnvalue)return $image_src;
		}
		if(file_exists($thenewimg)) $image_thumb= "cache/".$thumb;
	}	
	return $image_thumb;	
}        
?>