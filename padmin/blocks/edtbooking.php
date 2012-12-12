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
 *Edita el intervalo de dias de una temporada de alquiler de vacaciones.
 *Returns html into var html_out
 *@package blocks_admin
 **/

$PHP_SELF = $_SERVER['PHP_SELF'];
if (preg_match("/edtbooking.php/i",$PHP_SELF)) {
	Header("HTTP/1.0 404 Not Found");
	die();
}

require_once(_DirINCLUDES."forms/forms.php");
require_once(_DirINCLUDES."class_booking.php");
require_once(_DirINCLUDES."class_lovs.php");

$lovs= new lovs;
$lovs->getLovs('_LST_TP_RESERVSTATE',_IDIOMA);

global $id_bookings;
global $id_immo;
global $fid;
global $nm;
global $int_capacity;

$dbi= new booking;

if (isset($id_bookings)) {
	$dbi->booking_dtl($id_bookings);
	$this->add_msg($dbi->txt_error);
	extract($dbi->Record);
} else {
	if (isset($id_immo)){
		$dbi->Query("select ref_immo, int_capacity from ".$dbi->prefix."_immos where id_immo = ".$id_immo);
		if ($dbi->num_rows()>0) {
			$dbi->next_record_assoc();
			extract($dbi->Record);
		}
	}
}

$this->html_out .= $this->pgtitle($nm,true,null);

$form = new htmlform("form1","".LK_HOME_ADM."","GET",""._SAVE."");
$form->num_cols=2;
$form->add_static_listbox("tp_state",_TP_STATE.":",$form->convert(_LST_TP_RESERVSTATE));
$form->add_picker( "id_account", _NAME_ACCOUNT.":", 0, 0, "picker.php?".$this->url_encrypt("pg=veraccs&view=All&pk=id_account&pos=1&from=0"),true  );
$form->add_picker( "id_immo", _REF_IMMO.":", 0, 0, "picker.php?".$this->url_encrypt("pg=verimmo&view=All&pk=id_immo&pos=1&from=0"),true  );

$form->add_datebox( "dt_start", _DT_START.":", 8, 10 );
$form->add_datebox( "dt_end", _DT_END.":", 8, 10 );
$form->add_static_listbox("int_pers",_INT_PERS,"0; ,1;1,2;2,3;3,4;4,5;5,6;6,7;7,8;8,9;9,10;10,11;11,12;12");

$form->add_textarea( "txt_comment", _TXT_COMMENT, 45, 5 );

$form->add_hidden("data");
$form->fields["dt_end"]->col=2;

$processed = $form->process();

if (!$processed) {
	if (isset($tp_sdays)) $form->fields["tp_sdays"]->value = $tp_sdays;
	if (isset($dt_start)) $form->fields["dt_start"]->value = $dt_start;
	if (isset($dt_end)) $form->fields["dt_end"]->value = $dt_end;
	if (isset($tp_state)) $form->fields["tp_state"]->value = $tp_state;
	if (isset($int_pers)) $form->fields["int_pers"]->value = $int_pers;
	if (isset($txt_comment)) $form->fields["txt_comment"]->value = $txt_comment;

	if (isset($id_account)) $form->fields["id_account"]->value = $id_account;

	if (isset($name_account)) {
		if (isset($id_account)) {
			$form->fields["id_account"]->pktext=$name_account;
			$form->fields["id_account"]->pklink="<a href=\"".LK_PAG."".$this->url_encrypt("pg=veraccs&view=My&keywords=name_acc_search=$name_account,order_by=name_account")."\">$name_account</a>";
		} else $form->fields["id_account"]->pktext=$name_account;
	}

	if (isset($id_immo)) $form->fields["id_immo"]->value = $id_immo;

	if (isset($ref_immo)) {
		if (isset($id_immo)) {
			$form->fields["id_immo"]->pktext=$ref_immo;
			$form->fields["id_immo"]->pklink="<a href=\"".LK_PAG."".$this->url_encrypt("pg=verimmo&view=My&keywords=ref_immo=$ref_immo,order_by=ref_immo")."\">$ref_immo</a>";
		} else $form->fields["id_immo"]->pktext=$ref_immo;
	}

	if (isset($id_bookings))
	$pagina="fid=$id_bookings&int_capacity=$int_capacity";
	else
	$pagina="nm=$nm&int_capacity=$int_capacity";

	$form->fields["data"]->value = $this->txt_encrypt("pg=edtbooking&$pagina");
	$this->html_out .= $form->draw();
} else {

	reset($form->fields);
	while (list($key,$value)=each($form->fields)){
		if ($key!=="data") {$fields[$key]=$value->value;}
	}

	if (isset($fid)){
		if ($dbi->update_booking($fid,$fields, $int_capacity))	{
			$this->redirect();
		} else { $this->add_msg($dbi->txt_error); $this->html_out .=$form->draw();}
	} else {
		$fields["dt_create"]=date(_DATE_FORMAT);
		if ($dbi->add_booking($fields, $int_capacity)){
			$this->redirect();
		} else {$this->add_msg($dbi->txt_error);$this->html_out .=$form->draw();}
	}
}
/****************************************************************************************/
if (isset($fields)){
	if (array_key_exists("id_immo",$fields)) $id_immo = $fields["id_immo"];
}
if (isset($id_immo)) {
	require_once(_DirINCLUDES."class_calendar.php");
	require_once(_DirINCLUDES."class_himmo.php");

	$calendar= new Calendar;
	$calendar->cols=3;
	$himmo=new himmo;
	$today=getdate();
	$himmo->seasons($id_immo);
	$calendar->seasons=$himmo->select_array();

	if (count($calendar->seasons)>0) {
		$himmo->bookings($id_immo,(($today["year"]*100)+$today["mon"])*100+1,3); // only tp_state 3 confirmed
		$calendar->busydays=$himmo->select_array();
		$calendar->setStartMonth($today["mon"]);
		$calendar->endMonth=(int) substr($calendar->seasons[count($calendar->seasons)-1]["dt_end"],4,2);
		$this->html_out.=$calendar->getYearView($today["year"],1);
	}

}


?>