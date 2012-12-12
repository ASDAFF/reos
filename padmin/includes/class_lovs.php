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
 * Class get application list of values
 * @author IT eLazos SL  - May 2004
 **/
$PHP_SELF = $_SERVER['PHP_SELF'];
if (preg_match("/class_lovs.php/i",$PHP_SELF)) {
	Header("HTTP/1.0 404 Not Found");
	die();
}
/**
 * Class Database file
 * Utils extends DB_Sql class.
 **/
require_once(_DirINCLUDES."class_mysql.php");
/**
 * Gets all application list of values from table t_lists and create constants.
 * @author Josep Marxuach
 * @version 1.0
 * @copyright 2004 by IT eLazos SL
 * @package Site
 */
class lovs extends DB_Sql  {

	/**
	 * Gets all application list of values from table t_lists and create constants.
	 *
	 */
	function getLovs($id_name, $cod_lang, $type=false, $char_separator = ","){
		if (!defined($id_name)){
			$str_order = "ORDER BY NUM_ORDER";
			//if ($type) $str_order="ORDER BY TXT_CODE";
			if ($this->query("select txt_code, txt_value from ".$this->prefix."_lists where id_name='$id_name' and cod_lang ='$cod_lang' $str_order;")){
				if ($this->num_rows()==0)
				return false;
				else {
					$str="";
					while ($this->next_record()){
						if ($type) {
							if ($str=="") $str.=$this->Record[0].";".$this->Record[1];
							else $str.="$char_separator".$this->Record[0].";".$this->Record[1];
						} else {
							if ($str=="") $str.="'".$this->Record[1]."'";
							else $str.="$char_separator'".$this->Record[1]."'";
						}
					}
					define($id_name,$str);
					return true;
				}
			}
			else {
				$this->txt_error=""._ER_SELET_TBL." LISTS";
				return false;
			}
		} else return true;
	}
	//************************************FIN CLASSE
}
?>
