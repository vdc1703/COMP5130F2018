<?php
/* 
    
     Language file index:
		001 -- Initial page title for contact pages
		002 -- Extended page title for the contact us page
		003 -- Error to be displayed if all form fields are not filled in
		004 -- Error to be displayed if the wrong security code is given
		005 -- Error to be displayed if the given email address is invalid
		006 -- Subject line of the contact us email
		007 -- Message to be displayed on successful contact of administration
		008 -- Error to be displayed if an email fails to be sent
		009 -- Extended page title for the report abuse page
		010 -- Error to be displayed if an image file does not exist when being reported
		011 -- Subject line of the report abuse email
		012 -- Error to be displayed if an invalid page is requested
		013 -- List of reasons that can be used when reporting an image

*/
    
	$vdcclass->lang['001'] = "%s » Contact » ";
	$vdcclass->lang['002'] = "Contact Us";
	$vdcclass->lang['003'] = "The form on the previous page has not been filled in completely. <br />
One or more fields have been left blank. Please try again.<br />
<br />
<a href=\"javascript:void(0);\" onclick=\"history.go(-1);\">Return to Previous Page</a>";
	$vdcclass->lang['004'] = "The security code entered did not match the one displayed. <br />
A new code has been generated. Please try again.<br />
<br />
<a href=\"javascript:void(0);\" onclick=\"history.go(-1);\">Return to Previous Page</a>";
	$vdcclass->lang['005'] = "The email address <b>%s</b> appears to be in an invalid format.<br />
A valid address would look like: <b>username@example.com</b>.<br />
<br />
<a href=\"javascript:void(0);\" onclick=\"history.go(-1);\">Return to Previous Page</a>";
	$vdcclass->lang['006'] = "Contact Us Form (%s) #%s";
	$vdcclass->lang['007'] = "The %s administration has been successfully contacted.<br />
<br />
<a href=\"index.php\">Site Index</a>";
	$vdcclass->lang['008'] = "Failed to send email due to an error with the mail server. Please try again.<br />
<br />
<a href=\"javascript:void(0);\" onclick=\"history.go(-1);\">Return to Previous Page</a>";
	$vdcclass->lang['009'] = "Report Abuse";
	$vdcclass->lang['010'] = "The image file <b>%s</b> does not exist. <br />
Please ensure the filename is spelled correctly.<br />
<br />
<a href=\"javascript:void(0);\" onclick=\"history.go(-1);\">Return to Previous Page</a>";
	$vdcclass->lang['011'] = "Terms of Service Violation Report (%s) #%s";
	$vdcclass->lang['012'] = "You have reached this page in error. Please use your back button to return to the previous page.";
	$vdcclass->lang['013'] = array (
  1 => 'Pornographic Image',
  2 => 'Distribution Without Permission',
  3 => 'Harasses One or Many People',
  4 => 'Promotion Through Advertisement -- "Spam"',
  5 => 'Against International Media Distribution Laws',
  6 => 'Other',
);


?>