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
*Lista de noticias.
*Sirve para "Mis, Mi equipo, todos".
*Returns html into var html_out
*@package blocks_admin
**/


$PHP_SELF = $_SERVER['PHP_SELF'];
if (preg_match("/vernot.php/i",$PHP_SELF)) {
    Header("HTTP/1.0 404 Not Found");
    die();
}

require_once(_DirINCLUDES."class_noticia.php");
require_once(_DirINCLUDES."forms/forms.php");
require_once(_DirINCLUDES."class_lovs.php");

$lovs= new lovs;
$lovs->getLovs('_LST_NEWS_POS',_IDIOMA);

global $list_del;
global $lst_idiomas;
global $view;
global $from;
global $keywords;

$id_org=$this->auth["id_org"];
$id_position=$this->auth["id_position"];
if (!isset($from)) $from=0;

$noticia= new noticia;

if (isset($list_del)) foreach($list_del as $dlte){
	if (isset($dlte) && $dlte!="") {$noticia->delete_noticia($dlte);$this->add_msg($noticia->txt_error);}
}

$link_add[0]["href"]="pg=noti";
$link_add[0]["txt"]=""._ADD_NEW."";

if ($view=="My") $this->html_out .= $this->pgtitle(""._MY_NEWS."",false,$link_add);
if ($view=="All") $this->html_out .= $this->pgtitle(""._ALL_NEWS."",false,$link_add);

$form = new htmlform("form1","".LK_HOME_ADM."","GET",""._FIND."");
//$form->title=""._SEARCH_TITLE."";

$form->add_static_listbox( "txt_idioma", ""._LANGUAGE.":",  $form->convert($lst_idiomas));
$form->add_static_listbox("flag_home",_NEW_POS.":",";"._ANY.",".$form->convert(""._LST_NEWS_POS.""));
$form->add_textbox("txt_title",""._WORDS_TO_FIND.":",60,60);
$form->add_hidden("data");


if (!$form->process()){
if (isset($keywords)) {parse_str(preg_replace("/,/","&",$keywords),$fields);
                        while (list($key,$value)=each($fields)) $form->fields[$key]->value = $value;
                       }
$form->fields["data"]->value = $this->txt_encrypt("pg=vernot&view=$view&from=0");
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


$this->html_out .=$form->draw();


if (isset($fields)) {
   if ($noticia->ver_noticias($fields,$id_org, $id_position, $view,$from,20)){
      $this->add_msg($noticia->txt_error);
      $this->print_list($noticia->select_array(), $from,20,"","pg=noticia&id_news=","dlte=","view=$view&keywords=$keywords",$noticia->found_rows());
      } else $this->add_msg($noticia->txt_error);
}

?>
