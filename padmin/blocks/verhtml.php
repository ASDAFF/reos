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
*Lista de ficheros xml/html de contenidos.
*La extensi�n del fichero es el c�digo de idioma
*Returns html into var html_out
*@package blocks_admin
**/

$PHP_SELF = $_SERVER['PHP_SELF'];
if (preg_match("/verhtml.php/i",$PHP_SELF)) {
    Header("HTTP/1.0 404 Not Found");
    die();
}

global $nm;

$this->html_out .= $this->pgtitle($nm,false,Null);

if ($dir_wk = @opendir(_TPLDIR._THEME_DIR."/"._DirHTMLS."")) {
$i=0;
while (false !==($current_file = readdir($dir_wk))) {

if ($current_file!="." && $current_file!="..")
     {$files[$i]["file"]=$current_file;
     $files[$i]["page"]=$current_file;
     $i++;}

}

$this->print_list($files, 0,100,"","pg=edthtml&file=",null,"",100);

closedir($dir_wk);
} else $this->add_msg(""._ERROR_OPEN_DIR."");





?>
