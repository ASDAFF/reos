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
 * Exports cities tables State, county and city
 */
global $country;
error_reporting(E_ALL);
require_once(_DirINCLUDES."class_mysql.php");

$dbi= new DB_Sql;

$path = "install/tmp/";
/*
 $files=array("comunidad.xml", "provincia.xml","poblacion.xml");
 $tables[0]=array("comunidad");
 $tables[1]=array("provincia");
 $tables[2]=array("poblacion");

 foreach($files as $key=>$fname){
 $filename = $path.$fname;
 if (strval($filename)!="") $fptr=fopen($filename,"w"); else $fptr=false;
 if ($fptr!=false) {
 $data="<?xml version=\"1.0\" encoding=\""._CHARSET."\"?>\n<root>\n";
 fwrite($fptr,$data);
 foreach($tables[$key] as $tb) {
 $data="";
 if ($tb=="comunidad")
 $sql = "select * from ".$dbi->prefix."_".$tb." where id_country = $country";
 if ($tb=="provincia")
 $sql = "select * from ".$dbi->prefix."_".$tb." where id_comunidad in (select id_comunidad from ".$dbi->prefix."_comunidad where id_country = $country)";
 if ($tb=="poblacion")
 $sql = "select * from ".$dbi->prefix."_".$tb." where id_prov in (select id_prov from ".$dbi->prefix."_provincia where id_comunidad in (select id_comunidad from ".$dbi->prefix."_comunidad where id_country = $country))";
 if ($dbi->query($sql)){
 $data.="<".$tb.">\n";
 $rows=$dbi->select_xml($dbi->table_columns($dbi->prefix."_".$tb),false);
 $data.="$rows";
 $data.="</".$tb.">\n";
 fwrite($fptr,$data);
 }
 }
 $data="</root>";
 fwrite($fptr,$data);
 fclose($fptr);
 }
 }
 */

/**
 * Cities by State
 */
/*
$sql = "select id_comunidad, comdad_name from ".$dbi->prefix."_comunidad where id_country = $country";

$tables[0]="provincia";
$tables[1]="poblacion";

if ($dbi->query($sql)){

	$states = $dbi->select_array();

	foreach($states as $sta){
		

		foreach($tables as $tb) {
			if ($tb=="provincia")
			$filename = $path.$sta["comdad_name"]."_counties.xml";
			else
		    $filename = $path.$sta["comdad_name"]."_cities.xml";			
			if (strval($filename)!="") $fptr=fopen($filename,"w"); else $fptr=false;
			if ($fptr!=false) {
				$data="<?xml version=\"1.0\" encoding=\""._CHARSET."\"?>\n<root>\n";
				fwrite($fptr,$data);
				$data="";
				if ($tb=="provincia")
				$sql = "select * from ".$dbi->prefix."_".$tb." where id_comunidad = ".$sta["id_comunidad"];
				if ($tb=="poblacion")
				$sql = "select * from ".$dbi->prefix."_".$tb." where id_prov in (select id_prov from ".$dbi->prefix."_provincia where id_comunidad = ".$sta["id_comunidad"].")";
				if ($dbi->query($sql)){
					$data.="<".$tb.">\n";
					$rows=$dbi->select_xml($dbi->table_columns($dbi->prefix."_".$tb),false);
					$data.="$rows";
					$data.="</".$tb.">\n";
					fwrite($fptr,$data);
				}
				$data="</root>";
				fwrite($fptr,$data);
				fclose($fptr);
			}
				
		}
	}
}
*/

echo "Exit Job";

?>
