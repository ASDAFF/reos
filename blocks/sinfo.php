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
* Contact form.
* @package blocks_public
**/


$PHP_SELF = $_SERVER['PHP_SELF'];
if (preg_match("/sinfo.php/i",$PHP_SELF)) {
    Header("HTTP/1.0 404 Not Found");
    die();
}

require_once(_DirINCLUDES."forms/forms_xml.php");
require_once(_DirINCLUDES."class_immo.php");

global $nm;
global $id_org_session;
global $ref_immo;

$tit_pag="$nm";
//$dbi= new account;


$this->html_out .= $this->pgtitle($tit_pag,true,Null);

$this->html_out .="<contact>";

$this->html_out .="<comment>";
$this->html_out .= _SINFO_TXT.".<br />"._SINFO_ADVERT.".";
$this->html_out .= "</comment>";


$form = new xmlform("form1","".LK_HOME_ADM."","GET",""._ENVIAR."");
$form->num_cols=0;
$form->add_textbox( "name_account", ""._FULL_NAME."", 72, 72 );
$form->add_textbox( "txt_email1", ""._TXT_EMAIL1."", 40, 40 );
$form->add_textbox( "txt_telf1", ""._TXT_TELF1."", 13, 13 );
$form->add_textbox( "txt_asunt", ""._TXT_ASUNT."", 70, 45 );
$form->add_textarea( "txt_desc", ""._TXT_DESC."", 45, 13 );

//$form->fields["txt_telf1"]->col = 2;

$form->add_hidden("data");
$processed = $form->process();

if(!$processed) {
        $pagina="&ref_immo=$ref_immo&nm=$nm";
        if (isset($ref_immo)) $form->fields["txt_asunt"]->value=_REF_IMMO." : ".$ref_immo;

        $form->fields["data"]->value = $this->txt_encrypt("pg=".$this->vars["pg"].$pagina);
        $this->html_out .=$form->draw();

} else {
   reset($form->fields);
   while (list($key,$value)=each($form->fields)){
         if ($key!=="data") {$fields[$key]=$value->value;}
         }
   reset($fields);
   $check_fields=True;
   while (list($key,$value)=each($fields))
   {
    switch($key)
         {
         case "name_account":
         if (strlen($value)<3) {$this->add_msg(""._IN_NAME_ACCOUNT."");$check_fields=false;}
         break;
         case "txt_asunt":
         if (strlen($value)<5) {$this->add_msg(""._IN_SUBJECT."");$check_fields=false;}
         break;
         case "txt_email1":
         if (!preg_match('/^[.\w-]+@([\w-]+\.)+[a-zA-Z]{2,6}$/', $value))
             {$this->add_msg(""._MAIL_FORMAT."");$check_fields=false;}
         break;
         default:
         break;
        }
   }

   if ($check_fields) {
   //$to[0]["email"]=$fields["txt_email1"];
   //$to[0]["name"]=$fields["name_account"];

   $to=$fields["txt_email1"];
   
   $subject=_MAIL_NOTI;
   $msg=_MAIL_HRECEV." $nm : ".$fields["txt_asunt"]."\n\n"._MAIL_NMSG."\n\n"._LABEL_ORG." - "._SAT_DEPT;
   $this->add_email(""._SAT_EMAIL."",""._LABEL_ORG." - "._SAT_DEPT."",$to,$subject, $msg);

   $to=Null;
   //$to[0]["email"]=""._SAT_EMAIL."";
   //$to[0]["name"]=""._SAT_DEPT."";   
   
   $to = _SAT_EMAIL;
   
   $subject=$fields["txt_asunt"];
   $namefrom=$fields["name_account"];
   $from=$fields["txt_email1"];
   $msg="$nm :\n\n"
       ._NAME_ACCOUNT." : ".$fields["name_account"]."\n"
       ._TXT_EMAIL1." : ".$fields["txt_email1"]."\n"
       ._TXT_TELF1." : ".$fields["txt_telf1"]."\n\n"
       ._TXT_ASUNT." : ".$fields["txt_asunt"]."\n\n"
       ._TXT_DESC." : ".$fields["txt_desc"];
   
   $this->add_email($from,$namefrom,$to,$subject, $msg);

   $immo = new immo;

   if (($to=$immo->GetEmailOwner($ref_immo))!=""){
   	  $this->add_email($from,$namefrom,$to,$subject, $msg);
   }
   
   
   $this->html_out .= "<content>";
   $this->html_out .= _SINFO_CONFIRM;
   $this->html_out .= "</content>";
   } else $this->html_out .=$form->draw();

}
$this->html_out .="</contact>";


?>
