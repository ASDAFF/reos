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
*Returns xml node for menu of immos.
*Creates an menu option for each type of transaction.
*@package blocks_public
**/

$PHP_SELF = $_SERVER['PHP_SELF'];

if (preg_match("/mnu_immo.php/i",$PHP_SELF)) {
    Header("HTTP/1.0 404 Not Found");
    die();
}
require_once(_DirINCLUDES."class_lovs.php");

$lovs= new lovs;
$lovs->getLovs('_LST_TP_SERVICIO',_IDIOMA);

$imnu=0;
//*********************

eval("\$tipo=array("._LST_TP_SERVICIO.");");

$mnu[$imnu]["title"]=""._IMMOS."";
$mnu[$imnu]["name"]="mnu_oferta";
$n=0;
for($i=0;$i<count($tipo);$i++){
$mnu[$imnu]["links"][$n]["name"]="TP_".$i;
$mnu[$imnu]["links"][$n]["txt"]=$tipo[$i];
$tp=$i+1;
$mnu[$imnu]["links"][$n]["href"]="pg=tp_property&option=1&tp_servicio=$tp&nm=".$tipo[$i];
//$mnu[$imnu]["links"][$n]["href"]="pg=verimmo&show=0&keywords=tp_servicio=$tp#tp_state=1#order_by=dt_create DESC&nm=".$tipo[$i];

$n++;
}

$mnu[$imnu]["links"][$n]["name"]="search";
$mnu[$imnu]["links"][$n]["txt"]=""._FIND_BY_CRITERIA."";
$mnu[$imnu]["links"][$n]["href"]="pg=isearch&nm="._FIND_BY_CRITERIA."";

//*********************

//***********************

reset($mnu);

foreach($mnu as $mnu_tit){
  $this->html_out .="<".$mnu_tit["name"]." title=\"".$mnu_tit["title"]."\">";
  $this->html_out .="<moptions>";
  foreach ($mnu_tit["links"] as $mnu_links)
       {
        $this->html_out .= "<".$mnu_links["name"]." href=\"".$this->getPermalink($mnu_links["txt"])."".$this->url_encrypt("".$mnu_links["href"]."")."\">"
        .$mnu_links["txt"]
        ."</".$mnu_links["name"].">";
       }
   $this->html_out .="</moptions>";
  $this->html_out .="</".$mnu_tit["name"].">";
}

unset($mnu);

?>
