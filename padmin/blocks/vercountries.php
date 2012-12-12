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
 * List of countries.
 * Returns html into var html_out
 * @package blocks_admin
 */

$PHP_SELF = $_SERVER['PHP_SELF'];
if (preg_match("/vercountries.php/i",$PHP_SELF)) {
	Header("HTTP/1.0 404 Not Found");
	die();
}


require_once(_DirINCLUDES."class_cities.php");

$link_add[0]["href"]="pg=edtcountry";
$link_add[0]["txt"]=_ADD_COUNTRY;

$this->html_out .= $this->pgtitle(_VERCOUNTRIES,false, $link_add);

$dbi= new cities;

if ($dbi->CountryList()){
	$fields=$dbi->select_array();
	
	foreach($fields as $key=>$value) {
		$fields[$key]["edit"]=$this->html_link("pg=edtcountry&id_country=".$fields[$key]["id_country"],_EDIT);		
	}
	
	$this->html_out .= "<table><tr><td style=\"vertical-align: top;width:400px\">";

	$this->print_list($fields, 0,1000,"","pg=vercomdad&id_country=",null,"",$dbi->found_rows());

	$this->html_out .= "</td><td style=\"vertical-align: top;padding: 1cm 2cm 2cm 2cm\"><img src=\""._DirIMAGES."/level1.png\" alt=\"\" /> </td></tr></table>";
	
	
} else $this->add_msg($dbi->txt_error);

?>
