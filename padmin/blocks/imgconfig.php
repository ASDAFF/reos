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
*Lista las imagenes del dise�o de la pagina publica.
*Las imagenes deben estar en el directorio tpl/template_name/images
*Da la opci�n de a�adir y borrar.
*Returns html into var html_out
*@package blocks_admin
**/

$PHP_SELF = $_SERVER['PHP_SELF'];
if (preg_match("/imgconfig.php/i",$PHP_SELF)) {
    Header("HTTP/1.0 404 Not Found");
    die();
}
require_once(_DirINCLUDES."forms/forms.php");

global $dir_gal;
global $file_del;

$num_images_line=1;


if (isset($file_del) && $file_del!="" && isset($dir_gal) && $dir_gal!="" )
   {
   if (file_exists($dir_gal.$file_del))
       if (unlink($dir_gal.$file_del)) $this->add_msg(_DELETED);
   }

$this->html_out .= $this->pgtitle(""._IMGCONFIG."",true,null);
/*************************************************************************************************************/
$tmpdir = ""._DirHOME._DirADMIN._DirTMP."";

$form = new htmlform("form1","".LK_HOME_ADM."", "post",""._ENVIAR."");
//$form->title=$name;
$form->add_filebox( "image", ""._IMG.":", 1, 300000, $tmpdir );
$form->add_hidden("data");
$form->fields["data"]->value = $this->txt_encrypt("pg=".$this->vars["pg"]."&dir_gal=$dir_gal");

$processed = $form->process();
if( !$processed ) {


} else {
        $image=$form->fields["image"]->image_uploaded;
        //echo $_FILES[$form->fields["image"]->key]['name'];
        if ($image!="" && (preg_match("/\.jpg/i",$image) || preg_match("/\.gif/i",$image)
          || preg_match("/\.png/i",$image)|| preg_match("/\.swf/i",$image))){
           if (!copy(_DirTMP.$image,_TPLDIR._THEME_DIR."/"._DirIMAGES.$_FILES[$form->fields["image"]->key]['name']))
           {$this->add_msg("Error Uploading File !");}
           }
        if ($image!="" && file_exists(_DirTMP.$image)) unlink(_DirTMP.$image);
       }
$this->html_out.=$form->draw();
//********************************

if ($dir_wk = @opendir($dir_gal)) {

$this->html_out .= "<table class=\"thumb_gallery\">\n";
$this->html_out .= "<tr><td colspan=\"$num_images_line\" class=\"title_gallery\">"._IMGCONFIG."</td></tr>\n";

$i=0;

while (false !==($file = readdir($dir_wk))) {

       if (preg_match("/\.jpg/i",$file) || preg_match("/\.gif/i",$file)  
          || preg_match("/\.png/i",$file)|| preg_match("/\.swf/i",$file)) {
        if (($i % ($num_images_line))==0) $this->html_out .= "<tr>\n";
        $this->html_out .= "<td class=\"thumb_gallery\">\n";
        if (preg_match("/\.swf/i",$file)) $file_img=_DirIMAGES."flash.jpg";else $file_img="$dir_gal/$file";
        $this->html_out .= "<img class=\"thumb_gallery\" src=\"$file_img\" alt=\"$file\" />\n";
        $this->html_out .= "</a><br />$file<br/>\n";

        $this->html_out .= "<br />[ <a href=\"".LK_PAG."".$this->url_encrypt("pg=".$this->vars["pg"]."&dir_gal=$dir_gal&file_del=$file")."\" \n onclick=\"return confirm('"._DELETE." $file ? ')\">";
        $this->html_out .= ""._DELETE."</a> ]\n";
        $this->html_out .= "</td>\n";
        $i=++$i;
        if (($i % ($num_images_line))==0) $this->html_out .= "</tr>\n";
        }
}
if (($i % ($num_images_line))!=0) $this->html_out .= "</tr>\n";
closedir($dir_wk);
$this->html_out .= "</table>\n";
} else {
       if (isset($name)) $this->add_msg(""._ERROR_OPEN_DIR." : $name");
                         else $this->add_msg(""._ERROR_OPEN_DIR."");
       }


?>
