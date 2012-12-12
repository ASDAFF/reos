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
 *Returns xml node for list of immos with search form.
 *@package blocks_public
 **/

$PHP_SELF = $_SERVER['PHP_SELF'];

if (preg_match("/verbookings.php/i",$PHP_SELF)) {
	Header("HTTP/1.0 404 Not Found");
	die();
}

require_once(_DirINCLUDES."class_immo.php");
require_once(_DirINCLUDES."class_lovs.php");

$lovs= new lovs;
$lovs->getLovs('_LST_TP_SERVICIO',_IDIOMA);
$lovs->getLovs('_LST_TP_PROPIEDAD',_IDIOMA);
$lovs->getLovs('_LST_PRICE',_IDIOMA, TRUE, "#");
$lovs->getLovs('_LST_ORDER',_IDIOMA, TRUE);

require_once(_DirINCLUDES."forms/forms.php");

global $keywords;
global $from;
global $nm;

$id_org=$this->auth["id_org"];
$id_position=$this->auth["id_position"];

if (!isset($from)) $from=0;

$immo= new immo;

$form = new htmlform("form1","".LK_HOME_ADM."","GET",""._FIND."");
$form->num_cols=3;
$form->add_textbox("ref_immo",""._REF_IMMO.":",10,10);
$form->add_datebox( "dt_start_bk", ""._DT_START.":", 8, 10 );
$form->add_static_listbox("precio_min",_PRICE_MIN.":",_LST_PRICE, "#");
$form->add_static_listbox("order_by",""._ORDERBY.":","precio;"._PRECIO.",ref_immo;"._REF_IMMO.",txt_poblacion;"._TXT_POBLACION.",tp_propiedad;"._TP_PROPIEDAD."");
$form->add_datebox( "dt_end_bk", ""._DT_END.":", 8, 10 );
$form->add_static_listbox("precio_max",_PRICE_MAX.":",_LST_PRICE, "#");

$form->add_static_listbox("in_order",""._ORDER.":",""._LST_ORDER."");
$this->onload.="initDynamicOptionLists();"; 
$this->file_script="jscripts/dol.js";
$result=$immo->array_pob_zona($id_org,true);
$form->add_link_2listbox(""._TXT_POBLACION.":",""._TXT_ZONA.":","txt_poblacion","txt_zona",$result,""._ANY."");

$form->add_hidden("data");
$form->fields["order_by"]->col=3;
$form->fields["in_order"]->col=3;
$form->fields["precio_min"]->col=2;
$form->fields["precio_max"]->col=2;

if (!$form->process()){
	if (isset($keywords)) {
		parse_str(preg_replace("/,/","&",$keywords),$fields);
		while (list($key,$value)=each($fields))
		if ($key!="tp_state" && $key!="dt_create") $form->fields[$key]->value = $value;
	}
	$form->fields["data"]->value = $this->txt_encrypt("pg=verbookings&from=0");
} else {
	reset($form->fields);
	$keyword="";
	while (list($key,$value)=each($form->fields)){
		if ($key!=="data" && $value->value!="") {
			$fields[$key]=$value->value;
			if ($keywords=="") $keywords="$key=".$value->value; else $keywords=$keywords.",$key=".$value->value;
		}
	}
}

$order_by=$form->fields["order_by"]->value." ".$form->fields["in_order"]->value;

if (array_key_exists("nm",$this->vars_ste[count($this->vars_ste)-1]))
$tit_pag=$this->vars_ste[count($this->vars_ste)-1]["nm"];
else $tit_pag=""._RESULTS."";

$back=true;


$this->html_out .= $this->pgtitle($tit_pag,$back,null);
$this->html_out .= $form->draw();

if (isset($fields)){
	$fields["dt_isvalid"]=date(_DATE_FORMAT,mktime(0,0,0,date("m"),date("d"),  date("Y")));
	
	if ($immo->search_booking($fields,$id_org,$from,10,$order_by,true)){
		$this->print_list($immo->select_array(), $from,10,"","pg=edtbooking&id_immo=",null,"keywords=$keywords", $immo->found_rows());
	} else $this->add_msg($immo->txt_error);
}
?>
