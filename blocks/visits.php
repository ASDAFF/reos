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


$PHP_SELF = $_SERVER['PHP_SELF'];
if (preg_match("/visits.php/i",$PHP_SELF)) {
    Header("HTTP/1.0 404 Not Found");
    die();
}

require_once(_DirINCLUDES."class_visits.php");
require_once(_DirINCLUDES."forms/forms_xml.php");

global $id_org_session;
global $max_visits;
global $nm;
global $from;

$tit_pag="$nm";
$this->html_out .= $this->pgtitle($tit_pag,true,Null);

if (!isset($from)) $from=0;

$dbi = new visits;
$dbi->cod_lang = _IDIOMA;
$this->html_out .= "<comment>";
$this->html_out .= _BOOK_TXT."</comment>\n";
$this->html_out .= "<newslst>\n";
$this->html_out .= "<title>"._NEWS."</title>\n";
$dbi->visits_home( $id_org_session, $from, $max_visits );
$this->xml_list( $dbi->select_array(), $from, $max_visits, "pg=newslst",null, null, null,$dbi->found_rows());
$this->html_out .= "</newslst>\n";
//**************************** Formulari *******************************
$this->html_out .= "<visits>\n";
$form = new xmlform("form1","".LK_HOME_ADM."","GET",""._ENVIAR."");
$form->num_cols=2;

$form->add_textbox("txt_name",""._NM_PROFILE,30,30);

$form->add_textbox("txt_age",""._TXT_AGE,10,3);
$form->fields["txt_age"]->col = 2;

$form->add_textbox("txt_email",""._TXT_EMAIL,30,30);

$form->add_textbox("txt_web",""._TXT_WEB,35,50);
$form->fields["txt_web"]->col = 2;

$options = "0;"._ANY.",".$form->convert( $dbi->country_lst() );
$form->add_static_listbox("id_country", _TXT_COUNTRY, $options );

$form->add_textbox("txt_poblacion",""._NAME_POB,30,50);

$form->add_textbox("txt_como",""._TXT_COMO,30,255);

$options = $form->convert( $dbi->opinion_lst() );
$form->add_static_listbox("id_opinion", _OPINION, $options );
$form->fields["id_opinion"]->col = 2;

$form->add_smileys( "smileys", "", "txt_content" );

$form->add_textarea( "txt_content", ""._MSG."", 45, 6 );

$form->add_hidden("data");

$processed = $form->process();
if( !$processed ) {
  $form->fields["data"]->value = $this->txt_encrypt("pg=".$this->vars["pg"]."&nm=$nm&from=$from");
  $this->html_out .=$form->draw();
}
else {
  reset($form->fields);
  while (list($key,$value)=each($form->fields)){
    if ($key!=="data") {$fields[$key]=$value->value;}
  }
  
  if( $dbi->add_visit( $id_org_session, $fields ))
    $this->redirect();
  else {
   //echo"XXXXXXXXXX $dbi->txt_error<br/>";
    $this->add_msg($dbi->txt_error);$this->html_out .=$form->draw();}
}

$this->html_out .= "</visits>\n";


?>
