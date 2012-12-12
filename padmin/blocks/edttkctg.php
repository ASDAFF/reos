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
*Edits a support category
*Returns html into var html_out
*@package blocks_admin
**/

$PHP_SELF = $_SERVER['PHP_SELF'];
if (preg_match("/edttkctg.php/i",$PHP_SELF)) {
    Header("HTTP/1.0 404 Not Found");
    die();
}

Include_once(_DirINCLUDES."forms/forms.php");
require_once(_DirINCLUDES."class_support.php");

global $id_tk_ctg;
global $view;
global $dlt;

$id_org_session=$this->auth["id_org"];

$dbi= new support;

if (isset($dlt) && $dlt!="") { $dbi->del_ctg_org($id_tk_ctg,$dlt); $this->add_msg($dbi->txt_error); }

if (isset($id_tk_ctg)) {
    if ($dbi->ctg_dtl($id_tk_ctg))
        extract($dbi->Record);
    else $this->add_msg($dbi->txt_error);
}

if ($view!="Add") $tit_pag=""._EDTTKCTG.""; else $tit_pag=""._ADD_TK_CTG."";
$this->html_out .= $this->pgtitle($tit_pag,true,NULL);

$form = new htmlform("form1","".LK_HOME_ADM."","GET",""._SAVE."");

$form->add_textbox( "nm_tk_ctg", _NM_TK_CTG.":", 70, 70 );
$form->add_textbox( "pophost", _POPHOST.":", 70, 70 );
$form->add_textbox( "popuser", _POPUSER.":", 70, 70 );
$form->add_textbox( "poppass", _POPPASS.":", 70, 70 );
$form->add_textbox( "txt_email", _TXT_EMAIL.":", 70, 70 );
$form->add_textbox( "txt_sign", _TXT_SIGN.":", 70, 70 );
$form->add_static_listbox( "fg_hidden", _FG_HIDDEN.":","0;"._0.",1;"._1);


$form->add_hidden("data");


$processed = $form->process();
if(!$processed ) {
	
	if (isset($nm_tk_ctg)) $form->fields["nm_tk_ctg"]->value=$nm_tk_ctg;
	if (isset($pophost)) $form->fields["pophost"]->value=$pophost;
	if (isset($popuser)) $form->fields["popuser"]->value=$popuser;
	if (isset($poppass)) $form->fields["poppass"]->value=$poppass;
	if (isset($txt_email)) $form->fields["txt_email"]->value=$txt_email;
	if (isset($txt_sign)) $form->fields["txt_sign"]->value=$txt_sign;
	
	if ($view!="Add") $pagina="id_tk_ctg=$id_tk_ctg";else $pagina="view=Add";
	$form->fields["data"]->value = $this->txt_encrypt("pg=edttkctg&$pagina");
	$this->html_out .=$form->draw();


} else {

    reset($form->fields);
    while (list($key,$value)=each($form->fields)){
       if ($key!=="data") {$fields[$key]=$value->value;}
    }

    if($view!="Add") {
        if ($dbi->update_ctg($id_tk_ctg,$fields))
            $this->redirect();
        else {$this->add_msg($dbi->txt_error);$this->html_out .=$form->draw();}
    } else {
        if ($dbi->add_ctg($fields))
            $this->redirect();
            else {$this->add_msg($dbi->txt_error);$this->html_out .=$form->draw();}
        }
}     

/********************************************************************/

if ($view!="Add") {
 $this->html_out .= $this->pgtitle(_ORG." - "._DEPTS,false,NULL);

 $form2 = new htmlform("form2","".LK_HOME_ADM."","GET",_ADD);
 
 $lst_orgs = $dbi->org_lst($id_org_session);
 $form2->add_static_listbox( "id_org", _ORG.":",  $form->convert($lst_orgs));
 
 $form2->add_hidden("data");
 $form2->fields["data"]->value = $this->txt_encrypt("pg=edttkctg&id_tk_ctg=$id_tk_ctg");

 $processed2 = $form2->process();
 if( $processed2 ) {

    reset($form2->fields);
    while (list($key,$value)=each($form2->fields)){
       if ($key!=="data") {$fields[$key]=$value->value;}
    }

    if( !$dbi->add_ctg_orgs( $id_tk_ctg, $fields ))
    	$this->add_msg($dbi->txt_error);

	}
	$this->html_out .=$form2->draw();

}

/********************************************************************/

if (isset($id_tk_ctg)) {
 if ($dbi->ctg_orgs_list($id_tk_ctg)){
     $fields=$dbi->select_array();
     $this->print_list($fields, 0,1000,"",null,"id_tk_ctg=$id_tk_ctg&dlt=","",$dbi->found_rows());
     } else $this->add_msg($dbi->txt_error);
}

?>
