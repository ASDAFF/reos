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
*Lista de temporadas para alquiler de vacaciones.
*Returns html into var html_out
*@package blocks_admin
**/

$PHP_SELF = $_SERVER['PHP_SELF'];
if (preg_match("/verseasons.php/i",$PHP_SELF)) {
    Header("HTTP/1.0 404 Not Found");
    die();
}

require_once(_DirINCLUDES."forms/forms.php");
require_once(_DirINCLUDES."class_himmo.php");

global $list_del;
global $from;
global $keywords;

$id_org_session=$this->auth["id_org"];

$link_add[0]["href"]="pg=edtseason";
$link_add[0]["txt"]=""._ADD_SEASON."";

$this->html_out .= $this->pgtitle(""._VERSEASONS."",false, $link_add);

if (!isset($from)) $from=0;
$dbi= new himmo;

if (isset($list_del)) foreach($list_del as $dlte){
	if (isset($dlte) && $dlte!="") {$dbi->delete_season($dlte);$this->add_msg($dbi->txt_error);}
}



$form = new htmlform("form1","".LK_HOME_ADM."","GET",""._FIND."");

$form->add_textbox("name_seasons_search",""._NAME_SEASONS.":",60,60);

$form->add_hidden("data");

 if (!$form->process()){
 if (isset($keywords)) {parse_str(preg_replace("/,/","&",$keywords),$fields);
                        while (list($key,$value)=each($fields)) $form->fields[$key]->value = $value;
                       }
  $form->fields["data"]->value = $this->txt_encrypt("pg=verseasons&from=0");
 } else {
   reset($form->fields);
   $keywords="";
   while (list($key,$value)=each($form->fields)){
       if ($key!=="data" && $value->value!="") {
            $fields[$key]=$value->value;
            if ($keywords=="") $keywords="$key=".$value->value; else $keywords=$keywords.",$key=".$value->value;
            }
       }

  }
//$order_by=$form->fields["order_by"]->value." ".$form->fields["in_order"]->value;
$this->html_out .=$form->draw();


if (!isset($fields)) {$fields=NULL;}

   if ($dbi->seasons_list($fields,$id_org_session,$from,10,NULL)){
       $this->print_list($dbi->select_array(), $from,10,"","pg=edtseason&id_seasons=",true,"keywords=$keywords",$dbi->found_rows());
       }
        else $this->add_msg($dbi->txt_error);

?>
