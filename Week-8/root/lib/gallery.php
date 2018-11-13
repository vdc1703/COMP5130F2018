
<head>
<style type="text/css">
.style1 {
	text-align: center;
}
</style>
</head>

<?php

	include_once("config.php");
	include_once("inc/functions_imgs.php");
	// Call to check the PHP server to make sure it support Image Processing
	checkPHP();

	// depending request args find if we should show directory or image
	$gallery = true;
	$b_no_dir = true;
	$toplevel = false;
	
	// for remote call by another gallery on another server
	if (array_key_exists('getfirstdirimg', $_REQUEST))
	{
		// return the first image of the requierd directory
		$request = $_REQUEST['getfirstdirimg'];

		if ($request=="")
			$request=$imagePath;
		else
			$request=$imagePath.$request;
		
		$request=getFirstImageDir($request);
		
		authoriseRequest_resize($request,$thumb_landscape_width,$thumb_landscape_height);
		$cache=resize($request,$thumb_landscape_width,$thumb_landscape_height,$cacheDir);
		echo $cache;
		exit();
	}

	// write header
	echo $fsphpgallery_header; //from config.php
	include_once("folder.php");// must be after <html>
	// get and set all post info for remote gallery
	

	$objPostInfo=new PostInfo();
	$full_request="";
	$request ="";
	// check if we have to show dir or image
	if (array_key_exists('dir', $_REQUEST))
	{
		$request = $_REQUEST['dir'];
		if ($request!="")
		{
			$b_no_dir=false;
			$full_request=$imagePath.$request;
		}
	}
	if ($b_no_dir)
	{
		if (array_key_exists('display', $_REQUEST))
		{
			$request = $_REQUEST['display'];
			$full_request=$imagePath.$request;
			$gallery = false;
		}
		else// home
		{
			$request = "";
			$full_request=$imagePath;
		}
	}

	// get gallery page number if any
	if (array_key_exists('gallery_page', $_REQUEST))	
		$page = $_REQUEST['gallery_page'];
	else
		$page=1;

	// authorise Request (avoid gallery abuse)
	authoriseRequest($full_request);

	// if we are in gallery mode (means we show the content of a folder)
	if ($gallery)
	{
		$results = array();
		$up="";
		$nav ="";
		$directory="";

		$currentFolder=new Folder($request);
		// directory listing	
		$results = GetLocalAndRemoteDirList($full_request);

		// get slideshow link if necessary
		$slideshow_data="";
		if (($show_slideshow)&&(count($results)>1))
		{
			$slideshow_link="";
			$slideshow_button_link="";
			$slideshow_img="img/slideshow.png";
			$slideshow_alt=$enable_slideshow_caption;
			$slideshow_optlink="&slideshow=1";
			$slideshow_next_image_button=$results[0];

			////////////////////////////////////////////
			// get link for button slideshow
			////////////////////////////////////////////
			// if next image is a directory
			if (is_object($slideshow_next_image_button))
			{
				if ($up!="")
					$up.="/";
				$slideshow_button_link="?display=".removeSlashes($up.$slideshow_next_image_button->get_name())."&width=$img_preview_landscape_width&height=$img_preview_landscape_height";
			}
			// if next image is a file
			elseif (is_file($imagePath.$slideshow_next_image_button))
			{			
				$slideshow_button_link="?display=".removeSlashes($slideshow_next_image_button)."&width=$img_preview_landscape_width&height=$img_preview_landscape_height";
			}
				
			$slideshow_data="<a href=\"".CookiesLikeMakeLink($slideshow_button_link.$slideshow_optlink)."\"><img src=\"$slideshow_img\" width=\"32\" height=\"32\" border=\"0\" alt=\"".$slideshow_alt."\" /></a>";
		}


		// we need to decide whether to show the navigation buttons or not
		// if current directory is the home folder
		$toplevel = $currentFolder->is_home_folder();
	
		if ($toplevel)
		{ 
			echo $home_header;
			//echo $na ="<p align=\"center\"><img src=\"img/logo.png\" border=\"0\" alt=\"".$home_caption."\" /></p>";
			if ($slideshow_data!="")
				echo "<p align=\"center\">".$slideshow_data."</p>\n";
		}
		else // if not top level show path of upper dir
		{
			$home_link=$currentFolder->get_home_link();
			$up_link=removeSlashes($currentFolder->get_upper_dir_link());
			// $nav_top = "<p align=\"center\"><a href=\"".CookiesLikeMakeLink($home_link)."\">"
					// ."<img src=\"img/home.png\" width=\"32\" height=\"32\" border=\"0\" alt=\"".$home_caption."\" /></a>"
					// ."<a href=\"".CookiesLikeMakeLink($up_link)."\">"
					// ."<img src=\"img/up.png\" width=\"32\" height=\"32\"alt=\"".$up_dir_caption."\" border=\"0\" /></a>"
					// .$slideshow_data
					// ."</p>\n";
			$nav_top = "<p align=\"center\"><a href=\"".CookiesLikeMakeLink($home_link)."\">"
					."<img src=\"img/home.png\" width=\"32\" height=\"32\" border=\"0\" alt=\"".$home_caption."\" /></a>"
					."</p>\n";
			$nav_bottom = "<p align=\"center\"><a href=\"".CookiesLikeMakeLink($home_link)."\">"
					."<img src=\"img/home.png\" width=\"32\" height=\"32\" border=\"0\" alt=\"".$home_caption."\" /></a>"
					."<a href=\"".CookiesLikeMakeLink($up_link)."\">"
					."<img src=\"img/up.png\" width=\"32\" height=\"32\"alt=\"".$up_dir_caption."\" border=\"0\" /></a>"
					."</p>\n";
			echo $nav_top;
			// make links for all upper directories
			echo "<p align=\"center\">".$currentFolder->get_all_path_links(false)."</p>";
		}
	
		echo "<table border=\"0\" width=\"100%\">\n";
		echo "<tr height=\"".$thumb_landscape_height."px\">\n";

		// comput number of item per page and so get the number of pages requiered
		$itemsPerPage = $itemsInRow * $numberOfRows;
		$nb_pages=ceil(count($results)/$itemsPerPage);

		$count = 0;
		$item="";
		$item_name="";
		// show the number of wanted items per page
		for ($i = (($page-1)*$itemsPerPage);  ($i < ($page*$itemsPerPage))&&($i<count($results)); $i++)
		{
			$item = $results[$i];
			
			if (is_object($item)) // item is a folder object
			{
				echo "<td align=\"center\" valign=\"middle\">\n";
				echo $item->get_html_code_for_image_folder(false);
				// show directory name
				echo "<p align=\"center\">".$item->get_link()."</p>\n";
				// add line for presentation
				echo "<p>&nbsp;</p>";
				echo "</td>\n";
			}
			// if item is a file
			else
			{
				$full_path=$imagePath.$item;
				if (is_file($full_path))
				{
					$item_name=returnName($results[$i]);
					
					echo "<td align=\"center\" valign=\"middle\">\n";
					
					// show resized img
					echo "<a href=\"".CookiesLikeMakeLink("?display=".removeSlashes($item)."&width=".$img_preview_landscape_width."&height=".$img_preview_landscape_height)."\">"
						."<img src=\"resize.php?file=".makeUrl(removeSlashes($full_path),false)."&width=$thumb_landscape_width&height=$thumb_landscape_height\" border=\"0\" /></a>\n";
					
					// show file name
					if ($showFileName)
						echo "<p align=\"center\">$item_name<br>\n";
					
					// add line for presentation
					echo "<p>&nbsp;</p>";
					echo "</td>\n";
				}
			}

			$count++;// column counter
			// 
			if ($count == $itemsInRow)
			{
				$count = 0; //reset for the next row
				echo "</tr>\n";// close current row
				// open new row if not last raw
				if (($i+1<($page*$itemsPerPage))&&($i+1<count($results)))
					echo "<tr height=\"".$thumb_landscape_height."px\">\n";
			}
		}// end for
		
		if ($nb_pages>1) // show choose page only if more than one page
		{
			// begin of a new row
			echo "<tr align=\"center\" width=\"100%\">\n";
			
			// first column show previous page link if needed
			echo "<td align=\"right\" valign=\"middle\" width=\"40%\">";
			if($page>1) // show previous page link
				echo "<a href=\"".CookiesLikeMakeLink("?dir=".removeSlashes($request)."&gallery_page=".($page-1))."\">"
					 ."<img src=\"img/back.png\" width=\"32\" height=\"32\" border=\"0\" alt=\"".$prev_caption."\"></a>";
			echo " </td>\n";
			
			//second column show page number
			echo "<td align=\"center\" valign=\"middle\" width=\"20%\">\n";
			for($cnt=1;$cnt<=$nb_pages;$cnt++)
			{
				if ($page==$cnt) // if current page dont make link on it and put number to bold
					echo "<b>$cnt</b> ";
				else// make link on other page number
					echo "<a href=\"".CookiesLikeMakeLink("?dir=".removeSlashes($request)."&gallery_page=".$cnt)."\">$cnt</a> ";
			}
			echo "</td>\n";
			
			// third column show next page link if needed
			echo "<td align=\"left\" valign=\"middle\" width=\"40%\"> ";
			if($page<$nb_pages) // show next page link
				echo "<a href=\"".CookiesLikeMakeLink("?dir=".removeSlashes($request)."&gallery_page=".($page+1))."\">"
					."<img src=\"img/forward.png\" width=\"32\" height=\"32\" border=\"0\" alt=\"".$next_caption."\"></a>";
			echo "</td>\n";			
			
			// end of row
			echo "</tr>\n";
		}
		echo "</table>\n";
	
		if (!$toplevel)
		{
			echo $nav_bottom;
		}
		
	}
	else// not gallery (show one image)
	{
		$item = $_REQUEST['display'];// contains original img full path
		$width = $_REQUEST['width'];
		$height = $_REQUEST['height'];

		$up="";
		if (is_file($imagePath.$item))
			$up = returnPath($item);
		else
		{
			$up = returnUpperDir($item);
		}
		// $up is a local directory (we are listing files in it)
		$currentFolder=new Folder($up);
		
		// directory listing	
		$results = array();
		$full_path=$imagePath;
		if ($up!="")
			$full_path.="/".$up;
		$results = GetLocalAndRemoteDirList($full_path);

		// get the array index of the item to display
		$number=0;
		if ($results)
		{
			// search $_REQUEST['display'] value in $results
			for($cnt=0;$cnt<count($results);$cnt++)
			{
				if (is_object($results[$cnt]))
				{
					if (is_a($results[$cnt],"RemoteFolder"))
					{
						$tmp_up=$up;
						if ($tmp_up!="")
							$tmp_up.="/";
						$resName=$tmp_up.$results[$cnt]->get_name();
					}
					else
						$resName=$results[$cnt]->get_local_relative_path();
				}
				else
					$resName=$results[$cnt];
				if ($item==$resName)
				{
					$number=$cnt;
					break;
				}
			}
		}
		$home_link=$currentFolder->get_home_link();
		// links contains home and up
		$links = "<a href=\"".CookiesLikeMakeLink($home_link)."\"><img src=\"img/home.png\" width=\"32\" height=\"32\" border=\"0\" alt=\"".$home_caption."\" /></a>"
				. "<a href=\"".CookiesLikeMakeLink("?dir=".removeSlashes($up))."\"><img src=\"img/up.png\" width=\"32\" height=\"32\" border=\"0\" alt=\"".$up_dir_caption."\" /></a> ";

		// get slideshow link if necessary
		$slideshow_data="";
		if (($show_slideshow)&&(count($results)>1))
		{
			$slideshow_next_image_number=$number+1;
			// get next image index in array of results
			if ($slideshow_next_image_number>=count($results))
			{
				if ($looping_slideshow) // if we loop folder
					$slideshow_next_image_number=0;// next image is the first in folder
				else
				{
					$slideshow_next_image_number=count($results)-1;// stay locked on the last image of the folder
					$objPostInfo->slideshow_enable=false;// slide show is no more enabled
				}
			}
			$slideshow_next_image= $results[$slideshow_next_image_number];
			$slideshow_next_image_button=$results[$number];
			$slideshow_link="";
			$slideshow_optlink="";
			$slideshow_img="img/slideshow.png";
			$slideshow_alt=$enable_slideshow_caption;

			// if slideshow is enable
			if ($objPostInfo->slideshow_enable==true)
			{
				$slideshow_alt=$disable_slideshow_caption;
				$slideshow_img="img/disable_slideshow.png";
				$slideshow_next_image_button=$results[$number];// stop on current image if we click on Disable slide show button
				$slideshow_optlink="&slideshow=0";

			}
			else // slide show is disabled, do link for enabling it
			{
				$slideshow_optlink="&slideshow=1";
				$slideshow_next_image_button=$slideshow_next_image;
			}
			////////////////////////////////////////////
			// get link for button slideshow
			////////////////////////////////////////////
			// if next image is a directory
			if (is_object($slideshow_next_image_button))
			{
				$tmp_up=$up;
				if ($tmp_up!="")
					$tmp_up.="/";
				$slideshow_button_link="?display=".removeSlashes($tmp_up.$slideshow_next_image_button->get_name())."&width=$img_preview_landscape_width&height=$img_preview_landscape_height";
			}
			// if next image is a file
			elseif (is_file($imagePath.$slideshow_next_image_button))
			{			
				$slideshow_button_link="?display=".removeSlashes($slideshow_next_image_button)."&width=$img_preview_landscape_width&height=$img_preview_landscape_height";
			}
			////////////////////////////////////////////
			// get link for next image in the slideshow
			////////////////////////////////////////////
			// if next image is a directory
			if (is_object($slideshow_next_image))
			{
				$tmp_up=$up;
				if ($tmp_up!="")
					$tmp_up.="/";
				$slideshow_link="?display=".removeSlashes($tmp_up.$slideshow_next_image->get_name())."&width=$img_preview_landscape_width&height=$img_preview_landscape_height";
			}
			// if next image is a file
			elseif (is_file($imagePath.$slideshow_next_image))
			{			
				$slideshow_link="?display=".removeSlashes($slideshow_next_image)."&width=$img_preview_landscape_width&height=$img_preview_landscape_height";
			}

			if (($looping_slideshow==false) && ( $number==count($results)-2 ))
				$slideshow_link.="&slideshow=0";

			$slideshow_data="<a href=\"".CookiesLikeMakeLink($slideshow_button_link.$slideshow_optlink)."\"><img src=\"$slideshow_img\" width=\"32\" height=\"32\" border=\"0\" alt=\"".$slideshow_alt."\" /></a>";

		}


		// img_links contains home, up and all_path_links links
		$img_links="<td align=\"center\" valign=\"middle\" width=\"50%\">".$links.$slideshow_data."<br>".$currentFolder->get_all_path_links(true)."</td>";
		


		// search if require prev or/and next img
		$prev_img=false;
		$next_img=false;
		if ($number != 0) // not the first item in the gallery --> add prev img and link
		{
			$previousimage= $results[$number - 1];
			if (is_object($previousimage)) // $results[$number - 1] is a Folder object
			{
				$tmp_up=$up;
				if ($tmp_up!="")
					$tmp_up.="/";
				$prev_img=$previousimage->get_html_code_for_image_folder(true);	
				$prev_img_link="?display=".removeSlashes($tmp_up.$previousimage->get_name())."&width=$img_preview_landscape_width&height=$img_preview_landscape_height";
			}
			elseif (is_file($imagePath.$previousimage))
			{			
				// show resized img
				$prev_img="<img src=\"resize.php?file=".makeUrl(removeSlashes($imagePath.$previousimage),false)."&width=$thumb_landscape_width&height=$thumb_landscape_height\" border=\"0\">";
				$prev_img_link="?display=".removeSlashes($previousimage)."&width=$img_preview_landscape_width&height=$img_preview_landscape_height";
			}

			// add prev to links
			$links = $links . "<a href=\"".CookiesLikeMakeLink($prev_img_link)."\"><img src=\"img/back.png\" width=\"32\" height=\"32\" border=\"0\" alt=\"".$prev_caption."\"></a> ";
			$img_links="<td align=\"left\" valign=\"middle\" width=\"5%\"><a href=\"".CookiesLikeMakeLink($prev_img_link)."\"><img src=\"img/back.png\" width=\"32\" height=\"32\" border=\"0\" alt=\"".$prev_caption."\"></a></td>"
						."<td align=\"left\" valign=\"middle\" width=\"20%\"><a href=\"".CookiesLikeMakeLink($prev_img_link)."\">$prev_img</td></a>".$img_links;
		}
		else // first image in the gallery don't add prev img and prev link
			$img_links="<td width=\"25%\"></td>".$img_links;

		if ($number < count($results) - 1) // not the final item in the gallery --> add next img and link
		{
			$nextimage= $results[$number + 1];
			if (is_object($nextimage)) // $next_img is a Folder object
			{
				$tmp_up=$up;
				if ($tmp_up!="")
					$tmp_up.="/";
				$next_img=$nextimage->get_html_code_for_image_folder(true);
				$next_img_link="?display=".removeSlashes($tmp_up.$nextimage->get_name())."&width=$img_preview_landscape_width&height=$img_preview_landscape_height";
			}			
			elseif (is_file($imagePath.$nextimage))
			{			
				// show resized img
				$next_img="<img src=\"resize.php?file=".makeUrl(removeSlashes($imagePath.$nextimage),false)."&width=$thumb_landscape_width&height=$thumb_landscape_height\" border=\"0\">";
				$next_img_link="?display=".removeSlashes($nextimage)."&width=$img_preview_landscape_width&height=$img_preview_landscape_height";
			}
			// add next to link
			$links = $links . "<a href=\"".CookiesLikeMakeLink($next_img_link)."\"><img src=\"img/forward.png\" width=\"32\" height=\"32\" border=\"0\" alt=\"".$next_caption."\" /></a> ";
			$img_links.="<td align=\"right\" valign=\"middle\" width=\"20%\"><a href=\"".CookiesLikeMakeLink($next_img_link)."\">$next_img</a></td>"
						."<td align=\"left\" valign=\"middle\" width=\"5%\"><a href=\"".CookiesLikeMakeLink($next_img_link)."\"><img src=\"img/forward.png\" width=\"32\" height=\"32\" border=\"0\" alt=\"".$next_caption."\"></a></td>";
		}
		else
			$img_links.="<td width=\"25%\"></td>";		
		
		echo "<table border=\"0\" width=\"100%\">\n";
		
		// add home / up / prev / next imgs and links
		echo "<tr>\n<td align=\"center\">\n<table width=\"60%\"><tr>$img_links</td>\n</tr>\n</table></td>\n</tr>\n";
		
		// add resized img		
		echo "<tr>\n<td align=\"center\">\n";

		if (is_object($results[$number])) // $item is a Folder object
		{
			echo  $results[$number]->get_html_code_for_image_folder(false);
			echo "<br>".$results[$number]->get_link();
		}
		elseif (is_file($imagePath.$results[$number]))
		{
			echo "<a href=\"".$imagePath.$item."\" target=\"_blank\"><img src=\"resize.php?file=".makeUrl(removeSlashes($imagePath.$item),false)."&width=$img_preview_landscape_width&height=$img_preview_landscape_height\" border=\"0\" alt=\"".$click_to_enlarge_caption."\"></a>\n";
			// the following line is used to show exif and iptc info it can be remove if no use
			require("img_info_viewer.php");
		}

		echo "</td>\n</tr>\n";

		if ($showBottomNavBar)
			// add home / up / prev / next imgs and links
			echo "<tr>\n<td align=\"center\">\n$links</td>\n</tr>\n";		
		
		echo "</table>\n";
	}// end if galley 

	if ($showGalleryInfoOnlyForHome)
	{
		if ($toplevel)
			echo $fsphpgallery_yahoo;
	}
	else
		echo $fsphpgallery_yahoo;

	// footer
	echo $fsphpgallery_footer;
	echo $fsphpgallery_info;
	echo $fsphpgallery_count;

	?>
 
