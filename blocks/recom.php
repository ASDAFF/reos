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
*Returns xml node for recommended immos.
*@package blocks_public
**/

$PHP_SELF = $_SERVER['PHP_SELF'];

if (preg_match("/recom.php/i",$PHP_SELF)) {
    Header("HTTP/1.0 404 Not Found");
    die();
}

require_once(_DirINCLUDES."class_immo.php");
require_once(_DirINCLUDES."class_lovs.php");

$lovs= new lovs;
$lovs->getLovs('_LST_TP_PROPIEDAD',_IDIOMA);

global $id_org_session;

$immo= new immo;

$fields["ind_oferta"]=2;
$fields["tp_state"]=1;
$fields["dt_isvalid"]=date(_DATE_FORMAT,mktime(0,0,0,date("m"),date("d"),  date("Y")));

if ($immo->ver_immos($fields,$id_org_session,Null, "All",0,10,"id_immo",Null)){

eval("\$tp_propiedad=array("._LST_TP_PROPIEDAD.");");

$recom=$immo->select_array();

$this->html_out .="<recom title=\""._RECOM."\">";
//$this->html_out .="<title>"._RECOM."</title>";

foreach($recom as $value) {
$this->html_out .="<item href=\"".$this->getPermalink($value["tp_propiedad"]." ".$value["tp_servicio"]." ".$value["txt_poblacion"])."".$this->url_encrypt("pg=immo&id_immo=".$value["id_immo"]."&nm="._RECOM." ".$value["ref_immo"])."\">";

if (isset($value["img_front"])) $this->html_out .="<img src=\""._DirGALLERIES.$value["dir_gal"]."/thumbnails/".$value["img_front"]."\"/>";

$this->html_out .= "<refe>".$value["ref_immo"]."</refe>";
$this->html_out .= "<transaction>".$value["tp_propiedad"]." ".$value["tp_servicio"]."</transaction>";
$this->html_out .= "<city>".$value["txt_poblacion"]."</city>";

if (isset($value["txt_zona"]) && $value["txt_zona"]!="") $this->html_out .= "<zone>".$value["txt_zona"]."</zone>";

if (isset($value["precio"])){
          $this->html_out .= "<price><label>"._PRECIO." : </label><valprc>".number_format($value["precio"],0,_DEC_POINT,_THOUSANDS_SEP)." "._CURRENCY."</valprc>";
           $this->html_out .= "<type>".$value["tp_price"]."</type></price>";
           }
           else $this->html_out .="<price><label>"._PRECIO." : "._ACONSULTAR.".</label></price>";
$this->html_out .="</item>";
}
$this->html_out .="</recom>";


}// else $this->add_msg($immo->txt_error);
?>