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
*Returns xml node for a gallery thumbnail.
*@package blocks_public
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
global $ind_link; // If true every image has a link, if false no link on images
global $ind_href; //pg variable linke pg=img or other from outside
global $max_img;
global $file_del;
global $id_immo;
global $file_pred;




if (!isset($max_img)) $max_img=100;
if (!isset($ind_href)) $ind_href=false;
if (!isset($ind_link)) $ind_link=true;

$gal= new gallery(_IMAGE_SIZE, _THUMB_SIZE);


if (isset($id_gal) && isset($file_pred) && $file_pred!="")
   {
   $tmp["img_front"]=$file_pred;
   $gal->update_gallery($id_gal,$tmp);
   $this->add_msg($gal->txt_error);
   unset($tmp);
   }


if (isset($id_gal)) {
   if (!$gal->gallery_dtl($id_gal))
      $this->add_msg($gal->txt_error);
      else {
      list($id_gal,$dir_gal, $name, $desc, $img_front)=($gal->Record);
      //$dir_gal=_DirGALLERIES.$dir_gal;
      }
   }

$thumbnails="thumbnails";
$images="images";



if (isset($file_del) && $file_del!="" && isset($dir_gal) && $dir_gal!="" )
   {
   $gal->borrar_img($dir_gal, $file_del);
   $this->add_msg($gal->txt_error);
   if (isset($img_front) && $img_front==$file_del){
      $tmp["img_front"]="";
      $gal->update_gallery($id_gal,$tmp);
      $this->add_msg($gal->txt_error);
      $img_front=Null;
      unset($tmp);
      }
   }

/*************************************************************************************************************/
$out="";
if (isset($id_gal)) $dir_gal=_DirGALLERIES.$dir_gal;
if (isset($id_immo)) $param="&id_immo=$id_immo";else $param="";

if (isset($dir_gal) && $dir_gal!="") $dir_thumbnails="$dir_gal/$thumbnails";

if ($dir_wk = @opendir($dir_thumbnails)) {

$i=0;
while (false !==($file = readdir($dir_wk)) && $max_img>$i) {
   if (preg_match("/\.jpg/i",$file) || preg_match("/\.gif/i",$file)) {
   if ($this->vars["pg"]=="edtimmogal" && isset($this->auth) && array_key_exists("uid",$this->auth)){
       $delete="del=\"".LK_PAG."".$this->url_encrypt("pg=".$this->vars["pg"]."&id_gal=$id_gal&file_del=$file$param")."\" "
              ."delclick=\"return confirm('"._DELETE." $file ? ')\" "
              ."deltxt=\""._DELETE."\"";
              
       if ($file!=$img_front) {
              $front = " front=\"".LK_PAG."".$dbi->url_encrypt("pg=".$this->vars["pg"]."&id_gal=$id_gal&file_pred=$file$param")."\" \n onclick=\"return confirm('"._IMG_FRONT." $file ? ')\"";
              $front.= " fronttxt=\""._SET_IMG_FRONT."\"";
              } else $front = " fronttxt=\""._IMG_FRONT."\"";       
   } else {
   	   $delete="";
   	   $front="";
   }
       
   if ($ind_link)  {
       if ($ind_href) { $lnk_img=$ind_href."&dir_gal=$dir_gal&fname=$file";
                        $out .= "<item dir=\"$dir_gal\" src=\"$file\" href=\"".LK_PAG."".$this->url_encrypt($lnk_img)."\" $delete $front/>";
                      } else
                        $out .= "<item dir=\"$dir_gal\" src=\"$file\" pop=\"true\" $delete $front/>";

                      
       } else $out .="<item dir=\"$dir_gal\" src=\"$file\"/>";
      $i=++$i;
      }
}
closedir($dir_wk);
if ($out!="") {
   if (isset($img_front)) $tmp="dir=\"$dir_gal\" file=\"$img_front\"";else $tmp= "";
   $this->html_out .= "<thumb $tmp>\n";
   if (isset($name)) $this->html_out .= "<title>$name</title>\n";
   $this->html_out .=$out;
   $this->html_out .= "</thumb>\n";
  }
} //else $this->add_msg(""._ERROR_OPEN_DIR."");
unset($out);

?>
