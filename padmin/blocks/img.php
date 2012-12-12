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
*Muestra una imagen. Obsoleto en favor del uso de javascript.
*A partir de la variable file que contiene el nombre del fichero jpg,gif,png
*Returns html into var html_out
*@package blocks_admin
**/

$PHP_SELF = $_SERVER['PHP_SELF'];
if (preg_match("/img.php/i",$PHP_SELF)) {
    Header("HTTP/1.0 404 Not Found");
    die();
}

require_once(_DirINCLUDES."class_gallery.php");

global $name;
global $id_gal;
global $dir_gal;
global $file;


$this->html_out .= $this->pgtitle(""._GALLERY."",true,Null);

$this->html_out .= "<table class=\"img_gallery\">\n"
                  ."<tr><td class=\"img_title\">$name</td></td>"
                  ."<tr>\n<td class=\"img_gallery\">";

if (isset($dir_gal) && $dir_gal!="") { $dir_thumbnails=""._DirGALLERIES."$dir_gal/thumbnails"; $dir_fotos=""._DirGALLERIES."$dir_gal/images";}

$this->html_out .= "<img src=\"$dir_fotos/$file\" alt=\"\">\n";

$this->html_out .=  "</td>\n</tr></table>";

?>
