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
*Add image to gallery.
*Returns html into var html_out
*@package blocks_admin
**/
$PHP_SELF = $_SERVER['PHP_SELF'];

if (preg_match("/addimg.php/i",$PHP_SELF)) {
    Header("HTTP/1.0 404 Not Found");
    die();
}

require_once(_DirINCLUDES."forms/forms.php");
require_once(_DirINCLUDES."class_gallery.php");
require_once(_DirINCLUDES."class_zip.php");
require_once(_DirINCLUDES."class_prefs.php");

$prefs = new prefs;

$prefs->getPrefId("_IMAGE_SIZE");	
$prefs->getPrefId("_THUMB_SIZE");


global $id_gal;
global $id_immo;
global $id_product;

$max_kb_img=3*1024*1024;
$max_kb_zip=3*1024*1024;

//global $dir_gal;

$id_org=$this->auth["id_org"];
$tmpdir = _DirTMP;

$dbi=new gallery(_IMAGE_SIZE, _THUMB_SIZE);

if (isset($id_gal)) {
   if (!$dbi->gallery_dtl($id_gal))
      $this->add_msg($dbi->txt_error);
      else {list($id_gal,$dir_gal, , ,)=($dbi->Record);}
   }

$this->html_out .= $this->pgtitle(""._ADD_IMG_GAL."",true,null);

$form = new htmlform("form1","".LK_HOME_ADM."", "post",""._ENVIAR."");
//$form->title=$name;
$form->add_filebox( "image", ""._IMG.":", 1, $max_kb_img, $tmpdir );
$form->add_filebox( "zip_file", ""._ZIP_FILE.":", 1, $max_kb_zip, $tmpdir );
$form->add_hidden("data");


$processed = $form->process();
if( !$processed ) {
  if (isset($id_immo))$param="&id_immo=$id_immo";else $param="";
  if (isset($id_product))$param="&id_product=$id_product";else $param="";
  
  $form->fields["data"]->value = $this->txt_encrypt("pg=".$this->vars["pg"]."&id_gal=$id_gal$param");
  $this->html_out.=$form->draw();
} else {
        if ($form->fields["image"]->value!=""){
        if ($form->fields["image"]->value!="" && $form->fields["image"]->image_uploaded) {        	
            if (!$dbi->add_img($dir_gal,$form->fields["image"]->image_uploaded))
                {$this->add_msg(_IMG." ".$dbi->txt_error." ".($max_kb_img/1024)."Kb");}
                else
                {
                if (file_exists(_DirHOME._DirADMIN."tmp/".$form->fields["image"]->image_uploaded))
                     unlink(_DirHOME._DirADMIN."tmp/".$form->fields["image"]->image_uploaded);
                if (array_key_exists("redir",$this->vars) && $this->vars["redir"]) $this->redirect();}
           if (file_exists(_DirHOME._DirADMIN."tmp/".$form->fields["image"]->image_uploaded))
                unlink(_DirHOME._DirADMIN."tmp/".$form->fields["image"]->image_uploaded);
           } else $this->add_msg(_IMG." "._IMG_FILE_ERROR." ".($max_kb_img/1024)."Kb");
        }
        if ($form->fields["zip_file"]->value!=""){
        if ($form->fields["zip_file"]->image_uploaded) {
           $zip_file=$form->fields["zip_file"]->image_uploaded;
           $zip=new zip;
           if ($files=$zip->unzip(_DirHOME._DirADMIN."tmp/",$zip_file,_DirHOME._DirADMIN."tmp",array("jpg"),false))
              { foreach ($files as $file_item) $dbi->add_img($dir_gal,$file_item);
                if (file_exists(_DirHOME._DirADMIN."tmp/".$zip_file)) unlink(_DirHOME._DirADMIN."tmp/".$zip_file);
                if ($files && array_key_exists("redir",$this->vars) && $this->vars["redir"]) $this->redirect();
              } else {
              $this->add_msg(_ZIP_FILE." "._IMG_FILE_ERROR." ".($max_kb_zip/1024)."Kb");
              $this->add_msg(str_replace(array("array","true","false"),array(_ZIP_FILE,_1,_0),var_export($zip->type,true)));
              }
           if (file_exists(_DirHOME._DirADMIN."tmp/".$zip_file)) unlink(_DirHOME._DirADMIN."tmp/".$zip_file);
           } else $this->add_msg(_ZIP_FILE." "._IMG_FILE_ERROR." ".($max_kb_zip/1024)."Kb");
       }
       $this->html_out.=$form->draw();
       }

?>
