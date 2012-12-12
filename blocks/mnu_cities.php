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

if (preg_match("/mnu_cities.php/i",$PHP_SELF)) {
	Header("HTTP/1.0 404 Not Found");
	die();
}

/*
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
 */
require_once(_DirINCLUDES."class_mysql.php");
//require_once(_DirINCLUDES."class_lovs.php");

//$lovs= new lovs;
//$lovs->getLovs('_LST_TP_PROPIEDAD',_IDIOMA);

//global $tp_propiedad;
global $id_org_session;
//global $nm;

$dbi= new DB_Sql;
$today=$this->date_sql_format(date(_DATE_FORMAT,mktime(0,0,0,date("m"),date("d"),  date("Y"))),""._DATE_FORMAT."");


$dbi->query("select DISTINCT txt_poblacion, count(*) as num from ".$this->prefix."_immos"
." where ".$this->prefix."_immos.id_org=$id_org_session AND ".$this->prefix."_immos.tp_state=1 AND ".$this->prefix."_immos.dt_valid>=$today GROUP BY txt_poblacion limit 0,10;");

if ($dbi->num_rows()>0) {
	
	$array_pobs=$dbi->select_array();
	$i=0;
	foreach ($array_pobs as $pobs) {
		$mnu[$i]["name"]="city";
		$mnu[$i]["txt"]=$pobs["txt_poblacion"]." (".$pobs["num"].")";
		$mnu[$i]["href"]="pg=verimmo&show=0&keywords=txt_poblacion=".$pobs["txt_poblacion"].",order_by=precio ASC&nm=".$pobs["txt_poblacion"];
		$mnu[$i]["num"]=$pobs["num"];
		$i++;
	}
	
	    $mnu[$i]["name"]="city";
		$mnu[$i]["txt"]=_READMORE;
		$mnu[$i]["href"]="pg=cities&nm="._FIND_BY_CITIES;
		$mnu[$i]["num"]="";
}
	//***********************

	reset($mnu);
	$all_mnu[0]["links"]=$mnu;
	$all_mnu[0]["title"]=_FIND_BY_CITIES;
	$all_mnu[0]["name"]="bycities";
	//print_r($all_mnu);die;

	foreach($all_mnu as $mnu_tit){
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
	unset($all_mnu);

	?>
