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
*Lista de campa�as de mailing.
*Returns html into var html_out
*@package blocks_admin
**/

$PHP_SELF = $_SERVER['PHP_SELF'];
if (preg_match("/verpriceprd.php/i",$PHP_SELF)) {
    Header("HTTP/1.0 404 Not Found");
    die();
}

require_once(_DirINCLUDES."class_catalog.php");
require_once(_DirINCLUDES."forms/forms.php");
require_once(_DirINCLUDES."class_lovs.php");

$lovs= new lovs;
$lovs->getLovs('_LST_ORDER',_IDIOMA, true);

global $dlt;
global $from;
global $keywords;
global $id_price_lst;

$id_org_session=$this->auth["id_org"];

if (!isset($from)) $from=0;
$dbi= new catalog;
$dbi->cod_lang=_IDIOMA;

if (isset($id_price_lst)) {
     if (!$dbi->price_lst_dtl($id_price_lst))
     //extract($dbi->Record);
     //else
      $this->add_msg($dbi->txt_error);
     }

$link_add[0]["href"]="pg=edtpriceprd&id_price_lst=$id_price_lst";
$link_add[0]["txt"]=""._ADDPRICEPRD."";

$this->html_out .= $this->pgtitle($dbi->Record["name_price_lst"]." , ".$dbi->Record["dt_start"]." - ".$dbi->Record["dt_end"],true,$link_add);

if (isset($dlt)) { $dbi->delete_priceprd($id_price_lst, $dlt); $this->add_msg($dbi->txt_error);}

$form = new htmlform("form1","".LK_HOME_ADM."","GET",""._FIND."");
$form->num_cols=2;

$form->add_textbox("cod_product",""._COD_PRODUCT.":",30,30);
$form->add_textbox("name_prod_search",""._NAME_PRODUCT.":",60,60);
//$form->add_static_listbox("tp_state",""._TP_STATE.":",";"._ANY.",".$form->convert(""._LST_TP_STE_PRC.""));
$form->add_static_listbox("order_by",""._ORDERBY.":", "cod_product;"._COD_PRODUCT.",name_product;"._NAME_PRODUCT.",precio;"._PRECIO);
$form->add_static_listbox("in_order",""._ORDER.":",""._LST_ORDER."");

$form->add_hidden("data");
$form->fields["in_order"]->col=2;

 if (!$form->process()){
 if (isset($keywords)) {parse_str(preg_replace("/,/","&",$keywords),$fields);
                        while (list($key,$value)=each($fields)) $form->fields[$key]->value = $value;
                       }
  $form->fields["data"]->value = $this->txt_encrypt("pg=verpriceprd&id_price_lst=$id_price_lst&from=0");
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


if (isset($fields)) $order_by=$form->fields["order_by"]->value." ".$form->fields["in_order"]->value;
   else {$order_by=" cod_product ASC";$fields=NULL;}
    
$this->html_out .=$form->draw();


if (isset($fields)) {
   if ($dbi->pricelst_prods($fields,$id_price_lst,$from,10,$order_by)){
       $this->print_list($dbi->select_array(), $from,10,"id_price_lst=$id_price_lst","pg=edtpriceprd&id_price_lst=$id_price_lst&id_product=","id_price_lst=$id_price_lst&dlt=","keywords=$keywords",$dbi->found_rows());
       }
       else $this->add_msg($dbi->txt_error);
  }
?>
