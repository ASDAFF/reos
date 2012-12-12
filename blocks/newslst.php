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
*Returns xml node for news with search form.
*@package blocks_public
**/


$PHP_SELF = $_SERVER['PHP_SELF'];
if (preg_match("/newslst.php/i",$PHP_SELF)) {
    Header("HTTP/1.0 404 Not Found");
    die();
}

require_once(_DirINCLUDES."class_noticia.php");
require_once(_DirINCLUDES."forms/forms_xml.php");

global $from;
global $keywords;
global $id_org_session;

$id_org=$id_org_session;
$id_position=Null;
$view="All";

if (!isset($from)) $from=0;

$noticia= new noticia;


$form = new xmlform("form1","".LK_HOME_ADM."","GET",""._FIND."");
$form->images=_TPLDIR."/"._THEME_DIR."/images/";
$form->add_textbox("txt_title",""._WORDS_TO_FIND.":",60,60);
$form->add_hidden("data");


if (!$form->process()){
if (isset($keywords)) {parse_str(preg_replace("/,/","&",$keywords),$fields);
                        while (list($key,$value)=each($fields)) $form->fields[$key]->value = $value;
                       }
$form->fields["data"]->value = $this->txt_encrypt("pg=newslst&from=0");
}else {
   reset($form->fields);
   $keyword="";
   while (list($key,$value)=each($form->fields)){
       if ($key!=="data" && $value->value!="") {
            $fields[$key]=$value->value;
            if ($keywords=="") $keywords="$key=".$value->value; else $keywords=$keywords.",$key=".$value->value;
            }
       }
  }


$this->html_out .= "<newslst>";
$this->html_out .= "<title>"._NEWS."</title>\n";


$this->html_out .=$form->draw();
$fields["txt_idioma"]=_IDIOMA;

if (!defined("_NEWSLST_PAG")) $offset=10; else $offset=_NEWSLST_PAG;

if (isset($fields)) {
      if ($noticia->ver_noticias($fields,$id_org, $id_position, $view,$from,$offset)){
      $this->add_msg($noticia->txt_error);
      $this->xml_list( $noticia->select_array(), $from, $offset, "pg=newslst","pg=new&id_news=", null, "keywords=$keywords",$noticia->found_rows());
      //$this->print_list($noticia->select_array(), $from,20,"","pg=noticia&id_news=","dlte=","view=$view&keywords=$keywords",$noticia->found_rows());
      } else $this->add_msg($noticia->txt_error);
}
$this->html_out .="</newslst>";
?>
