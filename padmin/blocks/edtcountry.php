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
 * Edit/Add a Country
 * @package blocks_admin
 **/

$PHP_SELF = $_SERVER['PHP_SELF'];

if (preg_match("/edtcountry.php/i",$PHP_SELF)) {
	Header("HTTP/1.0 404 Not Found");
	die();
}

global $id_org_session;
global $id_country;

require_once(_DirINCLUDES."forms/forms.php");
require_once(_DirINCLUDES."class_cities.php");

$dbi= new cities;

$this->html_out .= $this->pgtitle(_EDTCOUNTRY,true, null);

$form = new htmlform("form1","".LK_HOME_ADM."","GET",_ADD);

$form->add_textbox("country_name",_NAME." :",60,60);

$form->add_hidden("data");

if (!$form->process()){

	if (isset($id_country)){
		$form->fields["country_name"]->value = $dbi->GetCountryName($id_country);
		$vars = "&id_country=$id_country";
	} else $vars = "";

	$form->fields["data"]->value = $this->txt_encrypt("pg=edtcountry$vars");

} else {
	if (strlen($form->fields["country_name"]->value)>2) {
		if (isset($id_country)) {
			$dbi->Query("update ".$dbi->prefix."_country set country_name = '".$form->fields["country_name"]->value."' where id_country = $id_country");
		} else {
			$dbi->Query("INSERT INTO ".$dbi->prefix."_country (country_name) VALUES ( '".$form->fields["country_name"]->value."')");
		}
		$this->redirect();
	}
}

$this->html_out.= $form->draw();


?>