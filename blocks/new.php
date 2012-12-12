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
*Returns xml node for a new.
*@package blocks_public
**/

$PHP_SELF = $_SERVER['PHP_SELF'];

if (preg_match("/new.php/i",$PHP_SELF)) {
    Header("HTTP/1.0 404 Not Found");
    die();
}

require_once(_DirINCLUDES."class_noticia.php");

global $id_news;

$dbi= new noticia;

if (isset($id_news)) {
      $result = $dbi->dtl_noticia($id_news);
      $this->add_msg($dbi->txt_error);
      $dbi->next_record();
      list($id_news, $fecha, $titol, $resum, $texto, $home, $idioma, $imatge, $posimg, $imatge1, $imatge2, $imatge3) = ($dbi->Record);
      if (isset($imatge)&& $imatge!="") $imatge="<imgnew src=\""._DirADMIN.$dbi->path_images."$imatge\" />\n";
      if (isset($imatge1)&& $imatge1!="") $imatge1="<imgnew src=\""._DirADMIN.$dbi->path_images."$imatge1\" />\n";
      if (isset($imatge2)&& $imatge2!="") $imatge2="<imgnew src=\""._DirADMIN.$dbi->path_images."$imatge2\" />\n";
      if (isset($imatge3)&& $imatge3!="") $imatge3="<imgnew src=\""._DirADMIN.$dbi->path_images."$imatge3\" />\n";
      }

//******************** Cabecera ***********************
$link[0]["txt"]=""._SEE2PRINT."";
$link[0]["print"]=true;

$this->html_out .= $this->pgtitle(""._NEWS."",true,$link);

$this->pagetitle = $titol;

$this->html_out .= "<new>\n";
$this->html_out .= "<hnew>"._NEW."</hnew>\n";

if ($imatge || $imatge1 || $imatge2 || $imatge3)
    {
    $this->html_out .= "$imatge1\n$imatge2\n$imatge3\n";
    }

$this->html_out .=   "<date>".$fecha."</date>\n";
$this->html_out .=   "<title>$titol</title>\n";
$this->html_out .=   "<resum>$resum</resum>\n";
$this->html_out .=   "<text>$texto</text>\n";

$this->html_out .=    "</new>\n";

/********************************************************************************/

?>
