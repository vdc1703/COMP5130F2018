<?php
/*
    Copyright (C) 2004 Chris Howells <howells@kde.org>
	Modified by Jacquelin POTIER <jacquelin.potier@free.fr>

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

// resize.php create image in cache (or get it if it exists) contained in (width,height) for landscape images
//                                                              or (height,width) for portrait images
// parameters in: - file : location source file
//				  - width : landscape width
//                - height : landscape height
// return : "Location:full_path_of_new_image_in_cache_dir" on success "Location:error_img" on error
	include_once("inc/functions_authorise.php");
	include_once("inc/functions_imgs.php");

	if (array_key_exists('file', $_REQUEST) && array_key_exists('width', $_REQUEST) && array_key_exists('height', $_REQUEST))
	{
		$request = $_REQUEST['file'];
		$thumbX = $_REQUEST['width'];
		$thumbY = $_REQUEST['height'];
		authoriseRequest_resize($request,$thumbX,$thumbY);

		$cache=resize($request,$thumbX,$thumbY,$cacheDir);
		header("Location: $cache");
	}

?>
