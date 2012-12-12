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
 *Search form is shown if var show is Null or 1. if show=0 no search form.
 *@package blocks_public
 **/

$PHP_SELF = $_SERVER['PHP_SELF'];

if (preg_match("/verimmo.php/i",$PHP_SELF)) {
	Header("HTTP/1.0 404 Not Found");
	die();
}

require_once(_DirINCLUDES."class_immo.php");
require_once(_DirINCLUDES."lists/adverts.php");
require_once(_DirINCLUDES."class_lovs.php");

$lovs= new lovs;
$lovs->getLovs('_LST_TP_SERVICIO',_IDIOMA);
$lovs->getLovs('_LST_TP_PROPIEDAD',_IDIOMA);
$lovs->getLovs('_LST_PRICE',_IDIOMA, TRUE, "#");
$lovs->getLovs('_LST_ORDER',_IDIOMA, TRUE);

if (!class_exists("xmlform")) require_once(_DirINCLUDES."forms/forms_xml.php");

global $keywords;
global $from;
global $id_account;
global $id_org_session;
global $show;
global $nm;

$out="";

if (!isset($from)) $from=0;
if (!isset($show)) $show=1;

$immo= new immo;

if (!isset($id_account)){

	$form = new xmlform("form1","".LK_HOME_ADM."","GET",""._FIND."");
	//$form->num_cols=2;
	$form->add_textbox("ref_immo",""._REF_IMMO.":",10,10);

	$form->add_static_listbox("order_by",""._ORDERBY.":","precio;"._PRECIO.",ref_immo;"._REF_IMMO.",txt_poblacion;"._TXT_POBLACION.",tp_propiedad;"._TP_PROPIEDAD."");
	$form->add_static_listbox("tp_propiedad",""._TP_PROPIEDAD.":",";"._ANY.",".$form->convert(""._LST_TP_PROPIEDAD.""));
	$form->add_static_listbox("in_order",""._ORDER.":",""._LST_ORDER."");

	$form->add_static_listbox("tp_servicio",""._TP_SERVICIO.":",";"._ANY.",".$form->convert(""._LST_TP_SERVICIO.""));

	$result=$immo->array_pob_zona($id_org_session,true);
	if ($show) { $this->onload.="initDynamicOptionLists();"; $this->file_script="padmin/jscripts/dol.js";}

	$form->add_link_2listbox(""._TXT_POBLACION.":",""._TXT_ZONA.":","txt_poblacion","txt_zona",$result,""._ANY."");
	$form->add_static_listbox("precio_min",_PRICE_MIN.":",_LST_PRICE, "#");

	$form->add_static_listbox("precio_max",_PRICE_MAX.":",_LST_PRICE, "#");

	$form->add_hidden("data");
	$form->fields["order_by"]->col=2;
	$form->fields["in_order"]->col=2;

	if (!$form->process()){
		if (isset($keywords)) {parse_str(preg_replace("/,/","&",$keywords),$fields);
		while (list($key,$value)=each($fields))
		if ($key!="tp_state" && $key!="dt_create") $form->fields[$key]->value = $value;
		}
		$form->fields["data"]->value = $this->txt_encrypt("pg=verimmo&nm="._FIND_BY_CRITERIA."&show=0&from=0");
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
	$out.=$form->draw();
	$order_by=$form->fields["order_by"]->value." ".$form->fields["in_order"]->value;
	$lnk_acc="";
} else {
	$lnk_acc="&id_account=$id_account";
	$fields=NULL;
	$order_by="ref_immo ASC";
}

if ($show) {
	$tit_pag=""._FIND_BY_CRITERIA."";$link_add=NULL;$back=false;
} else {
		
	if (array_key_exists("nm",$this->vars_ste[count($this->vars_ste)-1]))
	$tit_pag=$this->vars_ste[count($this->vars_ste)-1]["nm"];
	else $tit_pag=""._RESULTS."";
		
	$back=true;
	$link_add=NULL;
	$this->html_out .= $this->pgtitle($tit_pag,$back,$link_add);
}

if ($show) $this->html_out .= "<immosearch>"
."<title>"._FIND_BY_CRITERIA."</title>".$out."</immosearch>";

if ((isset($fields) || $lnk_acc!="") && !$show){
	$fields["tp_state"]=1;
	$fields["dt_isvalid"]=date(_DATE_FORMAT,mktime(0,0,0,date("m"),date("d"),  date("Y")));
	if ($immo->ver_immos($fields,$id_org_session,NULL, "All",$from,10,$order_by,$id_account)){
		$founds = $immo->found_rows();		
		
		$adv = new adverts;
		$this->html_out .= $adv->print_advert($immo->select_array(), $from,10,"","pg=immo&id_immo=",null,"pg=verimmo&show=0&keywords=$keywords$lnk_acc", $founds);
	} else $this->add_msg($immo->txt_error);
}
?>