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
 * Exports tables needed for installation to install/tmp folder
 */

//global $lang;
error_reporting(E_ALL);
require_once(_DirINCLUDES."class_mysql.php");

$dbi= new DB_Sql;

$path = "install/tmp/";

$files=array("struct.xml", "data.xml", "country.xml", "tk_ctg.xml","tk_msgtext.xml");
$tables[0]=array("perms","screens","views","scr_views","perm_scrs");
$tables[1]=array("org","auth_user","position","prefs");
$tables[2]=array("country");
$tables[3]=array("tk_ctg");
$tables[4]=array("tk_msgtext");

foreach($files as $key=>$fname){
	$filename = $path.$fname;
	if (strval($filename)!="") $fptr=fopen($filename,"w"); else $fptr=false;
	if ($fptr!=false) {
		$data="<?xml version=\"1.0\" encoding=\""._CHARSET."\"?>\n<root>\n";
		fwrite($fptr,$data);
		foreach($tables[$key] as $tb) {
			$data="";
			if ($dbi->query("select * from ".$dbi->prefix."_".$tb)){

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
/**
 * Export all languages xml files
 */

if ($dbi->query("select distinct cod_lang from ".$dbi->prefix."_labels where id_name='_0'")) {
	$langs = $dbi->select_array();
	
	$tables = array("labels","lists");
	foreach($langs as $cod_lang){

		$filename = $path."lang-".$cod_lang["cod_lang"].".xml";
		if (strval($filename)!="") $fptr=fopen($filename,"w"); else $fptr=false;
		if ($fptr!=false) {
			$data="<?xml version=\"1.0\" encoding=\""._CHARSET."\"?>\n<root>\n";
			fwrite($fptr,$data);
			foreach($tables as $tb) {
				$data="";
				if ($dbi->query("select * from ".$dbi->prefix."_".$tb." where cod_lang='".$cod_lang["cod_lang"]."'")){

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
}
echo "Exit Job";
?>