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
 * DB Class.
 **/
require_once(_DirINCLUDES."class_mysql.php");

$db = new DB_Sql;

$db->query("Select distinct id_name from ".$db->prefix."_labels");
$labels = $db->select_array();

$dirhome = "c:\users\pep\documents\workspace\phpxapp\public";
$dir_list = array();

$dir_list[] = $dirhome;
$dir_list[] = $dirhome."\blocks";
$dir_list[] = $dirhome."\includes";

$dir_list[] = $dirhome."\padmin";
//$dir_list[] = $dirhome."\padmin\idioma";
$dir_list[] = $dirhome."\padmin\blocks";
$dir_list[] = $dirhome."\padmin\blocks/pickers";
$dir_list[] = $dirhome."\padmin\includes";
$dir_list[] = $dirhome."\padmin\includes\forms";
$dir_list[] = $dirhome."\padmin\includes\email";
$dir_list[] = $dirhome."\padmin\jobs";
$dir_list[] = $dirhome."\install_v1_2";
//$dir_list[] = $dirhome."/tpl/reos";
//$dir_list[] = $dirhome."/tpl/reos\langs";

//*********************************************************************
// getting names of php blocks 
$blocks = "";
if ($dir_wk = opendir($dirhome."\padmin\blocks")) {
	while (false !==($file = readdir($dir_wk))) {
		if (preg_match("/\.php/",$file)){
			$blocks.=" _".strtoupper(substr($file,0,strpos($file,".php")));
		}
	}
	closedir($dir_wk);
}
if ($dir_wk = opendir($dirhome."\blocks")) {
	while (false !==($file = readdir($dir_wk))) {
		if (preg_match("/\.php/",$file)){
			$blocks.=" _".strtoupper(substr($file,0,strpos($file,".php")));
		}
	}
	closedir($dir_wk);
}

//echo $blocks."<br/>";
//**********************************************************************
// getting column names from database SQL creation script
$crebas =  file_get_contents($dirhome."\install_v1_2\crebas.sql");

//***********************************************************************

$tot = 0;
foreach ($labels as $row) {
	$found=false;
	$cter=0;
	// if is a name block or a sql column name then is ok
	if (strpos($blocks, $row["id_name"]) || stripos($crebas, substr($row["id_name"],1,strlen($row["id_name"])))) {
		$cter++;
		$found=true ;
	} else
	foreach($dir_list as $dir){
		if ($dir_wk = opendir($dir)) {
			//echo "Obrir Directori : $dir<br/>";
			while (false !==($file = readdir($dir_wk))) {
				if (preg_match("/\.php/",$file)){
					$content = file_get_contents($dir."/".$file);
					if (strpos($content, $row["id_name"])) {
						$cter++;
						$found=true ;
						break 2;
					}
				}
			}
			closedir($dir_wk);
		}
	}
	if ($cter==0) {
		$db->query("Select txt_label from ".$db->prefix."_labels where id_name = '".$row["id_name"]."' and cod_lang ='en'");
		$db->next_record();
		echo $row["id_name"]." ".$db->Record[0]." : ".$cter."<br/>";
		$tot++;
	}
}
echo "Total Found ".$tot."<br/>";
?>
