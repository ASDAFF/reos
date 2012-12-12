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
*Edit Product Price in a price list.
*Returns html into var html_out
*@package blocks_admin
**/

$PHP_SELF = $_SERVER['PHP_SELF'];
if (preg_match("/edtpriceprd.php/i",$PHP_SELF)) {
    Header("HTTP/1.0 404 Not Found");
    die();
}

Include_once(_DirINCLUDES."forms/forms.php");
require_once(_DirINCLUDES."class_catalog.php");

global $id_price_lst;
global $id_product;
//global $fid;
//global $view;

$id_org_session=$this->auth["id_org"];

$dbi= new catalog;

if (isset($id_price_lst)) {
     if ($dbi->price_lst_dtl($id_price_lst))
     $name_price_lst=$dbi->Record["name_price_lst"];
     else $this->add_msg($dbi->txt_error);
     }

if (isset($id_product)) {
     if ($dbi->price_prd_dtl($id_price_lst, $id_product))
     extract($dbi->Record);
     else $this->add_msg($dbi->txt_error);
    }

if (isset($id_product)) $tit_pag=""._EDTPRICEPRD.""; else $tit_pag=""._ADDPRICEPRD."";

$this->html_out .= $this->pgtitle($tit_pag." ".$name_price_lst,true,NULL);

$form = new htmlform("form1","".LK_HOME_ADM."","GET",""._SAVE."");
//$form->num_cols=2;

$form->add_text( "name_tmp_product", ""._NAME_PRODUCT.":");
if (!isset($id_product)) $form->add_textbox( "cod_tmp_product", ""._COD_PRODUCT.":", 30, 30 );
    else $form->add_text( "cod_tmp_product", ""._COD_PRODUCT.":" );
$form->add_textbox( "precio", ""._PRECIO.":", 10, 10 );

$form->add_hidden("data");
//$form->fields["precio"]->col=2;

if (!isset($id_product)) $pagina="";else  $pagina="&id_product=$id_product";

$form->fields["data"]->value = $this->txt_encrypt("pg=edtpriceprd&id_price_lst=$id_price_lst$pagina");

$processed = $form->process();

if(!$processed) {

   if (isset($cod_product)) $form->fields["cod_tmp_product"]->value=$cod_product;
   if (isset($precio)) $form->fields["precio"]->value=number_format($precio,2,",",".");
   if (isset($name_product)) $form->fields["name_tmp_product"]->value=$name_product;

   
   $this->html_out .=$form->draw();
   
   } else {
   reset($form->fields);
   while (list($key,$value)=each($form->fields)){
       if ($key!=="data") {$fields[$key]=$value->value;}
       }
   if (isset($id_product)) {
           if ($dbi->update_price_prd($id_price_lst,$id_product,$fields))
              //header ("Location: ".LK_PAG."".$dbi->url_encrypt("pg=verusers&from=$from")."");
              $this->redirect();
              else {$this->add_msg($dbi->txt_error);$this->html_out .=$form->draw();}
      } else {
           if ($dbi->add_price_prd($id_price_lst,$form->fields["cod_tmp_product"]->value, $id_org_session ,$fields))
              //header ("Location: ".LK_PAG."".$dbi->url_encrypt("pg=verusers&from=$from")."");
              $this->redirect();
              else {$this->add_msg($dbi->txt_error);$this->html_out .=$form->draw();}
      }
   }
?>
