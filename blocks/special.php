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
*Returns xml node for immo special offer.
*@package blocks_public
**/

$PHP_SELF = $_SERVER['PHP_SELF'];
if (preg_match("/special.php/i",$PHP_SELF)) {
    Header("HTTP/1.0 404 Not Found");
    die();
}


require_once(_DirINCLUDES."class_mysql.php");
require_once(_DirINCLUDES."lists/adverts.php");
require_once(_DirINCLUDES."class_lovs.php");

$lovs= new lovs;
$lovs->getLovs('_LST_TP_SERVICIO',_IDIOMA);
$lovs->getLovs('_LST_TP_PROPIEDAD',_IDIOMA);
$lovs->getLovs('_LST_TP_PRICE',_IDIOMA);
$lovs->getLovs('_LST_INTRO',_IDIOMA);
$lovs->getLovs('_LST_PROPERTIES',_IDIOMA);
$lovs->getLovs('_LST_PROPERORD',_IDIOMA);
$lovs->getLovs('_LST_PISCINA',_IDIOMA);


global $id_gal;
global $ind_link;
global $ind_href;
global $max_img;
global $id_org_session;


$today=$this->date_sql_format(date(_DATE_FORMAT,mktime(0,0,0,date("m"),date("d"),  date("Y"))),""._DATE_FORMAT."");

$dbi= new DB_Sql;

$dbi->query("select ".$this->prefix."_immos.*, dir_gal, ELT(tp_propiedad,"._LST_TP_PROPIEDAD.") as tp_propiedad, ELT(tp_servicio,"._LST_TP_SERVICIO.") as tp_servicio, ELT(ind_piscina,"._LST_PISCINA.") as ind_piscina,ELT(tp_price,"._LST_TP_PRICE.") as tp_price from ".$this->prefix."_immos"
 ." LEFT JOIN ".$this->prefix."_gallery ON ".$this->prefix."_immos.id_gal=".$this->prefix."_gallery.id_gal where "
 .$this->prefix."_immos.id_org=$id_org_session AND "
 .$this->prefix."_immos.tp_state=1 AND "
 .$this->prefix."_immos.ind_oferta=3 AND ".$this->prefix."_immos.dt_valid>=$today;");
$dbi->next_record();

if ($dbi->num_rows()>0) {

$this->html_out.="<special><title>"._NESPECIAL."</title>";

$ind_link=true; // Vars needed for the include thumb
$ind_href="pg=immo&nm="._NESPECIAL."&id_immo=".$dbi->Record["id_immo"]; // Vars needed for the include thumb
$max_img=3;

$id_gal=$dbi->Record["id_gal"];
include(_DirBLOCKS."thumb.php");

eval("\$lst_properties=array("._LST_PROPERTIES.");");
eval("\$lst_intro=array("._LST_INTRO.");");
eval("\$lst_properord=array("._LST_PROPERORD.");");

$adv = new adverts;

$this->html_out.=$adv->create_advert($dbi->Record,"pg=immo&nm="._NESPECIAL."&id_immo=",$lst_properties,$lst_intro,$lst_properord);

$this->html_out.="</special>";
}
?>
