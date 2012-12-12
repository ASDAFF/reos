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
*Edit Price list info.
*Returns html into var html_out
*@package blocks_admin
**/

$PHP_SELF = $_SERVER['PHP_SELF'];
if (preg_match("/edtpricelst.php/i",$PHP_SELF)) {
    Header("HTTP/1.0 404 Not Found");
    die();
}

Include_once(_DirINCLUDES."forms/forms.php");
require_once(_DirINCLUDES."class_catalog.php");
require_once(_DirINCLUDES."class_lovs.php");

$lovs= new lovs;
$lovs->getLovs('_LST_TP_STE_PRC',_IDIOMA);

global $id_price_lst;
global $fid;
global $view;

$id_org_session=$this->auth["id_org"];

$dbi= new catalog;

if (isset($id_price_lst)) {
     if ($dbi->price_lst_dtl($id_price_lst))
     extract($dbi->Record);
     else $this->add_msg($dbi->txt_error);
     }

if ($view!="Add") $tit_pag=""._EDTPRICELST.""; else $tit_pag=""._ADDPRICELST."";

$this->html_out .= $this->pgtitle($tit_pag,true,NULL);

$form = new htmlform("form1","".LK_HOME_ADM."","GET",""._SAVE."");
$form->num_cols=2;

$form->add_textbox( "name_price_lst", ""._NAME_PRICE_LST.":", 50, 50 );
$form->add_static_listbox("tp_state",""._TP_STATE.":",$form->convert(""._LST_TP_STE_PRC.""));
$form->add_textbox( "dt_create", ""._DT_CREATE.":", 8, 10 );
$form->add_textbox( "dt_start", ""._DT_START.":", 8, 10 );
$form->add_textbox( "dt_end", ""._DT_END.":", 8, 10 );

$form->add_hidden("data");
$form->fields["dt_end"]->col=2;
$form->fields["tp_state"]->col=2;

if ($view!="Add") $pagina="fid=$id_price_lst";else  $pagina="view=Add";

$form->fields["data"]->value = $this->txt_encrypt("pg=edtpricelst&$pagina");

$processed = $form->process();

if(!$processed) {

   if (isset($name_price_lst)) $form->fields["name_price_lst"]->value=$name_price_lst;
   if (isset($tp_state)) $form->fields["tp_state"]->value = $tp_state;
   if (isset($dt_create)) $form->fields["dt_create"]->value=$dt_create;
   else $form->fields["dt_create"]->value=date(""._DATE_FORMAT."");
   if (isset($dt_start)) $form->fields["dt_start"]->value = $dt_start;
   if (isset($dt_end)) $form->fields["dt_end"]->value = $dt_end;
   
   $this->html_out .=$form->draw();
   
   } else {
   reset($form->fields);
   while (list($key,$value)=each($form->fields)){
       if ($key!=="data") {$fields[$key]=$value->value;}
       }
   if ($view!="Add")
        {
         if ($dbi->update_price_lst($fid,$fields))
                //header ("Location: ".LK_PAG."".$dbi->url_encrypt("pg=verusers&from=$from")."");
                $this->redirect();
                else {$this->add_msg($dbi->txt_error);$this->html_out .=$form->draw();}
        } else
        {
        if ($dbi->add_price_lst($id_org_session,$fields ))
                //header ("Location: ".LK_PAG."".$dbi->url_encrypt("pg=verusers")."");
                $this->redirect();
                else {$this->add_msg($dbi->txt_error);$this->html_out .=$form->draw();}
        }
   }
?>
