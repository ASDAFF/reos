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
*Add image to new.
*Returns html into var html_out
*@package blocks_admin
**/
$PHP_SELF = $_SERVER['PHP_SELF'];

if (preg_match("/addimgnot.php/i",$PHP_SELF)) {
    Header("HTTP/1.0 404 Not Found");
    die();
}

include(_DirINCLUDES."class_noticia.php");
require_once(_DirINCLUDES."forms/forms.php");

global $id_news;
global $num;

$tmpdir = _DirTMP;

$dbi= new noticia;

if (isset($num)&& isset($id_news)) {$dbi->borrar_imagen($id_news,$num);$this->add_msg($dbi->txt_error);}

if (isset($id_news)) {
        $result = $dbi->query("select txt_title, txt_imatge, cod_posimg, txt_imatge1, txt_imatge2, txt_imatge3 from ".$dbi->prefix."_news where id_news=$id_news");
        $dbi->next_record();
        list($titol,  $imatge_old, $posimg, $imatge1_old, $imatge2_old, $imatge3_old) = ($dbi->Record);
        }

// ************************************************* Obtenci�n de los datos de la noticia

$form = new htmlform("form1","".LK_HOME_ADM."", "post",""._ENVIAR."");

$form->add_filebox( "portada", ""._IMG_PORT.":", 1, 300000, $tmpdir );

$form->add_filebox( "imagen1", ""._IMG_INT_1.":", 1, 300000, $tmpdir );
$form->add_filebox( "imagen2", ""._IMG_INT_2.":", 1, 300000, $tmpdir );
$form->add_filebox( "imagen3", ""._IMG_INT_3.":", 1, 300000, $tmpdir );

$form->add_hidden("data");
$form->fields["data"]->value = $this->txt_encrypt("pg=addimgnot&id_news=$id_news");

$form->add_hidden("imatge_old");
if (isset($imatge_old)) $form->fields["imatge_old"]->value = $imatge_old;

$form->add_hidden("imatge1_old");
if (isset($imatge1_old)) $form->fields["imatge1_old"]->value = $imatge1_old;

$form->add_hidden("imatge2_old");
if (isset($imatge2_old)) $form->fields["imatge2_old"]->value = $imatge2_old;

$form->add_hidden("imatge3_old");
if (isset($imatge3_old)) $form->fields["imatge3_old"]->value = $imatge3_old;

$processed = $form->process();

if($processed) {

         if ($this->save_imagen($form->fields["imatge_old"]->value, $form->fields["portada"]->image_uploaded, ""._DirTMP."", $dbi->path_images, $dbi->image_size_front))
            $portada=$form->fields["portada"]->image_uploaded; else $portada=NULL;
        if ($this->save_imagen($form->fields["imatge1_old"]->value, $form->fields["imagen1"]->image_uploaded, ""._DirTMP."", $dbi->path_images, $dbi->image_size))
            $imagen1=$form->fields["imagen1"]->image_uploaded; else $imagen1=NULL;
        if ($this->save_imagen($form->fields["imatge2_old"]->value, $form->fields["imagen2"]->image_uploaded, ""._DirTMP."", $dbi->path_images, $dbi->image_size))
            $imagen2=$form->fields["imagen2"]->image_uploaded; else $imagen2=NULL;
        if ($this->save_imagen($form->fields["imatge3_old"]->value, $form->fields["imagen3"]->image_uploaded, ""._DirTMP."", $dbi->path_images, $dbi->image_size))
            $imagen3=$form->fields["imagen3"]->image_uploaded; else $imagen3=NULL;

        if ($dbi->update_imatges($id_news, $portada, NULL, $imagen1, $imagen2, $imagen3)) // NULL is image position
            header ("location: ".LK_PAG."".$dbi->url_encrypt("pg=noticia&id_news=$id_news")."");
            else $this->add_msg($dbi->txt_error);
        }

if (!isset($imatge_old) && $form->fields["imatge_old"]->value!="" ) $imatge_old=$form->fields["imatge_old"]->value;
if (!isset($imatge1_old) && $form->fields["imatge1_old"]->value!="") $imatge1_old=$form->fields["imatge1_old"]->value;
if (!isset($imatge2_old) && $form->fields["imatge2_old"]->value!="") $imatge2_old=$form->fields["imatge2_old"]->value;
if (!isset($imatge3_old) && $form->fields["imatge3_old"]->value!="") $imatge3_old=$form->fields["imatge3_old"]->value;

        $this->html_out .= $this->pgtitle(""._IMGS_NOT."");

        $this->html_out .=$form->draw();

        $this->html_out .= "<table class=\"addimg_admin\">";
        $this->html_out .= "<tr><td class=\"noti_header\">"._IMG_PORT."</td></tr>";
        $this->html_out .= "<tr><td class=\"addimg_admin\">";

        if (isset($imatge_old))
            {
            $this->html_out .= "<img class=\"addimg_admin\" src=\"".$dbi->path_images.$imatge_old."\" alt=\"\"/><br />";
            $this->html_out .= "<a href=\"".LK_PAG."".$dbi->url_encrypt("pg=addimgnot&id_news=$id_news&num=1")."\">"._BORRAR."</a>";
            }
            else $this->html_out .= ""._NO_IMG_PORT."";
        $this->html_out .= "</td></tr></table>";

        $this->html_out .= "<table class=\"addimg_admin\">";
        $this->html_out .= "<td class=\"noti_header\">"._IMG_INT_1."</td>";
        $this->html_out .= "<td class=\"noti_header\">"._IMG_INT_2."</td>";
        $this->html_out .= "<td class=\"noti_header\">"._IMG_INT_3."</td></tr>";

        $this->html_out .= "</td><td class=\"addimg_admin\">";
        if (isset($imatge1_old))
            {
             $this->html_out .= "<img class=\"addimg_admin\" src=\"".$dbi->path_images.$imatge1_old."\" alt=\"\"/><br />";
             $this->html_out .= "<a href=\"".LK_PAG."".$dbi->url_encrypt("pg=addimgnot&id_news=$id_news&num=2")."\">"._BORRAR."</a>";
            }
            else $this->html_out .= ""._NO_IMG_INT_1."";
        $this->html_out .= "</td><td class=\"addimg_admin\">";
        if (isset($imatge2_old))
           {
           $this->html_out .= "<img class=\"addimg_admin\" src=\"".$dbi->path_images.$imatge2_old."\" alt=\"\"/><br />";
           $this->html_out .= "<a href=\"".LK_PAG."".$dbi->url_encrypt("pg=addimgnot&id_news=$id_news&num=3")."\">"._BORRAR."</a>";
           }
            else $this->html_out .= ""._NO_IMG_INT_2."";
        $this->html_out .= "</td><td class=\"addimg_admin\">";
        if (isset($imatge3_old))
           {
           $this->html_out .= "<img class=\"addimg_admin\" src=\"".$dbi->path_images.$imatge3_old."\" alt=\"\"/><br />";
           $this->html_out .= "<a href=\"".LK_PAG."".$dbi->url_encrypt("pg=addimgnot&id_news=$id_news&num=4")."\">"._BORRAR."</a>";
           }
            else $this->html_out .= ""._NO_IMG_INT_3."";
        $this->html_out .= "</td></tr></table>";


?>
