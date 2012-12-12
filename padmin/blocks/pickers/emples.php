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
*Picker de empleados.
*Returns html into var html_out
*@package pickers_admin
**/

$PHP_SELF = $_SERVER['PHP_SELF'];
if (preg_match("/emples.php/i",$PHP_SELF)) {
    Header("HTTP/1.0 404 Not Found");
    die();
}

$id_org_session=$this->auth["id_org"];

require_once(_DirINCLUDES."class_user.php");
require_once(_DirINCLUDES."forms/forms.php");
require_once(_DirINCLUDES."class_pklist.php");
require_once(_DirINCLUDES."class_lovs.php");

$lovs= new lovs;
$lovs->getLovs('_LST_ORDER',_IDIOMA,true);

global $pk;  // Picker control name
global $pos; // Position of the list field to send as a text to picker
global $from;
global $keywords;

$dbi= new user;

if (!isset($from)) $from=0;

$this->html_out .= $this->pgtitle(_EMPLES,false, null);


$form = new htmlform("form1","picker.php","GET",""._FIND."");
$form->add_textbox("search_name",_NAME_USER." :",30,30);
$form->add_hidden("data");


if (!$form->process()){
  if (isset($keywords)) {parse_str(preg_replace("/,/","&",$keywords),$fields);
                        while (list($key,$value)=each($fields)) $form->fields[$key]->value = $value;
                       }
  $form->fields["data"]->value = $this->txt_encrypt("pg=emples&pk=$pk&pos=$pos&from=0");
} else {
   reset($form->fields);
   $keyword="";
   while (list($key,$value)=each($form->fields)){
       if ($key!=="data" && $value->value!="") {
            $fields[$key]=$value->value;
            if ($keywords=="") $keywords="$key=".$value->value; else $keywords=$keywords.",$key=".$value->value;
            }
       }
} // end if process

$this->html_out .=$form->draw();

if (isset($fields)) $NameToSearch =  $fields["search_name"];else $NameToSearch=null;

 if ($dbi->UserNameSearch($id_org_session,$from,20,$NameToSearch)){
     $fields=$dbi->select_array();
     $list = new pklist;
     $this->html_out .= $list->print_list($fields, $from,20,"pg=emples","keywords=$keywords",$pk, $pos, $dbi->found_rows());
     } else $this->add_msg($dbi->txt_error);



?>
