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
*Edita los datos una campa�a de mailing.
*Returns html into var html_out
*@package blocks_admin
**/

$PHP_SELF = $_SERVER['PHP_SELF'];
if (preg_match("/edtmailing.php/i",$PHP_SELF)) {
    Header("HTTP/1.0 404 Not Found");
    die();
}

require_once(_DirINCLUDES."forms/forms.php");
require_once(_DirINCLUDES."class_mailing.php");
require_once(_DirINCLUDES."class_lovs.php");

$lovs= new lovs;
$lovs->getLovs('_LST_TP_SEND_TO',_IDIOMA);
$lovs->getLovs('_LST_TP_SEND',_IDIOMA);
$lovs->getLovs('_LST_TP_STE_MAIL',_IDIOMA);

global $id_mailing;
global $fid;
global $view;
global $perm_peso;
global $id_news;
global $lst_idiomas;


$id_org=$this->auth["id_org"];

$mailing= new mailing;

if (isset($id_news) && $view=="Add") {
      require_once(_DirINCLUDES."class_noticia.php");
      $noticia= new noticia;
      $result = $noticia->dtl_noticia($id_news);
      $this->add_msg($noticia->txt_error);
      $noticia->next_record();
      list($id_news, , $txt_subject, ,,, $txt_idioma, ,,,,) = ($noticia->Record);
      }

if (isset($id_mailing)) {
     if ($mailing->mailing_dtl($id_mailing))
     extract($mailing->Record);
     else $this->add_msg($mailing->txt_error);
     }



if ($view!="Add") $tit_pag=""._EDT_MAILING.""; else $tit_pag=""._ADD_MAILING."";

$this->html_out .= $this->pgtitle($tit_pag,true,NULL);

//$idiomas="";
//while (list ($key, $val) = each ($lst_idiomas))
//  {
//   $idiomas=$idiomas.",$key;$val";
//  }

$form = new htmlform("form1","".LK_HOME_ADM."","GET",""._SAVE."");

if (((isset($tp_state)) && $tp_state==2 ) || $perm_peso<16) $form->disabled=true;

$form->add_textbox( "name_mailing", ""._NAME_MAILING.":", 60, 60 );
$form->add_text( "lang", ""._LANGUAGE.":");
$form->add_hidden("txt_idioma");
$form->add_datebox("dt_create",""._DT_CREATE.":",8,10);
$form->add_textbox("dt_sent",""._DT_SENT.":",8,10);
$form->add_static_listbox("tp_send_to",""._TP_SEND_TO.":",$form->convert(""._LST_TP_SEND_TO.""));
$form->add_static_listbox("tp_send",""._TP_SEND.":",$form->convert(""._LST_TP_SEND.""));
$form->add_static_listbox("tp_state",""._TP_STATE.":",$form->convert(""._LST_TP_STE_MAIL.""));

$form->add_textbox( "txt_subject", ""._TXT_SUBJECT.":", 60, 60 );
//$form->add_textarea("txt_content", ""._txt_content.":", 40, 10);
if ($view!="Add" && ((isset($tp_state)) && $tp_state==2 )) $form->add_textbox( "num_sent", ""._NUM_SENT.":", 3, 3 );

$form->add_hidden("data");
if ($view!="Add") $pagina="fid=$id_mailing";else  $pagina="view=Add&id_news=$id_news";
$form->fields["data"]->value = $this->txt_encrypt("pg=edtmailing&$pagina");

$processed = $form->process();

if( $processed ) {

reset($form->fields);
while (list($key,$value)=each($form->fields)){
   if ($key!=="data") {$fields[$key]=$value->value;}
   }

if (isset($id_news)) $fields["id_news"]=$id_news;

if ($view!="Add")
        {
        if ($mailing->update_mailing($fid,$fields))
                //header ("Location: ".LK_PAG."".$mailing->url_encrypt("pg=verusers&from=$from")."");
                $this->redirect();
                else {$this->add_msg($mailing->txt_error);$this->html_out .=$form->draw();}
        } else
        {
        if ($mailing->add_mailing($id_org,$fields ))
                //header ("Location: ".LK_PAG."".$mailing->url_encrypt("pg=verusers")."");
                $this->redirect();
                else {$this->add_msg($mailing->txt_error);$this->html_out .=$form->draw();}
        }
} else {
       if (isset($name_mailing)) $form->fields["name_mailing"]->value=$name_mailing;
       if (isset($txt_subject)) $form->fields["txt_subject"]->value=$txt_subject;
       //if (isset($txt_content)) $form->fields["txt_content"]->value = $txt_content;
       if (isset($dt_create)) $form->fields["dt_create"]->value=$dt_create;
            else $form->fields["dt_create"]->value=date(""._DATE_FORMAT."");
       if (isset($dt_sent)) $form->fields["dt_sent"]->value=$dt_sent;
            else $form->fields["dt_sent"]->value=date(""._DATE_FORMAT."");

       if (isset($tp_state)) $form->fields["tp_state"]->value=$tp_state;
                    else $form->fields["tp_state"]->value = 1;
       if (isset($tp_state)) $form->fields["tp_send_to"]->value=$tp_send_to;
                    else $form->fields["tp_send_to"]->value = 1;

       if (isset($tp_send)) $form->fields["tp_send"]->value = $tp_send;
               else { $form->fields["tp_send"]->value = 1;}
      if (isset($txt_idioma)) {$form->fields["txt_idioma"]->value = $txt_idioma;
                              $form->fields["lang"]->value = $lst_idiomas[$txt_idioma];}
       
       if (isset($num_sent)) $form->fields["num_sent"]->value = $num_sent;
       
       $this->html_out .=$form->draw();
       if (isset($txt_content) and $tp_send==1) {

           $this->html_out .="<table class=\"noticia\"><tr><td class=\"noticia\">".str_replace("\n","<br/>", $txt_content)."</td></tr></table>\n";
           }
       }



?>
