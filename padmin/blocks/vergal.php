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
*Lista de galerias con formulario de busqueda.
*Returns html into var html_out
*@package blocks_admin
**/

$PHP_SELF = $_SERVER['PHP_SELF'];
if (preg_match("/veraccs.php/i",$PHP_SELF)) {
    Header("HTTP/1.0 404 Not Found");
    die();
}
require_once(_DirINCLUDES."forms/forms.php");
require_once(_DirINCLUDES."class_gallery.php");
require_once(_DirINCLUDES."class_prefs.php");

$prefs = new prefs;

$prefs->getPrefId("_IMAGE_SIZE");	
$prefs->getPrefId("_THUMB_SIZE");	

global $lst_idiomas;
global $lg;
global $dlte;
global $from;
global $keyword;

$id_org=$this->auth["id_org"];
if (!isset($from)) $from=0;

$dbi= new gallery(_IMAGE_SIZE,_THUMB_SIZE);

if (isset($dlte)) {$dbi->delete_gallery($dlte);$this->add_msg($dbi->txt_error);}

$link_add[0]["href"]="pg=edtgal";
$link_add[0]["txt"]=""._ADD_GAL."";

$this->html_out .= $this->pgtitle(""._GALLERIES."",false,$link_add);

$form = new htmlform("form1","".LK_HOME_ADM."","GET",""._FIND."");

$form->add_static_listbox( "idioma", ""._LANGUAGE.":",  $form->convert($lst_idiomas));
if (isset($lg)) $form->fields["idioma"]->value=$lg;

$form->add_textbox("keyword",""._WORDS_TO_FIND.":",60,60);
if (isset($keyword)) $form->fields["keyword"]->value = $keyword;

$form->add_hidden("data");
$form->fields["data"]->value = $this->txt_encrypt("pg=vergal&from=$from");

$processed = $form->process();
$this->html_out .=$form->draw();

if($processed) { $lg=$form->fields["idioma"]->value;$keyword=$form->fields["keyword"]->value;}


if (isset($lg)){
   if ($dbi->list_gallery($id_org, $lg, $keyword,$from,20,1,null,"id_gal, name_gal, txt_desc"))
     {
     $lg=$form->fields["idioma"]->value;
     $this->add_msg($dbi->txt_error);
     $this->print_list($dbi->select_array(), $from,20,"","pg=edtgal&id_gal=","dlte=","lg=$lg&keyword=$keyword",$dbi->found_rows());
     } else $this->add_msg($dbi->txt_error);
   }

?>
