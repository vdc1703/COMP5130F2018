<?php
include_once("config.php");
include_once("inc/functions_std.php");
include_once("inc/functions_authorise.php");
include_once("inc/functions_imgs.php");
echo "Please wait during image generation<br>"
	."If timeout(s) occurs, use refresh button of your browser until you see the \"All thumbs generated\" message<br><br>"
	."Generating images...<br><br>";
make_all_thumbs($imagePath,$cacheDir,$thumb_landscape_width,$thumb_landscape_height,$img_preview_landscape_width,$img_preview_landscape_height);
echo "All thumbs generated";
?>