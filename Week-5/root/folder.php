<?php

include_once("config.php");
include_once("inc/functions_authorise.php");
include_once("inc/functions_imgs.php");
include_once("inc/cookies_like.php");

// get local and remote folder list
// $path : full path to list
// return an array of Folder objects
function GetLocalAndRemoteDirList($path)
{
	global $RemoteDirConfigFileName,$imagePath;
	$imagePath_size=strlen($imagePath);
	$LocalAndRemoteArray=array();
	$LocalArray=dirList($path);
	// LocalArray contains Directory or image
	// create a folder object for each folder
	$tmp_path=$path;
	if ($tmp_path!="")
		$tmp_path.="/";
	if (count($LocalArray)>0)
	{
		foreach($LocalArray as $item)
		{
			$full_path=$tmp_path.$item;
			if (is_file($full_path))
				array_push($LocalAndRemoteArray,removeSlashes(substr($full_path,$imagePath_size)));
			else
				array_push($LocalAndRemoteArray,new Folder(removeSlashes(substr($full_path,$imagePath_size))));
		}
	}
	// get remote folder info from a file
	$tmp_path=$path;
	if ($tmp_path!="")
		$tmp_path.="/";
	$RemoteConfigFile=$tmp_path.$RemoteDirConfigFileName;
	// if file exists, parse it (syntax is describe in config.php)
	if (file_exists($RemoteConfigFile))
	{
		$Lines=file($RemoteConfigFile,"rb");
		// for each line
		for($cnt=0;$cnt<count($Lines);$cnt++)
		{
			$array=explode(",",$Lines[$cnt]);
			if (count($array)!=4)
				echo "<br>Error in configuration file ".$RemoteConfigFile." at line ".$cnt."<br>";
			else
				array_push($LocalAndRemoteArray,new RemoteFolder(trim($array[0]),trim($array[1]),trim($array[2]),trim($array[3])));
		}
	}
	return $LocalAndRemoteArray;
}


class PostInfo
{
	var $NbRefGalleries=0;    // full number of referer galleries
	var $AllReferers=array(); // array of RemoteCallingGalleryInfo objects where AllReferers[0] is the oldest referer and AllReferers[NbRefGalleries-1] the newest
	var $slideshow_enable=false;
	
	// GetAllRemoteCallingGalleryInfo
	function PostInfo()
	{
		global $imagePath;
		$this->NbRefGalleries=CookiesLikeGetValue("NumberOfRefererGalleries");
		if ($this->NbRefGalleries==FALSE)
			$this->NbRefGalleries=0;
		
		$this->AllReferers=array();
		// get all informations contained in post
		for ($cnt=0;$cnt<$this->NbRefGalleries;$cnt++)
		{
			// get
			// refererX
			// pseudo_root_depthX
			// pseudo_root_nameX
			$referer=CookiesLikeGetValue("Referer".$cnt);
			$pseudo_root_depth=CookiesLikeGetValue("PseudoRootDepth".$cnt);
			$pseudo_root_name=CookiesLikeGetValue("PseudoRootName".$cnt);
			
			// check referer --> can't begin with / or ../
			$trimed_referer=trim($referer);
			if ((strncmp($trimed_referer,"/",1)==0) || strncmp($trimed_referer,"../",3)==0)
				reportAbuse();
				
			array_push($this->AllReferers,new RemoteCallingGalleryInfo($referer,$pseudo_root_depth,$pseudo_root_name));
		}
		
		
		// if we come from a remote gallery, there is the var "&rgpsrdn=pseudo_root_name" set
		// if we come back from a remote gallery, the var "&rgup=nb_galleries_up" is set

		// check if we are back from gallery
		if (array_key_exists('rgup', $_REQUEST))
		{
			$rgup=$_REQUEST['rgup'];
			if ($this->NbRefGalleries<$rgup)
				$rgup=$this->NbRefGalleries;
			for ($cnt=0;$cnt<$rgup;$cnt++)
			{
				// remove last referer
				array_pop($this->AllReferers);
				// remove post data
				CookiesLikeRemoveVar("Referer".($this->NbRefGalleries-1-$cnt));
				CookiesLikeRemoveVar("PseudoRootDepth".($this->NbRefGalleries-1-$cnt));
				CookiesLikeRemoveVar("PseudoRootName".($this->NbRefGalleries-1-$cnt));
			}
			$this->NbRefGalleries-=$rgup;
		}
		
		// if we go inside another gallery
		if (array_key_exists('rgpsrdn', $_REQUEST))
		{
			// get pseudo root name
			$pseudo_root_name=$_REQUEST['rgpsrdn'];
			// get referer address
			$referer=$_SERVER['HTTP_REFERER'];
			// remove rgpsrdn and rgup from $referer
			$referer=$this->RemoveVarFromRequest($referer,"rgpsrdn");
			$referer=$this->RemoveVarFromRequest($referer,"rgup");

			// get current dir and depth
			$local_dir="";
			if (array_key_exists('dir', $_REQUEST))
			{
				$local_dir=$_REQUEST['dir'];
			}
			if ($local_dir=="")
			{
				$pseudo_root_depth=0;
			}
			else
			{
				$res=count_chars($local_dir);
				$pseudo_root_depth=$res[0x2f]+1;//number of '/'+1
			}

			// push data to array
			array_push($this->AllReferers,new RemoteCallingGalleryInfo($referer,$pseudo_root_depth,$pseudo_root_name));
			// add data to post data
			CookiesLikeSetValue("Referer".($this->NbRefGalleries),$referer);
			CookiesLikeSetValue("PseudoRootDepth".($this->NbRefGalleries),$pseudo_root_depth);
			CookiesLikeSetValue("PseudoRootName".($this->NbRefGalleries),$pseudo_root_name);
			$this->NbRefGalleries++;
		}

		CookiesLikeSetValue("NumberOfRefererGalleries",$this->NbRefGalleries);

		// slideshow
		$this->slideshow_enable=CookiesLikeGetValue("slideshow");
		if (array_key_exists('slideshow', $_REQUEST))
		{
			$arg_slideshow_enable=$_REQUEST['slideshow'];
			if ($arg_slideshow_enable==1)
				$this->slideshow_enable=true;
			else
				$this->slideshow_enable=false;
			CookiesLikeSetValue("slideshow",$this->slideshow_enable);
		}
		if (array_key_exists('dir', $_REQUEST))
			CookiesLikeSetValue("slideshow",false);
		
		// save post data
		CookiesLikeWriteVarValues();

//CookiesLikeShowVars(); // for debug purpose only
	}
	
	function RemoveVarFromRequest($request,$varName)
	{
		$pos=strpos($request,$varName."=");
		if (!($pos===false))
		{
			$posNextVar=strpos($request,"&",$pos+strlen($varName)+1);
			if ($posNextVar===false)
				$request=substr($request,0,$pos-1);// $pos-1 to remove ? or &
			else
				$request=substr($request,0,$pos).substr($request,$posNextVar+1);
		}
		return $request;
	}
	
	function RemoteCallingGalleryExists()
	{
		return $this->NbRefGalleries!=0;
	}
}

class RemoteCallingGalleryInfo
{
	var $referer="";		  // referer link contains full adress (website+dir) something like 127.0.0.1/pictures/?dir=Subdir1/Subdir2
	var $pseudo_root_depth=0; // pseudo root depth for current folder
	var $pseudo_root_name=""; // pseudo root name for current folder 
	function RemoteCallingGalleryInfo($referer,$pseudo_root_depth,$pseudo_root_name)
	{
		$this->referer=$referer;
		$this->pseudo_root_depth=$pseudo_root_depth;
		$this->pseudo_root_name=$pseudo_root_name;
		
		$this->check_pseudo_root_name();
	}

	// assume that a name is defined if caller is linked to the home gallery
	function check_pseudo_root_name()
	{
		// if link is done on local root folder
		if ($this->pseudo_root_depth==0)
		{
			// if no pseudo_root_name
			if ($this->pseudo_root_name=="")
				// give a standart one
				$this->pseudo_root_name="Gallery";
		}
	}
	
	// get referer directory (directory leaved to go to next gallery)
	function get_referer_dir()
	{
		// check if referer is a directory
		$pos=strpos($this->referer,"dir=");
		if ($pos===false)
		{
			// check for display
			$pos=strpos($this->referer,"display=");
			if ($pos===false)
				return "?";
			else
			{
				$buffer=substr($this->referer,$pos+strlen("display="));
				$pos=strpos($buffer,"&");
				if ($pos===false)
					return $buffer;
				// else
				$buffer=substr($buffer,0,$pos);
				// remove last dir as it is the displayed dir
				$buffer=returnUpperDir($buffer);
				return $buffer;
			}
		}
		else
		{
			$buffer=substr($this->referer,$pos+strlen("dir="));
			$pos=strpos($buffer,"&");
			if ($pos===false)
				return $buffer;
			// else
			return substr($buffer,0,$pos);
		}
	}
	
	// get referer home (directory containing the index.php of the image gallery) 
	function get_referer_home()
	{
		// if referer is a directory
		$pos=strpos($this->referer,"?dir=");
		if ($pos===false)
		{
			// if referer is display=
			$pos=strpos($this->referer,"?display=");
			if ($pos===false)
				return $this->referer;
			else
				return substr($this->referer,0,$pos);
		}
		else
			return substr($this->referer,0,$pos);
	}
}
class RemoteFolder extends Folder// only to have another constructor
{
	// constructor used for remote folder from information available in the optionnal add_remote_dir.php file
	function RemoteFolder($gallery_root_folder,$relative_path_from_gallery_root_folder,$displayed_name,$show_in_new_window)
	{
		$this->Folder($relative_path_from_gallery_root_folder);
		
		$this->private_gallery_root_folder=$gallery_root_folder;
		$this->private_displayed_name=$displayed_name;
		$this->private_show_in_new_window=$show_in_new_window;
	}
}
class Folder
{
	var $private_gallery_root_folder="";
	var $private_relative_path_from_gallery_images_root_folder="";
	var $private_displayed_name="";
	var $private_show_in_new_window=false;// for remote gallery access
	
	// constructor used for local folder
	function Folder($relative_path_from_gallery_root_folder)
	{
		$this->private_relative_path_from_gallery_images_root_folder=removeSlashes($relative_path_from_gallery_root_folder);
	}
	
	// return true if local folder, false if remote folder
	function is_local_folder()
	{
		return ($this->private_gallery_root_folder=="");
	}
	// return true if pseudo root folder
	function is_pseudo_root_folder()
	{
	    global $objPostInfo;
		// compare local depth and pseudo_root_depth
		if ($objPostInfo->NbRefGalleries==0)
			return FALSE;
		else
			return $this->get_local_depth()==$objPostInfo->AllReferers[$objPostInfo->NbRefGalleries-1]->pseudo_root_depth;
	}
	//return true if home folder of the gallery --> no calling gallery and root folder
	function is_home_folder()
	{
		global $objPostInfo;
		// if calling gallery exists --> not home
		if ($objPostInfo->RemoteCallingGalleryExists())
			return false;
		// if local folder
		return ($this->private_relative_path_from_gallery_images_root_folder=="");
	}
	// get local depth from local iamge root directory
	function get_local_depth()
	{
		if ($this->private_relative_path_from_gallery_images_root_folder=="")
			return 0;
		$res=count_chars($this->private_relative_path_from_gallery_images_root_folder);
 		return $res[0x2f]+1;//number of '/'+1
	}	
	
	// return folder name depending if it is local or remote
	function get_name()
	{
	    global $objPostInfo;
		// if we are a remote folder
		if ($this->is_local_folder())
		{
			// if there's a calling gallery, pseudo root folder name can be changed
			if ($objPostInfo->RemoteCallingGalleryExists())
			{
				// if folder is the pseudo root folder
				if ($this->is_pseudo_root_folder())
				{
					// if folder name is asked to be changed
					if ($objPostInfo->AllReferers[$objPostInfo->NbRefGalleries-1]->pseudo_root_name!="")
						return $objPostInfo->AllReferers[$objPostInfo->NbRefGalleries-1]->pseudo_root_name;
				}
			}
			// return folder name
			return getLastPath($this->private_relative_path_from_gallery_images_root_folder);
		}
		else
			return $this->private_displayed_name;
	}
	
	// get relative path from local gallery root folder
	function get_local_relative_path()
	{
		return $this->private_relative_path_from_gallery_images_root_folder;
	}
	
	function private_get_link($link_data)
	{
		if ($this->is_local_folder())
		{
			$link=$this->get_local_relative_path();
			if ($link=="")
				$link="?";
			else
				$link="?dir=".removeSlashes($link);
			return "<a href=\"".CookiesLikeMakeLink($link)."\">".$link_data."</a>";
		}
		else
		{
			$dir="";
			if ($this->private_relative_path_from_gallery_images_root_folder!="")
				$dir="?dir=".removeSlashes($this->private_relative_path_from_gallery_images_root_folder);
			if (strtolower($this->private_show_in_new_window)=="true")
			{
				return "<a href=\"".$this->private_gallery_root_folder
					.$dir
					."\" target=\"_blank\" >".$link_data."</a>";
			}
			else
			{
				$joiner="&";
				if ($dir=="")
					$joiner="?";
				return "<a href=\"".CookiesLikeMakeLink($this->private_gallery_root_folder
					.$dir
					.$joiner
					."rgpsrdn=".$this->private_displayed_name)
					."\">".$link_data."</a>";
			}
		}	
	}
	// get link to folder
	function get_link()
	{
		return $this->private_get_link($this->get_name());
	}
	// get upper directory link depending the existense of remotes calling galleries
	function get_upper_dir_link()
	{
	    global $objPostInfo;
		// get upper directory name
		$up=returnUpperDir($this->private_relative_path_from_gallery_images_root_folder);

		if ($objPostInfo->RemoteCallingGalleryExists())
		{
			// check if current folder is the pseudo root one
			$res=count_chars($up);
			$up_depth=$res[0x2f];//number of '/'
			// if it is the pseudo root folder
			if (($up_depth<$objPostInfo->AllReferers[$objPostInfo->NbRefGalleries-1]->pseudo_root_depth)
			// or the local gallery root folder
			||($this->private_relative_path_from_gallery_images_root_folder==""))
			{
				// return to the remote calling directory last dir, with rgup=1 remote gallery up from 1
				$link=$objPostInfo->AllReferers[$objPostInfo->NbRefGalleries-1]->referer;
				if (strstr($link,"?"))
					$link.="&";
				else
					$link.="?";
				$link.="rgup=1";
				return $link;
			}
			// else just make a link to the upper dir
			return "?dir=".removeSlashes($up);
		}
		else
		{
			if ($up=="")
				return "?";
			// else
			return "?dir=".removeSlashes($up);
		}
	}

	function get_home_link()
	{
	    global $objPostInfo;
		if ($objPostInfo->RemoteCallingGalleryExists())
		{
			$link=$objPostInfo->AllReferers[0]->get_referer_home();
			if (strstr($link,"?"))
				$link.="&";
			else
				$link.="?";
			$link.="rgup=".($objPostInfo->NbRefGalleries);
			return $link;
		}
		else
			return "?";
	}
	
	// get all upper directory with their links
	// parameters in: 
	//				  - link_to_last_folder_name : true if you want to make a link last folder name
	//                - separator : optional string used between different directories
	// return : all upper directories with their link (html formated with class_link_color style) 
	function get_all_path_links($link_to_last_folder_name,$separator ="&gt;")
	{
	    global $objPostInfo,$home_caption;
		$str_all_directories="";
		$splited_dir=explode("/",$this->private_relative_path_from_gallery_images_root_folder);
		$str_all_directories="";
		$pseudo_root_depth=0;

		$ref_home=$this->get_home_link();
		$str_all_directories="<a href=\"".CookiesLikeMakeLink($ref_home)."\">".$home_caption."</a>";
		$remoteGalleryExists=($objPostInfo->RemoteCallingGalleryExists());

		// add remote calling gallery links
		if ($remoteGalleryExists)
		{
			////////////////////////////////////			
			// add remote gallery dir
			////////////////////////////////////

			// for each referer
			for ($cnt_ref=0;$cnt_ref<$objPostInfo->NbRefGalleries;$cnt_ref++)
			{
				// retreive referer home
				$ref_home=$objPostInfo->AllReferers[$cnt_ref]->get_referer_home();

				// check if there's a referer dir
				if (($objPostInfo->AllReferers[$cnt_ref]->get_referer_dir()=="")||
					($objPostInfo->AllReferers[$cnt_ref]->get_referer_dir()=="?"))// link from first home dir
					continue;
					
				// retreive referer leaving dir
				$remote_splited_dir=explode("/",$objPostInfo->AllReferers[$cnt_ref]->get_referer_dir());

				// make link for each upper dir of referer leaving dir
				for ($cnt=0;$cnt<count($remote_splited_dir);$cnt++)
				{
					if ($cnt_ref>0)
					{
						if ($cnt<$objPostInfo->AllReferers[$cnt_ref-1]->pseudo_root_depth-1)
							continue;
					}
						
					// get full path for folder $splited_dir[$cnt]
					$str_path="";
					for ($cnt2=0;$cnt2<=$cnt;$cnt2++)
					{
						$str_path.=$remote_splited_dir[$cnt2];
						if ($cnt!=$cnt2)// dont add / for last one
							$str_path.="/";
					}
					
					// get directory name
					$dir_name=$remote_splited_dir[$cnt];
					if ($cnt_ref>0)
					{
						if ($cnt==$objPostInfo->AllReferers[$cnt_ref-1]->pseudo_root_depth-1)
							$dir_name=$objPostInfo->AllReferers[$cnt_ref-1]->pseudo_root_name;
					}
					// add link to $str_all_directories
					$str_all_directories.=" $separator <a href=\"".CookiesLikeMakeLink($ref_home."?dir=".removeSlashes($str_path)."&rgup=".($objPostInfo->NbRefGalleries-$cnt_ref))."\">".rawurldecode($dir_name)."</a>";
				}
			}
		
			/////////////////////////////////////////
			// if we are not in pseudo root dir, make link to current pseudo root dir (change name or give one name if local root dir)
			// else just display pseudo root dir name and make link only if link_to_last_folder_name
			/////////////////////////////////////////
			$dir_name=$objPostInfo->AllReferers[$cnt_ref-1]->pseudo_root_name;
			if (($this->is_pseudo_root_folder())&&(!$link_to_last_folder_name))
			{
				$str_all_directories.=" $separator ".$dir_name;	
				// fill pseudo root depth value
				$pseudo_root_depth=$objPostInfo->AllReferers[$objPostInfo->NbRefGalleries-1]->pseudo_root_depth;
			}
			else
			{
				if ($objPostInfo->AllReferers[$objPostInfo->NbRefGalleries-1]->pseudo_root_depth==0)
				{
					// add link
					$str_all_directories.=" $separator <a href=\"".CookiesLikeMakeLink("?")."\">".$dir_name."</a>";
				}
				else
				{
					// get link to current pseudo root dir
					$str_path="";
					for ($cnt2=0;$cnt2<$objPostInfo->AllReferers[$objPostInfo->NbRefGalleries-1]->pseudo_root_depth;$cnt2++)
					{
						$str_path.=$splited_dir[$cnt2];
						if ($cnt!=$cnt2)// dont add / for last one
							$str_path.="/";
					}
					// add link
					$str_all_directories.=" $separator <a href=\"".CookiesLikeMakeLink("?dir=".removeSlashes($str_path))."\">".$dir_name."</a>";
					// fill pseudo root depth value
					$pseudo_root_depth=$objPostInfo->AllReferers[$objPostInfo->NbRefGalleries-1]->pseudo_root_depth;
				}
			}

		}
		else
		{
			$splited_dir=explode("/",$this->private_relative_path_from_gallery_images_root_folder);
			$pseudo_root_depth=0;
		}

		////////////////////////////////////			
		// add locals dir (only those after the pseudo root dir)
		////////////////////////////////////
		if ($this->get_local_depth()>0)
		{
			for ($cnt=$pseudo_root_depth;$cnt<count($splited_dir);$cnt++)
			{
				// get full path for folder $splited_dir[$cnt]
				$str_path="";
				for ($cnt2=0;$cnt2<=$cnt;$cnt2++)
				{
					$str_path.=$splited_dir[$cnt2];
					if ($cnt!=$cnt2)// dont add / for last one
						$str_path.="/";
				}
				if ((($cnt!=0) || ($remoteGalleryExists==false))&&($cnt!=count($splited_dir)-1)) // don't show home link again
					// add link to $str_all_directories
					$str_all_directories.=" $separator <a href=\"".CookiesLikeMakeLink("?dir=".removeSlashes($str_path))."\">".$splited_dir[$cnt]."</a>";
				if ($cnt==count($splited_dir)-1)
				{
					// last folder name
					if ($link_to_last_folder_name)
						$str_all_directories.=" $separator <a href=\"".CookiesLikeMakeLink("?dir=".removeSlashes($str_path))."\">".$splited_dir[$cnt]."</a>";
					else // (no link to it)
						$str_all_directories.=" $separator ".$splited_dir[$cnt];
				}
			}
		}
		
		return "<span class=\"class_link_color\">$str_all_directories</span>";		
	}

	
	function get_html_code_for_image_folder($link_is_preview)
	{
		if ($this->is_local_folder())
			return $this->private_get_html_code_for_local_image_folder($this->private_relative_path_from_gallery_images_root_folder,$link_is_preview);
		else
			return $this->private_get_html_code_for_remote_image_folder(
													$this->private_gallery_root_folder,
													$this->private_relative_path_from_gallery_images_root_folder,
													$this->private_displayed_name,
													$link_is_preview);
	}
	
	// get html code for thumbs for folder
	// parameters in: - dir : relative path from gallery images root folder LOCAL DIR ONLY
	//				  - link_is_preview : true if target is a preview of folder, false if target is content of folder
	// return : html code
	function private_get_html_code_for_local_image_folder($dir,$link_is_preview)
	{
		global $imagePath,$img_preview_landscape_width,$img_preview_landscape_height,$thumb_landscape_width,$thumb_landscape_height,$folder_caption;
		$full_path=$imagePath;
		if ($dir!="");
			$full_path.="/".$dir;
		// find first image in dir
		$item=getFirstImageDir($full_path);

		// if there's an img in dir (or subdir)
		if ($item)
		{	
			// use thumb and "resize" it in html code (avoid creation of new image and use cache)
			$folder_icon_max_size=90;
			// check that size are less than $folder_icon_max_size (the background image size)
			list($display_width, $display_height, $type, $attr) = getimagesize($item);			
			if ($display_width>$folder_icon_max_size)
			{
				// keep ratio
				$display_height=$display_height*$folder_icon_max_size/$display_width;
				$display_width=$folder_icon_max_size;
			}
			if ($display_height>$folder_icon_max_size)
			{
				// keep ratio
				$display_width=$display_width*$folder_icon_max_size/$display_height;
				$display_height=$folder_icon_max_size;
			}

			$str_ret="<table align=\"center\" background=\"folder.png\" width=\"128\" height=\"128\"border=\"0\">"
					."<tr>\n<td align=\"center\" valign=\"middle\">\n";
			if ($link_is_preview)		
				$str_ret.="<a href=\"".CookiesLikeMakeLink("?display=".removeSlashes($dir)."&width=".$img_preview_landscape_width."&height=".$img_preview_landscape_height)."\">\n";			
			else
				$str_ret.="<a href=\"".CookiesLikeMakeLink("?dir=".removeSlashes($dir))."\">\n";			
				
			$str_ret.="<img src=\"resize.php?file=".makeUrl(removeSlashes($item),false)."&width=".$thumb_landscape_width."&height=".$thumb_landscape_height."\" border=\"0\" width=\"$display_width\" height=\"$display_height\" alt=\"".$folder_caption." ".getLastPath($dir)."\">\n"
					 ."</a>\n</td>\n</tr>\n</table>\n";
		}
		else
		{
			// show default folder icon
			if ($link_is_preview)
				$str_ret="<a href=\"".CookiesLikeMakeLink("?display=".removeSlashes($dir)."&width=".$img_preview_landscape_width."&height=".$img_preview_landscape_height)."\"><img src=\"folder.png\" border=\"0\" alt=\"".$folder_caption." ".getLastPath($dir)."\" /></a>";			
			else				
				$str_ret="<a href=\"".CookiesLikeMakeLink("?dir=".removeSlashes($dir))."\"><img src=\"folder.png\" border=\"0\" alt=\"".$folder_caption." ".getLastPath($dir)."\" /></a>";
		}
		return 	$str_ret;
	}	
	
	

}

?>