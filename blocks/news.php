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
*Returns xml node for news with type 1.
*@package blocks_public
**/

$PHP_SELF = $_SERVER['PHP_SELF'];
if (preg_match("/news.php/i",$PHP_SELF)) {
    Header("HTTP/1.0 404 Not Found");
    die();
}

require_once(_DirINCLUDES."class_noticia.php");

global $id_org_session;


$dbi= new noticia;
$dbi->noticias_home(""._IDIOMA."", $id_org_session, 1,_MAX_NEWS_BLOCK);
$result=$dbi->select_array();

if (is_array($result)){
$this->html_out .="<news title=\""._NEWS."\">\n";

foreach($result as $noti){

$this->html_out .="<new>\n<date>".$noti["fecha_ft"]."</date>\n";

if (isset($noti["txt_imatge"]) && $noti["txt_imatge"]!="" ) $this->html_out .="<imgnew src=\""._DirADMIN._DirNEWS.$noti["txt_imatge"]."\"/>";
$this->html_out .="<title>".$noti["txt_title"]."</title>\n";
$this->html_out .="<more href=\"".$this->getPermalink($noti["txt_title"]).$this->url_encrypt("pg=new&id_news=".$noti["id_news"]."")."\">"._READMORE."</more>\n";
//$this->html_out .="<more href=\"new_".$noti["id_news"]."_".str_replace(" ","_",$noti["txt_title"])."html\">"._READMORE."</more>\n";

$this->html_out .="</new>\n";
}

$this->html_out .="</news>\n";
}


?>
