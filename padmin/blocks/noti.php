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
*Edita los datos de una noticia.
*Returns html into var html_out
*@package blocks_admin
**/


$PHP_SELF = $_SERVER['PHP_SELF'];
if (preg_match("/noti.php/i",$PHP_SELF)) {
    Header("HTTP/1.0 404 Not Found");
    die();
}

require_once(_DirINCLUDES."class_noticia.php");
require_once(_DirINCLUDES."forms/forms.php");
require_once(_DirINCLUDES."class_lovs.php");

$lovs= new lovs;
$lovs->getLovs('_LST_NEWS_POS',_IDIOMA);

global $id_news;
global $fid;
global $lst_idiomas;

$id_org=$this->auth["id_org"];
$id_position=$this->auth["id_position"];

$dbi= new noticia;

if (isset($id_news)) {
      $result = $dbi->dtl_noticia($id_news);
      $this->add_msg($dbi->txt_error);
      $dbi->next_record();
      list($id_news, $fecha, $titol, $resum, $texto, $home, $idioma) = ($dbi->Record);
      $resum=preg_replace('/<br \/>/', '', $resum);
      $texto=preg_replace('/<br \/>/', '', $texto);
      } else {
        $titol = NULL;
        $resum = NULL;
        $texto = NULL;
        $home=NULL;
        $idioma=NULL;
        $update= NULL;
        $id_news=NULL;
        }

//******************** Cabecera ***********************
if (isset($id_news) || isset($fid)) $tit_pag=""._EDT_NEW.""; else $tit_pag=""._ADD_NEW."";
$this->html_out .= $this->pgtitle($tit_pag,true,null);

/********************************************************************************/
$this->file_script ="jscripts/tiny_mce/tiny_mce_src.js";
$this->onload ="tinyMCE.init({" 
	."mode : 'exact', "
	."elements : 'texto', "
	."theme : 'advanced', " 
	."entity_encoding : 'raw', "
	."plugins : 'table', "
	."theme_advanced_buttons1 : 'bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,formatselect,fontselect,fontsizeselect,|,forecolor,backcolor', "
	."theme_advanced_buttons3 : 'tablecontrols', "
	."theme_advanced_toolbar_location : 'top', "
	."theme_advanced_toolbar_align : 'left'"
	."});";

$form = new htmlform("form1","".LK_HOME_ADM."","POST",""._SAVE."");

$idiomas="";
while (list ($key, $val) = each ($lst_idiomas))
  {
   $idiomas=$idiomas.",$key;$val";
  }
$form->add_static_listbox( "home", _NEW_POS, $form->convert(""._LST_NEWS_POS.""));
if (isset($home)) $form->fields["home"]->value = $home; else $form->fields["home"]->value=2;

$form->add_static_listbox( "idioma", ""._LANGUAGE.":",  $idiomas );
if (isset($idioma)) $form->fields["idioma"]->value = $idioma;

$form->add_textarea( "titol", ""._NEW_TITLE.":", 90, 2 );
if (isset($titol)) $form->fields["titol"]->value = $titol;

$form->add_textarea("resum", ""._NEW_RESUM.":", 90, 5);
if (isset($resum)) $form->fields["resum"]->value = $resum;

$form->add_texteditor("texto", ""._NEW_TXT.":", 90, 30);
if (isset($texto)) $form->fields["texto"]->value = $texto;



$form->add_hidden("data");
if (isset($id_news)) $pagina="&fid=$id_news";else  $pagina="";
$form->fields["data"]->value = $this->txt_encrypt("pg=noti$pagina");

$processed = $form->process();

if($processed)
//****************** GRABAR **************************************
{
$ind_error=false;
$resum=mysql_escape_string(nl2br($form->fields["resum"]->value));
//$texto=mysql_escape_string(nl2br($form->fields["texto"]->value));
$texto=mysql_escape_string($form->fields["texto"]->value);
$titol=mysql_escape_string($form->fields["titol"]->value);

if (isset($fid)) {
        if(!$dbi->update_noticia($fid, $titol, $resum, $texto, $form->fields["home"]->value, $form->fields["idioma"]->value)) {
           $this->add_msg($dbi->txt_error);$ind_error=true;
          }
        }
        else {
        if(!$dbi->insert_noticia($titol, $resum, $texto, $form->fields["home"]->value, $form->fields["idioma"]->value, $id_org, $id_position))
           {
            $this->add_msg($dbi->txt_error);$ind_error=true;
           } else {$fid=$dbi->last_insert_id();}
        }

if ($ind_error) {$this->html_out .=$form->draw();}
        else $this->redirect();//header ("location: ".LK_PAG."".$dbi->url_encrypt("pg=noticia&id=$fid")."");
} else $this->html_out .=$form->draw();

//********************** FIN GRABAR *******************************************************************

?>
