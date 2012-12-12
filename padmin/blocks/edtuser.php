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
*Edita los datos de un usuario.
*Returns html into var html_out
*@package blocks_admin
**/

$PHP_SELF = $_SERVER['PHP_SELF'];
if (preg_match("/edtuser.php/i",$PHP_SELF)) {
    Header("HTTP/1.0 404 Not Found");
    die();
}

Include_once(_DirINCLUDES."forms/forms.php");
require_once(_DirINCLUDES."class_user.php");

global $user_id;
global $fid;
global $view;
global $perm_peso;

$id_org=$this->auth["id_org"];

$dbi= new user;

if (isset($user_id)) {
     if ($dbi->user_dtl($user_id))
     extract($dbi->Record);
     else $this->add_msg($dbi->txt_error);
     }

if ($view!="Add") $tit_pag=""._EDT_USER.""; else $tit_pag=""._ADD_USER."";

$this->html_out .= $this->pgtitle($tit_pag,isset($this->auth["state"][1]),NULL);

$form = new htmlform("form1","".LK_HOME_ADM."","GET",""._SAVE."");

$form->add_textbox( "name_user", ""._NAME_USER.":", 50, 50 );
if (isset($name_user)) $form->fields["name_user"]->value=$name_user;

$form->add_textbox( "txt_telf1", ""._TXT_TELF1.":", 20, 20 );
if (isset($txt_telf1)) $form->fields["txt_telf1"]->value = $txt_telf1;

$form->add_textbox( "txt_telf2", ""._TXT_TELF2.":", 20, 20 );
if (isset($txt_telf2)) $form->fields["txt_telf2"]->value = $txt_telf2;

$form->add_textbox( "txt_email", ""._TXT_EMAIL.":", 50, 50);
if (isset($txt_email)) $form->fields["txt_email"]->value=$txt_email;

if ($view!="Add") $form->add_text("username", ""._USERNAME.":");
    else $form->add_textbox( "username", ""._USERNAME.":", 20, 20 );//no se puede modificar

if (isset($username)) $form->fields["username"]->value=$username;

if ($view=="Add") {
$form->add_password( "password", ""._PASSWORD.":", 20, 20 );
$form->add_password( "passwd1", ""._RE_PASSWORD.":", 20, 20 );
}

$form->add_hidden("data");
if ($view!="Add") $pagina="fid=$user_id";else  $pagina="view=Add";
$form->fields["data"]->value = $this->txt_encrypt("pg=edtuser&$pagina");

$processed = $form->process();

if( $processed ) {

reset($form->fields);
while (list($key,$value)=each($form->fields)){
   if ($key!=="data") {$fields[$key]=$value->value;}
   }
$fields["user_type"]=1;

if ($view!="Add")
        {
         if ($dbi->update_user($fid,$fields))
                //header ("Location: ".LK_PAG."".$dbi->url_encrypt("pg=verusers&from=$from")."");
                $this->redirect();
                else {$this->add_msg($dbi->txt_error);$this->html_out .=$form->draw();}
        } else
        {
        if ($dbi->add_user($id_org,$fields ))
                //header ("Location: ".LK_PAG."".$dbi->url_encrypt("pg=verusers")."");
                $this->redirect();
                else {$this->add_msg($dbi->txt_error);$this->html_out .=$form->draw();}
        }
} else $this->html_out .=$form->draw();



?>
