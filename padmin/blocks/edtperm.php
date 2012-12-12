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
*Edita los datos de un perfil de usuario.
*Returns html into var html_out
*@package blocks_admin
**/

$PHP_SELF = $_SERVER['PHP_SELF'];
if (preg_match("/edtperm.php/i",$PHP_SELF)) {
    Header("HTTP/1.0 404 Not Found");
    die();
}

Include_once(_DirINCLUDES."forms/forms.php");
require_once(_DirINCLUDES."class_struct.php");

global $perms;
global $fid;
global $view;
global $perm_peso;
global $list_del;

//$id_org=$this->auth["id_org"];

$dbi= new struct;

if (isset($list_del)) foreach($list_del as $dlte){
	if (isset($dlte) && $dlte!="") {$dbi->del_perm_scr($perms,$dlte);$this->add_msg($dbi->txt_error);}
}

if (isset($perms)) {
     if ($dbi->perm_dtl($perms))
     {extract($dbi->Record);}
     else $this->add_msg($dbi->txt_error);
     }

if ($view!="Add") $tit_pag=""._EDT_PERM.""; else $tit_pag=""._ADD_PERM."";
$this->html_out .= $this->pgtitle($tit_pag,isset($this->auth["state"][1]),NULL);

$form = new htmlform("form1","".LK_HOME_ADM."","GET",""._SAVE."");

$form->add_textbox( "nm_profile", ""._NM_PROFILE.":", 30, 30 );
if (isset($nm_profile)) $form->fields["nm_profile"]->value=$nm_profile;

$form->add_textbox( "txt_obsrv", ""._TXT_OBSRV.":", 50, 50 );
if (isset($txt_obsrv)) $form->fields["txt_obsrv"]->value = $txt_obsrv;

$form->add_hidden("data");
if ($view!="Add") $pagina="fid=$perms";else  $pagina="view=Add";
$form->fields["data"]->value = $this->txt_encrypt("pg=edtperm&$pagina");

$processed = $form->process();

if( $processed ) {

    reset($form->fields);
    while (list($key,$value)=each($form->fields)){
       if ($key!=="data") {$fields[$key]=$value->value;}
    }

    if ($view!="Add")
        {
         if ($dbi->update_perm($fid,$fields))
                //header ("Location: ".LK_PAG."".$dbi->url_encrypt("pg=verusers&from=$from")."");
                $this->redirect();
                else {$this->add_msg($dbi->txt_error);$this->html_out .=$form->draw();}
        } else
        {
        if ($dbi->add_perm($fields ))
                //header ("Location: ".LK_PAG."".$dbi->url_encrypt("pg=verusers")."");
                $this->redirect();
                else {$this->add_msg($dbi->txt_error);$this->html_out .=$form->draw();}
        }
} else $this->html_out .=$form->draw();

/********************************************************************/

if ($view!="Add") {
 $this->html_out .= $this->pgtitle(""._SCREENS."",false,NULL);

 $form2 = new htmlform("form2","".LK_HOME_ADM."","GET",_ADD);
 
 $lst_screens=$dbi->screen_lst();
 $form2->add_static_listbox( "id_screen", ""._SCREEN.":",  $form->convert($lst_screens));

 $form2->add_textbox( "numorder", ""._NUM_ORDER.":", 10, 3 );
 if (isset($numorder)) $form2->fields["numorder"]->value = $numorder;

 $form2->add_hidden("data");
 $form2->fields["data"]->value = $this->txt_encrypt("pg=edtperm&perms=$perms");

 $processed2 = $form2->process();
 if( $processed2 ) {

    reset($form2->fields);
    while (list($key,$value)=each($form2->fields)){
       if ($key!=="data") {$fields[$key]=$value->value;}
    }

    if( !$dbi->add_perm_scrs( $perms, $fields ))
	    $this->add_msg($dbi->txt_error);

 }
 $this->html_out .=$form2->draw();
}


/*******************************************************************/



if (isset($perms)) {
 if ($dbi->perm_scrs_list($perms)){
     $fields=$dbi->select_array();
     $this->print_list($fields, 0,1000,"",null,true,"perms=$perms",$dbi->found_rows());
     } else $this->add_msg($dbi->txt_error);
}

?>
