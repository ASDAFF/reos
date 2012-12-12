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
*Returns xml node for subscription form.
*@package blocks_public
**/


$PHP_SELF = $_SERVER['PHP_SELF'];
if (preg_match("/subs.php/i",$PHP_SELF)) {
    Header("HTTP/1.0 404 Not Found");
    die();
}

require_once(_DirINCLUDES."forms/forms_xml.php");
require_once(_DirINCLUDES."class_account.php");

global $nm;
global $id_org_session;
global $op;

if (!isset($op)) $op=0;

$tit_pag="$nm";
$dbi= new account;


$this->html_out .= $this->pgtitle($tit_pag,true,Null);
$this->html_out .= "<subs>";
$this->html_out .= "<subs_title>$tit_pag</subs_title>\n";



$form = new xmlform("form1","".LK_HOME_ADM."","GET",""._ENVIAR."");

$form->num_cols=0;
$form->add_textbox( "name_account", ""._FULL_NAME."", 72, 72 );
$form->add_textbox( "txt_email1", ""._TXT_EMAIL1, 40, 40 );
$form->add_textbox( "txt_telf1", ""._TXT_TELF1._OPTIONAL."", 13, 13 );
$form->add_captcha( "captcha", _CAPTCHA);

//$form->fields["txt_telf1"]->col = 2;

$form->add_hidden("data");
$processed = $form->process();

if(!$processed) {
        $pagina="&nm=$nm&op=$op";
        $form->fields["data"]->value = $this->txt_encrypt("pg=".$this->vars["pg"].$pagina);
        $this->html_out .="<comment>";
        switch($op) {
         case 0:
         $this->html_out .= _SUBS_TXT."<br />";
         $this->html_out .=_SINFO_ADVERT;
         break;
         case 1:
         $this->html_out .= _REGS_TXT."<br />";
         $this->html_out .=_SINFO_ADVERT;
         break;
         default:
         break;
        }	    
        
        $this->html_out .= "</comment>";

       
        $this->html_out .=$form->draw();

} else {
   
	reset($form->fields);   
   
   while (list($key,$value)=each($form->fields)){
     if ($key!=="data" || $key!=="captcha") {$fields[$key]=$value->value;}
     }

   $checkfields=false;
   $fields["username"]=$fields["txt_email1"];
   $fields["password"]=substr(md5(uniqid("")), 0, 7);
   $fields["tp_state"]=3;  // to validate
   $fields["cod_lang"]=_IDIOMA;
   $fields["ind_mailing"]=1;   // Mailing yes
   $fields["dt_create"]=date(""._DATE_FORMAT."");
   
   if ($fields["txt_email1"]=="") $this->add_msg(_MAIL_FORMAT);
       else $checkfields=true;    
           
   if (strlen($form->fields["captcha"]->value)!=6 || $form->fields["captcha"]->value!=$form->fields["captcha"]->captcha){ 
   		$this->add_msg(_ERROR_CAPTCHA);
   		$checkfields=false;
   } else $checkfields=true;    
   		   
   if ($checkfields && $dbi->add_account($id_org_session, "NULL", $fields)) {

	  	   	
      //$to["email"]=$fields["txt_email1"];
      //$to["name"]=$fields["name_account"];
	 
   	  $to=$fields["txt_email1"]; 
      $subject=_MAIL_SUBS;
      if (_IDIOMA=="fa") $dir = "rtl";else $dir = "ltr"; 
      $msg = "<span dir=\"$dir\">"._MAIL_SUBS_TXT."<a href=\"http://"._UrlSITE."/".LK_PAG.$this->url_encrypt("pg=confirm&op=$op&usr=".$fields["username"]."&psw=".$fields["password"]."")."\">"._MAIL_SUBS."</a><br/><br/>";
      if ($op==1) $msg.=_USERNAME." : ".$fields["txt_email1"]."<br/>"._PASSWORD." : <span dir=\"ltr\">".$fields["password"]."</span><br/><br/>";
      $msg.="</span><span dir=\"ltr\">"._LABEL_ORG." - "._SAT_DEPT."</span>";
      $this->add_email(""._SAT_EMAIL."",""._LABEL_ORG." - "._SAT_DEPT."",$to,$subject, $msg, true);

      $this->html_out .= "<comment>";
      $this->html_out .= ""._MAIL_CONFIRM."";
      $this->html_out .= "</comment>";

   } else {
   $this->add_msg($dbi->txt_error);
   $this->html_out .="<comment>";
   switch($op) {
       case 0:
       $this->html_out .= _SUBS_TXT."<br />";
       $this->html_out .=_SINFO_ADVERT;
       break;
       case 1:
       $this->html_out .= _REGS_TXT."<br />";
       $this->html_out .=_SINFO_ADVERT;
       break;
       default:
       break;
   }
   $this->html_out .= "</comment>";
   $this->html_out .=$form->draw();}
}

$this->html_out .= "</subs>";


?>
