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
*Edita los datos de un screen.
*Returns html into var html_out
*@package blocks_admin
**/

$PHP_SELF = $_SERVER['PHP_SELF'];
if (preg_match("/edtscr.php/i",$PHP_SELF)) {
    Header("HTTP/1.0 404 Not Found");
    die();
}

Include_once(_DirINCLUDES."forms/forms.php");
require_once(_DirINCLUDES."class_struct.php");

global $scr_id;
global $view;
global $list_del;

$dbi= new struct;

if (isset($list_del)) foreach($list_del as $dlte){
	if (isset($dlte) && $dlte!="") {$dbi->del_scr_view($scr_id,$dlte);$this->add_msg($dbi->txt_error);}
}


if (isset($scr_id)) {
    if ($dbi->scr_dtl($scr_id))
        extract($dbi->Record);
    else $this->add_msg($dbi->txt_error);
}

if ($view!="Add") $tit_pag=""._EDT_SCR.""; else $tit_pag=""._ADD_SCR."";
$this->html_out .= $this->pgtitle($tit_pag,isset($this->auth["state"][1]),NULL);

$form = new htmlform("form1","".LK_HOME_ADM."","GET",""._SAVE."");

$form->add_textbox( "nm_screen", ""._NM_PROFILE.":", 30, 30 );
if (isset($nm_screen)) $form->fields["nm_screen"]->value=$nm_screen;

$form->add_textbox( "txt_obsrv", ""._TXT_OBSRV.":", 80, 255 );
if (isset($txt_obsrv)) $form->fields["txt_obsrv"]->value=$txt_obsrv;

$form->add_textbox( "app_file", ""._APP_FILE.":", 80, 255 );
if (isset($app_file)) $form->fields["app_file"]->value=$app_file;
                 else $form->fields["app_file"]->value='home';
                 
$form->add_hidden("data");
if ($view!="Add") $pagina="scr_id=$scr_id";else $pagina="view=Add";
$form->fields["data"]->value = $this->txt_encrypt("pg=edtscr&$pagina");

$processed = $form->process();
if( $processed ) {

    reset($form->fields);
    while (list($key,$value)=each($form->fields)){
       if ($key!=="data") {$fields[$key]=$value->value;}
    }

    if($view!="Add") {
        if ($dbi->update_scr($scr_id,$fields))
            $this->redirect();
        else {$this->add_msg($dbi->txt_error);$this->html_out .=$form->draw();}
    } else {
        if ($dbi->add_scr($fields))
            $this->redirect();
            else {$this->add_msg($dbi->txt_error);$this->html_out .=$form->draw();}
        }

} else $this->html_out .=$form->draw();

/********************************************************************/

if ($view!="Add") {
 $this->html_out .= $this->pgtitle(""._VIEWS."",false,NULL);

 $form2 = new htmlform("form2","".LK_HOME_ADM."","GET",_ADD);
 
 $lst_views = $dbi->view_lst();
 $form2->add_static_listbox( "id_view", ""._VIEW.":",  $form->convert($lst_views));
 
 $form2->add_textbox( "numorder", ""._NUM_ORDER.":", 10, 3 );
 if (isset($numorder)) $form2->fields["numorder"]->value = $numorder;

 $form2->add_hidden("data");
 $form2->fields["data"]->value = $this->txt_encrypt("pg=edtscr&scr_id=$scr_id");

 $processed2 = $form2->process();
 if( $processed2 ) {

    reset($form2->fields);
    while (list($key,$value)=each($form2->fields)){
       if ($key!=="data") {$fields[$key]=$value->value;}
    }

    if( !$dbi->add_screen_views( $scr_id, $fields ))
    	$this->add_msg($dbi->txt_error);

	}
	$this->html_out .=$form2->draw();

}

/********************************************************************/

if (isset($scr_id)) {
 if ($dbi->scr_views_list($scr_id)){
     $fields=$dbi->select_array();
     $this->print_list($fields, 0,1000,"",null,true,"scr_id=$scr_id",$dbi->found_rows());
     } else $this->add_msg($dbi->txt_error);
}

?>
