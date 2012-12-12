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
*Returns xml node for remember usr/pass form.
*@package blocks_public
**/


$PHP_SELF = $_SERVER['PHP_SELF'];
if (preg_match("/remember.php/i",$PHP_SELF)) {
    Header("HTTP/1.0 404 Not Found");
    die();
}

require_once(_DirINCLUDES."forms/forms_xml.php");
require_once(_DirINCLUDES."class_account.php");

global $nm;
global $id_org_session;


$tit_pag="$nm";
$dbi= new account;

$this->html_out .= $this->pgtitle($tit_pag,true,Null);
$this->html_out .= "<remember title=\"$tit_pag\">";

$form = new xmlform("form1","".LK_HOME_ADM."","GET",""._ENVIAR."");

$form->num_cols=0;
$form->add_textbox( "txt_email1",_TXT_EMAIL1, 40, 40 );

$form->add_hidden("data");
$processed = $form->process();

if(!$processed) {
        $pagina="&nm=$nm";
        $form->fields["data"]->value = $this->txt_encrypt("pg=".$this->vars["pg"].$pagina);
        $this->html_out .="<comment>";
        $this->html_out .= _REM_TXT;
        $this->html_out .= "</comment>";
        $this->html_out .=$form->draw();

} else
{
   reset($form->fields);
   while (list($key,$value)=each($form->fields)){
     if ($key!=="data") {$fields[$key]=$value->value;}
     }

   $checkfields=false;
   if ($fields["txt_email1"]=="") $this->add_msg(""._MAIL_FORMAT."");
       else $checkfields=true;

   if ($checkfields && $usr=$dbi->remember($id_org_session, $fields["txt_email1"])) {

      //$to[0]["email"]=$fields["txt_email1"];
      //$to[0]["name"]=$usr["name_account"];

      $to = $fields["txt_email1"];
      
      $subject=_MAIL_REM;
      $msg = "\n"._MAIL_REM_TXT;
      $msg.="\n\n"._USERNAME." : ".$usr["username"]."\n"._PASSWORD." : ".$usr["password"]."\n\n";
      $msg.="http://"._UrlSITE."/\n"._LABEL_ORG." - "._SAT_DEPT;
      $this->add_email(""._SAT_EMAIL."",""._LABEL_ORG." - "._SAT_DEPT."",$to,$subject, $msg);

      $this->html_out .= "<comment>";
      $this->html_out .= ""._REM_CONFIRM."";
      $this->html_out .= "</comment>";

   } else {
   $this->add_msg($dbi->txt_error);
   $this->html_out .="<comment>";
   $this->html_out .= _REM_TXT;
   $this->html_out .= "</comment>";
   $this->html_out .=$form->draw();}
}

$this->html_out .= "</remember>";


?>
