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
*Change user password.
*Returns html into var html_out
*@package blocks_admin
**/
$PHP_SELF = $_SERVER['PHP_SELF'];

if (preg_match("/cpass.php/i",$PHP_SELF)) {
    Header("HTTP/1.0 404 Not Found");
    die();
}

require_once(_DirINCLUDES."forms/forms.php");
require_once(_DirINCLUDES."class_user.php");


$id_uid=$this->auth["uid"];

$this->html_out .= $this->pgtitle(""._CHANGE_PASS."",false,null);
     
$form = new htmlform("form1","".LK_HOME_ADM."","GET",""._SAVE."");

$form->add_password( "old_pass", ""._CURRENT_PASS.":", 20, 20 );
$form->add_password( "new_pass1", ""._NEW_PASSWORD.":", 20, 20 );
$form->add_password( "new_pass2", ""._RE_NEW_PASS.":", 20, 20 );

$form->add_hidden("data");
$form->fields["data"]->value = $this->txt_encrypt("pg=cpass");

$processed = $form->process();

if( $processed ) {
$dbi= new user;
if ($dbi->change_pass($id_uid, $form->fields["old_pass"]->value,$form->fields["new_pass1"]->value,$form->fields["new_pass2"]->value))
    {
    //$this->add_msg($dbi->txt_error);
    $this->redirect();
    } else {$this->add_msg($dbi->txt_error);$this->html_out .=$form->draw();}
    
} else $this->html_out .=$form->draw();

?>
