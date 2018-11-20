<?php
require_once "{$root_path}includes/simpleimage.php";
if(!defined('DS')){
	define('DS',DIRECTORY_SEPARATOR);
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