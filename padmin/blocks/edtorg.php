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
 *Edita los datos de una organizaci�n.
 *Returns html into var html_out
 *@package blocks_admin
 **/

$PHP_SELF = $_SERVER['PHP_SELF'];
if (preg_match("/edtorg.php/i",$PHP_SELF)) {
	Header("HTTP/1.0 404 Not Found");
	die();
}

require_once(_DirINCLUDES."forms/forms.php");
require_once(_DirINCLUDES."class_org.php");

global $sub_id;
global $id_dept;
global $fid;

$id_org_session=$this->auth["id_org"];

$dbi= new org;
if (isset($id_dept)) { $dbi->organization($id_dept);
$this->add_msg($dbi->txt_error);
extract($dbi->Record);
}

if (isset($id_dept)) $tit_pag=""._EDT_DEPT.""; else $tit_pag=""._ADD_DEPT."";

$this->html_out .= $this->pgtitle($tit_pag,true,null);

$form = new htmlform("form1",LK_HOME_ADM,"GET",_SAVE);

$form->add_textbox( "name_org", _DEPT.":", 60, 60 );
if (isset($name_org)) $form->fields["name_org"]->value=$name_org;

$form->add_textbox( "txt_address1", ""._TXT_ADDRESS1.":", 70, 70 );
if (isset($txt_address1)) $form->fields["txt_address1"]->value = $txt_address1;

$result=$dbi->array_pob_zona($id_org_session,true);
$this->onload.="initDynamicOptionLists();";
$this->file_script="jscripts/dol.js";
$form->add_link_2listbox(""._TXT_POBLACION.":",""._TXT_ZONA.":","txt_poblacion","txt_zona",$result,""._SELECT."");
if (isset($txt_poblacion)) $form->fields["txt_poblacion"]->value = $txt_poblacion;
if (isset($txt_zona)) $form->fields["txt_zona"]->value = $txt_zona;


$form->add_textbox( "txt_cp", ""._TXT_CP.":", 15, 15 );
if (isset($txt_cp)) $form->fields["txt_cp"]->value = $txt_cp;

$form->add_textbox( "txt_telf1", ""._TXT_TELF1.":", 13, 13 );
if (isset($txt_telf1)) $form->fields["txt_telf1"]->value = $txt_telf1;

$form->add_textbox( "txt_telf2", ""._TXT_TELF2.":", 13, 13 );
if (isset($txt_telf2)) $form->fields["txt_telf2"]->value = $txt_telf2;

$form->add_textbox( "txt_fax", ""._TXT_FAX.":", 13, 13 );
if (isset($txt_fax)) $form->fields["txt_fax"]->value = $txt_fax;

$form->add_textbox( "txt_email1", ""._TXT_EMAIL1.":", 50, 50 );
if (isset($txt_email1)) $form->fields["txt_email1"]->value = $txt_email1;

$form->add_textbox( "txt_web", ""._TXT_WEB.":", 50, 50 );
if (isset($txt_web)) $form->fields["txt_web"]->value = $txt_web;


if (!$lst_prfs=$dbi->prfs()) $lst_prfs["NULL"]=_UNTITLED;
$form->add_static_listbox( "id_perm", ""._PERMS.":", $form->convert($lst_prfs) );
if (isset($id_perm)) $form->fields["id_perm"]->value=$id_perm;


$form->add_hidden("data");
if (isset($id_dept)) $pagina="id_dept=$id_dept&sub_id=$sub_id"; else $pagina="sub_id=$sub_id";
$form->fields["data"]->value = $this->txt_encrypt("pg=edtorg&$pagina");

$processed = $form->process();

if($processed) {

	reset($form->fields);
	while (list($key,$value)=each($form->fields)){
		if ($key!=="data") {$fields[$key]=$value->value;}
	}
	
	if (isset($id_dept)) {		
		if ($dbi->update_org($id_dept, $fields)){
			//$url="pg=verorgs&sub_id=$id_dept";
			//header ("Location: ".LK_PAG."".$dbi->url_encrypt("$url")."");
			$this->redirect();
		} else { $this->add_msg($dbi->txt_error);$this->html_out .=$form->draw();}
	} else	{
		if ($dbi->add_org($id_org_session,$sub_id, $fields)){			
			$url="pg=verorgs&sub_id=$sub_id";
			header ("Location: ".LK_PAG."".$dbi->url_encrypt("$url")."");
		} else { $this->add_msg($dbi->txt_error);$this->html_out .=$form->draw();}
	}
} else $this->html_out .=$form->draw();



?>
