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
 *Lista de poblaciones con su marca de activado por cliente.
 *Sirve para activar o desactivar poblaciones y as� estar disponibles a la hora de introducir nuevos inmuebles.
 *Returns html into var html_out
 *@package blocks_public
 **/

$PHP_SELF = $_SERVER['PHP_SELF'];
if (preg_match("/verpobs.php/i",$PHP_SELF)) {
	Header("HTTP/1.0 404 Not Found");
	die();
}


require_once(_DirINCLUDES."forms/forms_xml.php");

global $from;
global $keyword;
//global $id_prov;
global $id_pob;
global $id_account;
global $nm;

$id_org=$this->auth["id_org"];
if (!isset($from)) $from=0;

if (isset($id_account)) {
	require_once(_DirINCLUDES."class_account.php");
	$org = new account;
	$id_object = $id_account;
}
else {
	require_once(_DirINCLUDES."class_org.php");
	$org= new org;
	$id_object=$id_org;
}

if (isset($id_pob) && $id_pob!="") {
	$org->active_pob($id_object, $id_pob);
	$this->add_msg($org->txt_error);}

	if (!isset($nm)) $nm=_POBLACIONES;
	$this->html_out .= $this->pgtitle($nm,isset($id_account),null);

	$form = new xmlform("form1","".LK_HOME_ADM."","GET",""._FIND."");

	//$org->provincias(1);
	//$provincias="";
	//while ($org->next_record())
	//{
	//	list ($key, $val) = ($org->Record);
	//	$provincias=$provincias.",$key;$val";
	//}

	//$form->add_static_listbox( "id_prov", _PROV_NAME.":",  $provincias);
	//if (isset($id_prov)) $form->fields["id_prov"]->value=$id_prov;

	$form->add_textbox("keyword",""._WORDS_TO_FIND.":",60,60);
	if (isset($keyword)) $form->fields["keyword"]->value = $keyword;

	$form->add_hidden("data");
	if (isset($id_account)) $pagina="&nm=$nm&id_account=$id_account";
	else $pagina="&nm=$nm";

	$form->fields["data"]->value = $this->txt_encrypt("pg=verpobs&from=0$pagina");

	$processed = $form->process();
	$this->html_out .="<verpobs>";
	if (isset($id_account)) {
		$pobs=$org->poblaciones($id_account);
		$this->html_out .="<prefered>".$pobs."</prefered>";
	}
	$this->html_out .=$form->draw();


	//$id_prov=$form->fields["id_prov"]->value;
	$keyword=$form->fields["keyword"]->value;
	if ($org->ver_pobs($id_object, null, $keyword, $from, 20)){
		$this->add_msg($org->txt_error);
		require_once(_DirINCLUDES."lists/xmlist.php");
		$list = new xmllist;

		$this->html_out .=$list->xml_list($org->select_array(), $from,20,"pg=verpobs","pg=verpobs&from=$from&keyword=$keyword$pagina&id_pob=",Null,"keyword=$keyword$pagina", $org->found_rows());
	} else $this->add_msg($org->txt_error);

	$this->html_out .="</verpobs>";

	?>
