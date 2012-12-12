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
*Lista de clientes con formulario de busqueda.
*Returns html into var html_out
*@package blocks_admin
**/
$PHP_SELF = $_SERVER['PHP_SELF'];
if (preg_match("/veraccs.php/i",$PHP_SELF)) {
    Header("HTTP/1.0 404 Not Found");
    die();
}

require_once(_DirINCLUDES."class_account.php");
require_once(_DirINCLUDES."forms/forms.php");
require_once(_DirINCLUDES."class_pklist.php");
require_once(_DirINCLUDES."class_lovs.php");

$lovs= new lovs;
$lovs->getLovs('_LST_TP_ACCSTATE',_IDIOMA);
$lovs->getLovs('_LST_IND_ACTIVE',_IDIOMA);
$lovs->getLovs('_LST_ORDER',_IDIOMA,true);


global $pk;  // Picker control name
global $pos; // Position of the list field to send as a text to picker

global $keywords;
global $from;
global $view;

$id_org=$this->auth["id_org"];
$id_position=$this->auth["id_position"];

if (!isset($from)) $from=0;

$account= new account;

$form = new htmlform("form1","picker.php","GET",""._FIND."");


   $form->num_cols=2;
   $form->add_textbox("name_acc_search",""._NAME_ACCOUNT.":",60,60);
   $form->add_static_listbox("tp_state",""._TP_STATE.":",";"._ANY.",".$form->convert(""._LST_TP_ACCSTATE.""));

   $form->add_static_listbox("order_by",""._ORDERBY.":","name_account;"._NAME_ACCOUNT.",txt_poblacion;"._TXT_POBLACION.",username;"._USERNAME);
   $form->add_static_listbox( "ind_mailing", ""._IND_MAILING.":",";"._ANY.",".$form->convert(""._LST_IND_ACTIVE.""));
   $form->add_static_listbox("in_order",""._ORDER.":",""._LST_ORDER."");
   $form->fields["order_by"]->col=2;
   $form->fields["in_order"]->col=2;

 

$form->add_hidden("data");

 if (!$form->process()){
 if (isset($keywords)) {parse_str(preg_replace("/,/","&",$keywords),$fields);
                        while (list($key,$value)=each($fields)) $form->fields[$key]->value = $value;
                       }
  $form->fields["data"]->value = $this->txt_encrypt("pg=veraccs&view=$view&pk=$pk&pos=$pos&from=0");
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
$order_by=$form->fields["order_by"]->value." ".$form->fields["in_order"]->value;

$this->html_out .=$form->draw();

if (isset($fields)) {

  if ($account->ver_accs($fields,$id_org,$id_position, $view,$from,20,$order_by)){
  	 $fields=$account->select_array();
     $list = new pklist;     
     $this->html_out .= $list->print_list($fields, $from,20,"pg=veraccs&view=$view","keywords=$keywords",$pk, $pos, $account->found_rows());
     
     } else $this->add_msg($account->txt_error);
}

?>
