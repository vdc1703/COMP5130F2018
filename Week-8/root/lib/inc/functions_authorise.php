<?php

	include_once("config.php");
	include_once("inc/functions_std.php");
	function reportAbuse()
	{
		echo "<p>This request has been denied to prevent potential abuse of FsPHPGallery (providing listing of arbitrary directories under the file system). If you are attempting to set up FsPHPGallery for the first time, please do not set \$imagePath (in config.php) to a value starting with \"/\" or \"..\" -- it is advisable to use symbolic links to get around this.</p>";
		echo "</body></html>\n";
		global $abuseReports;
		global $email;
		if ($abuseReports)
		{
			mail($email, "Script abuse", $_SERVER["REMOTE_ADDR"] . " tried to abuse " . $_SERVER['REQUEST_URI']);
		}
		exit();	
	}

	/* This function looks at what the GET request is and decides if someone is trying to exploit the script or not */
	function authoriseRequest($request)
	{
		global $imagePath,$cacheDir;
		if (!$request)
			return;

		$trimed_request=trim($request);
	
		if (strncmp($trimed_request,$imagePath,strlen($imagePath))==0)
		{
			$trimed_request=substr($trimed_request,strlen($imagePath));
			if (!(strpos($trimed_request,"../")===false))
				reportAbuse();
		}
		else if (strncmp($trimed_request,$cacheDir,strlen($cacheDir))==0)
		{
			$trimed_request=substr($trimed_request,strlen($cacheDir));
			if (!(strpos($trimed_request,"../")===false))
				reportAbuse();
		}
		else
			reportAbuse();
		
		if (is_file($request))
		{
			// assume file extension
			if (!file_exists($request))
				reportAbuse();
			if ( (strtolower(returnExtension($request)) != "jpg") && (strtolower(returnExtension($request)) != "png") )
				reportAbuse();
		}
	}

	/* This function looks at what the GET request is and decides if someone is trying to exploit the script or not */
	function authoriseRequest_resize($request,$x,$y)
	{
		if (!$request)
			return;
			
		global $thumb_landscape_width;
		global $thumb_landscape_height;
		global $img_preview_landscape_width;
		global $img_preview_landscape_height;

		// protection against DOS attack --> limit resized size 
		$dos=true;
		if ((($x==$thumb_landscape_width)&&($y==$thumb_landscape_height))
		||(($y==$thumb_landscape_width)&&($x==$thumb_landscape_height))
		||(($x==$img_preview_landscape_width)&&($y==$img_preview_landscape_height))
		||(($y==$img_preview_landscape_width)&&($x==$img_preview_landscape_height)))
			$dos=false;
		if ($dos)
			reportAbuse();
			
		// assume directory && file extension
		authoriseRequest($request);
	}
?>