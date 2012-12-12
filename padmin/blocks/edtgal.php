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
*Edita los datos de una galeria.
*Returns html into var html_out
*@package blocks_admin
**/

$PHP_SELF = $_SERVER['PHP_SELF'];
if (preg_match("/edtgal.php/i",$PHP_SELF)) {
    Header("HTTP/1.0 404 Not Found");
    die();
}

require_once(_DirINCLUDES."forms/forms.php");
require_once(_DirINCLUDES."class_gallery.php");

global $id_gal;
global $fid;
global $lst_idiomas;
global $id_trans;
global $dir_gal;

$id_org=$this->auth["id_org"];
$id_pos=$this->auth["id_position"];

if (isset($id_gal)|| isset($fid)) $tit_pag=""._EDT_GAL.""; else $tit_pag=""._ADD_GAL."";

$dbi= new gallery;

if (isset($id_gal)) {
                      $dbi->gallery_dtl($id_gal);
                      $this->add_msg($dbi->txt_error);
                      extract($dbi->Record);
                    }
if (isset($id_trans))
                    {
                      $dbi->gallery_dtl($id_trans);
                      $this->add_msg($dbi->txt_error);
                      list(,$dir_gal,,,,)=($dbi->Record);
                      $img_front=NULL;
                      $name=_UNTITLED;
                    }



$form = new htmlform("form1","".LK_HOME_ADM."","GET",""._SAVE."");

$form->add_textbox( "name_gal", ""._NAME_GAL.":", 76, 76 );
$form->add_textbox( "txt_desc", ""._TXT_DESC.":", 76, 76 );
$form->add_static_listbox( "cod_lang", ""._LANGUAGE.":",  $form->convert($lst_idiomas) );
$form->add_textbox( "tmp_gal", ""._FOLDER.":", 76, 76 );
$form->add_hidden("tp_gal");

$form->add_hidden("data");
$pagina="";
if (isset($id_gal)) $pagina="&fid=$id_gal&dir_gal=$dir_gal";
if (isset($id_trans)) $pagina="&id_trans=$id_trans";

$form->fields["data"]->value = $this->txt_encrypt("pg=".$this->vars["pg"].$pagina);

if (isset($name_gal)) $form->fields["name_gal"]->value = $name_gal;
if (isset($txt_desc)) $form->fields["txt_desc"]->value = $txt_desc;
if (isset($dir_gal)) $form->fields["tmp_gal"]->value=$dir_gal;
if (isset($cod_lang)) $form->fields["cod_lang"]->value = $cod_lang;
    else $form->fields["cod_lang"]->value = ""._IDIOMA."";
$form->fields["tp_gal"]->value = 1;

$processed = $form->process();

if(!$processed) {



} else
{
        reset($form->fields);
        while (list($key,$value)=each($form->fields)){
              if ($key!=="data") {$fields[$key]=$value->value;}
              }

        if (isset($fid)){
                if ($dbi->update_gallery($fid,$fields))
                {
                  //$this->redirect();
                  $form->disabled=true;
                } else {$this->add_msg($dbi->txt_error);}
        } else
        {
        //********************************************
        $create_folder=false;
        if (isset($id_trans)) $create_folder=$dir_gal;
        if ($tmp=$dbi->add_gallery($id_org, $fields,$create_folder))
                {
                $this->redirect();
                //$this->add_msg($dbi->txt_error);
                $id_gal=$dbi->last_insert_id();
                $dir_gal=$tmp;
                $form->disabled=true;
                } else {$this->add_msg($dbi->txt_error);}
        //*********************************************
        }

}

$link=null;
if (isset($id_gal)){$link[0]["href"] = "pg=addimg&id_gal=$id_gal";$link[0]["txt"]=""._ADD_IMG_GAL."";}
if (isset($id_gal)){$link[1]["href"] = "pg=edtgal&id_trans=$id_gal";$link[1]["txt"]=""._ADD_GAL."";}
$this->html_out .= $this->pgtitle($tit_pag,true,$link);

if (isset($id_gal)|| isset($fid) || isset($id_trans)) {
            if ($dbi->list_gallery($id_org, null, NULL,NULL,NULL,1,$dir_gal,"id_gal, name_gal, cod_lang , txt_desc"))
                  {
                  //$this->add_msg($dbi->txt_error);
                  $this->print_list($dbi->select_array(), 0,20,"","pg=edtgal&id_gal=",null,NULL,$dbi->found_rows());
                  } else $this->add_msg($dbi->txt_error);
        }


$this->html_out .=$form->draw();

//variables para modulos posteriores
if (isset($fid)) $id_gal=$fid;

?>
