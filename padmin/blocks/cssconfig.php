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
*Muestra la lista de ficheros CSS.
*Tiene la opci�n de a�adir un nuevo fichero CSS.
*Returns html into var html_out
*@package blocks_admin
**/

$PHP_SELF = $_SERVER['PHP_SELF'];
if (preg_match("/cssconfig.php/i",$PHP_SELF)) {
    Header("HTTP/1.0 404 Not Found");
    die();
}
require_once(_DirINCLUDES."forms/forms.php");

global $dir_css;
global $file_del;

$num_images_line=1;


if (isset($file_del) && $file_del!="" && isset($dir_css) && $dir_css!="" )
   {
   if (file_exists($dir_css.$file_del))
       if (unlink($dir_css.$file_del)) $this->add_msg(_CSS_DELETED);
   }

$this->html_out .= $this->pgtitle(""._CSSCONFIG."",true,null);
/*************************************************************************************************************/
$tmpdir = ""._DirHOME._DirADMIN._DirTMP."";

$form = new htmlform("form1","".LK_HOME_ADM."", "post",""._ENVIAR."");
//$form->title=$name;
$form->add_filebox( "cssfile", ""._CSSFILE.":", 1, 300000, $tmpdir );
$form->add_hidden("data");
$form->fields["data"]->value = $this->txt_encrypt("pg=".$this->vars["pg"]."&dir_css=$dir_css");

$processed = $form->process();
if( !$processed ) {


} else {
        $cssfile=$form->fields["cssfile"]->image_uploaded;
        //echo $_FILES[$form->fields["image"]->key]['name'];
        if ($cssfile!="" && (preg_match("/\.css/i",$cssfile))){
           if (!copy(_DirTMP.$cssfile,_TPLDIR._THEME_DIR."/"._DirSTYLE.$_FILES[$form->fields["cssfile"]->key]['name']))
           {$this->add_msg("Error Uploading File !");}
           }
        if ($cssfile!="" && file_exists(_DirTMP.$cssfile)) unlink(_DirTMP.$cssfile);
       }
$this->html_out.=$form->draw();
//********************************

if ($dir_wk = @opendir($dir_css)) {

$this->html_out .= "<table class=\"thumb_gallery\">\n";
$this->html_out .= "<tr><td colspan=\"$num_images_line\" class=\"title_gallery\">"._CSSCONFIG."</td></tr>\n";

$i=0;

while (false !==($file = readdir($dir_wk))) {

       if (preg_match("/\.css/i",$file)) {
        if (($i % ($num_images_line))==0) $this->html_out .= "<tr>\n";
        $this->html_out .= "<td class=\"thumb_gallery\">\n";
        $file_img=_DirIMAGES."css.jpg";
        $this->html_out .= "<a href=\""._TPLDIR._THEME_DIR."/"._DirSTYLE."$file\" ><img class=\"thumb_gallery\" src=\"$file_img\" alt=\"$file\" />\n";
        $this->html_out .= "</a><br />$file<br/>\n";

        $this->html_out .= "<br />[ <a href=\"".LK_PAG."".$this->url_encrypt("pg=".$this->vars["pg"]."&dir_css=$dir_css&file_del=$file")."\" \n onclick=\"return confirm('"._DELETE." $file ? ')\">";
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
