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
 * Loads List from a language file. Takes default language or lang URL variable.
 * It gets all php constants that means you have to delete no language constants after loaded from table LABELS
 */

/**
 * DB Class.
 */
require_once(_DirINCLUDES."class_mysql.php");

$db = new DB_Sql;

$conts = get_defined_constants(true);

foreach($conts["user"] as $id => $text){	
	$db->query("insert into ".$db->prefix."_labels (id_name, cod_lang, txt_label) values ('".$id."','"._IDIOMA."','".addslashes(iconv(_CHARSET,"UTF-8",$text))."')");	
}

echo "Constants loaded to Labels table";



?>
