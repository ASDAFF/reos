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
 *Lista de clientes con formulario de busqueda.
 *Sirve para "Mis, Mi equipo, todos".
 *No esta implementada !!!
 *Returns html into var html_out
 *@package blocks_admin
 **/

$PHP_SELF = $_SERVER['PHP_SELF'];
if (preg_match("/veraccs.php/i",$PHP_SELF)) {
	Header("HTTP/1.0 404 Not Found");
	die();
}

require_once(_DirINCLUDES."class_account.php");
require_once(_DirINCLUDES."forms/forms.php");
require_once(_DirINCLUDES."class_lovs.php");

$lovs= new lovs;
$lovs->getLovs('_LST_TP_ACCSTATE',_IDIOMA);
$lovs->getLovs('_LST_IND_ACTIVE',_IDIOMA);
$lovs->getLovs('_LST_ORDER',_IDIOMA, true);
$lovs->getLovs('_LST_TP_SERVICIO',_IDIOMA);
$lovs->getLovs('_LST_TP_PROPIEDAD',_IDIOMA);
$lovs->getLovs('_LST_NUMBERS_OP',_IDIOMA, true);
$lovs->getLovs('_LST_PISCINA',_IDIOMA);

//global $dlte;
global $keywords;
global $from;
global $view;
global $lst_screens;
global $list_del;

$id_org=$this->auth["id_org"];
$id_position=$this->auth["id_position"];

if (!isset($from)) $from=0;

$account= new account;

if (isset($list_del)) foreach($list_del as $dlte){
	if (isset($dlte) && $dlte!="") {$account->delete_account($dlte);$this->add_msg($account->txt_error);}
}

if ($view=="My") $tit_pag=""._MY_ACCOUNTS."";
if ($view=="Team") $tit_pag=""._TEAM_ACCOUNTS."";
if ($view=="All") $tit_pag=""._ALL_ACCOUNTS."";
if ($view=="Join") {$tit_pag=""._BUYERS_ACCOUNTS."";$perfil=true;}else $perfil=false;

if (!$perfil) {
	$link_add[0]["href"]="pg=edtacc";
	$link_add[0]["txt"]=""._ADD_ACCOUNT."";} else $link_add=NULL;

	$form = new htmlform("form1","".LK_HOME_ADM."","GET",""._FIND."");

	if (!$perfil) {
		$form->num_cols=2;
		$form->add_textbox("name_acc_search",""._NAME_ACCOUNT.":",60,60);
		$form->add_static_listbox("tp_state",""._TP_STATE.":",";"._ANY.",".$form->convert(""._LST_TP_ACCSTATE.""));

		$form->add_static_listbox("order_by",""._ORDERBY.":",$account->prefix."_accounts.dt_create;"._DT_CREATE.",name_account;"._NAME_ACCOUNT.",txt_poblacion;"._TXT_POBLACION.",username;"._USERNAME);
		$form->add_static_listbox( "ind_mailing", ""._IND_MAILING.":",";"._ANY.",".$form->convert(""._LST_IND_ACTIVE.""));
		$form->add_static_listbox("in_order",""._ORDER.":",""._LST_ORDER."");
		$form->fields["order_by"]->col=2;
		$form->fields["in_order"]->col=2;

	} else {
		//$form->add_static_listbox("ind_active","".ind_active.":",$form->convert(""._LST_IND_ACTIVE.""));
		$form->add_static_listbox("tp_servicio",""._TP_SERVICIO.":",";"._ANY.",".$form->convert(""._LST_TP_SERVICIO.""));
		$form->add_static_listbox("tp_propiedad",""._TP_PROPIEDAD.":",";"._ANY.",".$form->convert(""._LST_TP_PROPIEDAD.""));
		$form->add_static_listbox("order_by",""._ORDERBY.":","name_account;"._NAME_ACCOUNT.",precio_compra;"._PRECIO_COMPRA.",precio_alquiler;"._PRECIO_ALQUILER);
		$form->add_static_listbox( "num_dormitorios", ""._NUM_DORMITORIOS.":", ""._LST_NUMBERS_OP."" );
		$form->add_static_listbox("in_order",""._ORDER.":",""._LST_ORDER."");
		$form->add_static_listbox( "num_wc", ""._NUM_WC.":", ""._LST_NUMBERS_OP."" );
		$form->add_static_listbox("tp_state",""._TP_STATE.":",";"._ANY.",".$form->convert(""._LST_TP_ACCSTATE.""));
		$form->add_static_listbox("ind_piscina",""._IND_PISCINA.":",";"._ANY.",".$form->convert(""._LST_PISCINA.""));
		//$form->add_static_listbox( "precio_compra", "".precio_compra.":", ""._LST_PRICE."" );
		//$form->add_textbox( "precio_alquiler", "".precio_alquiler.":", 10, 10 );
		if ($account->pobs_select())
		$form->add_static_listbox("id_poblacion",""._PREF_POBS.":",";"._ANY.",".$this->array2list($account->select_array(),","));
		$form->fields["order_by"]->col=2;
		$form->fields["in_order"]->col=2;
		$form->fields["tp_state"]->col=2;
	}




	$form->add_hidden("data");

	if (!$form->process()){
		if (isset($keywords)) {parse_str(preg_replace("/,/","&",$keywords),$fields);
		while (list($key,$value)=each($fields)) $form->fields[$key]->value = $value;
		}
		$form->fields["data"]->value = $this->txt_encrypt("pg=veraccs&view=$view&from=0");
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

	//if (isset($fields)){
		//$this->html_out .="<HTA:APPLICATION ID=\"myHTA\" APPLICATIONNAME=\"demo2\" SCROLL=\"no\" SINGLEINSTANCE=\"yes\" WINDOWSTATE=\"normal\" >\n";
		//$this->html_out .="<script type=\"text/vbscript\" src=\"jscripts/getxml.vbs\"> </script>\n";
		//$link_add[1]["op"]="href=\"\" onclick=\"return scontacts('http://"._UrlSITE.$_SERVER["PHP_SELF"]."?".$this->url_encrypt("pg=xcontact&from=$from&view=$view&keywords=$keywords&xout=1")."')\"";
		//$link_add[1]["txt"]=""._SEND_OUTLOOK."";
	//}

	$this->html_out .= $this->pgtitle($tit_pag,false,$link_add);
	$this->html_out .= $form->draw();

	if (isset($fields)) {

		if ($account->ver_accs($fields,$id_org,$id_position, $view,$from,20,$order_by)){
			if ($lst_screens["immo"]) $pag="edtacc verimmo";else $pag="edtacc";
			$this->print_list($account->select_array(), $from,20,"","pg=$pag&id_account=","dlte=","view=$view&keywords=$keywords",$account->found_rows());
		} else $this->add_msg($account->txt_error);
	}

	?>
