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
*Edita los datos de un producto.
*Returns html into var html_out
*@package blocks_admin
**/

$PHP_SELF = $_SERVER['PHP_SELF'];
if (preg_match("/edtprd.php/i",$PHP_SELF)) {
    Header("HTTP/1.0 404 Not Found");
    die();
}

require_once(_DirINCLUDES."forms/forms.php");
require_once(_DirINCLUDES."class_catalog.php");
require_once(_DirINCLUDES."class_lovs.php");
require_once(_DirINCLUDES."class_prefs.php");

$prefs = new prefs;

$prefs->getPrefId("_IMAGE_SIZE");	

$lovs= new lovs;
$lovs->getLovs('_LST_TP_VAT',_IDIOMA, true);

global $id;
global $id_category;
global $fid;
global $path_image;

$tmpdir = ""._DirHOME._DirTMP."";

$id_org=$this->auth["id_org"];

if (isset($id) || isset($fid)) $tit_pag=""._EDT_PROD.""; else $tit_pag=""._ADD_PROD."";

$form = new htmlform("form1",LK_HOME_ADM, "GET", _SAVE);

$dbi= new catalog;
$dbi->cod_lang=_IDIOMA;

if (isset($id))
   { $product=$dbi->product($id);
     $this->add_msg($dbi->txt_error);
     extract($product);
   }

$link=NULL;
if (isset($id)) {$link[0]["href"] = "pg=edtprdgal&id_product=$id&id_gal=$id_gal";$link[0]["txt"]=""._PICTURES."";}
$this->html_out .= $this->pgtitle($tit_pag,true,$link);

if (isset($dir_gal) && $dir_gal!="") {
$this->file_script=_DirSCRIPTS."img.js";
$this->html_out .="<table style=\"width:100%\"><tr><td>";
}

$dbi->categories("ALL", $id_org);
$this->add_msg($dbi->txt_error);
$familia="";
while ($dbi->next_record())
    {
    list($id_tmp, $name)=($dbi->Record);
    $familia=$familia.",$id_tmp;$name";
    }
// camps del form
$form->add_textbox("cod_product", ""._COD_PRODUCT."", 30, 30 );
$form->add_textbox("name_product", ""._NAME_PRODUCT."", 50, 50 );
$form->add_static_listbox( "id_category", ""._NAME_CATEGORY.":",  $familia );
$form->add_static_listbox( "tp_vat", ""._TP_VAT.":", _LST_TP_VAT  );
$form->add_textarea("desc_product", ""._DESC_PROD.":", 30, 10);
//$form->add_filebox( "path_image", ""._IMG.":", 1, 300000, $tmpdir );
$form->add_hidden("data");

$processed = $form->process();

if(!$processed){
//inicialitzacio camps del form
if (isset($name_product)) $form->fields["name_product"]->value = $name_product;
if (isset($cod_product)) $form->fields["cod_product"]->value = $cod_product;
if (isset($desc_product)) $form->fields["desc_product"]->value = $desc_product;
if (isset($id_category)) $form->fields["id_category"]->value = $id_category;
if (isset($tp_vat)) $form->fields["tp_vat"]->value = $tp_vat;

if (isset($id)) $pagina="&fid=$id"; else $pagina="";
//if (isset($path_image)) $pagina=$pagina."&path_image=$path_image";
$form->fields["data"]->value = $this->txt_encrypt("pg=edtprd$pagina");
$this->html_out .=$form->draw();
}else
{

//if ($dbi->save_imagen($path_image, $form->fields["path_image"]->image_uploaded, ""._DirTMP."", $dbi->path_images, $dbi->image_size))
  // $path_image=$form->fields["path_image"]->image_uploaded;

reset($form->fields);
while (list($key,$value)=each($form->fields)){
    if ($key!=="data") {$fields[$key]=$value->value;}
    }
    
if (isset($fid)){
   if ($dbi->edit_product($fid, $fields))
   //$form->draw();
   //header ("Location: ".LK_PAG."".$dbi->url_encrypt("pg=verprd&ctg=".$form->fields["id_category"]->value."")."");
   $this->redirect();
   else { $this->add_msg($dbi->txt_error);$this->html_out .=$form->draw();}
   } else
   {
   if ($dbi->add_product($id_org,$fields))
   //header ("Location: ".LK_PAG."".$dbi->url_encrypt("pg=verprd&ctg=".$form->fields["id_category"]->value."")."");
   $this->redirect();
   else {$this->add_msg($dbi->txt_error);$this->html_out .=$form->draw();}
   }

}

if (isset($dir_gal) && $dir_gal!="") {
$this->html_out .="</td><td style=\"text-align:center;width:30%\">";
$this->html_out .= "<a href=\"javascript:CargarFoto('"._DirGALLERIES."$dir_gal/images/$img_front','"._IMAGE_SIZE."','550')\">";
$this->html_out .="<img src=\""._DirGALLERIES."$dir_gal/thumbnails/$img_front\" /></a>";
$this->html_out .="</td></tr></table>";
}

?>
