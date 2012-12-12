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
*Muestra el menu horizontal superior.
*Guarda la selecci�n en la var $screen para que el menu vertical mnu_block muestre las opciones.
*Returns html into var html_out
*@package blocks_admin
**/

$PHP_SELF = $_SERVER['PHP_SELF'];
if (preg_match("/mnu_top.php/i",$PHP_SELF)) {
    Header("HTTP/1.0 404 Not Found");
    die();
}

require_once(_DirINCLUDES."class_struct.php");

$perm=$this->auth["perm"];
$struct=new struct;
$screens=$struct->getscreens($perm);

$imnu=0;
$mnu=array();

if ($screens) {
foreach($screens as $scr) {
   $imnu++;
   $mnu[$imnu]["title"]=constant($scr["nm_screen"]);
   $mnu[$imnu]["href"]="pg=".$scr["app_file"]."&screen=".$scr["id_screen"];
   }
}

reset($mnu);
$this->html_out .="<table class=\"mnu_top1\"><tr><td>";
$this->html_out .="<table class=\"mnu_top\"><tr>";
foreach($mnu as $mnu_tit){
  $this->html_out .= "<td class=\"mnu_top\">"
                  ."<a class=\"mnu_top\" href=\"".LK_PAG."".$this->url_encrypt("".$mnu_tit["href"]."")."\">"
                  .$mnu_tit["title"]."</a></td>";

}
$this->html_out .="</tr></table>";
$this->html_out .="</tr></table>";

unset($mnu);

?>
