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
*Returns xml node for cities of a selected type of transaction.
*@package blocks_public
**/

$PHP_SELF = $_SERVER['PHP_SELF'];
if (preg_match("/tp_property.php/i",$PHP_SELF)) {
    Header("HTTP/1.0 404 Not Found");
    die();
}

require_once(_DirINCLUDES."class_mysql.php");
require_once(_DirINCLUDES."class_lovs.php");

$lovs= new lovs;
$lovs->getLovs('_LST_TP_PROPIEDAD',_IDIOMA);

//*********************
global $id_org_session;
global $tp_servicio;
global $option; // if not set goes to cities page else goes to verimmo page

$dbi= new DB_Sql;

if (isset($tp_servicio)) $tp_serv=" ".$this->prefix."_immos.tp_servicio=$tp_servicio AND";else
                         $tp_serv="";

$today=$this->date_sql_format(date(_DATE_FORMAT,mktime(0,0,0,date("m"),date("d"),  date("Y"))),""._DATE_FORMAT."");

$dbi->query("select DISTINCT ELT(tp_propiedad,"._LST_TP_PROPIEDAD.") as txt_propiedad, tp_propiedad, count(*) as num from ".$this->prefix."_immos"
  ." where $tp_serv ".$this->prefix."_immos.tp_state=1 AND ".$this->prefix."_immos.dt_valid>=$today AND ".$this->prefix."_immos.id_org=$id_org_session  GROUP BY tp_propiedad;");


if ($dbi->num_rows()>0) {
$array_pobs=$dbi->select_array();
$i=0;
foreach ($array_pobs as $pobs) {
$mnu[$i]["txt"]=$pobs["txt_propiedad"];
if (!isset($option))
    $mnu[$i]["href"]="pg=cities&tp_propiedad=".$pobs["tp_propiedad"]."";
    else
    $mnu[$i]["href"]="pg=verimmo&show=0&keywords=tp_propiedad=".$pobs["tp_propiedad"].",tp_servicio=$tp_servicio,tp_state=1,order_by=precio ASC&nm=".$pobs["txt_propiedad"];
$mnu[$i]["tp"]=$pobs["tp_propiedad"];
$mnu[$i]["num"]=$pobs["num"];
$i++;
}

reset($mnu);

if (isset($tp_servicio)) $tp_serv="tp=\"$tp_servicio\"";else $tp_serv="";

$this->html_out .="<tp_property $tp_serv>";
$this->html_out .= "<title>"._FIND_BY_TPPROP."</title>";
foreach ($mnu as $mnu_links)
       {
        $this->html_out .= "<item id=\"".$mnu_links["tp"]."\" num=\"".$mnu_links["num"]."\" href=\"".$this->getPermalink($mnu_links["txt"]).$this->url_encrypt("".$mnu_links["href"]."&nm=".$mnu_links["txt"])."\">";
        $this->html_out .= $mnu_links["txt"]."</item>";
       }
$this->html_out .="</tp_property>";

unset($mnu);
}


?>
