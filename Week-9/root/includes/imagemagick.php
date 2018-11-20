<?php
	
	class vdc_image_functions
	{
		// Class Initialization Method
		function __construct() { global $vdc; $this->vdc = &$vdc; }
		
		function file_extension($img_name)
		{
			$fileparts = explode(".", $img_name);
			
			return strtolower(end($fileparts));
		}
		
		function basename($img_name, $extension = NULL)
		{
			if (is_array($img_name) == false) {
				return strtolower(basename(trim($img_name), $extension));
			} else {
				return array_map("strtolower", array_map("basename", array_map("trim", $img_name), array($extension)));	
			}
		}
		
		function is_image($img_name)
		{
			if ($this->vdc->funcs->file_exists($img_name) == true) {
					
					if ($this->manipulator == "imagick") {
						// Why can't Imagick just return a false boolean?
						// Catching error exceptions uses unnecessary code
						
						try {
							$imageh = new Imagick("{$img_name}[0]");
							
							if ($imageh == false) {
								return true;
							}
						} catch (Exception $e) {
							return false;
						}
					} else {
						// Come on seriously? No exif or Imagick?
						// GD supports like nothing. Oh well :-(
	
						$imageinfo = getimagesize($img_name);
						
						if (isset($imageinfo['2']) == false) {
							return false;	
						}
					}
			} else {
				// Well, well, well. Looks like the image doesn't exist.
				
				// Other than for debugging purposes for former support
				
				trigger_error("vdc->image->is_image(): image does not exist. ({$img_name})", E_USER_ERROR);
				
				return false;
			}
			
			return true;
		}
		
		function thumbnail_name($img_name)
		{
			$file_extension = $this->file_extension($img_name);
			$base_img_name = $this->basename($img_name, ".{$file_extension}");
			
			if ($this->manipulator == "gd") {
				return sprintf("%s_thumb.%s", $base_img_name, $file_extension);	
			} else {
				// An easier method could most likely be developed 
				// to do this but this will have to do for now :-)
						
				$real_extension = (($this->vdc->info->config['thumbnail_type'] == "png") ? "png" : "jpg");
			
				$check_extension = (($this->vdc->info->config['thumbnail_type'] == "png") ? "jpg" : "png");
				$check_thumbtype = (($this->vdc->info->config['thumbnail_type'] == "png") ? "jpeg" : "png");
				
				$thumbname = sprintf("%s_thumb.%s", $base_img_name, $check_extension);
			
				$file_check = $this->vdc->funcs->file_exists($this->vdc->info->root_path.$this->vdc->info->config['upload_path'].$thumbname);
			
				return (($file_check == true || $file_check == false && $this->vdc->info->config['thumbnail_type'] == $check_thumbtype) ? $thumbname : sprintf("%s_thumb.%s", $base_img_name, $real_extension));
			}
		}
		
		function format_filesize($filesize = 0, $returnbytes = false)
		{			
			while (($filesize / 1024) >= 1) { 
				$filesize_count++; 
				$filesize = ($filesize / 1024); 
			} 
			
			$filesize_count = (($filesize_count < 0) ? 0 : $filesize_count);
			
			if ($returnbytes == true) {
				return array("f" => $filesize, "c" => $filesize_count);
			} else {
				$finalsize = substr($filesize, 0, (strpos($filesize, ".") + 4));
				$finalname = (($filesize > 0.9 && $filesize < 2.0) ? $this->vdc->lang['3103'][$filesize_count] : $this->vdc->lang['4191'][$filesize_count]);
				
				return (($filesize < 0 || $filesize_count > 9) ? $this->vdc->lang['5454'] : sprintf("%s %s", $finalsize, $finalname));
			}
		}
		
		function create_thumbnail($img_name, $save2disk = true, $resize_type = 0)
		{
			$img_name = $this->basename($img_name);
				
			if ($this->is_image($this->vdc->info->root_path.$this->vdc->info->config['upload_path'].$img_name) == true) {
				$extension = $this->file_extension($img_name);
				$thumbnail = $this->thumbnail_name($img_name);
				
				if ($save2disk == true) {
					// Seemed easier to build the image resize upload  
					// option into the already established thumbnail function 
					// instead of waisting time trying to chop it up for new one.
					
					if ($resize_type > 0 && $resize_type <= 8) {
						$thumbnail = $img_name;
						
						$this->vdc->info->config['advanced_thumbnails'] = false;
					
						$size_values = array(
							1 => array("w" => 100, "h" => 75),
							2 => array("w" => 150, "h" => 112),
							3 => array("w" => 320, "h" => 240),
							4 => array("w" => 640, "h" => 480),
							5 => array("w" => 800, "h" => 600),
							6 => array("w" => 1024, "h" => 768),
							7 => array("w" => 1280, "h" => 1024),
							8 => array("w" => 1600, "h" => 1200),	
						);
						
						$thumbnail_size = $size_values[$resize_type];
					} else {
						$thumbnail_size = $this->scale($img_name, $this->vdc->info->config['thumbnail_width'], $this->vdc->info->config['thumbnail_height']);
					}
					
					chmod($this->vdc->info->root_path.$this->vdc->info->config['upload_path'].$thumbnail, 0644);
				} else {
					readfile($this->vdc->info->root_path.$this->vdc->info->config['upload_path'].$thumbnail);
				}
			}
		}
	}
	
?>