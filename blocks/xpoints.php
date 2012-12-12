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
 * Google Map points.
 * Used by gmaps block
 * Returns xml into var html_out
 * @package blocks
 */


$PHP_SELF = $_SERVER['PHP_SELF'];
if (preg_match("/xpoints.php/i",$PHP_SELF)) {
    Header("HTTP/1.0 404 Not Found");
    die();
}
global $minx;
global $miny;
global $maxx;
global $maxy;

require_once(_DirINCLUDES."class_mysql.php");
require_once(_DirINCLUDES."class_lovs.php");

$lovs= new lovs;
$lovs->getLovs('_LST_TP_SERVICIO',_IDIOMA);
$lovs->getLovs('_LST_TP_PROPIEDAD',_IDIOMA);
$lovs->getLovs('_LST_TP_PRICE',_IDIOMA);

$dbi= new DB_Sql;

 //  if( point.x < bounds.minX || point.y < bounds.minY ||
 //        point.x > bounds.maxX || point.y > bounds.maxY ) {
     

//$dbi->query("SELECT * FROM ".$dbi->prefix."_point");
$dbi->query("SELECT pt.id_immo, ref_immo, txt_x, txt_y, txt_poblacion, CONCAT(FORMAT(precio,0),' "._CURRENCY."') as precio, ELT(tp_price,"._LST_TP_PRICE.") as tp_price, ELT(tp_propiedad,"._LST_TP_PROPIEDAD.") as tp_propiedad, ELT(tp_servicio,"._LST_TP_SERVICIO.") as tp_servicio FROM ".$dbi->prefix."_point pt, ".$dbi->prefix."_immos im where im.id_immo = pt.id_immo and txt_x > $minx and txt_y > $miny and txt_x < $maxx and txt_y < $maxy");

$rows = $dbi->select_array();
$xml = "";
foreach ($rows as $key=>$Record) {
	$Record["url"]=$this->url_encrypt("pg=immo&id_immo=".$Record["id_immo"]);
     $xml=$xml."<row>";
     foreach($Record as $name => $elem)
     if ( (isset($elem) && $elem!="")) {
         if (defined(strtoupper("_".$name))) $label=" label=\"".constant(strtoupper("_".$name))."\""; else $label="";
         $xml=$xml."<$name$label>".htmlspecialchars($elem)."</$name>";
         }
     $xml=$xml."</row>\n";
}

$this->html_out .=$xml;


?>
