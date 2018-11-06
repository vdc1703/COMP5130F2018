
<style type="text/css">
<!--
.span_hidden{display:none;}
.span_visible{display:inline;background-color:#EEEEEE;}
.hide_show_out{
	text-decoration: underline;
	color: #000000;
	font-weight: bold;
	background-color: #99CCFF;
	cursor:pointer;
}
.hide_show_over{
	text-decoration: underline;
	color: #FFFFFF;
	font-weight: bold;
	background-color: #316AC5;
	cursor:pointer;	
}
--></style>
<script>
function change_state(id)
{
   if (document.getElementById(id).className=="span_hidden")
   {
       document.getElementById(id).className="span_visible";
   }
   else
   {
       document.getElementById(id).className="span_hidden";
   }
}

</script>
<?php
	include_once("config.php");
	include_once("iptc_exif_class.php");

			$img_infos=new Image_IPTC_EXIF($imagePath."/".$item);

			echo "<table width=\"50%\">";
			if ($show_caption)
			{
				// get caption
				$formatted_text=$img_infos->getTag(IMAGE_IPTC_CAPTION);
				if ($formatted_text)
					echo "<tr><td align=\"center\">$formatted_text</td></tr>";
			}			
			if ($show_date)
			{
				// get date
				$formatted_text=$img_infos->get_date();
				if ($show_hour)
					$formatted_text.=$img_infos->get_hour();
				if ($formatted_text)
					echo "<tr><td align=\"center\"><b>Date: </b>$formatted_text</td></tr>";
			}
			if ($show_source)
			{
				// get source
				$formatted_text=$img_infos->getTag(IMAGE_IPTC_SOURCE);
				if ($formatted_text)
					echo "<tr><td align=\"center\"><b>Source: </b>$formatted_text</td></tr>";
			}
			if ($show_copyright)
			{
				// get copyright
				$formatted_text=$img_infos->getTag(IMAGE_IPTC_COPYRIGHT_STRING);
				if ($formatted_text)
					echo "<tr><td align=\"center\"><b>Copyright: </b>$formatted_text</td></tr>";
			}
			echo "</table>";
			
			if ($show_iptc_info)
			{
				// get full iptc data
				$formatted_text=$img_infos->get_formatted_iptc_data();
				if ($formatted_text)
				{ 
					echo "<table width=\"50%\"><tr><td align=\"left\">";
					echo "<span class=\"hide_show_out\" onclick=\"change_state('id_iptc_info');\" onMouseOver=\"this.className='hide_show_over';\" onMouseOut=\"this.className='hide_show_out';\">$show_hide_iptc_caption</span>";
					echo "</td>";
					echo "<tr><td align=\"center\"><span class=\"span_hidden\" id=\"id_iptc_info\">$formatted_text</span></td></tr>";
					echo "</tr></table>";
				}
			}

			if ($show_exif_info)
			{
				// get full exif data
				$formatted_text=$img_infos->get_formatted_exif_data();
				if ($formatted_text)
				{ 
					echo "<table width=\"50%\"><tr><td align=\"left\">";
					echo "<span class=\"hide_show_out\" onclick=\"change_state('id_exif_info');\" onMouseOver=\"this.className='hide_show_over';\" onMouseOut=\"this.className='hide_show_out';\">$show_hide_exif_caption</span>";
					echo "</td>";
					echo "<tr><td align=\"center\"><span class=\"span_hidden\" id=\"id_exif_info\">$formatted_text</span></td></tr>";
					echo "</tr></table>";
				}
			}
?>