<?php

	$imagePath= "images/";// images root directory of your gallery (../ is allowed)
	$cacheDir = "cache/"; // the directory in which to store the cache of local pictures-- must be writable by the web server (e.g. chmod 777) (../ is allowed)
	
	$RemoteDirConfigFileName= "remote_dir.txt";// name of file put in any directory for making link for remote gallery/ remote directory gallery
	$itemsInRow = 3; // how many items in each row
	$numberOfRows = 3; // the number of rows per page
	$thumb_landscape_width=160;
	$thumb_landscape_height=120;
	$img_preview_landscape_width=640;
	$img_preview_landscape_height=480;

	$showFileName=false;
	$show_date=true;
	$dd_mm_yy_date=false;// date presentation. default is mm/dd/yyyy put to true for dd/mm/yyyy
	$show_hour=false;
	$show_caption=true;
	$show_source=true;
	$show_copyright=true;
	$show_iptc_info=false;
	$show_exif_info=false;
	
	$showBottomNavBar=false;// show bottom navigation bar
	$showGalleryInfoOnlyForHome=true;// show $fsphpgallery_info content only for home page	
	
	$show_slideshow=true;	
	$looping_slideshow=false; // true slideshow doesn't stop at the end of directory but restart with first image
	//$slideshow_speed=4000;  // time in ms
	
	$links_color="#6699FF";
	$links_font_weight="bold";
	
	$background_color="#FFffFF";// you will need to modify some png images as Internet Explorer dislike png transparency
								// so it's better to put your background color as background color for png images
	
	$home_caption="Homepage";
	$next_caption="Homepage Info";
	$prev_caption="Next";
	$up_dir_caption="Upper Directory";
	$folder_caption="Folder Image";
	$click_to_enlarge_caption="Details";
	$show_hide_iptc_caption="Show / Hide General informations";
	$show_hide_exif_caption="Show / Hide Technical informations";
	$enable_slideshow_caption="Display Image";
	$disable_slideshow_caption="Stop Display";

	// abuse config
	$abuseReports = false; // if you want to be emailed if someone tries to abuse the script
	$email = "chuong_vu@student.uml.edu";
	
	/* header and footer customizing */	
	$page_title="Image Gallery";	
	
	// header for home of the gallery
	$home_header="<h1 align=\"center\">ChuongVu - Image Gallery</h1>";

	$fsphpgallery_info= "<p align=\"center\"><a href='../index.php'><img src=\"img/info.png\" 
						border=\"0\" alt=\"".$home_caption."\" /></i>HOMEPAGE</a></p>";
	
	$fsphpgallery_yahoo="";
	$fsphpgallery_count="";

	$fsphpgallery_header="<html>\n"
						."<head>\n"
						."<title>$page_title</title>\n"
						."<meta http-equiv=\"Page-Enter\" content=\"blendTrans(Duration=0.2)\">\n"
						."<meta http-equiv=\"Page-Exit\" content=\"blendTrans(Duration=0.2)\">\n"
						."<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" />\n"
						."<meta http-equiv=\"Content-Style-Type\" content=\"text/css\" />\n"
						."<style type=\"text/css\"><!--\n"
						."a:hover {font-weight: $links_font_weight; color: $links_color;text-decoration: none;}\n"
						."a:link {font-weight: $links_font_weight; color:$links_color;}\n"
						."a:visited {font-weight: $links_font_weight; color: $links_color;}\n"
						.".class_link_color {color: $links_color;font-weight: $links_font_weight;}\n"
						."body {background-color: $background_color;}\n"
						."--></style>\n"
						."</head>\n"
						."<body>\n";

	$fsphpgallery_footer="</body>\n"
						."</html>\n";
						
	/////////////////////////////////////////////////////////
	////          END OF CONFIGURATION                   ////
	/////////////////////////////////////////////////////////
	if ($imagePath=="")
		$imagePath="./";
	else
	{
		if ($imagePath[strlen($imagePath) - 1] != "/")
			$imagePath.="/";
	}
	if ($cacheDir=="")
		$cacheDir="./";
	else
	{
		if ($cacheDir[strlen($cacheDir) - 1] != "/")
			$cacheDir.="/";
	}
?>
