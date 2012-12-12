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
 * Edit/Add a City
 * @package blocks_admin
 **/

$PHP_SELF = $_SERVER['PHP_SELF'];

if (preg_match("/edtpob.php/i",$PHP_SELF)) {
	Header("HTTP/1.0 404 Not Found");
	die();
}

global $id_org_session;
global $id_prov;
global $id_pob;

require_once(_DirINCLUDES."forms/forms.php");
require_once(_DirINCLUDES."class_cities.php");

$dbi= new cities;

$this->html_out .= $this->pgtitle(_NAME_POB,true, null);

$form = new htmlform("form1","".LK_HOME_ADM."","GET",_ADD);

$form->add_textbox("name_pob",_NAME_POB." :",60,60);

$form->add_hidden("data");

if (!$form->process()){

	if (isset($id_pob)){
		$form->fields["name_pob"]->value = $dbi->GetCityName($id_pob);
		$vars = "&id_pob=$id_pob";
	} else $vars = "";

	$form->fields["data"]->value = $this->txt_encrypt("pg=edtpob$vars&id_prov=$id_prov");

} else {
	if (strlen($form->fields["name_pob"]->value)>2) {
		if (isset($id_pob)) {
			$dbi->Query("update ".$dbi->prefix."_poblacion set name_pob = '".$form->fields["name_pob"]->value."' where id_poblacion = $id_pob");
		} else {
			$dbi->Query("INSERT INTO ".$dbi->prefix."_poblacion (id_prov, name_pob, ind_active) VALUES ($id_prov, '".$form->fields["name_pob"]->value."',1)");
		}
		$this->redirect();
	}
}

$this->html_out.= $form->draw();


?>