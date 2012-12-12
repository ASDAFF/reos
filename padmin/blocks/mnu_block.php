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
*Muestra el menu izquierdo de la aplicaci�n.
*Se basa en el menu horizontal superior que guarda la selecci�n en la var $screen.
*Returns html into var html_out
*@package blocks_admin
**/

$PHP_SELF = $_SERVER['PHP_SELF'];
if (preg_match("/mnu_block.php/i",$PHP_SELF)) {
    Header("HTTP/1.0 404 Not Found");
    die();
}

require_once(_DirINCLUDES."class_struct.php");

global $perm_peso;
global $version;
global $screen;

//print_r($this->auth);
if (!isset($screen)) {
    $screen=false;
    if (array_key_exists("screen",$this->auth)) $screen=$this->auth["screen"];
    }

$perm=$this->auth["perm"];

$struct=new struct;
$views=$struct->getviews($screen,$perm);

$mnu=array();

if ($views) {
   $this->auth["screen"]=$views[0]["ID_SCREEN"];
   $mnu[0]["title"]=constant($views[0]["NM_SCREEN"]);
   $i=0;
   foreach($views as $vw) {
   $mnu[0]["links"][$i]["txt"]=constant($vw["NM_VIEW"]);
   if ($vw["PARAMS"]!="") $params="&".$vw["PARAMS"];else $params="";
   $mnu[0]["links"][$i]["href"]="pg=".$vw["APP_FILE"].$params."&nm=".constant($vw["NM_VIEW"]);
   $i++;
   }
}


//reset($mnu);

foreach($mnu as $mnu_tit){
  $this->html_out .="<table class=\"mnu\">";
  $this->html_out .= "<tr><td class=\"mnu_title\">".$mnu_tit["title"]."</td></tr>";
  foreach ($mnu_tit["links"] as $mnu_links)
       {
        $this->html_out .= "<tr>"
        ."<td class=\"mnu_item\" onmouseover=\"this.className='mnu_item2'\" onmouseout=\"this.className='mnu_item'\">"
        ."<a class=\"mnu\" href=\"".LK_PAG."".$this->url_encrypt("".$mnu_links["href"]."")."\">"
        .$mnu_links["txt"]
        ."</a>"
        ."</td></tr>";

       }
  $this->html_out .="</table>";
}

unset($mnu);

?>
