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
*Edita los datos de una temporada de alquiler de vacaciones.
*Returns html into var html_out
*@package blocks_admin
**/

$PHP_SELF = $_SERVER['PHP_SELF'];
if (preg_match("/edtseason.php/i",$PHP_SELF)) {
    Header("HTTP/1.0 404 Not Found");
    die();
}

require_once(_DirINCLUDES."forms/forms.php");
require_once(_DirINCLUDES."class_himmo.php");
require_once(_DirINCLUDES."class_lovs.php");

$lovs= new lovs;
$lovs->getLovs('_LST_TP_PRICE',_IDIOMA);

global $id_seasons;
global $fid;
global $list_del;


$id_org_session=$this->auth["id_org"];

$dbi= new himmo;


if (isset($list_del)) foreach($list_del as $dlte){
	if (isset($dlte) && $dlte!="") {$dbi->delete_sday($dlte);$this->add_msg($dbi->txt_error);}
}


$link_add=NULL;
if (isset($id_seasons)) {
    if ($dbi->dtl_season($id_seasons)) extract($dbi->Record);
    $this->add_msg($dbi->txt_error);
    $link_add[0]["href"]="pg=edtsday&id_seasons=$id_seasons&name_seasons=$name_seasons";
    $link_add[0]["txt"]=""._ADD_SDAY."";
    }



$this->html_out .= $this->pgtitle(_EDTSEASON,true,$link_add);

$form = new htmlform("form1","".LK_HOME_ADM."","GET",""._SAVE."");

$form->add_textbox( "name_seasons", ""._NAME_SEASONS.":", 20, 20 );
$form->add_static_listbox("tp_price",""._TP_PRICE.":",$form->convert(""._LST_TP_PRICE.""));
$form->add_hidden("data");

$processed = $form->process();

if (!$processed) {

if (isset($name_seasons)) $form->fields["name_seasons"]->value = $name_seasons;
if (isset($tp_price)) $form->fields["tp_price"]->value = $tp_price;

if (isset($id_seasons)) $pagina="fid=$id_seasons"; else $pagina="";
$form->fields["data"]->value = $this->txt_encrypt("pg=edtseason&$pagina");
$this->html_out .=$form->draw();
}
else
{
// Processar el form
reset($form->fields);
while (list($key,$value)=each($form->fields)){
    if ($key!=="data") {$fields[$key]=$value->value;}
    }
    
if (isset($fid)){
   if ($dbi->update_vals($fid,$fields,"seasons"))
      {
      $this->redirect();
      } else { $this->add_msg($dbi->txt_error); $this->html_out .=$form->draw();}
}else{
   if ($dbi->add_vals($id_org_session, $fields,"seasons"))
      {
       $this->redirect();
      } else {$this->add_msg($dbi->txt_error);$this->html_out .=$form->draw();}
 }
}

if (isset($id_seasons) || isset($fid))
   {
   if (!isset($id_seasons))$id_seasons=$fid;
   if ($dbi->days_season($id_seasons)){
       $this->print_list($dbi->select_array(), 0,20,"","pg=edtsday&id_sdays=",true,"id_seasons=$id_seasons",$dbi->found_rows());
       }
        else $this->add_msg($dbi->txt_error);
   }
?>
