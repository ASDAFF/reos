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
*Edita los datos de un view.
*Returns html into var html_out
*@package blocks_admin
**/

$PHP_SELF = $_SERVER['PHP_SELF'];
if (preg_match("/edtview.php/i",$PHP_SELF)) {
    Header("HTTP/1.0 404 Not Found");
    die();
}

Include_once(_DirINCLUDES."forms/forms.php");
require_once(_DirINCLUDES."class_struct.php");

global $view_id;
global $view;

$dbi= new struct;

if (isset($view_id)) {
    if ($dbi->view_dtl($view_id))
        extract($dbi->Record);
    else $this->add_msg($dbi->txt_error);
}

if ($view!="Add") $tit_pag=""._EDT_VIEW.""; else $tit_pag=""._ADD_VIEW."";
$this->html_out .= $this->pgtitle($tit_pag,isset($this->auth["state"][1]),NULL);

$form = new htmlform("form1","".LK_HOME_ADM."","GET",""._SAVE."");

$form->add_textbox( "nm_view", ""._NM_PROFILE.":", 30, 30 );
if (isset($nm_view)) $form->fields["nm_view"]->value=$nm_view;

$form->add_textbox( "app_file", ""._APP_FILE.":", 30, 30 );
if (isset($app_file)) $form->fields["app_file"]->value=$app_file;

$form->add_textbox( "txt_obsrv", ""._TXT_OBSRV.":", 80, 255 );
if (isset($txt_obsrv)) $form->fields["txt_obsrv"]->value=$txt_obsrv;

$form->add_textbox( "params", ""._PARAMS.":", 80, 255 );
if (isset($params)) $form->fields["params"]->value=$params;

$form->add_hidden("data");
if ($view!="Add") $pagina="view_id=$view_id";else $pagina="view=Add";
$form->fields["data"]->value = $this->txt_encrypt("pg=edtview&$pagina");

$processed = $form->process();
if( $processed ) {

    reset($form->fields);
    while (list($key,$value)=each($form->fields)){
       if ($key!=="data") {$fields[$key]=$value->value;}
    }

    if($view!="Add") {
        if ($dbi->update_view($view_id,$fields))
            $this->redirect();
        else {$this->add_msg($dbi->txt_error);$this->html_out .=$form->draw();}
    } else {
        if ($dbi->add_view($fields))
            $this->redirect();
            else {$this->add_msg($dbi->txt_error);$this->html_out .=$form->draw();}
        }

} else $this->html_out .=$form->draw();

?>
