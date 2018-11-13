<?php

	include_once("config.php");
	
	// return gd version
	function gdVersion() {
	   if (! extension_loaded('gd')) { return; }
	   ob_start();
	   phpinfo(8);
	   $info=ob_get_contents();
	   ob_end_clean();
	   $info=stristr($info, 'gd version');
	   preg_match('/\d/', $info, $gd);
	   return $gd[0];
	} // end function gdVersion()

	/* Checks whether the PHP has image/gd support */
	function checkPHP()
	{
		if (!gdVersion())
		{
			echo "<p>Your version of PHP does not appear to have gd extension loaded. This extension is requiered for this gallery</p>";
			exit();
		}
	}


	/* This function returns true if the image is portrait, otherwise returns false */
	function isLandscape($path)
	{
		list($width, $height, $type, $attr) = getimagesize($path);
		return ($width > $height);
	}
	
	// resize.php create image in cache (or get it if it exists) contained in (width,height) for landscape images
	//                                                              or (height,width) for portrait images
	// parameters in: - file : location source file
	//				  - width : landscape width
	//                - height : landscape height
	//				  - cacheDir : cache directory
	// return : "full_path_of_new_image_in_cache_dir" on success "error_img" on error
	function resize($file,$thumbX,$thumbY,$cacheDir)
	{
		global $imagePath;
		$error_image_path="error.png";// different error image can be used to debug
		if ((strlen($thumbX)<2)||(strlen($thumbY)<2))
		{
			return $error_image_path;
		}
		// create full path name in cachedir like cachedir/Folder/subfolder/640x480/filename
		$relative_path_from_cache=removeSlashes(substr(returnPath($file),strlen($imagePath)));
		if ($relative_path_from_cache!="")
			$relative_path_from_cache.="/";
		$cache = $cacheDir . $relative_path_from_cache . $thumbX . "x" . $thumbY . "/" . returnName($file);
		if (file_exists($cache))
		{
			return $cache;
		}
		else
		{
			if (!$gdv = gdVersion())// if gd not loaded
			{
				return $error_image_path;
			}
		
			$extension = strtolower(returnExtension(returnName($file)));
			
			if ($extension == "jpg")
			{
				$source = imagecreatefromjpeg($file); // can cause a fatal error
			}
			else
			if ($extension == "png")
			{
				$source = imagecreatefrompng($file);
			}
			
			$img_source_height=imagesy($source);
			$img_source_widht=imagesx($source);
			$ratio=$img_source_widht/$img_source_height;
			
			if ($img_source_height>$img_source_widht) // if portrait
			{// switch thumbX and thumbY
				$tmp=$thumbX;
				$thumbX=$thumbY;
				$thumbY=$tmp;
			}

			// assume img ratio and size respect $thumbX and $thumbY as max
			$img_dest_height=$thumbY;			
			$img_dest_width=$thumbY*$ratio;
			if ($img_dest_width>$thumbX)
			{
				$img_dest_width=$thumbX;
				$img_dest_height=$thumbX/$ratio;
			}

			// create images depending gd version
			if ($gdv >=2)
				$dest = imagecreatetruecolor($img_dest_width, $img_dest_height);
			else // if not using gd 2
				$dest = imagecreate($img_dest_width, $img_dest_height);			

			// if an error has occured during image creation, free memory and return
			if ((!$source)||(!$dest))
			{
				imagedestroy($dest);
				imagedestroy($source);
				return $error_image_path;
			}

			// at this point the 2 image were successfully created
			$imageX = imagesx($source);
			$imageY = imagesy($source);
	
			if ($gdv >=2)
				imagecopyresampled($dest, $source, 0, 0, 0, 0,$img_dest_width, $img_dest_height, $img_source_widht, $img_source_height);
			else // if not using gd 2
				imagecopyresized($dest, $source, 0, 0, 0, 0,$img_dest_width, $img_dest_height, $img_source_widht, $img_source_height);

			// create directory if needed
			recursiveMkdir(returnPath($cache));

			// save resampled file
			if ($extension == "jpg")
			{
				imagejpeg($dest, $cache);
				chmod($cache,0777);
			}
			else
			if ($extension == "png")
			{
				imagepng($dest, $cache);
				chmod($cache,0777);				
			}
			// free memory
			imagedestroy($dest);
			imagedestroy($source);
			return $cache;
		 }
	}
	
	function make_all_thumbs($img_dir,$cache_dir,$thumb_landscape_width,$thumb_landscape_height,$img_preview_landscape_width,$img_preview_landscape_height)
	{
		$results = array();
		$results=dirList($img_dir);
		for ($i=0;$i<count($results);$i++)
		{
			$fullpath=$img_dir.'/'.$results[$i];
			
			if (is_file($fullpath))
			{
				$extension = strtolower(returnExtension(returnName($fullpath)));
				if (($extension == "jpg")||($extension == "png"))
				{
					resize($fullpath,$thumb_landscape_width,$thumb_landscape_height,$cache_dir);
					resize($fullpath,$img_preview_landscape_width,$img_preview_landscape_height,$cache_dir);
				}
			}
			elseif(is_dir($fullpath))
			{
				make_all_thumbs($fullpath,$cache_dir,$thumb_landscape_width,$thumb_landscape_height,$img_preview_landscape_width,$img_preview_landscape_height);
			}
		}
	}
?>