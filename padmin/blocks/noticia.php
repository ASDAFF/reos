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
*Muestra una noticia en dos formatos portada e interior.
*Returns html into var html_out
*@package blocks_admin
**/


$PHP_SELF = $_SERVER['PHP_SELF'];
if (preg_match("/noticia.php/i",$PHP_SELF)) {
    Header("HTTP/1.0 404 Not Found");
    die();
}

require_once(_DirINCLUDES."class_noticia.php");

global $id_news;
global $perm_peso;

$dbi= new noticia;

if (isset($id_news)) {
      $result = $dbi->dtl_noticia($id_news);
      $this->add_msg($dbi->txt_error);
      $dbi->next_record();
      list($id_news, $fecha, $titol, $resum, $texto, $home, $idioma, $imatge, $posimg, $imatge1, $imatge2, $imatge3) = ($dbi->Record);
      if (isset($imatge)&& $imatge!="") $imatge="<img class=\"noti_port\" src=\"".$dbi->path_images."$imatge\" alt=\"\" />\n";
      if (isset($imatge1)&& $imatge1!="") $imatge1="<img class=\"noticia_img\" src=\"".$dbi->path_images."$imatge1\" alt=\"\" /><br />\n";
      if (isset($imatge2)&& $imatge2!="") $imatge2="<img class=\"noticia_img\" src=\"".$dbi->path_images."$imatge2\" alt=\"\" /><br />\n";
      if (isset($imatge3)&& $imatge3!="") $imatge3="<img class=\"noticia_img\" src=\"".$dbi->path_images."$imatge3\" alt=\"\" />\n";
      }

//******************** Cabecera ***********************

$link_add[0]["href"]="pg=noti&id_news=$id_news";
$link_add[0]["txt"]=""._EDT_NEW."";
$link_add[1]["href"]="pg=addimgnot&id_news=$id_news";
$link_add[1]["txt"]=""._PICTURES."";
if ($perm_peso>=16) {
       $link_add[2]["href"]="pg=edtmailing&view=Add&id_news=$id_news";
       $link_add[2]["txt"]=""._ADD_MAILING."";
       }

$this->html_out .= $this->pgtitle(""._VIEW_FRONT_IN."",true,$link_add);

$this->html_out .= "<table class=\"noti_port\">\n";
$this->html_out .= "<tr><td class=\"noti_header\">"._FRONT."</td></tr>\n";
$this->html_out .= "<tr><td class=\"noticia\">\n";
$this->html_out .= "$imatge<div class=\"noti_fecha\">".$fecha."</div><div class=\"titnoti\">$titol</div>\n";

$this->html_out .= "<div class=\"txt_noti\">$resum</div>\n";
$this->html_out .= "</td></tr></table>\n";

$this->html_out .= "<table class=\"noticia\">\n";
$this->html_out .= "<tr><td class=\"noti_header\">"._INSIDE."</td>\n";
$this->html_out .= "<tr><td class=\"noticia\">\n";

if ($imatge || $imatge1 || $imatge2 || $imatge3)
    {
    $this->html_out .= "<table class=\"noticia_img\"><tr><td class=\"noticia_img\">\n";
    $this->html_out .= "$imatge1$imatge2$imatge3</td></tr></table>\n";
    }

$this->html_out .=   "<div class=\"noti_fecha\">".$fecha."</div><div class=\"titnoti\">\n";
$this->html_out .=   "$titol</div>\n";
$this->html_out .=   "<div class=\"resum_noti\"><b>$resum</b></div>\n";
$this->html_out .=   "<div class=\"txt_noti\">$texto</div>\n";

$this->html_out .=    "</td></tr></table>\n";

/********************************************************************************/

?>
