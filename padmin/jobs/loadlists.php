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
 * Loads labels from a language file. Takes default language or lang URL variable.
 * It gets all php constants that means you have to delete no language constants after loaded from table LABELS
 */

/**
 * DB Class.
 */
require_once(_DirINCLUDES."class_mysql.php");
require_once(_DirINCLUDES."forms/forms.php");

$form = new htmlform("form1","".LK_HOME_ADM."","GET",""._FIND."");

$db = new DB_Sql;

$conts = get_defined_constants(true);

foreach($conts["user"] as $id => $text){
	if (substr($id ,0,5)=="_LST_"){
		if (!strpos($text  , ";") && substr($text ,0,1)!=";")
			$vals=$form->convert($text);
			else
			$vals=$text;
		//echo $vals."<br/>\n";	
			
		$vals = explode(",",$vals);
		//echo $id."<br/>\n";	
		//print_r($vals);
			
		$num =1;
		foreach($vals as $tmp=>$value) { 
		   $item=explode(";",$value);
		   $db->query("insert into ".$db->prefix."_lists (id_name, cod_lang, num_order, txt_code, txt_value) values ('".$id."','"._IDIOMA."',$num,'".$item[0]."','".addslashes(iconv(_CHARSET,"UTF-8",$item[1]))."')");
		   $num++;
		}		   
	}	
}

echo "Constants loaded to lists table";



?>
