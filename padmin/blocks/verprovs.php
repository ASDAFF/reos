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
 * List of Provinces.
 * Returns html into var html_out
 * @package blocks_admin
 */

$PHP_SELF = $_SERVER['PHP_SELF'];
if (preg_match("/verprovs.php/i",$PHP_SELF)) {
	Header("HTTP/1.0 404 Not Found");
	die();
}

global $id_comunidad;
global $active;
global $id_org_session;

require_once(_DirINCLUDES."class_cities.php");

$dbi= new cities;

if (isset($active)) { 
	if ($active=="1")
		$dbi->ActiveAllComdadCities($id_org_session, $id_comunidad, "1");
	else 	
		$dbi->ActiveAllComdadCities($id_org_session, $id_comunidad, "0");
}

$ComdadName = $dbi->GetComdadName($id_comunidad);

$this->vars_ste[count($this->vars_ste)-1]["nm"]=$ComdadName;
$this->set_cookie_state(""._CkSTATE."", $this->vars_ste);

$link_add[0]["href"]="pg=edtprov&id_comunidad=$id_comunidad";
$link_add[0]["txt"]=_ADD_PROV;

$link_add[1]["href"]="pg=verprovs&id_comunidad=$id_comunidad&active=1";
$link_add[1]["txt"]=_ACTIVE_ALL;

$link_add[2]["href"]="pg=verprovs&id_comunidad=$id_comunidad&active=0";
$link_add[2]["txt"]=_DEACTIVE_ALL;


$this->html_out .= $this->pgtitle($ComdadName,true, $link_add);

if ($dbi->ProvList($id_comunidad)){
	$fields=$dbi->select_array();
	
	foreach($fields as $key=>$value) {
		$fields[$key]["edit"]=$this->html_link("pg=edtprov&id_prov=".$fields[$key]["id_prov"],_EDIT);		
	}
	
	$this->html_out .= "<table><tr><td style=\"vertical-align: top;width:400px\">";
	 
	$this->print_list($fields, 0,1000,"","pg=vercities&id_prov=",null,"",$dbi->found_rows());
	
	$this->html_out .= "</td><td style=\"vertical-align: top;padding: 1cm 2cm 2cm 2cm\"><img src=\""._DirIMAGES."/level3.png\" alt=\"\" /> </td></tr></table>";

} else $this->add_msg($dbi->txt_error);

?>
