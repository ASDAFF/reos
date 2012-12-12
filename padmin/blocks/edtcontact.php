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
*Edita los datos de un contact.
*Returns html into var html_out
*@package blocks_admin
**/

$PHP_SELF = $_SERVER['PHP_SELF'];
if (preg_match("/edtcontact.php/i",$PHP_SELF)) {
    Header("HTTP/1.0 404 Not Found");
    die();
}

require_once(_DirINCLUDES."forms/forms.php");
require_once(_DirINCLUDES."class_contacts.php");

global $id_contact;
global $fid;
global $id_account;
global $name_account;

$id_org_session=$this->auth["id_org"];
$id_pos_session=$this->auth["id_position"];

$dbi= new contact;

if (isset($id_contact)) { $dbi->dtl_contact($id_contact);
                       $this->add_msg($dbi->txt_error);
                       extract($dbi->Record);
                     }
if (isset($id_contact) || isset($fid))
  {
  $tit_pag=_EDT_CONTACT." - ".$name_account;
  
  $link[2]["txt"]=""._SEE2PRINT."";  $link[2]["print"]=true;
  } else {$tit_pag=_ADD_CONTACT." - ".$name_account;$link=NULL;}

$this->html_out .= $this->pgtitle($tit_pag,true,$link);


$form = new htmlform("form1","".LK_HOME_ADM."","GET", ""._SAVE."");

$form->title=_DTL."";
$form->num_cols=2;

$form->add_textbox("nm_contact",""._NM_CONTACT.":", 72, 72);
//$form->add_text("name_account",""._NAME_ACCOUNT.":");

$form->add_picker( "id_account", _NAME_ACCOUNT.":", 0, 0, "picker.php?".$this->url_encrypt("pg=veraccs&view=All&pk=id_account&pos=1&from=0"),true  );

$form->add_datebox("dt_create",_DT_CREATE.":");
$form->add_textbox("txt_email",_TXT_EMAIL.":",50,50);

$form->add_textarea( "txt_comment", _TXT_COMMENT.":", 50, 2 );

//$form->add_hidden("id_account");
$form->add_hidden("data");

$processed = $form->process();

if(!$processed) {

    
  if (isset($dt_create)) $form->fields["dt_create"]->value=$dt_create;
       else $form->fields["dt_create"]->value=date(""._DATE_FORMAT."");

  if (isset($nm_contact)) $form->fields["nm_contact"]->value = $nm_contact;
  if (isset($txt_comment)) $form->fields["txt_comment"]->value = $txt_comment;
  if (isset($txt_email)) $form->fields["txt_email"]->value = $txt_email;


  if (isset($id_account)) $form->fields["id_account"]->value = $id_account;
  
  if (isset($id_contact)) $pagina="&fid=$id_contact";else  $pagina="";
  
  if (isset($name_account)) {
         if (isset($id_contact)) {
         $form->fields["id_account"]->pklink="<a href=\"".LK_PAG."".$this->url_encrypt("pg=veraccs&view=My&keywords=name_acc_search=$name_account,order_by=name_account")."\">$name_account</a>";
         $form->fields["id_account"]->pktext=$name_account;
         } else $form->fields["id_account"]->pktext=$name_account;
  }
  $form->fields["data"]->value = $this->txt_encrypt("pg=".$this->vars["pg"].$pagina);
  //print_r($form->fields);
  $this->html_out .=$form->draw();

}else {

   reset($form->fields);
   while (list($key,$value)=each($form->fields)){
       if ($key!=="data") {$fields[$key]=$value->value;}
     }

if (isset($fid)){
        if ($dbi->update_contact($fid,$fields))
        {
        $this->redirect();
        } else {$this->add_msg($dbi->txt_error);$this->html_out .=$form->draw();}
} else
{
if ($dbi->add_contact($fields))
        {
        $this->redirect();
        //$this->add_msg($dbi->txt_error);
        } else {$this->add_msg($dbi->txt_error);$this->html_out .=$form->draw();}
}
}


?>
