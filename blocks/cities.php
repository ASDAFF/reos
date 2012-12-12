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
*Returns xml node for cities.
*@package blocks_public
**/

$PHP_SELF = $_SERVER['PHP_SELF'];
if (preg_match("/cities.php/i",$PHP_SELF)) {
    Header("HTTP/1.0 404 Not Found");
    die();
}

require_once(_DirINCLUDES."class_mysql.php");
require_once(_DirINCLUDES."class_lovs.php");

$lovs= new lovs;
$lovs->getLovs('_LST_TP_PROPIEDAD',_IDIOMA);

global $tp_propiedad;
global $id_org_session;
global $nm;

$dbi= new DB_Sql;
$today=$this->date_sql_format(date(_DATE_FORMAT,mktime(0,0,0,date("m"),date("d"),  date("Y"))),""._DATE_FORMAT."");

if (isset($tp_propiedad)) {
    $dbi->query("select DISTINCT txt_poblacion, count(*) as num from ".$this->prefix."_immos"
    ." where ".$this->prefix."_immos.id_org=$id_org_session AND ".$this->prefix."_immos.tp_propiedad=$tp_propiedad AND ".$this->prefix."_immos.tp_state=1 AND ".$this->prefix."_immos.dt_valid>=$today GROUP BY txt_poblacion;");
    $tp_propiedad="tp_propiedad=$tp_propiedad,";
    } else {
    $dbi->query("select DISTINCT txt_poblacion, count(*) as num from ".$this->prefix."_immos"
    ." where ".$this->prefix."_immos.id_org=$id_org_session AND ".$this->prefix."_immos.tp_state=1 AND ".$this->prefix."_immos.dt_valid>=$today GROUP BY txt_poblacion;");
    $tp_propiedad="";
    }

$this->html_out .= $this->pgtitle($nm,true,null);

if ($dbi->num_rows()>0) {
eval("\$lst_propiedad=array("._LST_TP_PROPIEDAD.");");
$array_pobs=$dbi->select_array();
$i=0;
foreach ($array_pobs as $pobs) {
$mnu[$i]["txt"]=$pobs["txt_poblacion"];
$mnu[$i]["href"]="pg=verimmo&show=0&keywords=txt_poblacion=".$pobs["txt_poblacion"].",$tp_propiedad order_by=precio ASC&nm=".$mnu[$i]["txt"];
$mnu[$i]["num"]=$pobs["num"];
$i++;
}

reset($mnu);

$this->html_out .="<cities>";
$this->html_out .= "<title>"._POBS."</title>";
foreach ($mnu as $mnu_links)
       {
        $this->html_out .= "<item num=\"".$mnu_links["num"]."\" href=\"".$this->getPermalink($mnu_links["txt"]).$this->url_encrypt($mnu_links["href"])."\">".$mnu_links["txt"]."</item>";
        }
$this->html_out .="</cities>";

unset($mnu);
}
?>