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
*Visualiza una galeria con las imagenes de thumbnails.
*Permite borrar y seleccionar una imagen predeterminada
*Returns html into var html_out
*@package blocks_admin
**/

$PHP_SELF = $_SERVER['PHP_SELF'];
if (preg_match("/thumb.php/i",$PHP_SELF)) {
    Header("HTTP/1.0 404 Not Found");
    die();
}

require_once (_DirINCLUDES."class_gallery.php");
require_once(_DirINCLUDES."class_prefs.php");

$prefs = new prefs;

$prefs->getPrefId("_IMAGE_SIZE");
$prefs->getPrefId("_THUMB_SIZE");	

global $id_gal;
global $file_del;
global $file_pred;
global $id_immo;

$dbi= new gallery(_IMAGE_SIZE,_THUMB_SIZE);
$num_images_line=4;


if (isset($id_gal) && isset($file_pred) && $file_pred!="")
   {
   $tmp["img_front"]=$file_pred;
   $dbi->update_gallery($id_gal,$tmp);
   $this->add_msg($dbi->txt_error);
   unset($tmp);
   }
   
if (isset($id_gal)) {
   if (!$dbi->gallery_dtl($id_gal))
      $this->add_msg($dbi->txt_error);
      else {list($id_gal,$dir_gal, $name, $desc, $img_front)=($dbi->Record);}
   }

if (isset($file_del) && $file_del!="" && isset($dir_gal) && $dir_gal!="" )
   {
   $dbi->borrar_img($dir_gal, $file_del);
   $this->add_msg($dbi->txt_error);
   if (isset($img_front) && $img_front==$file_del){
      $tmp["img_front"]="";
      $dbi->update_gallery($id_gal,$tmp);
      $this->add_msg($dbi->txt_error);
      $img_front=Null;
      unset($tmp);
      }
   }
   


/*************************************************************************************************************/

if (isset($dir_gal) && $dir_gal!="") { $dir_thumbnails=""._DirGALLERIES."$dir_gal/thumbnails"; $dir_fotos=""._DirGALLERIES."$dir_gal/images";}

if ($dir_wk = @opendir($dir_thumbnails)) {
/*
$link_add[0]["href"]="pg=addimg&dir_gal=$dir_gal&name=$name";
$link_add[0]["txt"]=""._ADD_PICTURE."";
if (!isset($id_immo)) {
  $link_add[1]["href"]="pg=edtgal&id_gal=$id_gal";
  $link_add[1]["txt"]=""._EDT_GAL.""; }

$this->html_out .= $this->pgtitle(""._GALLERY."",!isset($id_immo),$link_add);
 */
$this->html_out .= "<table class=\"thumb_gallery\">\n";
if (!isset($id_immo)) $this->html_out .= "<tr><td colspan=\"$num_images_line\" class=\"title_gallery\">$name</td></tr>\n";
     else $name="";
$i=0;
$this->file_script=_DirSCRIPTS."img.js";
while (false !==($file = readdir($dir_wk))) {

       if (preg_match("/\.jpg/i",$file) || preg_match("/\.gif/i",$file)) {
        if (($i % ($num_images_line))==0) $this->html_out .= "<tr>\n";
        $this->html_out .= "<td class=\"thumb_gallery\">\n";
        $this->html_out .= "<a href=\"javascript:CargarFoto('$dir_fotos/$file','"._IMAGE_SIZE."','550')\">";
        //$this->html_out .= "<a href=\"".LK_PAG."".$dbi->url_encrypt("pg=img&dir_gal=$dir_gal&file=$file&name=$name&id_gal=$id_gal")."\">";
        $this->html_out .= "<img class=\"thumb_gallery\" src=\"$dir_thumbnails/$file\" alt=\"$file\" />\n";
        $this->html_out .= "</a><br />\n";
        if (isset($id_immo)) $param_immo="&id_immo=$id_immo";else $param_immo="";
        if (isset($id_gal)) {
           if ($file!=$img_front) {
              $this->html_out .= "[ <a href=\"".LK_PAG."".$dbi->url_encrypt("pg=".$this->vars["pg"]."&id_gal=$id_gal&file_pred=$file$param_immo")."\" \n onclick=\"return confirm('"._IMG_FRONT." $file ? ')\">";
              $this->html_out .= ""._SET_IMG_FRONT."</a> ]\n";
              } else $this->html_out .= ""._IMG_FRONT."";

        $this->html_out .= "<br />[ <a href=\"".LK_PAG."".$dbi->url_encrypt("pg=".$this->vars["pg"]."&id_gal=$id_gal&file_del=$file$param_immo")."\" \n onclick=\"return confirm('"._DELETE." $file ? ')\">";
        $this->html_out .= ""._DELETE."</a> ]\n";
        }
        $this->html_out .= "</td>\n";
        $i=++$i;
        if (($i % ($num_images_line))==0) $this->html_out .= "</tr>\n";
        }
}
if (($i % ($num_images_line))!=0) $this->html_out .= "</tr>\n";
closedir($dir_wk);
$this->html_out .= "</table>\n";
if ($i>0 && isset($id_gal) && !isset($img_front)) $this->add_msg(""._NO_IMG_FRONT."");
} else {
       if (isset($id_gal)) {
             if (isset($name)) $this->add_msg(""._ERROR_OPEN_DIR." : $name");
                 else $this->add_msg(""._ERROR_OPEN_DIR."");
              }
       }


?>
