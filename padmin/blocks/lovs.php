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
 * List of values
 * Returns html into var html_out
 * @package blocks_admin
 */

$PHP_SELF = $_SERVER['PHP_SELF'];
if (preg_match("/lovs.php/i",$PHP_SELF)) {
    Header("HTTP/1.0 404 Not Found");
    die();
}

$this->query("select distinct SQL_CALC_FOUND_ROWS id_name, id_name as name from ".$this->prefix."_lists");
$lists = $this->select_array();

$this->html_out .= $this->pgtitle(_LOVS,false,null);

foreach($lists as $key=>$lsts){	
	
	if (defined(substr($lsts["id_name"],4)))
		$name = constant(substr($lsts["id_name"],4));
		else 
		$name= _UNTITLED;
	$lists[$key]["txt_desc"]=$name;	
}
$this->print_list($lists, 0,1000,"","pg=versets&table=",null,"",$this->found_rows());
?>
