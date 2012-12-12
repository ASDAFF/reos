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
*Edita los datos de alquiler vacaciones de un immo.
*Returns html into var html_out
*@package blocks_admin
**/

$PHP_SELF = $_SERVER['PHP_SELF'];
if (preg_match("/edthimmo.php/i",$PHP_SELF)) {
    Header("HTTP/1.0 404 Not Found");
    die();
}

require_once(_DirINCLUDES."forms/forms.php");
require_once(_DirINCLUDES."class_immo.php");
require_once(_DirINCLUDES."class_himmo.php");
require_once(_DirINCLUDES."class_lovs.php");

$lovs= new lovs;
$lovs->getLovs('_LST_EQUIP',_IDIOMA);
$lovs->getLovs('_LST_SERVICES',_IDIOMA);
$lovs->getLovs('_LST_ACTIVITIES',_IDIOMA);
$lovs->getLovs('_LST_OBSERV',_IDIOMA);


global $id_immo;
global $fid;
global $perm_peso;


$id_org_session=$this->auth["id_org"];
$id_pos_session=$this->auth["id_position"];

$himmo =new himmo;
$himmo->seasons_list(NULL,$id_org_session,0,200,null);
$seasons=$himmo->select_array();
$seasons_lst=";";
foreach($seasons as $vals) {
$seasons_lst.=",".$vals["id_seasons"].";".$vals["name_seasons"];
if ($vals["tp_price"]!=="") $seasons_lst.=" - "._PRECIO." ".$vals["tp_price"];
}
//print_r($seasons);


$dbi= new immo;
if (isset($id_immo)) { $dbi->dtl_immo($id_immo);
                       $this->add_msg($dbi->txt_error);
                       extract($dbi->Record);
                     }

$this->html_out .= $this->pgtitle(_EDTHIMMO,true,NULL);

$form = new htmlform("form1","".LK_HOME_ADM."","GET", ""._SAVE."");

$form->title=""._DTL_PROP."";
//$form->num_cols=2;

$form->add_text( "ref_immo", ""._REF_IMMO.":");

$form->add_textbox( "int_capacity", ""._INT_CAPACITY.":", 3,3 );

$form->add_static_listbox("id_seasons",""._LBL_SEASONS.":",$seasons_lst);

$form->add_static_checkbox("set_equip",""._SET_EQUIP.":",$form->convert(""._LST_EQUIP.""));
$form->add_static_checkbox("set_services",""._SET_SERVICES.":",$form->convert(""._LST_SERVICES.""));
$form->add_static_checkbox("set_activities",""._SET_ACTIVITIES.":",$form->convert(""._LST_ACTIVITIES.""));
$form->add_static_checkbox("set_observ",""._SET_OBSERV.":",$form->convert(""._LST_OBSERV.""));

$form->add_hidden("data");

$processed = $form->process();

if(!$processed) {

  if (isset($ref_immo)) $form->fields["ref_immo"]->value = $ref_immo;

  if (isset($int_capacity)) $form->fields["int_capacity"]->value = $int_capacity;
  if (isset($id_seasons)) $form->fields["id_seasons"]->value = $id_seasons;

  if (isset($set_observ)) { eval("\$tmp=array(".$set_observ.");");
            $form->fields["set_observ"]->value = $tmp;unset($tmp);}


  if (isset($set_equip)) { eval("\$tmp=array(".$set_equip.");");
            $form->fields["set_equip"]->value = $tmp;unset($tmp);}

  if (isset($set_services)) { eval("\$tmp=array(".$set_services.");");
            $form->fields["set_services"]->value = $tmp;unset($tmp);}

  if (isset($set_activities)) { eval("\$tmp=array(".$set_activities.");");
            $form->fields["set_activities"]->value = $tmp;unset($tmp);}

  if (isset($id_immo)) $pagina="&fid=$id_immo";else  $pagina="";
  

  $form->fields["data"]->value = $this->txt_encrypt("pg=".$this->vars["pg"].$pagina);
  //print_r($form->fields);
  $this->html_out .=$form->draw();

}else {

   reset($form->fields);
   while (list($key,$value)=each($form->fields)){
       if ($key!=="data") {$fields[$key]=$value->value;}
     }

if (isset($fid)){
        if ($dbi->update_immo($fid,$fields))
        {
        $this->redirect();
        } else {$this->add_msg($dbi->txt_error);$this->html_out .=$form->draw();}
} else
{
if ($dbi->add_immo($id_org_session, $id_pos_session, $fields))
        {
        $this->redirect();
        //$this->add_msg($dbi->txt_error);
        } else {$this->add_msg($dbi->txt_error);$this->html_out .=$form->draw();}
}
}

?>
