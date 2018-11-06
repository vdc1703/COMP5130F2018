<?php
/*
    Copyright (C) 2005 Jacquelin POTIER <jacquelin.potier@free.fr>

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

// NOTICE : use the include_once or require_once for this file

// this file allows to pass data as post instead of get
// like this we don't need to use cookies and we can use lots of data
// how it works :
// we create a form with an hidden field that contains a string with the same way as the get
// (var1=value1&var2=value2&var3=value3....)
// but we are no more limited to 255 chars as with get

//////////////////////////////////////////////////
//////////////// SAMPLE OF USE ///////////////////
//////////////////////////////////////////////////
/*
////////// inside php code //////////////

		CookiesLikeSetValue("titi1","val1");
		CookiesLikeSetValue("titi2","val2");
		CookiesLikeSetValue("titi3","val3");
		CookiesLikeSetValue("toto",45);
		// change toto value
		CookiesLikeSetValue("toto",46);
		// remove titi2
		CookiesLikeRemoveVar("titi2");
		
		echo CookiesLikeGetValue("toto");		
		// save vars
		CookiesLikeWriteVarValues();

////////// inside html code relapce all links by //////////////

		<a href="<?php echo CookiesLikeMakeLink("link") ?>">toto</a>
*/
//////////////////////////////////////////////////
//////////////// END OF SAMPLE OF USE ////////////
//////////////////////////////////////////////////


$CookiesLikeVarSeparator='&&';   // must allow to use url with GET data (avoid '&' and '=')
$CookiesLikeVarValSeparator='==';// must allow to use url with GET data (avoid '&' and '=')
$CookiesLikeFormName='CookiesLikePostForm';
$CookiesLikePostFieldName='CookiesLikePostField';
$CookiesLikeArrayData=array();
CookiesLikeWriteScript($CookiesLikeFormName);
CookiesLikeGetVarsFromPost($CookiesLikePostFieldName);
// CookiesLikeShowVars(); // for debug only


// write the following javascript where form_name is previously defined
//function make_link(mlink){
//document.getElementById('form_name').action=mlink;
//document.getElementById('form_name').submit();}
// parameters :
// return : none
function CookiesLikeWriteScript($form_name)
{
	echo "<script>function CLMakeLink(mlink){";
	echo "document.getElementById('".$form_name."').action=mlink;";
	echo "document.getElementById('".$form_name."').submit();";
	echo "}</script>";
}

// just give it the link without vars
// parameters :
// return : requiered linked string to pass all set Vars with CookiesLikeSetValue
function CookiesLikeMakeLink($link)
{
	return "javascript:CLMakeLink('".$link."')";
}

// parse CookiesLike var located in post and put them to $CookiesLikeArrayData array
// parameters :
// return : none
function CookiesLikeGetVarsFromPost($post_var_name)
{
	$posted_data="";
	global $CookiesLikeArrayData;
	global $CookiesLikeVarSeparator;
	global $CookiesLikeVarValSeparator;
	if (array_key_exists($post_var_name, $_POST))
	{
		$posted_data=$_POST[$post_var_name];
		// get all var=value
		$posted_array=explode($CookiesLikeVarSeparator,$posted_data);
		foreach($posted_array as $var_value)
		{
			$var_value_array=explode($CookiesLikeVarValSeparator,$var_value);
			if (count($var_value_array)==2)
				array_push($CookiesLikeArrayData,array("Name"=>$var_value_array[0],"Value"=>$var_value_array[1]));
		}
	}
}
// for debug purpose only : write all found posted vars using CookiesLike
// parameters :
// return : none
function CookiesLikeShowVars()
{
	global $CookiesLikeArrayData;
	echo '<br>';
	foreach($CookiesLikeArrayData as $name_value)
	{
		echo $name_value['Name']."=".$name_value['Value'].'<br>';
	}
}

// set value for a new or an existing var
// parameters : -$var_name :name of var
//              -$var_value : value of var
// return : none
function CookiesLikeSetValue($var_name,$var_value)
{
	global $CookiesLikeArrayData;
	// replace value if exists
	$counted_data=count($CookiesLikeArrayData);
	for ($cnt=0;$cnt<$counted_data;$cnt++)
	{
		if ($CookiesLikeArrayData[$cnt]['Name']==$var_name)
		{
			$CookiesLikeArrayData[$cnt]['Value']=$var_value;
			return;
		}
	}
	// not found --> add new var
	array_push($CookiesLikeArrayData,array("Name"=>$var_name,"Value"=>$var_value));
}

// get value for a specified var
// parameters : $var_name : name of var
// return : var value or FALSE if not found
function CookiesLikeGetValue($var_name)
{
	global $CookiesLikeArrayData;
	foreach($CookiesLikeArrayData as $name_value)
	{
		if ($name_value['Name']==$var_name)
			return $name_value['Value'];
	}
	// not found
	return FALSE;
}

// remove the specified var
// return TRUE on success, FALSE if not found
function CookiesLikeRemoveVar($var_name)
{
	global $CookiesLikeArrayData;
	$counted_data=count($CookiesLikeArrayData);
	for ($cnt=0;$cnt<$counted_data;$cnt++)
	{
		if ($CookiesLikeArrayData[$cnt]['Name']==$var_name)
		{
			// switch element to remove with the last one
			$CookiesLikeArrayData[$cnt]=$CookiesLikeArrayData[$counted_data-1];
			// remove last element
			array_pop($CookiesLikeArrayData);
			return TRUE;
		}
	}
	// not found
	return FALSE;
}
// call this func when all your vars/values are set and won't be modify any more
// must be called only once
function CookiesLikeWriteVarValues()
{
	global $CookiesLikeArrayData;
	global $CookiesLikeFormName;
	global $CookiesLikePostFieldName;
	global $CookiesLikeVarSeparator;
	global $CookiesLikeVarValSeparator;
	$posted_data="";
	$counted_data=count($CookiesLikeArrayData);
	for ($cnt=0;$cnt<$counted_data;$cnt++)
	{
		if ($cnt!=0)
			$posted_data.=$CookiesLikeVarSeparator;
		$posted_data.=$CookiesLikeArrayData[$cnt]['Name'].$CookiesLikeVarValSeparator.$CookiesLikeArrayData[$cnt]['Value'];
	}
	echo '<form id="'.$CookiesLikeFormName.'" method="post" action="" style="display:none">';
	echo '<input type="hidden" name="'.$CookiesLikePostFieldName.'" value="'.$posted_data.'"/>';
	echo '</form>';
}