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
 *Edita los datos de un cliente.
 *Returns html into var html_out
 *@package blocks_admin
 **/


$PHP_SELF = $_SERVER['PHP_SELF'];
if (preg_match("/edtacc.php/i",$PHP_SELF)) {
	Header("HTTP/1.0 404 Not Found");
	die();
}

require_once(_DirINCLUDES."forms/forms.php");
require_once(_DirINCLUDES."class_account.php");
require_once(_DirINCLUDES."class_contacts.php");
require_once(_DirINCLUDES."class_immo.php");
require_once(_DirINCLUDES."class_lovs.php");

$lovs= new lovs;
$lovs->getLovs('_LST_TP_ACCSTATE',_IDIOMA);
$lovs->getLovs('_LST_IND_ACTIVE',_IDIOMA);

global $id_account;
global $fid;
global $perm_peso;
global $lst_idiomas;
global $lst_screens;

$id_org_session=$this->auth["id_org"];
$id_pos_session=$this->auth["id_position"];

if (isset($id_account)|| isset($fid)) $tit_pag=""._EDT_ACCOUNT.""; else $tit_pag=""._ADD_ACCOUNT."";

$dbi= new account;

if (isset($id_account)) {
	$dbi->dtl_account($id_account);
	$this->add_msg($dbi->txt_error);
	extract($dbi->Record);

	//if ($lst_screens["immo"]) {
	// $link[0]["href"]="pg=edtimmo&id_account=$id_account&name_account=$name_account&view=Add";
	// $link[0]["txt"]=""._ADD_IMMO."";
	if (isset($ind_active)) {$tmp["url"]="";$tmp["txt"]=""._VER_PERFIL."";}
	else {$tmp["url"]="&view=Add";$tmp["txt"]=""._ADD_PERFIL."";}
	$link[1]["href"]="pg=edtperfil&id_account=$id_account".$tmp["url"];
	$link[1]["txt"]=$tmp["txt"];
	//}

	$link[3]["txt"]=""._SEE2PRINT."";
	$link[3]["print"]=true;
	$link[4]["txt"]=""._SEND_OUTLOOK."";
	$link[4]["name"]="contact";
} else $link=false;


$idiomas="";
reset($lst_idiomas);
while (list ($key, $val) = each ($lst_idiomas))
{
	$idiomas=$idiomas.",$key;$val";
}
$this->html_out .="<script type=\"text/vbscript\" src=\"jscripts/outlook.vbs\"></script>";
$this->html_out .= $this->pgtitle($tit_pag,true,$link);

$form = new htmlform("form1","".LK_HOME_ADM."","GET",""._SAVE."");
$form->num_cols=2;
$form->title=""._DTL_ACCOUNT."";
$form->add_textbox( "name_account", ""._NAME_ACCOUNT.":", 72, 72 );

if ($perm_peso>=8) {
	$dbi->employees($id_org_session,1);
	$form->add_static_listbox("id_position",""._ID_POSITION.":",";,".$dbi->array2list($dbi->select_array(),","));
}

$form->add_datebox("dt_create",""._DT_CREATE.":",8,10);
$form->add_static_listbox("tp_state",""._TP_STATE.":",$form->convert(""._LST_TP_ACCSTATE.""));

$form->add_textbox( "txt_address1", ""._TXT_ADDRESS1.":", 72, 72 );
$form->add_textbox( "txt_poblacion",""._TXT_POBLACION.":", 50, 50 );
$form->add_textbox( "txt_cp", ""._TXT_CP.":", 15, 15 );
$form->add_textbox( "txt_telf1", ""._TXT_TELF1.":", 13, 13 );
$form->add_textbox( "txt_telf2", ""._TXT_TELF2.":", 13, 13 );
$form->add_textbox( "txt_fax", ""._TXT_FAX.":", 13, 13 );
$form->add_textbox( "txt_email1", ""._TXT_EMAIL1.":", 40, 40 );
$form->add_textbox( "txt_web", ""._TXT_WEB.":", 20, 20 );
$form->add_static_listbox( "ind_mailing", ""._IND_MAILING.":",$form->convert(""._LST_IND_ACTIVE.""));
$form->add_textbox( "username", ""._USERNAME.":", 32, 32 );
$form->add_static_listbox( "cod_lang", ""._LANGUAGE.":",  $idiomas );
$form->add_textbox( "password", ""._PASSWORD.":", 10, 10 );

$form->add_hidden("data");
$form->fields["txt_telf2"]->col=2;
$form->fields["tp_state"]->col=2;
$form->fields["cod_lang"]->col=2;
$form->fields["ind_mailing"]->col=2;

$processed = $form->process();

if(!$processed) {

	if (isset($name_account)) $form->fields["name_account"]->value = $name_account;
	if (isset($tp_state)) $form->fields["tp_state"]->value = $tp_state;
	else $form->fields["tp_state"]->value = 1;
	if (isset($dt_create)) $form->fields["dt_create"]->value=$dt_create;
	else $form->fields["dt_create"]->value=date(""._DATE_FORMAT."");
	if (isset($cod_lang)) $form->fields["cod_lang"]->value = $cod_lang;
	else $form->fields["cod_lang"]->value = ""._IDIOMA."";
	if (isset($txt_address1)) $form->fields["txt_address1"]->value = $txt_address1;
	if (isset($txt_poblacion)) $form->fields["txt_poblacion"]->value = $txt_poblacion;
	if (isset($txt_cp)) $form->fields["txt_cp"]->value = $txt_cp;
	if (isset($txt_telf1)) $form->fields["txt_telf1"]->value = $txt_telf1;
	if (isset($txt_telf2)) $form->fields["txt_telf2"]->value = $txt_telf2;
	if (isset($txt_fax)) $form->fields["txt_fax"]->value = $txt_fax;
	if (isset($txt_email1)) $form->fields["txt_email1"]->value = $txt_email1;
	if (isset($txt_web)) $form->fields["txt_web"]->value = $txt_web;
	if (isset($ind_mailing)) $form->fields["ind_mailing"]->value = $ind_mailing;
	else $form->fields["ind_mailing"]->value = 2;
	if (isset($username)) $form->fields["username"]->value = $username;
	if (isset($password)) $form->fields["password"]->value = $password;

	if ($perm_peso>=8) {
		if (isset($id_position)) $form->fields["id_position"]->value = $id_position;
		else if (!isset($name_account)) $form->fields["id_position"]->value = $id_pos_session;
	}

	if (isset($id_account)) $pagina="&fid=$id_account";else  $pagina="";

	$form->fields["data"]->value = $this->txt_encrypt("pg=".$this->vars["pg"].$pagina);
	$this->html_out .=$form->draw();

} else
{

	reset($form->fields);
	while (list($key,$value)=each($form->fields)){
		if ($key!=="data") {$fields[$key]=$value->value;}
	}

	if (isset($fid)){
		if ($dbi->update_account($fid,$fields))
		{
			$this->redirect();
		} else {$this->add_msg($dbi->txt_error);$this->html_out .=$form->draw();}
	} else
	{
		//$fields["tp_state"]=0;
		if ($dbi->add_account($id_org_session, $id_pos_session, $fields))
		{
			$this->redirect();
			//$this->add_msg($dbi->txt_error);
		} else {$this->add_msg($dbi->txt_error);$this->html_out .=$form->draw();}
	}
}
$this->add_msg($dbi->Error); //Add native DB error
//variables para modulos posteriores
//if (isset($fid)) $id_account=$fid;

//*********** CONTACTS AND PROPERTIES *******************

if (isset($id_account)){

	$this->html_out .="<table style=\"width: 99%;\" ><tr style=\"vertical-align: top;\"><td style=\"width: 50%;\" >";
	// Contacts

	global $list_del;

	$contact = new contact;

	if (isset($list_del)) foreach($list_del as $dlte){
		if (isset($dlte) && $dlte!="") {$contact->delete_contact($dlte);$this->add_msg($contact->txt_error);}
	}


	$link=array();
	$link[2]["href"]="pg=edtcontact&id_account=$id_account&name_account=$name_account&view=Add";
	$link[2]["txt"]=""._ADD_CONTACT."";
	$this->html_out .= $this->pgtitle(_CONTACTS,false,$link);

	$fields=null;
	$keywords=null;
	$order_by=null;
	$from = 0;
	if ($contact->ver_contacts($fields, $from,1000,$order_by,$id_account)){
		$this->print_list($contact->select_array(), $from,1000,"","pg=edtcontact&id_contact=","","id_account=$id_account&name_account=$name_account&keywords=$keywords",$contact->found_rows());
	} else $this->add_msg($contact->txt_error);

	// Properties
	$this->html_out .="</td><td>";

	$this->html_out .= $this->pgtitle(_IMMOS,false,null);

	$immo= new immo;

	$lnk_acc="&id_account=$id_account";
	$view="All";
	$fields=NULL;
	$order_by="ref_immo ASC";


	if (isset($fields) || $lnk_acc!=""){
		if ($immo->ver_immos($fields,$id_org,$id_position, $view,$from,20,$order_by,$id_account)){
			$this->print_list($immo->select_array(), $from,20,"","pg=edtimmo&id_immo=",null,"view=$view&keywords=$keywords$lnk_acc", $immo->found_rows());
		} else $this->add_msg($immo->txt_error);
	}




	$this->html_out .="</td></tr></table>";
}

?>
