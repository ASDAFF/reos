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
 * Edit a application label.
 * Returns html into var html_out
 * @package blocks_admin
 **/

$PHP_SELF = $_SERVER['PHP_SELF'];
if (preg_match("/edtlabel.php/i",$PHP_SELF)) {
	Header("HTTP/1.0 404 Not Found");
	die();
}

Include_once(_DirINCLUDES."forms/forms.php");

global $id_name;
global $view;

if (isset($id_name)) {
	if ($this->query("select * from ".$this->prefix."_labels where id_name='$id_name';")){
		$LOFs = $this->select_array();
	}else $this->add_msg(_ERROR_DATS);
}

if ($view!="Add") $tit_pag=_EDT_VAL; else $tit_pag=_ADD_VAL;
$this->html_out .= $this->pgtitle($tit_pag,true,null);

$form = new htmlform("form1","".LK_HOME_ADM."","POST",""._SAVE."");



$form->add_hidden("data");
if ($view!="Add"){
	foreach ($LOFs as $value){
		$form->add_textarea( $value["cod_lang"], $value["cod_lang"].":", 70, 1 );
		$form->fields[$value["cod_lang"]]->value = $value["txt_label"];
	}
	$pagina="id_name=$id_name";
} else {
	$form->add_textbox( "name", _COD.":", 40, 40 );
	$pagina="view=Add";
}

$form->fields["data"]->value = $this->txt_encrypt("pg=edtlabel&$pagina");

$processed = $form->process();

if( $processed ) {

	reset($form->fields);
	while (list($key,$value)=each($form->fields)){
		if ($key!=="data") {$fields[$key]=$value->value;}
	}	

	if ($view!="Add"){
		$str=NULL;
		$error = false;
		while (list($key,$value)=each($fields)){
			//$value="'".addslashes($value)."'";
			$value="'".mysql_real_escape_string($value)."'";
			if (!$this->query("update ".$this->prefix."_labels set txt_label = $value where id_name='$id_name' and cod_lang='$key';"))
			$error=true;
		}
		if (!$error) $this->redirect();
		else {$this->add_msg(""._ERROR_DATS."");$this->html_out .=$form->draw();}

	} else {
		if ($fields["name"]!="") {
		$error_found = false;
		if ($this->query("select distinct cod_lang from ".$this->prefix."_labels"))
		$langs=	$this->select_array();
		foreach($langs as $lg) {
			if (!$this->query("insert into ".$this->prefix."_labels (id_name, cod_lang, txt_label) values ('".$fields["name"]."','".$lg["cod_lang"]."','TO_TRANSLATE');"))
			$error_found = true;
		}
		if (!$error_found) $this->redirect();
		else {$this->add_msg(""._ERROR_DATS."");$this->html_out .=$form->draw();}
		} else {$this->add_msg(""._MANDATORY."");$this->html_out .=$form->draw();}
	}
} else {
	$this->html_out .=$form->draw();
}



?>