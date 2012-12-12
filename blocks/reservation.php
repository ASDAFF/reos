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
 *Returns xml node for contact form.
 *@package blocks_public
 **/


$PHP_SELF = $_SERVER['PHP_SELF'];
if (preg_match("/reservation.php/i",$PHP_SELF)) {
	Header("HTTP/1.0 404 Not Found");
	die();
}

require_once(_DirINCLUDES."forms/forms_xml.php");
//require_once(_DirINCLUDES."class_account.php");

global $nm;
global $id_immo;
global $id_org_session;

$tit_pag=$nm;
//$dbi= new account;


$this->html_out .= $this->pgtitle($tit_pag,true,Null);

$this->html_out .="<reservation>";

$form = new xmlform("form1","".LK_HOME_ADM."","GET",""._ENVIAR."");

$form->add_textbox( "name_account", _FULL_NAME, 72, 72 );
$form->add_textbox( "txt_email1", _TXT_EMAIL1, 40, 40 );
$form->add_textbox( "txt_telf1", _TXT_TELF1, 13, 13 );
$form->add_datebox( "dt_start", _DT_START, 8, 10 );
$form->add_datebox( "dt_end", _DT_END, 8, 10 );
$form->add_static_listbox("int_pers",_INT_PERS,"0; ,1;1,2;2,3;3,4;4,5;5,6;6,7;7,8;8,9;9,10;10,11;11");
$form->add_textarea( "txt_comment", _TXT_COMMENT, 45, 5 );
$form->add_captcha( "captcha", _CAPTCHA);

$form->add_hidden("data");
$processed = $form->process();

if(!$processed) {

	$this->html_out .="<comment>";
	$this->html_out .= _SINFO_TXT.".<br />"._SINFO_ADVERT.".";
	$this->html_out .= "</comment>";

	$pagina="&id_immo=$id_immo&nm=$nm";
	if (isset($ref_immo)) $form->fields["txt_asunt"]->value=_REF_IMMO." : ".$ref_immo;

	$form->fields["data"]->value = $this->txt_encrypt("pg=".$this->vars["pg"].$pagina);
	$this->html_out .=$form->draw();

} else {
	reset($form->fields);
	while (list($key,$value)=each($form->fields)){
		if ($key!=="data" || $key!=="captcha") {$fields[$key]=$value->value;}
	}
	reset($fields);

	$captcha=True;

	if (strlen($form->fields["captcha"]->value)!=6 || $form->fields["captcha"]->value!=$form->fields["captcha"]->captcha){
		$this->add_msg(_ERROR_CAPTCHA);
		$captcha=false;
	}

	if ($captcha) {

		require_once(_DirINCLUDES."class_account.php");
		require_once(_DirINCLUDES."class_booking.php");
		$account = new account;
		$booking = new booking;

		$fields["tp_state"]=3;  // to validate
		$fields["cod_lang"]=_IDIOMA;
		$fields["ind_mailing"]=1;   // Mailing yes
		$fields["dt_create"]=date(_DATE_FORMAT);

		if ($account->add_account($id_org_session, "NULL", $fields)) {
			$id_account = $account->last_insert_id();
		} else {
			$account->query("select id_account from ".$account->prefix."_accounts where username = '".$fields["txt_email1"]."'");
			if ($account->num_rows()==1){
				$account->next_record_assoc();
				$id_account = $account->Record["id_account"];
			} else {
				$this->add_msg($account->txt_error);
			}
		}
		if (isset($id_account)) {

			$fields["id_immo"] = $id_immo;
			$fields["id_account"] = $id_account;
			$fields["tp_state"]=1;  // to validate

			if ($booking->add_booking($fields)) {

				$id_bookings = $booking->last_insert_id();
				$to=$fields["txt_email1"];
				$subject=_MAIL_BKVALID;
				$msg = "\n\n"._MAIL_BKVALID_TXT."\n\nhttp://"._UrlSITE."/".LK_PAG.$this->url_encrypt("pg=bookvalid&id_account=$id_account&id_bookings=$id_bookings")."\n\n";
				$msg.=_LABEL_ORG." - "._SAT_DEPT;
				$this->add_email(""._SAT_EMAIL."",""._LABEL_ORG." - "._SAT_DEPT."",$to,$subject, $msg);
				$this->html_out .= "<confirm>";
				$this->html_out .= _BOOKING_VALID;
				$this->html_out .= "</confirm>";
			} else {
				$this->add_msg($booking->txt_error);
				$this->html_out .=$form->draw();
			}
		} else $this->html_out .=$form->draw();
	} else $this->html_out .=$form->draw();
}
$this->html_out .="</reservation>";


?>
