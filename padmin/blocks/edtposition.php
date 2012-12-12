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
*Edita los datos de un cargo/position.
*Returns html into var html_out
*@package blocks_admin
**/

$PHP_SELF = $_SERVER['PHP_SELF'];
if (preg_match("/edtposition.php/i",$PHP_SELF)) {
    Header("HTTP/1.0 404 Not Found");
    die();
}

Include_once(_DirINCLUDES."forms/forms.php");
require_once(_DirINCLUDES."class_org.php");
require_once(_DirINCLUDES."class_lovs.php");

$lovs= new lovs;
$lovs->getLovs('_LST_POSITIONS',_IDIOMA);

global $id_pos;
global $sub_id;


$id_org=$this->auth["id_org"];

$this->html_out .= $this->pgtitle(""._EDT_EMPL."",true,null);

$dbi= new org;

$form = new htmlform("form1","".LK_HOME_ADM."");

if (isset($id_pos))
   {
   list($user_id, $id_sub, $position, $name_user)=($dbi->position($id_pos));
   $this->add_msg($dbi->txt_error);
   }

//*************************
$dbi->all_orgs($id_org,"id_org, name_org");
$this->add_msg($dbi->txt_error);
$orgs="$id_org;"._ROOT_DEPT."";
while ($dbi->next_record()){
list($orgs_id, $orgs_name)=($dbi->Record);
$orgs=$orgs.",$orgs_id;$orgs_name";
}

$form = new htmlform("form1","".LK_HOME_ADM."");

if (!isset($id_pos)) {
/*
  $dbi->employees($id_org,0); //empleados 0 asignados
  $this->add_msg($dbi->txt_error);
  $empleados="";
  while ($dbi->next_record()){
  list($user_id_lst, $name_user)=($dbi->Record);
  $empleados=$empleados.",$user_id_lst;$name_user";
  }
  $form->add_static_listbox( "user_id", ""._EMPL.":", $empleados );
  if (isset($user_id) && $user_id!="") $form->fields["user_id"]->value = $user_id;
*/
  $form->add_picker( "user_id", _EMPL.":", 0, 0, "picker.php?".$this->url_encrypt("pg=emples&view=All&pk=user_id&pos=1&from=0"),true  );
} else {

  $form->add_text("name_user", ""._EMPL.":");
  if (isset($name_user)) $form->fields["name_user"]->value = $name_user;
}



$form->add_static_listbox( "sub_id", ""._DEPT.":", $orgs );
if (isset($sub_id) && $sub_id!="") $form->fields["sub_id"]->value = $sub_id;

$form->add_static_listbox( "position", ""._POSITION.":", $form->convert(""._LST_POSITIONS."") );
if (isset($position) && $position!="") $form->fields["position"]->value = $position;

$form->add_hidden("data");
if (isset($id_pos)) $pagina="&id_pos=$id_pos"; else $pagina="";
$form->fields["data"]->value = $this->txt_encrypt("pg=edtposition$pagina");

$processed = $form->process();

if( $processed ) {

if (isset($id_pos))
{
   if ($dbi->update_position($id_pos, $form->fields["sub_id"]->value, $form->fields["position"]->value))
       header ("Location: ".LK_PAG."".$dbi->url_encrypt("pg=verorgs&sub_id=".$form->fields["sub_id"]->value."")."");
       else {$this->add_msg($dbi->txt_error);$this->html_out .=$form->draw();}
} else {
   if ($dbi->add_position($form->fields["user_id"]->value, $form->fields["sub_id"]->value, $form->fields["position"]->value))
       header ("Location: ".LK_PAG."".$dbi->url_encrypt("pg=verorgs&sub_id=".$form->fields["sub_id"]->value."")."");
       else {$this->add_msg($dbi->txt_error);$this->html_out .=$form->draw();}
   }
    
} else $this->html_out .=$form->draw();


?>
