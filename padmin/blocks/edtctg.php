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
*Edita una categoria del catalogo de productos.
*Returns html into var html_out
*@package blocks_admin
**/

$PHP_SELF = $_SERVER['PHP_SELF'];
if (preg_match("/edtctg.php/i",$PHP_SELF)) {
    Header("HTTP/1.0 404 Not Found");
    die();
}

require_once(_DirINCLUDES."forms/forms.php");
require_once(_DirINCLUDES."class_catalog.php");

global $id;
global $fid;
global $id_parent_category;

$id_org=$this->auth["id_org"];

if (isset($id) || isset($fid)) $titpag=_EDT_CTG;else $titpag=_ADD_CTG;
$this->html_out .= $this->pgtitle($titpag,true,NULL);

//$this->html_out .= "<p class=\"titcentral\">"._EDT_CTG."</p>";
     

$dbi= new catalog;
$dbi->cod_lang=_IDIOMA;

if (isset($id)) {
    if ($dbi->category($id)) extract($dbi->Record);
    $this->add_msg($dbi->txt_error);
    }

$form = new htmlform("form1", LK_HOME_ADM, "GET",_SAVE);

$dbi->categories("ALL", $id_org, $id); //devuelve todas menos la categoria actual
$this->add_msg($dbi->txt_error);
$familia=NULL;
while ($dbi->next_record())
    {
    list($cod, $name)=($dbi->Record);
    $familia=$familia.",$cod;$name";
    }
// camps del form
$form->add_textbox( "cod_category", ""._COD_CATEGORY.":", 20, 20 );
$form->add_textbox( "name_category", ""._NAME_CATEGORY.":", 20, 20 );
$form->add_static_listbox( "id_parent_category", ""._CTG_BELONGS.":",  "NULL;"._START.",".$familia );
$form->add_textarea("desc_category", ""._DESC_CATEGORY.":", 30, 10);
$form->add_hidden("data");


$processed = $form->process();

if (!$processed) {
// inicialitza variables del form
if (isset($name_category)) $form->fields["name_category"]->value = $name_category;
if (isset($cod_category)) $form->fields["cod_category"]->value = $cod_category;
if (isset($desc_category)) $form->fields["desc_category"]->value = $desc_category;
if ((isset($id_parent_category))) $form->fields["id_parent_category"]->value = $id_parent_category;
if (isset($id)) $pagina="fid=$id"; else $pagina="";
$form->fields["data"]->value = $this->txt_encrypt("pg=edtctg&$pagina");
$this->html_out .=$form->draw();
}
else
{
// Processar el form
reset($form->fields);
while (list($key,$value)=each($form->fields)){
    if ($key!=="data") {$fields[$key]=$value->value;}
    }
if (isset($fid)){
   if ($dbi->update_category($fid,$fields))
      {
      //header ("Location: ".LK_PAG."".$this->url_encrypt("pg=verprd&ctg=$fid")."");
      $this->redirect();
      } else { $this->add_msg($dbi->txt_error); $this->html_out .=$form->draw();}
}else{
   if ($dbi->add_category($id_org, $fields))
      {
       //header ("Location: ".LK_PAG."".$this->url_encrypt("pg=verprd&ctg=".$form->fields["id_parent_category"]->value."")."");
       $this->redirect();
      } else {$this->add_msg($dbi->txt_error);$this->html_out .=$form->draw();}
 }
}


?>
