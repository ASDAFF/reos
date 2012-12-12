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
*Returns xml node for an image.
*Not used since a javascripts open a window.
*@package blocks_public
**/

$PHP_SELF = $_SERVER['PHP_SELF'];

if (preg_match("/img.php/i",$PHP_SELF)) {
    Header("HTTP/1.0 404 Not Found");
    die();
}

global $name;
global $dir_gal;
global $fname;

//$this->html_out .= $this->pgtitle(""._img."",true,Null);

if (isset($fname) && $fname!="") {
$this->html_out .= "<imggal>\n"
                ."<title>$name</title>";

if (isset($dir_gal) && $dir_gal!="") $dir_fotos="$dir_gal/images";

$this->html_out .= "<item src=\"$dir_fotos/$fname\"/>\n";

$this->html_out .=  "</imggal>";
}

?>
