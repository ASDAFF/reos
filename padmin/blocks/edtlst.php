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
 * Edita una lista de valores contenida en una tabla.
 * Returns html into var html_out
 * @package blocks_admin
 **/

$PHP_SELF = $_SERVER['PHP_SELF'];
if (preg_match("/edtlst.php/i",$PHP_SELF)) {
	Header("HTTP/1.0 404 Not Found");
	die();
}

Include_once(_DirINCLUDES."forms/forms.php");

global $id_name;
global $view;
global $lst_idiomas;
global $txt_code;

if ($view!="Add") {
	if ($this->query("select * from ".$this->prefix."_lists where id_name='$id_name' and txt_code='$txt_code';")){
		if ($this->num_rows()>0) {
		$LOFs = $this->select_array();
		$num_order = $LOFs[0]["num_order"];
		$txt_code = $LOFs[0]["txt_code"];
		}
	} else $this->add_msg(""._ERROR_DATS."");
}

if ($view!="Add") $tit_pag=""._EDT_VAL.""; else $tit_pag=""._ADD_VAL."";
$this->html_out .= $this->pgtitle($tit_pag,true,null);

$form = new htmlform("form1","".LK_HOME_ADM."","POST",""._SAVE."");

$form->add_textbox( "num_order", ""._NUM_ORDER.":", 3, 3 );
if (isset($num_order)) $form->fields["num_order"]->value= $num_order;

$form->add_textbox( "new_code", _COD.":", 20, 20 );
if (isset($txt_code)) $form->fields["new_code"]->value= $txt_code;

$form->add_hidden("data");
if ($view!="Add"){
	$pagina="txt_code=$txt_code";
	foreach ($LOFs as $value){
		$form->add_textarea( $value["cod_lang"], $value["cod_lang"].":", 70, 1 );
		$form->fields[$value["cod_lang"]]->value = $value["txt_value"];
	}
} else {
	$pagina="view=Add";
}

$form->fields["data"]->value = $this->txt_encrypt("pg=edtlst&id_name=$id_name&$pagina");

$processed = $form->process();

if( $processed ) {

	reset($form->fields);
	while (list($key,$value)=each($form->fields)){
		if ($key!=="data") {$fields[$key]=$value->value;}
	}

	if ($view!="Add"){
		$str=null;
		$error = false;
		while (list($key,$value)=each($fields)){
			if ($key!="num_order") {
				if (!$this->query("update ".$this->prefix."_lists set txt_value = '$value', txt_code='".$fields["new_code"]."', num_order=".$fields["num_order"]." where id_name='$id_name' and cod_lang='$key' and txt_code='$txt_code';"))
				$error=true;
			}
		}
		if (!$error) $this->redirect();
		else {$this->add_msg(""._ERROR_DATS."");$this->html_out .=$form->draw();}

	} else {

		if ($fields["num_order"]!="") {
			$error_found = false;
			if ($this->query("select distinct cod_lang from ".$this->prefix."_lists"))
			$langs=	$this->select_array();
			foreach($langs as $lg) {
				if (!$this->query("insert into ".$this->prefix."_lists (id_name, cod_lang, txt_code,txt_value, num_order) values ('$id_name','".$lg["cod_lang"]."','".$fields["new_code"]."','TO_TRANSLATE',".$fields["num_order"].");"))
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