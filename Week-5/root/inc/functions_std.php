<?php
// For all url link functions

	function removeSlashes($string)
	{
		if (strlen($string)<1)
			return "";
		while($string[0] == "/")
		{
			$string = substr($string, 1); //return all but first char
			if (strlen($string)<1)
				return "";
		}
		while ($string[strlen($string) - 1] == "/")
		{
			$string = substr($string, 0, strlen($string) - 1); //return all but last char
			if (strlen($string)<1)
				return "";
		}
		return $string;
	}

	/* This takes a path and filename and returns just the path (not the filename) */
	function returnPath($string)
	{
		// if no filename, $string is already a path so return it
		if (returnExtension($string)=="")
			return $string;
			
		//else
		return returnUpperDir($string);
	}
	
	function returnUpperDir($string)
	{
		$pos=strrpos($string,"/");
		if (($pos===false)||($pos==0))
			$up="";
		else
			$up = substr($string,0,$pos);
		return removeSlashes($up);
	}
	
	/* Takes a path and filename and returns just the file name (not the path) */
	function returnName($string)
	{
		return basename($string);
	}

	/* This function returns the extension of a file name */
	function returnExtension($string)
	{
		$path_parts = pathinfo($string);
		if (!array_key_exists("extension",$path_parts))
			return "";
			
		//else
			return $path_parts["extension"];
	}
	
	// get last folder name : sample dir1/dir2/file will return dir2
	// parameters in: - string : full path of FOLDER only (not file)
	// return : last folder name
	function getLastPath($string)
	{
		$ret=strrchr($string,'/');
		if ($ret)
			return removeSlashes($ret);
		else
			return $string;
	}
	
	// remove space or other folder names troubles
	// parameters in: - path : full path (file or folder)
	//				  - b_absolute : true if absolute path, false if relative
	// return : url useable with fopen() 
	function makeUrl($path,$b_absolute)
	{
		$url="";
		if ($b_absolute)
		{
			$url="http://";
			if (strncmp("http://",strtolower($path),strlen("http://"))==0)
			{
				$path=substr($path,strlen("http://"));
			}
		}
		$path_array=explode("/",$path);
		for ($cnt=0;$cnt<count($path_array);$cnt++)
		{
			if (($cnt>0)||(!$b_absolute))
				$path_array[$cnt]=rawurlencode($path_array[$cnt]);
			$url.=$path_array[$cnt];
			if ($cnt<count($path_array)-1)
				$url.="/";
		}
		return $url;
	}		
	
	/* Recursive creates directories (e.g. mkdir -p) */
	function recursiveMkdir($directory)
	{
		$dir = explode("/", $directory);
		$create = "";
		for ($i = 0; $i < count($dir); $i++)
		{
			$final = ($i == count($dir) - 1) ? true : false;
			$create = $create . $dir[$i] . "/";
			if (file_exists($create))
			{
				if ($final)
				{
					return true;
				}
			}
			else
			if (file_exists($create)  && !is_dir($create))
			{
				return false;
			}
			else
			{
				if (mkdir($create,0777))
				{
					chmod($create,0777);// sometimes mkdir seems to not be enought
					if ($i == (count($dir) - 1))
					{
						return true;
					}
				}
				else
				{
					return false;
				}
			}
		}
	}
	
	/* This function reads all the items in a directory and returns an array with this information */
	function dirList ($directory)
	{
		$directory=removeSlashes($directory);
		if ($directory=="")
			$directory="./";
		else
		{
			if ($directory[strlen($directory) - 1] != "/")
				$directory.="/";
		}
		$results = array();
        $handler = opendir($directory);
		while ($file = readdir($handler))
		{
			if ($file != '.' && $file != '..')
			{
				if (is_file($directory . $file))
				{
					if ( (strtolower(returnExtension($file)) == "jpg") || (strtolower(returnExtension($file)) == "png") )
						array_push($results, $file);
				}
				else
				if (is_dir($directory . $file) && ($file[0] != "."))
				{
					array_push($results, $file);
				}
			}
		}
		closedir($handler);
		sort($results);
		reset($results);
		return $results;
	}	
	
	// get first image file in directory (or in subdirectories)
	// parameters in : - $directory : directory to browse
	// return : image path or false if no image
	function getFirstImageDir($directory)
	{
		$array_dir=array();
		if ($directory!="")
		{
			if ($directory[strlen($directory) - 1] != "/")
				$directory.="/";
		}
		$handler = opendir($directory);
		while ($file = readdir($handler))
		{
			if ($file != '.' && $file != '..')
			{
				if (is_file($directory . $file))
				{
					if ( (strtolower(returnExtension($file)) == "jpg") || (strtolower(returnExtension($file)) == "png") )
					{
						closedir($handler);
						return $directory.$file;
					}
				}
				else
				if (is_dir($directory . $file) && ($file[0] != "."))
				{
					array_push($array_dir, $file);
				}
			}
		}
		// no image found in current directory -> parse subdirectories
		while($dir=array_pop($array_dir))
		{
			// recursive call
		    if ($file=getFirstImageDir($directory.$dir))
			{
				closedir($handler);
			    return $file;
			}
		}
		// no file found in directory nor in subdirectories
		closedir($handler);
		return false;
	}
	
?>