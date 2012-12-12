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
 * Edita las plantillas de emails de soporte.
 * Returns html into var html_out
 *@package blocks_admin
 **/

$PHP_SELF = $_SERVER['PHP_SELF'];
if (preg_match("/edttkemail.php/i",$PHP_SELF)) {
	Header("HTTP/1.0 404 Not Found");
	die();
}

Include_once(_DirINCLUDES."forms/forms.php");
Include_once(_DirINCLUDES."class_support.php");

global $id_msgtext;
global $cod_lang;
global $view;

$dbi = new support;

if (isset($id_msgtext)) {
	if ($this->query("select * from ".$this->prefix."_tk_msgtext WHERE id_msgtext = '$id_msgtext' and cod_lang = '$cod_lang'")){
		$this->next_record();
		extract($this->Record);
	} else $this->add_msg($this->txt_error);
}

if ($view!="Add") $tit_pag=""._EDT_VIEW.""; else $tit_pag=""._ADD_VIEW."";
$this->html_out .= $this->pgtitle($tit_pag,true,NULL);

$form = new htmlform("form1","".LK_HOME_ADM."","GET",""._SAVE."");

$form->add_textbox( "id_msgtext", ""._COD.":",10, 10 );
if (isset($id_msgtext)) $form->fields["id_msgtext"]->value=$id_msgtext;

$form->add_textbox( "cod_lang", ""._COD_LANG.":", 2, 2 );
if (isset($cod_lang)) $form->fields["cod_lang"]->value=$cod_lang;

$form->add_textbox( "txt_subject", ""._TXT_SUBJECT.":", 80, 80);
if (isset($txt_subject)) $form->fields["txt_subject"]->value=$txt_subject;

$form->add_textarea( "txt_email", ""._TXT_EMAIL.":", 80, 15 );
if (isset($txt_email)) $form->fields["txt_email"]->value=$txt_email;

$form->add_hidden("data");
if ($view!="Add") $pagina="id_msgtext=$id_msgtext&cod_lang=$cod_lang";else $pagina="view=Add";
$form->fields["data"]->value = $this->txt_encrypt("pg=edttkemail&$pagina");

$processed = $form->process();
if( $processed ) {

	reset($form->fields);
	while (list($key,$value)=each($form->fields)){
		if ($key!=="data") {$fields[$key]=$value->value;}
	}

	if($view!="Add") {
		if ($dbi->update_emailTemplate($id_msgtext,$fields))
		$this->redirect();
		else {$this->add_msg($dbi->txt_error);$this->html_out .=$form->draw();}
	} else {
		if ($dbi->add_emailTemplate($fields))
		$this->redirect();
		else {$this->add_msg($dbi->txt_error);$this->html_out .=$form->draw();}
	}

} else $this->html_out .=$form->draw();

?>
