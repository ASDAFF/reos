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
*Returns xml node for galleries with search form.
*@package blocks_public
**/


$PHP_SELF = $_SERVER['PHP_SELF'];
if (preg_match("/gals.php/i",$PHP_SELF)) {
    Header("HTTP/1.0 404 Not Found");
    die();
}
require_once(_DirINCLUDES."forms/forms_xml.php");
require_once(_DirINCLUDES."class_gallery.php");

global $from;
global $keyword;
global $id_org_session;

$id_org=$id_org_session;

if (!isset($from)) $from=0;

$dbi= new gallery;


$form = new xmlform("form1","".LK_HOME_ADM."","GET",""._FIND."");
$form->images=_TPLDIR."/"._THEME_DIR."/images/";

$form->add_textbox("keyword",""._WORDS_TO_FIND.":",60,60);
if (isset($keyword)) $form->fields["keyword"]->value = $keyword;

$form->add_hidden("data");
$form->fields["data"]->value = $this->txt_encrypt("pg=gals&from=$from");

$processed = $form->process();

$this->html_out .="<gals>";
$this->html_out .= "<title>"._GALLERIES."</title>\n";
$this->html_out .=$form->draw();

if($processed) {$keyword=$form->fields["keyword"]->value;}

if ($dbi->list_gallery($id_org, _IDIOMA, $keyword,$from,5,1,null))
     {
     $this->add_msg($dbi->txt_error);
     $this->xml_list($dbi->select_array(), $from,5,"","pg=thumb&id_gal=",NULL,"keyword=$keyword",$dbi->found_rows());
     } else $this->add_msg($dbi->txt_error);

$this->html_out .="</gals>";

?>
