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
 * Check that all languages have same number of labels.
 * If a language does not have a label, creates one with spaces  
 */

/**
 * DB Class.
 **/
require_once(_DirINCLUDES."class_mysql.php");

$db = new DB_Sql;

$db->query("Select distinct cod_lang from ".$db->prefix."_labels");
$langs = $db->select_array();
$num_langs = count($langs);

$db->query("Select distinct id_name from ".$db->prefix."_labels");
$labels = $db->select_array();

foreach ($labels as $row) {
	$db->query("Select cod_lang from ".$db->prefix."_labels where id_name='".$row["id_name"]."'");
	if ($db->num_rows()!=$num_langs){
		$tb_langs=$db->select_array();
		foreach($langs as $lgs) {
			$in = false;
			foreach($tb_langs as $tg_lgs){
				if ($lgs["cod_lang"]==$tg_lgs["cod_lang"]) $in = true;
			}
			if (!$in)
				$db->query("insert into ".$db->prefix."_labels (id_name, cod_lang, txt_label) values ('".$row["id_name"]."','".$lgs["cod_lang"]."','TO_TRANSLATE') ");
		}
	}
}

?>
