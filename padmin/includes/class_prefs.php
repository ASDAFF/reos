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
 * Class get application vars
 * @author IT eLazos SL  - May 2004
 **/
$PHP_SELF = $_SERVER['PHP_SELF'];
if (preg_match("/class_prefs.php/i",$PHP_SELF)) {
	Header("HTTP/1.0 404 Not Found");
	die();
}
/**
 * Class Database file
 * Utils extends DB_Sql class.
 **/
require_once(_DirINCLUDES."class_mysql.php");
/**
 * Gets all application vars from table t_prefs and create constants.
 * @author Josep Marxuach
 * @version 1.0
 * @copyright 2004 by IT eLazos SL
 * @package Site
 */
class prefs extends DB_Sql  {

	/**
	 * Gets all application vars from table t_prefs and create constants.
	 *
	 */
	function getPrefs(){

		if ($this->query("select id_pref, vl_pref from ".$this->prefix."_prefs where onload = 1;")){
			if ($this->num_rows()==0)
			return false;
			else {
				while ($this->next_record()){
					if (!defined($this->Record[0])) define($this->Record[0],$this->Record[1]);
				}
				return true;
			}
		}
		else {
			$this->txt_error=""._ER_SELET_TBL." PERMS";
			return false;
		}
	}
	/**
	 * Enter description here...
	 *
	 * @param unknown_type $strId_pref
	 * @return unknown
	 */
	function getPrefId($strId_pref){
		if (!defined($strId_pref)){
			if ($this->query("select id_pref, vl_pref from ".$this->prefix."_prefs where id_pref = '$strId_pref';")){
				if ($this->num_rows()==0)
				return false;
				else {
					while ($this->next_record()){
						define($this->Record[0],$this->Record[1]);
					}
					return true;
				}
			}
			else {
				$this->txt_error=""._ER_SELET_TBL." PERMS";
				return false;
			}
		}

	}
	/**
	 * Enter description here...
	 *
	 * @param unknown_type $id_pref
	 * @param unknown_type $vl_pref
	 * @return unknown
	 */
	function setPrefId($id_pref, $vl_pref){

		if ($this->query("update ".$this->prefix."_prefs set vl_pref='$vl_pref' where id_pref='$id_pref';")){
			return true;
		} else {
			$this->txt_error=""._ERROR_UPDATE.""; return false;
		}
	}


	//************************************END CLASS
}
?>
