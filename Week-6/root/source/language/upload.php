<?php
/* 
Language file index:
		001 -- Page title for upload page
		002 -- Error to be displayed if file extension is not allowed
		003 -- Error to be displayed if a filesize goes above max allowed
		004 -- Message to be displayed when uploading is disabled
		005 -- Page title for when uploading is disabled
		006 -- Error to be displayed if the file being uploaded is not a true image
		007 -- Message to be displayed when uploading is disabled for Guests only
		008 -- Standard PHP upload errors - http://php.net/features.file-upload.errors
		009 -- Error to be displayed if server runs out of randomly generated filenames - Most likely will never be reached
		010 -- Error to be displayed if an image file upload fails
		011 -- Error to be displayed if URL uploading is not possible
		012 -- Error to be displayed if an invalid URL is given during URL upload
		013 -- Error to be displayed if the given URL during cannot be accessed
		014 -- Error to be displayed if no files are given during upload
		254 -- Error if no linked images are found
		743 -- Error to be displayed if webpage cannot be opened for scanning

*/
    
	$vdcclass->lang['001'] = "%s » Upload";
	$vdcclass->lang['002'] = "The image file <b>%s</b>, with the extension <b>.%s</b>, is not allowed to be uploaded.";
	$vdcclass->lang['003'] = "The filesize of the image file <b>%s</b> exceeds the maximum allowed size of <b>%s</b> per file.";
	$vdcclass->lang['004'] = "Uploading is currently disabled. <br />
For more information <a href=\"contact.php?act=contact_us\">contact us</a>.<br />
<br />
<a href=\"javascript:void(0);\" onclick=\"history.go(-1);\">Return to Previous Page</a>";
	$vdcclass->lang['005'] = "Uploading Disabled";
	$vdcclass->lang['006'] = "The file <b>%s</b> is not a true image and therefore could not be uploaded.";
	$vdcclass->lang['007'] = "Sorry, but uploading is restricted to registered users. <br />
Please log in by clicking above to begin uploading.<br />
<br />
<a href=\"javascript:void(0);\" onclick=\"history.go(-1);\">Return to Previous Page</a>";
	$vdcclass->lang['008'] = array (
  1 => '<b>PHP Engine Error</b>: The image file <b>%s</b> exceeds the <a href="http://us2.php.net/manual/en/ini.core.php#ini.upload-max-filesize">upload_max_filesize</a> directive in php.ini.',
  2 => '<b>PHP Engine Error</b>: The image file <b>%s</b> exceeds the <b><i>MAX_FILE_SIZE</i></b> directive that was specified in the HTML form.',
  3 => '<b>PHP Engine Error</b>: The image file <b>%s</b> was only partially uploaded.',
  6 => '<b>PHP Engine Error</b>: Missing a temporary folder.',
  7 => '<b>PHP Engine Error</b>: Failed to write the image file <b>%s</b> to disk.',
  8 => '<b>PHP Engine Error</b>: Upload stopped by extension.',
);
	$vdcclass->lang['009'] = "The filename <b>%s</b> already exists on this server.";
	$vdcclass->lang['010'] = "The image file <b>%s</b> failed to upload due to disk write error.";
	$vdcclass->lang['011'] = "<b>PHP Engine Error:</b> The <a href=\"http://php.net/ini.core\"><b>allow_url_fopen</b></a> directive must be enabled in php.ini OR <a href=\"http://www.php.net/manual/en/book.curl.php\">cURL</a> must be loaded as a PHP extension in order to use URL upload.";
	$vdcclass->lang['012'] = "The source URL of the image file <b>%s</b> is malformed.";
	$vdcclass->lang['013'] = "The source URL of the image file <b>%s</b> did not return a <a href=\"http://en.wikipedia.org/wiki/List_of_HTTP_status_codes\">200 OK</a> status and therefore could not be uploaded.";
	$vdcclass->lang['014'] = "Please go back and select at least one image file to upload.<br />
<br />
<a href=\"javascript:void(0);\" onclick=\"history.go(-1);\">Return to Previous Page</a>";
	$vdcclass->lang['254'] = "No linked images were found on the requested webpage. <br />
<br />
<a href=\"javascript:void(0);\" onclick=\"history.go(-1);\">Return to Previous Page</a>";
	$vdcclass->lang['743'] = "The requested webpage could not be opened to scan for images.<br />
<br />
<a href=\"javascript:void(0);\" onclick=\"history.go(-1);\">Return to Previous Page</a>";


?>