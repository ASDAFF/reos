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
 * Applications Varriables.
 * Returns html into var html_out
 * @package blocks_admin
 **/

$PHP_SELF = $_SERVER['PHP_SELF'];
if (preg_match("/edtpref.php/i",$PHP_SELF)) {
	Header("HTTP/1.0 404 Not Found");
	die();
}

Include_once(_DirINCLUDES."forms/forms.php");

global $id_pref;
global $fid;
global $view;


$table = "prefs";

$id_org=$this->auth["id_org"];

if (isset($id_pref)) {
	if (!is_int($id_pref)) $tmp="'$id_pref'"; else $tmp=$id_pref;
	if ($this->query("select * from ".$this->prefix."_$table where id_pref=$tmp;")){
		$this->next_record();
		extract($this->Record);
	}

	else $this->add_msg(""._ERROR_DATS."");
}

if ($view!="Add") $tit_pag=""._EDT_VAL.""; else $tit_pag=""._ADD_VAL."";

$form = new htmlform("form1","".LK_HOME_ADM."","POST",""._SAVE."");

$this->html_out .= $this->pgtitle($tit_pag,true,null);

$form->add_textbox( "id_pref", _NAME.":", 20 );
$form->add_textbox( "vl_pref", _VALUE.":", 100);

$form->add_hidden("data");
if ($view!="Add")
$pagina="fid=$id_pref";
else
$pagina="view=Add";

$form->fields["data"]->value = $this->txt_encrypt("pg=edtpref&table=$table&$pagina");

$processed = $form->process();

if( !$processed ){
	$form->fields["id_pref"]->value =$id_pref;
	$form->fields["vl_pref"]->value =$vl_pref;
	$this->html_out .=$form->draw();
} else {

	reset($form->fields);
	while (list($key,$value)=each($form->fields)){
		if ($key!=="data") {$fields[$key]=$value->value;}
	}

	if ($view!="Add"){
		$str=NULL;
		while (list($key,$value)=each($fields)){
			$value="'".addslashes($value)."'";
			if ($str!=NULL)
			$str=$str.", $key=$value";
			else
			$str="$key=$value";
		}
		if (!is_int($fid)) $fid="'$fid'";
		if ($this->query("update ".$this->prefix."_$table set $str where id_pref=$fid;"))

		$this->redirect();
		else {
			$this->add_msg(""._ERROR_DATS."");
			$this->html_out .=$form->draw();
		}
	} else	{
		$st_field="";$st_value="";
		while (list($key,$value)=each($fields)){
			$value="'".addslashes($value)."'";
			if ($st_field!="") {
				$st_field=$st_field.", ";
				$st_value=$st_value.", ";
			}
			$st_field=$st_field.$key;
			$st_value=$st_value.$value;
		}
		if ($this->query("insert into ".$this->prefix."_$table ($st_field) values ($st_value);"))
		$this->redirect();
		else {
			$this->add_msg(""._ERROR_DATS."");
			$this->html_out .=$form->draw();
		}
	}
}

?>
