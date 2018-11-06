<?php
/*
    Copyright (C) 2004 Jacquelin POTIER <jacquelin.potier@free.fr>
    Dynamic aspect ratio code Copyright (C) 2004 Jacquelin POTIER <jacquelin.potier@free.fr>

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; version 2 of the License.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
*/
require_once "iptc_class.php";

class Image_IPTC_EXIF extends Image_IPTC
{
	var $m_formatted_exif;
	var $m_formatted_iptc;
	var $m_date;	// exif date
	function Image_IPTC_EXIF( $sFilename )
	{
		Image_IPTC::Image_IPTC($sFilename);
		$this->m_formatted_exif=false;
		$this->m_formatted_iptc=false;
		$this->m_date=false;
		//allow to call get_date
		$this->get_formatted_exif_data();
	}
	// return thumbdnail exif date
	function get_date()
	{
		if(!$this->m_date)
			return false;
		if (strlen($this->m_date)<19)
			return $this->m_date;
		// exif date is like 2001:12:15 22:36:06 
		$en_date=true;
		if(isset($dd_mm_yy_date))
		{
			if($dd_mm_yy_date)
			{
				$ret=substr($this->m_date,8,2);//day
				$ret.="/".substr($this->m_date,5,2);// month
				$ret.="/".substr($this->m_date,0,4);// year
				$en_date=false;
			}
		}
		if ($en_date)
		{
			$ret=substr($this->m_date,5,2);// month
			$ret.="/".substr($this->m_date,8,2);//day
			$ret.="/".substr($this->m_date,0,4);// year
			$en_date=false;
		}
		return $ret;
	}
	function get_hour()
	{
		return substr($this->m_date,10,9);// hour
	}
	
	function get_formatted_exif_data()
	{
		// avoid reparsing if already done
		if ($this->m_formatted_exif)
			return $this->m_formatted_exif;
		$ret="";
		/* // php function got some troubles with some jpeg 
		if (!function_exists("exif_read_data"))	
			return false;
		// exif data
		$exif_data=exif_read_data($this->m_sFilename);
		
		
		foreach($exif_data as $key=>$section) 
		{
			if (!is_array($section))
				continue;
			// show key name
			$ret.="<tr><td align=\"left\"><i>$key</i></td></tr>\n";

			foreach($section as $name=>$val) 
			{
				// show property name and value
				$ret.="<tr><td align=\"left\" width=\"50%\"><b>$name</b></td><td align=\"left\" width=\"50%\">$val</td></tr>\n";
				
				//
				if ($name=="DateTime")
					$this->m_date=$val;
			}
		}
		*/
		// use PHP_JPEG_Metadata_Toolkit instead
		
		// Turn off Error Reporting
		error_reporting ( 0 );
		// Hide any unknown EXIF tags
		$GLOBALS['HIDE_UNKNOWN_TAGS'] = TRUE;
		include_once 'PHP_JPEG_Metadata_Toolkit_1.11/EXIF.php';		
		
		$Exif_array=get_EXIF_JPEG( $this->m_sFilename );
		foreach($Exif_array[0] as $Tag_ID => $Exif_Tag)
		{
                // Ignore the non numeric elements - they aren't tags
                if ( ! is_numeric ( $Tag_ID ) )
                {
                        // Skip Tags Name
                }
                // Check if the Tag has been decoded successfully
                else if ( $Exif_Tag['Decoded'] == TRUE )
                {
                        // This tag has been successfully decoded

                        // Table cells won't get drawn with nothing in them -
                        // Ensure that at least a non breaking space exists in them
                        if ( trim($Exif_Tag['Text Value']) == "" )
                        {
                                $Exif_Tag['Text Value'] = "&nbsp;";
                        }
                        // Check if the tag is a sub-IFD or a makernote or IPTC/NAA Record within the EXIF IFD
                        if (( $Exif_Tag['Type'] == "SubIFD" )||( $Exif_Tag['Type'] == "Maker Note" )||( $Exif_Tag['Type'] == "IPTC" ))
                        {
							continue;
                        }
                        // Check if the tag is Numeric
                        else if ( $Exif_Tag['Type'] == "Numeric" )
                        {
                                // Numeric Tag - Output text value as is.
                                $ret .= "<tr><td>" . $Exif_Tag['Tag Name'] . "</td><td>" . $Exif_Tag['Text Value'] . "</td></tr>\n";
								if ($Exif_Tag['Tag Name']=="Date and Time")
									$this->m_date=$Exif_Tag['Text Value'];											
                        }
                        else
                        {
                                // Other tag - Output text as preformatted
                                $ret .= "<tr><td>" . $Exif_Tag['Tag Name'] . "</td><td>" . trim( $Exif_Tag['Text Value']) . "</td></tr>\n";
                        }
				}
		}
		
		if ($ret!="")
			$ret="<table width=\"75%\" border=\"0\" align=\"center\">\n$ret</table>\n";
		// end of exif data
		$this->m_formatted_exif=$ret;
		return $this->m_formatted_exif;
	}
	
	function get_formatted_iptc_data()
	{	
		// avoid reparsing if already done
		if ($this->m_formatted_iptc)
			return $this->m_formatted_iptc;

		// iptc data				
		if (empty($this->m_aIPTC))	
			return false;	

		$ret=false;
		$str_key="";
		foreach($this->m_aIPTC as $key => $value)
		{
			if (strlen($key)<5)
				continue;
			$key=substr($key,2,3);// remove 2#
			$str_key=false;
		   switch($key)
		   {
		   case IMAGE_IPTC_OBJECT_NAME:
				$str_key="Object name";
				break;
		   case IMAGE_IPTC_EDIT_STATUS:
				$str_key="Edit Status";
				break;
		   case IMAGE_IPTC_PRIORITY:
				$str_key="Priority";
				break;
		   case IMAGE_IPTC_CATEGORY:
				$str_key="Category";
				break;
		   case IMAGE_IPTC_SUPPLEMENTARY_CATEGORY:
				$str_key="Supplemental Category";
				break;
		   case IMAGE_IPTC_FIXTURE_IDENTIFIER:
				$str_key="Fixture Identifier";
				break;
		   case IMAGE_IPTC_KEYWORDS:
				$str_key="Keywords";
				break;
		   case IMAGE_IPTC_RELEASE_DATE:
				$str_key="Release Date";
				break;
		   case IMAGE_IPTC_RELEASE_TIME:
				$str_key="Release Time";
				break;
		   case IMAGE_IPTC_SPECIAL_INSTRUCTIONS:
				$str_key="Special Instructions";
				break;
		   case IMAGE_IPTC_REFERENCE_SERVICE:
				$str_key="Reference Service";
				break;
		   case IMAGE_IPTC_REFERENCE_DATE:
				$str_key="Reference Date";
				break;
		   case IMAGE_IPTC_REFERENCE_NUMBER:
				$str_key="Reference Number";
				break;
		   case IMAGE_IPTC_CREATED_DATE:
				$str_key="Created Date";
				break;
		   case IMAGE_IPTC_CREATED_TIME:
				$str_key="Created Time";
				break;
		   case IMAGE_IPTC_ORIGINATING_PROGRAM:
				$str_key="Originating Program";
				break;
		   case IMAGE_IPTC_PROGRAM_VERSION:
				$str_key="Program Version";
				break;
		   case IMAGE_IPTC_OBJECT_CYCLE:
				$str_key="Object Cycle";
				break;
		   case IMAGE_IPTC_BYLINE:
				$str_key="Byline";
				break;
		   case IMAGE_IPTC_BYLINE_TITLE:
				$str_key="Byline Title";
				break;
		   case IMAGE_IPTC_CITY:
				$str_key="City";
				break;
		   case IMAGE_IPTC_PROVINCE_STATE:
				$str_key="Province State";
				break;
		   case IMAGE_IPTC_COUNTRY_CODE:
				$str_key="Country Code";
				break;
		   case IMAGE_IPTC_COUNTRY:
				$str_key="Country";
				break;
		   case IMAGE_IPTC_ORIGINAL_TRANSMISSION_REFERENCE:
				$str_key="Original Transmission Reference";
				break;
		   case IMAGE_IPTC_HEADLINE:
				$str_key="Headline";
				break;
		   case IMAGE_IPTC_CREDIT:
				$str_key="Credit";
				break;
		   case IMAGE_IPTC_SOURCE:
				$str_key="Source";
				break;
		   case IMAGE_IPTC_COPYRIGHT_STRING:
				$str_key="Copyright String";
				break;
		   case IMAGE_IPTC_CAPTION:
				$str_key="Caption";
				break;
		   case IMAGE_IPTC_LOCAL_CAPTION:
				$str_key="Local Caption";
				break;
		   case IMAGE_IPTC_CAPTION_WRITER:
				$str_key="Local Caption";
				break;			
			// to debug or find new fields value
		    //default:
			//	$str_key=$key;
			//	break;		   
		   }
		   $content=false;
		   foreach($value as $innerkey => $innervalue)
		   {
			   if( ($innerkey+1) != count($value) )
				   $content.="$innervalue, ";
			   else
				   $content.="$innervalue";
		   }
		   // show only if content and key name are defined
		   if ($content && $str_key)
				// show property name and value
				$ret.="<tr><td align=\"left\" width=\"50%\"><b>$str_key</b></td><td align=\"left\" width=\"50%\">$content</td></tr>\n";
		}
		if ($ret)
			$ret="<table width=\"75%\" border=\"0\" align=\"center\">\n$ret</table>\n";
			
		$this->m_formatted_iptc=$ret;
		return $this->m_formatted_iptc;
	}	

}

?>