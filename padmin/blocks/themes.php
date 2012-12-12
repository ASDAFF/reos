<?php
/**
 * ReOS is a vertical software for real estates.
 * Copyright 2010 IT ELAZOS S.L.
 *
 * This file is part of ReOS v2.x.x.
 *
 * ReOS is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * ReOS is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with ReOS.  If not, see <http://www.gnu.org/licenses/>.
 **/

/**
 *Used to select the active theme.
 *Shows all themes within theme folder
 *Returns html into var html_out
 *@package blocks_admin
 **/

$PHP_SELF = $_SERVER['PHP_SELF'];
if (preg_match("/themes.php/i",$PHP_SELF)) {
	Header("HTTP/1.0 404 Not Found");
	die();
}

global $newtheme;
require_once (_DirINCLUDES."class_gallery.php");

if (isset($newtheme))
		$this->query("update ".$this->prefix."_prefs set vl_pref ='$newtheme' where id_pref='_THEME_DIR';");
	else {
		$this->query("select vl_pref from  ".$this->prefix."_prefs where id_pref='_THEME_DIR';"); 
		$this->next_record();
		$newtheme = $this->Record[0];
	}

$num_images_line = 4;
$realpath = realpath(_TPLDIR);
if ($dir_wk = @opendir(_TPLDIR)) {

	$this->html_out .= "<table class=\"thumb_gallery\">\n";
	$this->html_out .= "<tr><td colspan=\"$num_images_line\" class=\"title_gallery\">"._THEMES."</td></tr>\n";
	$i=0;
	while (false !==($file = readdir($dir_wk))) {

		if (is_dir($realpath."/".$file) && !preg_match("/[.+]/",$file)){				
			$dir_thumbnails = _TPLDIR."/".$file;
			if (($i % ($num_images_line))==0) $this->html_out .= "<tr>\n";
			$this->html_out .= "<td class=\"thumb_gallery\">\n";
			$this->html_out .= "<div class=\"titcentral\">$file";
			if ($file==$newtheme) $this->html_out .= "<b> - "._ACTIVE."</b>"; 
			$this->html_out .= "</div>";
			$this->html_out .= "<a href=\"".LK_PAG."".$this->url_encrypt("pg=themes&newtheme=$file")."\">";
			if (file_exists($dir_thumbnails."/theme.jpg"))
			$this->html_out .= "<img class=\"thumb_gallery\" src=\"$dir_thumbnails/theme.jpg\" alt=\"$file\" />\n";
			else
			$this->html_out .= "<img class=\"thumb_gallery\" src=\"images/no_image.gif\" alt=\"Theme.jpg not found\" />\n";
			$this->html_out .= "</a>\n";
			$this->html_out .= "</td>\n";
			$i=++$i;
			if (($i % ($num_images_line))==0) $this->html_out .= "</tr>\n";
		}
	}
}
if (($i % ($num_images_line))!=0) $this->html_out .= "</tr>\n";
closedir($dir_wk);
$this->html_out .= "</table>\n";
?>
