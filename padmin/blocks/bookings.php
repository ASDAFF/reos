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
 * List of property bookings for holiday rentals
 * Returns html into var html_out
 * @package blocks_admin
 **/

$PHP_SELF = $_SERVER['PHP_SELF'];
if (preg_match("/bookings.php/i",$PHP_SELF)) {
	Header("HTTP/1.0 404 Not Found");
	die();
}

require_once(_DirINCLUDES."class_booking.php");
require_once(_DirINCLUDES."forms/forms.php");
//require_once(_DirINCLUDES."class_account.php");
require_once(_DirINCLUDES."class_lovs.php");

$lovs= new lovs;
$lovs->getLovs('_LST_ORDER',_IDIOMA, true);
$lovs->getLovs('_LST_TP_RESERVSTATE',_IDIOMA);

global $list_del;
global $id;
global $from;
global $keywords;
global $lst_idiomas;

$id_org_session=$this->auth["id_org"];
$link_add[0]["href"]="pg=edtbooking&nm="._ADD." - "._BOOKINGS;
$link_add[0]["txt"]=_ADD;
$this->html_out .= $this->pgtitle(_BOOKINGS,false, $link_add);

if (!isset($from)) $from=0;

$dbi= new booking;

if (isset($list_del)) foreach($list_del as $dlte){
	if (isset($dlte) && $dlte!="") {$dbi->delete_booking($dlte);$this->add_msg($dbi->txt_error);}
}

$form = new htmlform("form1","".LK_HOME_ADM."","GET",""._FIND."");
$form->num_cols=2;
$form->add_static_listbox("tp_state",_TP_STATE.":",";"._ANY.",".$form->convert(_LST_TP_RESERVSTATE));

$form->add_static_listbox("order_by",_ORDERBY.":",$dbi->prefix."_bookings.dt_create;"._DT_CREATE.",tp_state;"._TP_STATE.",dt_start;"._DT_START.",dt_end;"._DT_END);
$form->add_static_listbox("in_order",_ORDER.":",""._LST_ORDER."");

$form->add_hidden("data");
$form->fields["order_by"]->col=2;
$form->fields["in_order"]->col=2;
//$form->fields["tp_state"]->col=2;

if (!$form->process()){
	if (isset($keywords)) {parse_str(preg_replace("/,/","&",$keywords),$fields);
	while (list($key,$value)=each($fields)) $form->fields[$key]->value = $value;
	}
	$form->fields["data"]->value = $this->txt_encrypt("pg=bookings&from=0");
} else {
	reset($form->fields);
	$keywords="";
	while (list($key,$value)=each($form->fields)){
		if ($key!=="data" && $value->value!="") {
			$fields[$key]=$value->value;
			if ($keywords=="") $keywords="$key=".$value->value; else $keywords=$keywords.",$key=".$value->value;
		}
	}
}

$order_by=$form->fields["order_by"]->value." ".$form->fields["in_order"]->value;
$this->html_out .=$form->draw();


if (isset($fields)) {
	if ($dbi->booking_list($fields,$from,10,$order_by)){
		$this->print_list($dbi->select_array(), $from,10,"","pg=edtbooking&id_bookings=","dlte=","keywords=$keywords",$dbi->found_rows());
	}
	else $this->add_msg($dbi->txt_error);
}
?>
